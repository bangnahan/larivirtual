<?php

namespace App\Services;

use App\Models\Event;
use App\Models\OrderAddon;
use App\Models\Participant;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductionPoExportService
{
    /**
     * Generate rekap komprehensif PO Pabrik (Medali, Jersey Event, Jersey Finisher, dan Manifest Packing)
     */
    public function exportVendorPo(Event $event): StreamedResponse
    {
        $filename = 'rekap-produksi-jersey-po-vendor-'.str($event->slug ?: 'event')->slug().'-'.now()->format('Ymd-His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->streamDownload(function () use ($event) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM agar Excel di Windows/Mac otomatis membaca format aksen & karakter rapi
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Ambil seluruh peserta dengan order lunas (paid)
            $participants = Participant::with(['order.shippingAddress', 'category', 'jerseySize'])
                ->whereHas('order', function ($q) use ($event) {
                    $q->where('event_id', $event->id)
                        ->where('status', 'paid');
                })
                ->orderBy('bib_number', 'asc')
                ->get();

            $jerseySizes = $event->jerseySizes()->orderBy('id', 'asc')->get();

            // 1. Hitung total medali per kategori
            $medalCountsByCategory = [];
            $totalMedals = 0;

            // 2. Hitung matriks Jersey Event: [size_id][jersey_type] => count
            $eventJerseyMatrix = [];
            $totalEventJerseys = 0;

            // 3. Hitung matriks Jersey Finisher: [size_id][jersey_type] => count
            $finisherJerseyMatrix = [];
            $totalFinisherJerseys = 0;

            foreach ($participants as $p) {
                $category = $p->category;
                $sizeId = $p->jersey_size_id ?: 'no_size';
                $type = $p->jersey_type ?: 'short';

                // Medali
                if ($p->getsMedal()) {
                    $catName = $category ? $category->name : 'Unassigned';
                    $medalCountsByCategory[$catName] = ($medalCountsByCategory[$catName] ?? 0) + 1;
                    $totalMedals++;
                }

                // Jersey Event
                if ($p->getsEventJersey()) {
                    $eventJerseyMatrix[$sizeId][$type] = ($eventJerseyMatrix[$sizeId][$type] ?? 0) + 1;
                    $totalEventJerseys++;
                }

                // Jersey Finisher
                if ($p->getsFinisherJersey()) {
                    $finisherJerseyMatrix[$sizeId][$type] = ($finisherJerseyMatrix[$sizeId][$type] ?? 0) + 1;
                    $totalFinisherJerseys++;
                }
            }

            // Hitung Add-ons jika ada
            $addonsData = [];
            $eventAddons = $event->addons()->orderBy('sort_order', 'asc')->get();
            $totalAddonsQty = 0;

            if ($eventAddons->isNotEmpty()) {
                $addonCounts = OrderAddon::query()
                    ->join('orders', 'order_addons.order_id', '=', 'orders.id')
                    ->where('orders.event_id', $event->id)
                    ->where('orders.status', 'paid')
                    ->selectRaw('order_addons.event_addon_id, sum(order_addons.quantity) as total_qty')
                    ->groupBy('order_addons.event_addon_id')
                    ->pluck('total_qty', 'event_addon_id')
                    ->toArray();

                foreach ($eventAddons as $addon) {
                    $qty = (int) ($addonCounts[$addon->id] ?? 0);
                    $totalAddonsQty += $qty;
                    $addonsData[] = [
                        'name' => $addon->name,
                        'price' => $addon->price,
                        'paid_qty' => $qty,
                    ];
                }
            }

            // ==========================================
            // HEADER DOKUMEN
            // ==========================================
            fputcsv($handle, ['REKAP KEBUTUHAN PRODUKSI JERSEY & MERCHANDISE - VIRUN']);
            fputcsv($handle, ['REKAP KEBUTUHAN PRODUKSI VENDOR (PURCHASE ORDER)']);
            fputcsv($handle, ['Nama Event:', $event->title]);
            fputcsv($handle, ['Tanggal Unduh:', now()->format('d F Y, H:i').' WIB']);
            fputcsv($handle, ['Filter Status:', 'HANYA PESANAN LUNAS (PAID)']);
            fputcsv($handle, []);

            // ==========================================
            // BAGIAN 1: RINGKASAN ORDER PRODUKSI (EXECUTIVE SUMMARY)
            // ==========================================
            fputcsv($handle, ['BAGIAN 1: RINGKASAN ORDER PRODUKSI (EXECUTIVE SUMMARY)']);
            fputcsv($handle, ['JENIS BARANG / ITEM', 'TOTAL KEBUTUHAN (PCS)', 'TARGET VENDOR / PABRIK']);
            fputcsv($handle, ['Keping Medali Finisher Logam', $totalMedals, 'Pabrik Medali (Logam / Zinc Alloy)']);
            fputcsv($handle, ['Jersey Event Resmi', $totalEventJerseys, 'Konveksi Sublimasi Jersey Event']);
            fputcsv($handle, ['Jersey Finisher Eksklusif', $totalFinisherJerseys, 'Konveksi Sublimasi Jersey Finisher']);
            if ($totalAddonsQty > 0) {
                fputcsv($handle, ['Merchandise & Paket Add-on Tambahan', $totalAddonsQty, 'Vendor Merchandise']);
            }
            fputcsv($handle, ['Total Peserta Lunas (Terdaftar)', $participants->count(), 'Basis Data Produksi']);
            fputcsv($handle, []);
            fputcsv($handle, ['----------------------------------------------------------------------------------------------------------------']);
            fputcsv($handle, []);

            // ==========================================
            // BAGIAN 2: DETAIL PO PABRIK MEDALI
            // ==========================================
            fputcsv($handle, ['BAGIAN 2: DETAIL ORDER PABRIK MEDALI']);
            fputcsv($handle, ['KATEGORI / PAKET LARI', 'JUMLAH PESERTA BERHAK MEDALI', 'TOTAL MEDALI (PCS)']);

            if (empty($medalCountsByCategory)) {
                fputcsv($handle, ['(Tidak ada kategori yang memuat medali pada event ini)', '0', '0']);
            } else {
                foreach ($medalCountsByCategory as $catName => $count) {
                    fputcsv($handle, [$catName, $count, $count]);
                }
            }
            fputcsv($handle, ['TOTAL KEPING MEDALI YANG HARUS DICETAK', '', $totalMedals]);
            fputcsv($handle, []);
            fputcsv($handle, ['----------------------------------------------------------------------------------------------------------------']);
            fputcsv($handle, []);

            // ==========================================
            // BAGIAN 3: DETAIL PO KONVEKSI - JERSEY EVENT
            // ==========================================
            fputcsv($handle, ['BAGIAN 3: DETAIL PO KONVEKSI - JERSEY EVENT (BERDASARKAN UKURAN & MODEL LENGAN)']);
            fputcsv($handle, [
                'UKURAN JERSEY',
                'GENDER',
                'LEBAR DADA (CM)',
                'PANJANG BADAN (CM)',
                'LENGAN PENDEK (PCS)',
                'LENGAN PANJANG (PCS)',
                'SINGLET (PCS)',
                'TOTAL PER UKURAN (PCS)',
            ]);

            $totalEventShort = 0;
            $totalEventLong = 0;
            $totalEventSinglet = 0;

            foreach ($jerseySizes as $j) {
                $short = $eventJerseyMatrix[$j->id]['short'] ?? 0;
                $long = $eventJerseyMatrix[$j->id]['long'] ?? 0;
                $singlet = $eventJerseyMatrix[$j->id]['singlet'] ?? 0;
                $subtotal = $short + $long + $singlet;

                $totalEventShort += $short;
                $totalEventLong += $long;
                $totalEventSinglet += $singlet;

                fputcsv($handle, [
                    $j->size_name,
                    ucfirst((string) $j->gender_type),
                    $j->chest_width_cm ?: '-',
                    $j->body_length_cm ?: '-',
                    $short,
                    $long,
                    $singlet,
                    $subtotal,
                ]);
            }

            // Jika ada peserta tanpa size
            if (isset($eventJerseyMatrix['no_size'])) {
                $short = $eventJerseyMatrix['no_size']['short'] ?? 0;
                $long = $eventJerseyMatrix['no_size']['long'] ?? 0;
                $singlet = $eventJerseyMatrix['no_size']['singlet'] ?? 0;
                $subtotal = $short + $long + $singlet;

                $totalEventShort += $short;
                $totalEventLong += $long;
                $totalEventSinglet += $singlet;

                fputcsv($handle, [
                    'Belum Memilih Size',
                    '-',
                    '-',
                    '-',
                    $short,
                    $long,
                    $singlet,
                    $subtotal,
                ]);
            }

            fputcsv($handle, [
                'TOTAL JERSEY EVENT KESELURUHAN',
                '',
                '',
                '',
                $totalEventShort,
                $totalEventLong,
                $totalEventSinglet,
                $totalEventJerseys,
            ]);
            fputcsv($handle, []);
            fputcsv($handle, ['----------------------------------------------------------------------------------------------------------------']);
            fputcsv($handle, []);

            // ==========================================
            // BAGIAN 4: DETAIL PO KONVEKSI - JERSEY FINISHER
            // ==========================================
            fputcsv($handle, ['BAGIAN 4: DETAIL PO KONVEKSI - JERSEY FINISHER (BERDASARKAN UKURAN & MODEL LENGAN)']);
            fputcsv($handle, [
                'UKURAN JERSEY FINISHER',
                'GENDER',
                'LEBAR DADA (CM)',
                'PANJANG BADAN (CM)',
                'LENGAN PENDEK (PCS)',
                'LENGAN PANJANG (PCS)',
                'SINGLET (PCS)',
                'TOTAL PER UKURAN (PCS)',
            ]);

            $totalFinisherShort = 0;
            $totalFinisherLong = 0;
            $totalFinisherSinglet = 0;

            foreach ($jerseySizes as $j) {
                $short = $finisherJerseyMatrix[$j->id]['short'] ?? 0;
                $long = $finisherJerseyMatrix[$j->id]['long'] ?? 0;
                $singlet = $finisherJerseyMatrix[$j->id]['singlet'] ?? 0;
                $subtotal = $short + $long + $singlet;

                $totalFinisherShort += $short;
                $totalFinisherLong += $long;
                $totalFinisherSinglet += $singlet;

                fputcsv($handle, [
                    $j->size_name,
                    ucfirst((string) $j->gender_type),
                    $j->chest_width_cm ?: '-',
                    $j->body_length_cm ?: '-',
                    $short,
                    $long,
                    $singlet,
                    $subtotal,
                ]);
            }

            // Jika ada peserta tanpa size
            if (isset($finisherJerseyMatrix['no_size'])) {
                $short = $finisherJerseyMatrix['no_size']['short'] ?? 0;
                $long = $finisherJerseyMatrix['no_size']['long'] ?? 0;
                $singlet = $finisherJerseyMatrix['no_size']['singlet'] ?? 0;
                $subtotal = $short + $long + $singlet;

                $totalFinisherShort += $short;
                $totalFinisherLong += $long;
                $totalFinisherSinglet += $singlet;

                fputcsv($handle, [
                    'Belum Memilih Size',
                    '-',
                    '-',
                    '-',
                    $short,
                    $long,
                    $singlet,
                    $subtotal,
                ]);
            }

            fputcsv($handle, [
                'TOTAL JERSEY FINISHER KESELURUHAN',
                '',
                '',
                '',
                $totalFinisherShort,
                $totalFinisherLong,
                $totalFinisherSinglet,
                $totalFinisherJerseys,
            ]);
            fputcsv($handle, []);

            // Bagian Addons jika ada
            if (! empty($addonsData)) {
                fputcsv($handle, ['----------------------------------------------------------------------------------------------------------------']);
                fputcsv($handle, []);
                fputcsv($handle, ['REKAP KEBUTUHAN PRODUKSI ADD-ON & MERCHANDISE EVENT']);
                fputcsv($handle, ['BAGIAN 4B: DETAIL MERCHANDISE / ADD-ON TERJUAL']);
                fputcsv($handle, ['NAMA PRODUK ADD-ON', 'HARGA SATUAN (RP)', 'TOTAL TERJUAL (PCS)']);
                foreach ($addonsData as $ad) {
                    fputcsv($handle, [$ad['name'], number_format($ad['price'], 0, ',', '.'), $ad['paid_qty']]);
                }
                fputcsv($handle, ['TOTAL SELURUH ITEM ADD-ON', '', $totalAddonsQty]);
                fputcsv($handle, []);
            }

            fputcsv($handle, ['----------------------------------------------------------------------------------------------------------------']);
            fputcsv($handle, []);

            // ==========================================
            // BAGIAN 5: MANIFEST PACKING GUDANG & PENGIRIMAN RACEPACK
            // ==========================================
            fputcsv($handle, ['BAGIAN 5: MANIFEST PACKING GUDANG & PENGIRIMAN RACEPACK (CHECKLIST TIAP PELARI)']);
            fputcsv($handle, [
                'NO',
                'KODE INVOICE',
                'NOMOR BIB',
                'NAMA PELARI',
                'WHATSAPP',
                'ALAMAT LENGKAP PENGIRIMAN',
                'KOTA / KABUPATEN',
                'PROVINSI',
                'KODE POS',
                'KATEGORI PAKET TERPILIH',
                'DAPAT MEDALI?',
                'JERSEY EVENT RESMI',
                'JERSEY FINISHER',
                'ADD-ON MERCHANDISE',
                'STATUS ORDER',
            ]);

            $no = 1;
            foreach ($participants as $p) {
                $order = $p->order;
                $shipping = $order?->shippingAddress;

                $medalText = $p->getsMedal() ? 'YA (Dapat Medali)' : 'TIDAK';
                $eventJerseyText = $p->getsEventJersey() ? $p->getEventJerseySpec() : 'TIDAK';
                $finisherJerseyText = $p->getsFinisherJersey() ? $p->getFinisherJerseySpec() : 'TIDAK';

                // Rangkum add-on per order
                $addonSummary = '-';
                if ($order && $order->addons->isNotEmpty()) {
                    $addonSummary = $order->addons->map(fn ($oa) => $oa->addon_name.' (x'.$oa->quantity.')')->implode(', ');
                }

                $fullAddress = $shipping ? ($shipping->address ?: $p->address) : $p->address;
                $city = $shipping ? ($shipping->city ?: $p->city) : $p->city;
                $province = $shipping ? $shipping->province : '-';
                $postalCode = $shipping ? $shipping->postal_code : '-';

                fputcsv($handle, [
                    $no++,
                    $order?->order_code ?: '-',
                    $p->bib_number ?: 'PROSES',
                    $p->full_name,
                    "'".($p->phone ?: '-'),
                    $fullAddress ?: '-',
                    $city ?: '-',
                    $province ?: '-',
                    $postalCode ?: '-',
                    $p->category?->name ?: '-',
                    $medalText,
                    $eventJerseyText,
                    $finisherJerseyText,
                    $addonSummary,
                    'LUNAS (PAID)',
                ]);
            }

            fclose($handle);
        }, $filename, $headers);
    }
}
