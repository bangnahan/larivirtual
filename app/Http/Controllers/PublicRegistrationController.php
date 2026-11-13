<?php

namespace App\Http\Controllers;

use App\Jobs\SendPaymentPendingEmailJob;
use App\Jobs\SendTicketEmailJob;
use App\Models\Event;
use App\Models\EventAddon;
use App\Models\EventCategory;
use App\Models\JerseySize;
use App\Models\Order;
use App\Models\OrderAddon;
use App\Models\OrderItem;
use App\Models\OrderShippingAddress;
use App\Models\Participant;
use App\Services\BibGeneratorService;
use App\Services\OrderManagementService;
use App\Services\SpxShippingService;
use App\Services\TripayService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PublicRegistrationController extends Controller
{
    public function __construct(
        protected TripayService $tripayService,
        protected BibGeneratorService $bibService,
        protected SpxShippingService $shippingService
    ) {}

    /**
     * Step 1: Tampilkan katalog pemilihan tiket & kuota (Tampilan ala Jelatix)
     */
    public function show(string $slug)
    {
        $event = Event::where('slug', $slug)
            ->with([
                'categories' => fn ($q) => $q->where('is_active', true)->orderBy('distance_km', 'asc'),
                'addons' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
                'organizer',
            ])
            ->firstOrFail();

        return view('public.register', compact('event'));
    }

    /**
     * Step 2: Tampilkan formulir data peserta, SPX shipping, dan metode pembayaran
     */
    public function showParticipants(Request $request, string $slug)
    {
        $event = Event::where('slug', $slug)
            ->with([
                'categories' => fn ($q) => $q->where('is_active', true)->orderBy('distance_km', 'asc'),
                'jerseySizes',
                'customFields',
                'addons' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order'),
                'organizer',
            ])
            ->firstOrFail();

        if (! $event->isRegistrationOpen()) {
            return redirect()->route('public.register.show', $slug)
                ->withErrors(['registration' => 'Pendaftaran untuk event ini belum dibuka atau telah ditutup.']);
        }

        $tickets = $request->input('tickets') ?: old('tickets');
        $addons = $request->input('addons') ?: old('addons');

        // Jika tiket belum ditemukan (misal reload GET atau redirect dari checkout error), baca dari session
        if (empty($tickets)) {
            $tickets = session()->get("event_{$event->id}_tickets", []);
        }

        // Jika session kosong tapi ada old input peserta, rekontruksi daftar tiket dari input peserta
        if (empty($tickets) && is_array(old('participants'))) {
            $tickets = [];
            foreach (old('participants') as $p) {
                $cId = $p['category_id'] ?? null;
                if ($cId) {
                    $tickets[$cId] = ($tickets[$cId] ?? 0) + 1;
                }
            }
        }

        if ($addons === null) {
            $addons = session()->get("event_{$event->id}_addons", []);
        }

        // Filter tiket dengan kuantitas > 0
        $selectedTickets = [];
        $totalCount = 0;
        $totalPrice = 0;

        if (is_array($tickets)) {
            foreach ($tickets as $catId => $qty) {
                $qty = (int) $qty;
                if ($qty > 0) {
                    $category = $event->categories->firstWhere('id', (int) $catId);
                    if ($category) {
                        $available = $category->available_slots;
                        if ($qty > $available) {
                            return redirect()->route('public.register.show', $slug)
                                ->withErrors(['tickets' => "Jumlah tiket untuk kategori {$category->name} melebihi sisa kuota yang tersedia ({$available} tiket)."]);
                        }
                        $selectedTickets[$catId] = [
                            'category' => $category,
                            'qty' => $qty,
                            'subtotal' => $category->getCurrentPrice() * $qty,
                        ];
                        $totalCount += $qty;
                        $totalPrice += ($category->getCurrentPrice() * $qty);
                    }
                }
            }
        }

        if ($totalCount <= 0) {
            return redirect()->route('public.register.show', $slug)
                ->withErrors(['tickets' => 'Silakan tentukan minimal 1 tiket lari sebelum melanjutkan ke pengisian data peserta.']);
        }

        if ($totalCount > 10) {
            return redirect()->route('public.register.show', $slug)
                ->withErrors(['tickets' => 'Maksimal pembelian tiket dalam satu transaksi adalah 10 tiket.']);
        }

        // Filter add-on dengan kuantitas > 0
        $selectedAddons = [];
        $totalAddonsPrice = 0;
        $totalAddonsCount = 0;

        if (is_array($addons)) {
            foreach ($addons as $addonId => $qty) {
                $qty = (int) $qty;
                if ($qty > 0) {
                    $addon = $event->addons->firstWhere('id', (int) $addonId);
                    if ($addon && $addon->is_active) {
                        if (! $addon->isUnlimited()) {
                            $available = $addon->available_stock;
                            if ($qty > $available) {
                                return redirect()->route('public.register.show', $slug)
                                    ->withErrors(['addons' => "Jumlah add-on {$addon->name} melebihi sisa stok yang tersedia ({$available} pcs)."]);
                            }
                        }
                        $subtotalAddon = (float) $addon->price * $qty;
                        $selectedAddons[$addonId] = [
                            'addon' => $addon,
                            'qty' => $qty,
                            'unit_price' => (float) $addon->price,
                            'subtotal' => $subtotalAddon,
                        ];
                        $totalAddonsCount += $qty;
                        $totalAddonsPrice += $subtotalAddon;
                    }
                }
            }
        }

        // Simpan tiket dan add-on ke session untuk antisipasi reload / validation back()
        session()->put("event_{$event->id}_tickets", $tickets);
        session()->put("event_{$event->id}_addons", $addons);

        // Siapkan flat array slot peserta untuk looping form di blade
        $participantSlots = [];
        $slotIndex = 0;
        foreach ($selectedTickets as $catId => $item) {
            for ($i = 0; $i < $item['qty']; $i++) {
                $participantSlots[] = [
                    'index' => $slotIndex++,
                    'category' => $item['category'],
                    'itemNumber' => $i + 1,
                    'totalOfCategory' => $item['qty'],
                ];
            }
        }

        $paymentChannels = $this->tripayService->getPaymentChannels();

        return view('public.participants', compact(
            'event',
            'selectedTickets',
            'participantSlots',
            'totalCount',
            'totalPrice',
            'selectedAddons',
            'totalAddonsCount',
            'totalAddonsPrice',
            'paymentChannels'
        ));
    }

    /**
     * Proses checkout dan request pembayaran ke Tripay
     */
    public function checkout(Request $request, string $slug): RedirectResponse
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        if (! $event->isRegistrationOpen()) {
            return redirect()->route('public.register.show', $slug)
                ->withErrors(['registration' => 'Pendaftaran untuk event ini belum dibuka atau telah ditutup.']);
        }

        // Cek apakah payload bertipe multi-peserta (array `participants`) atau single-peserta
        if ($request->has('participants') && is_array($request->input('participants'))) {
            $validator = Validator::make($request->all(), [
                'buyer_name' => 'required|string|max:100',
                'buyer_email' => 'required|email|max:100',
                'buyer_phone' => 'required|string|max:20',
                'payment_method' => 'required|string',
                'waiver_accepted' => 'accepted',
                'participants' => 'required|array|min:1|max:10',
                'participants.*.category_id' => 'required|exists:event_categories,id',
                'participants.*.jersey_size_id' => 'nullable|exists:jersey_sizes,id',
                'participants.*.jersey_type' => 'nullable|in:short,long,singlet',
                'participants.*.bib_name' => 'required|string|max:40',
                'participants.*.gender' => 'required|in:male,female',
                'participants.*.birth_date' => 'nullable|date',
                'participants.*.phone' => 'nullable|string|max:20',
                'participants.*.email' => 'nullable|email|max:100',
                'participants.*.custom_fields' => 'nullable|array',
            ], [
                'waiver_accepted.accepted' => 'Anda harus menyetujui pernyataan pelepasan tanggung jawab (Waiver) untuk melanjutkan.',
                'payment_method.required' => 'Silakan pilih salah satu metode pembayaran yang tersedia.',
                'buyer_name.required' => 'Nama lengkap pemesan wajib diisi.',
                'buyer_email.required' => 'Email pemesan wajib diisi untuk pengiriman e-ticket.',
                'buyer_phone.required' => 'Nomor WhatsApp pemesan wajib diisi.',
            ]);

            if ($validator->fails()) {
                return redirect()->route('public.register.participants', $slug)
                    ->withInput()
                    ->withErrors($validator);
            }

            $buyerName = $request->input('buyer_name');
            $buyerEmail = $request->input('buyer_email');
            $buyerPhone = $request->input('buyer_phone');
            $participantsData = $request->input('participants');

            // Validasi: jika kategori menyertakan jersey, ukuran jersey wajib dipilih
            foreach ($participantsData as $idx => $p) {
                $cat = EventCategory::find($p['category_id']);
                if ($cat && $cat->includes_jersey && empty($p['jersey_size_id'])) {
                    return redirect()->route('public.register.participants', $slug)
                        ->withInput()
                        ->withErrors([
                            "participants.{$idx}.jersey_size_id" => 'Ukuran jersey wajib dipilih untuk peserta #'.($idx + 1)." ({$cat->name}).",
                        ]);
                }
            }
        } else {
            // Fallback untuk single-participant (backward compatibility)
            $singleCat = EventCategory::find($request->input('category_id'));
            $isJerseyReq = $singleCat ? $singleCat->includes_jersey : true;

            $validator = Validator::make($request->all(), [
                'category_id' => 'required|exists:event_categories,id',
                'jersey_size_id' => $isJerseyReq ? 'required|exists:jersey_sizes,id' : 'nullable|exists:jersey_sizes,id',
                'jersey_type' => 'nullable|in:short,long,singlet',
                'gender' => 'required|in:male,female',
                'full_name' => 'nullable|string|max:100',
                'birth_date' => 'nullable|date',
                'phone' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:100',
                'payment_method' => 'required|string',
                'waiver_accepted' => 'accepted',
                'custom_fields' => 'nullable|array',
            ]);

            if ($validator->fails()) {
                return redirect()->route('public.register.participants', $slug)
                    ->withInput()
                    ->withErrors($validator);
            }

            $buyerName = $request->input('full_name') ?: ($request->input('bib_name') ?: 'Buyer');
            $buyerEmail = $request->input('email') ?: 'buyer@example.com';
            $buyerPhone = $request->input('phone') ?: '08120000000';
            $participantsData = [
                [
                    'category_id' => $request->input('category_id'),
                    'jersey_size_id' => $request->input('jersey_size_id'),
                    'jersey_type' => $request->input('jersey_type', 'short'),
                    'full_name' => $request->input('full_name') ?: $request->input('bib_name'),
                    'gender' => $request->input('gender'),
                    'birth_date' => $request->input('birth_date'),
                    'phone' => $request->input('phone') ?: $buyerPhone,
                    'email' => $request->input('email') ?: $buyerEmail,
                    'custom_fields' => $request->input('custom_fields'),
                ],
            ];
        }

        $addons = $request->input('addons', session()->get("event_{$event->id}_addons", []));

        try {
            $order = DB::transaction(function () use ($request, $event, $buyerName, $buyerEmail, $buyerPhone, $participantsData, $addons) {
                // 1. Kunci dan validasi kuota per kategori lari
                $categoryCounts = [];
                foreach ($participantsData as $p) {
                    $catId = (int) $p['category_id'];
                    $categoryCounts[$catId] = ($categoryCounts[$catId] ?? 0) + 1;
                }

                $categoryModels = [];
                $totalTicketsPrice = 0;

                foreach ($categoryCounts as $catId => $qtyNeeded) {
                    $category = EventCategory::where('id', $catId)
                        ->where('event_id', $event->id)
                        ->lockForUpdate()
                        ->firstOrFail();

                    if (($category->slots_taken + $qtyNeeded) > $category->quota) {
                        $available = max(0, $category->quota - $category->slots_taken);
                        throw new Exception("Mohon maaf, sisa kuota kategori {$category->name} tersisa {$available} tiket (Anda memesan {$qtyNeeded} tiket).");
                    }

                    $category->increment('slots_taken', $qtyNeeded);
                    $categoryModels[$catId] = $category;
                    $totalTicketsPrice += ($category->getCurrentPrice() * $qtyNeeded);
                }

                // 2. Kunci dan validasi stok jersey (hanya untuk kategori yang menyertakan jersey)
                $jerseyCounts = [];
                foreach ($participantsData as $p) {
                    $catId = (int) $p['category_id'];
                    $cat = $categoryModels[$catId] ?? null;
                    if ($cat && $cat->includes_jersey && ! empty($p['jersey_size_id'])) {
                        $jerseyId = (int) $p['jersey_size_id'];
                        $jerseyCounts[$jerseyId] = ($jerseyCounts[$jerseyId] ?? 0) + 1;
                    }
                }

                $jerseyModels = [];
                foreach ($jerseyCounts as $jerseyId => $qtyNeeded) {
                    $jersey = JerseySize::where('id', $jerseyId)
                        ->where('event_id', $event->id)
                        ->lockForUpdate()
                        ->firstOrFail();

                    if (! $jersey->isUnlimited() && ($jersey->allocated_stock + $qtyNeeded) > $jersey->stock) {
                        $available = max(0, $jersey->stock - $jersey->allocated_stock);
                        throw new Exception("Mohon maaf, sisa stok ukuran kaos {$jersey->size_name} tersisa {$available} pcs (Anda memilih {$qtyNeeded} pcs).");
                    }

                    $jersey->increment('allocated_stock', $qtyNeeded);
                    $jerseyModels[$jerseyId] = $jersey;
                }

                // Hitung biaya tambahan upgrade jersey (model lengan dan big size)
                $totalJerseySurcharges = 0;
                $processedParticipants = [];

                foreach ($participantsData as $pData) {
                    $catId = (int) $pData['category_id'];
                    $cat = $categoryModels[$catId] ?? null;

                    if ($cat && ! $cat->includes_jersey) {
                        // Kategori tanpa jersey (misal: Medali Only / E-Pass)
                        $pData['jersey_size_id'] = null;
                        $pData['jersey_type'] = null;
                        $pData['jersey_sleeve_surcharge'] = 0;
                        $pData['jersey_size_surcharge'] = 0;
                        $pData['jersey_surcharge'] = 0;
                    } else {
                        $jerseyId = (int) ($pData['jersey_size_id'] ?? 0);
                        $jerseyType = $pData['jersey_type'] ?? 'short';
                        if (! in_array($jerseyType, ['short', 'long', 'singlet'], true)) {
                            $jerseyType = 'short';
                        }

                        $sleeveSurcharge = ($jerseyType === 'long') ? 20000 : 0;
                        $sizeSurcharge = (float) ($jerseyModels[$jerseyId]->surcharge ?? 0);
                        $pSurcharge = $sleeveSurcharge + $sizeSurcharge;

                        $totalJerseySurcharges += $pSurcharge;

                        $pData['jersey_type'] = $jerseyType;
                        $pData['jersey_sleeve_surcharge'] = $sleeveSurcharge;
                        $pData['jersey_size_surcharge'] = $sizeSurcharge;
                        $pData['jersey_surcharge'] = $pSurcharge;
                    }

                    $processedParticipants[] = $pData;
                }
                $participantsData = $processedParticipants;

                // 3. Kunci dan validasi add-on merchandise yang dipilih
                $addonRecords = [];
                $totalAddonsPrice = 0;
                $totalAddonsWeightGrams = 0;

                if (is_array($addons)) {
                    foreach ($addons as $addonId => $qty) {
                        $qty = (int) $qty;
                        if ($qty > 0) {
                            $addonModel = EventAddon::where('id', (int) $addonId)
                                ->where('event_id', $event->id)
                                ->where('is_active', true)
                                ->lockForUpdate()
                                ->first();

                            if ($addonModel) {
                                if (! $addonModel->isUnlimited() && ($addonModel->allocated_stock + $qty) > $addonModel->stock) {
                                    $available = max(0, $addonModel->stock - $addonModel->allocated_stock);
                                    throw new Exception("Mohon maaf, sisa stok add-on {$addonModel->name} tersisa {$available} pcs (Anda memilih {$qty} pcs).");
                                }

                                $addonModel->increment('allocated_stock', $qty);
                                $subtotalAddon = (float) $addonModel->price * $qty;
                                $totalAddonsPrice += $subtotalAddon;
                                $totalAddonsWeightGrams += ($addonModel->weight_grams * $qty);

                                $addonRecords[] = [
                                    'event_addon_id' => $addonModel->id,
                                    'addon_name' => $addonModel->name,
                                    'quantity' => $qty,
                                    'unit_price' => $addonModel->price,
                                    'total_price' => $subtotalAddon,
                                ];
                            }
                        }
                    }
                }

                // 4. Hitung ongkos kirim SPX jika data alamat disediakan (termasuk bobot add-on)
                $destinationCity = $request->input('destination_city');
                $destinationDistrict = $request->input('destination_district');
                $shippingCost = 0;
                $shippingCalculation = null;
                $totalWeightGrams = (count($participantsData) * 500) + $totalAddonsWeightGrams;

                if (! empty($destinationCity) && ! empty($destinationDistrict)) {
                    $shippingCalculation = $this->shippingService->calculateShipping(
                        $destinationCity,
                        $destinationDistrict,
                        $totalWeightGrams
                    );
                    $shippingCost = $shippingCalculation['shipping_cost'];
                }

                $platformFee = 0;
                $subtotal = $totalTicketsPrice + $totalAddonsPrice + $totalJerseySurcharges;
                $paymentMethod = $request->input('payment_method', 'QRIS');
                $paymentGatewayFee = $this->tripayService->calculateFee($paymentMethod, $subtotal + $shippingCost);
                $grandTotal = $subtotal + $platformFee + $shippingCost + $paymentGatewayFee;
                $orderCode = 'JLTX-'.date('Ymd').'-'.strtoupper(Str::random(5));

                $order = Order::create([
                    'event_id' => $event->id,
                    'order_code' => $orderCode,
                    'customer_name' => $buyerName,
                    'customer_email' => $buyerEmail,
                    'customer_phone' => $buyerPhone,
                    'subtotal' => $subtotal,
                    'addons_total' => $totalAddonsPrice,
                    'jersey_surcharges_total' => $totalJerseySurcharges,
                    'shipping_cost' => $shippingCost,
                    'platform_fee' => $platformFee,
                    'payment_gateway_fee' => $paymentGatewayFee,
                    'grand_total' => $grandTotal,
                    'status' => 'pending',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'fbp' => $request->cookie('_fbp') ?: $request->input('_fbp'),
                    'fbc' => $request->cookie('_fbc') ?: $request->input('_fbc'),
                ]);

                // Simpan data alamat pengiriman jika kalkulasi SPX berhasil
                if ($shippingCalculation) {
                    OrderShippingAddress::create([
                        'order_id' => $order->id,
                        'recipient_name' => $request->input('recipient_name') ?: $buyerName,
                        'recipient_phone' => $request->input('recipient_phone') ?: $buyerPhone,
                        'province' => $request->input('province'),
                        'destination_city' => $shippingCalculation['destination_city'],
                        'destination_district' => $shippingCalculation['destination_district'],
                        'postal_code' => $request->input('postal_code'),
                        'address_detail' => $request->input('address_detail') ?: '-',
                        'total_weight_grams' => $totalWeightGrams,
                        'courier_name' => 'SPX',
                        'service_type' => 'Regular',
                        'shipping_cost' => $shippingCost,
                        'estimated_sla_days' => $shippingCalculation['sla_days'],
                        'shipping_status' => 'pending',
                    ]);
                }

                // 5. Buat OrderItem per kategori tiket
                foreach ($categoryCounts as $catId => $qty) {
                    $cat = $categoryModels[$catId];
                    $price = $cat->getCurrentPrice();
                    OrderItem::create([
                        'order_id' => $order->id,
                        'event_category_id' => $cat->id,
                        'quantity' => $qty,
                        'unit_price' => $price,
                        'total_price' => $price * $qty,
                    ]);
                }

                // 6. Buat OrderAddon per item merchandise / add-on
                foreach ($addonRecords as $addonRec) {
                    OrderAddon::create([
                        'order_id' => $order->id,
                        'event_addon_id' => $addonRec['event_addon_id'],
                        'addon_name' => $addonRec['addon_name'],
                        'quantity' => $addonRec['quantity'],
                        'unit_price' => $addonRec['unit_price'],
                        'total_price' => $addonRec['total_price'],
                    ]);
                }

                // 7. Buat data Participant per pelari
                foreach ($participantsData as $pData) {
                    $rawBib = ! empty($pData['bib_name']) ? trim($pData['bib_name']) : (! empty($pData['full_name']) ? trim($pData['full_name']) : 'RUNNER');
                    $fullName = ! empty($pData['full_name']) ? trim($pData['full_name']) : $rawBib;
                    $safeBibName = mb_substr(strtoupper($rawBib), 0, 40);
                    $safeFinishTime = ! empty($pData['estimated_finish_time']) ? mb_substr(trim($pData['estimated_finish_time']), 0, 45) : null;

                    Participant::create([
                        'order_id' => $order->id,
                        'event_category_id' => (int) $pData['category_id'],
                        'jersey_size_id' => ! empty($pData['jersey_size_id']) ? (int) $pData['jersey_size_id'] : null,
                        'jersey_type' => $pData['jersey_type'] ?? 'short',
                        'jersey_sleeve_surcharge' => $pData['jersey_sleeve_surcharge'] ?? 0,
                        'jersey_size_surcharge' => $pData['jersey_size_surcharge'] ?? 0,
                        'jersey_surcharge' => $pData['jersey_surcharge'] ?? 0,
                        'full_name' => $fullName,
                        'bib_name' => $safeBibName,
                        'gender' => $pData['gender'],
                        'birth_date' => $pData['birth_date'] ?? null,
                        'phone' => $pData['phone'] ?? $buyerPhone,
                        'email' => $pData['email'] ?? $buyerEmail,
                        'custom_fields_data' => $pData['custom_fields'] ?? null,
                        'waiver_accepted' => true,
                        'waiver_accepted_at' => now(),
                        'waiver_ip_address' => $request->ip(),
                    ]);
                }

                return $order;
            });

            // Request transaksi ke Tripay
            $tripayData = $this->tripayService->createTransaction($order, $request->input('payment_method'));

            // Hapus data session pemesanan HANYA SETELAH request ke Tripay sukses
            session()->forget(["event_{$event->id}_tickets", "event_{$event->id}_addons"]);

            // Kirim email panduan pembayaran & pemberitahuan batas bayar 1 jam ke pendaftar
            SendPaymentPendingEmailJob::dispatchAfterResponse($order);

            // Jika Tripay mengembalikan checkout_url resmi, langsung alihkan calon peserta ke halaman pembayaran Tripay
            if (! empty($tripayData['checkout_url']) && filter_var($tripayData['checkout_url'], FILTER_VALIDATE_URL) && ! str_contains($tripayData['checkout_url'], url('/orders/'))) {
                return redirect()->away($tripayData['checkout_url']);
            }

            return redirect()->route('public.order.show', $order->order_code);
        } catch (Exception $e) {
            return redirect()->route('public.register.participants', $slug)
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Tampilkan halaman invoice pembayaran & instruksi bayar Tripay
     */
    public function showInvoice(string $orderCode)
    {
        $order = Order::where('order_code', $orderCode)
            ->with(['event', 'items.category', 'addons', 'participants.category', 'participants.jerseySize', 'shippingAddress'])
            ->firstOrFail();

        return view('public.invoice', compact('order'));
    }

    /**
     * Polling real-time cek status pembayaran
     */
    public function checkStatus(string $orderCode): JsonResponse
    {
        $order = Order::where('order_code', $orderCode)->firstOrFail();

        return response()->json([
            'status' => $order->status,
            'is_paid' => $order->isPaid(),
            'paid_at' => $order->paid_at?->format('d M Y, H:i'),
        ]);
    }

    /**
     * Simulasi pelunasan untuk Sandbox Testing Tripay
     */
    public function simulatePay(string $orderCode): RedirectResponse
    {
        if (config('tripay.mode') !== 'sandbox' && ! app()->environment('local', 'testing')) {
            abort(403, 'Simulasi pelunasan hanya tersedia pada mode sandbox.');
        }

        $order = Order::where('order_code', $orderCode)->firstOrFail();

        if ($order->status !== 'paid') {
            app(OrderManagementService::class)->updateStatus(
                order: $order,
                newStatus: 'paid',
                reason: 'Simulasi Pelunasan Instan (Tripay Sandbox Mode)',
                adminName: 'Sandbox Tester'
            );
        }

        return redirect()->route('public.order.show', $orderCode)->with('success', 'Pembayaran Sandbox Berhasil Dikonfirmasi!');
    }

    /**
     * Kirim ulang E-Ticket resmi ke email peserta/pemesan
     */
    public function resendTicket(string $orderCode): RedirectResponse
    {
        $order = Order::where('order_code', $orderCode)->firstOrFail();

        if (! $order->isPaid()) {
            return redirect()->route('public.order.show', $orderCode)->withErrors(['error' => 'Pesanan belum lunas. Silakan lakukan pembayaran terlebih dahulu.']);
        }

        SendTicketEmailJob::dispatchAfterResponse($order);

        return redirect()->route('public.order.show', $orderCode)->with('success', 'E-Ticket & Nomor BIB berhasil dikirimkan ulang ke email Anda!');
    }
}
