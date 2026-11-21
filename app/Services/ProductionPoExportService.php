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
            $participants = Participant::with(['order.shippingAddress', 'order.addons', 'category', 'jerseySize'])
                ->whereHas('order', function ($q) use ($event) {
                    $q->where('event_id', $event->id)
                        ->where('status', 'paid');
                })
                ->orderBy('bib_number', 'asc')
                ->get();

            // Konsolidasi ukuran jersey agar tidak ada baris ganda di PO vendor
            $rawJerseySizes = $event->jerseySizes()->orderBy('id', 'asc')->get();
            $consolidatedSizes = [];

            foreach ($rawJerseySizes as $raw) {
                $name = strtoupper(trim((string) $raw->size_name));
                if (! isset($consolidatedSizes[$name])) {
                    $consolidatedSizes[$name] = [
                        'size_name' => $name,
                        'gender_type' => $raw->gender_type ? ucfirst((string) $raw->gender_type) : 'Unisex',
                        'chest_width_cm' => $raw->chest_width_cm ?: '-',
                        'body_length_cm' => $raw->body_length_cm ?: '-',
                    ];
                }
            }

            // 1. Hitung total medali per kategori
            $medalCountsByCategory = [];
            $totalMedals = 0;

            // 2. Hitung matriks Jersey Event: [size_name][jersey_type] => count
            $eventJerseyMatrix = [];
            $totalEventJerseys = 0;

            // 3. Hitung matriks Jersey Finisher: [size_name][jersey_type] => count
            $finisherJerseyMatrix = [];
            $totalFinisherJerseys = 0;

            foreach ($participants as $p) {
                $category = $p->category;
                $sizeName = $p->jerseySize ? strtoupper(trim((string) $p->jerseySize->size_name)) : 'no_size';
                $type = $p->jersey_type ?: 'short';

                // Medali
                if ($p->getsMedal()) {
                    $catName = $category ? $category->name : 'Unassigned';
                    $medalCountsByCategory[$catName] = ($medalCountsByCategory[$catName] ?? 0) + 1;
                    $totalMedals++;
                }

                // Jersey Event
                if ($p->getsEventJersey()) {
                    $eventJerseyMatrix[$sizeName][$type] = ($eventJerseyMatrix[$sizeName][$type] ?? 0) + 1;
                    $totalEventJerseys++;
                }

                // Jersey Finisher
                if ($p->getsFinisherJersey()) {
                    $finisherJerseyMatrix[$sizeName][$type] = ($finisherJerseyMatrix[$sizeName][$type] ?? 0) + 1;
                    $totalFinisherJerseys++;
                }
            }

            // Masukkan size jika ada yang dipilih peserta tapi belum tercatat di template master
            foreach (array_unique(array_merge(array_keys($eventJerseyMatrix), array_keys($finisherJerseyMatrix))) as $name) {
                if ($name !== 'no_size' && ! isset($consolidatedSizes[$name])) {
                    $consolidatedSizes[$name] = [
                        'size_name' => $name,
                        'gender_type' => 'Unisex',
                        'chest_width_cm' => '-',
                        'body_length_cm' => '-',
                    ];
                }
            }

            // Urutkan size berdasarkan urutan standar konveksi baju lari
            $standardOrder = [
                'XS' => 1,
                'S' => 2,
                'M' => 3,
                'L' => 4,
                'XL' => 5,
                'XXL' => 6,
                '2XL' => 6,
                '3XL' => 7,
                '4XL' => 8,
                '5XL' => 9,
                '6XL' => 10,
                '7XL' => 11,
            ];

            uasort($consolidatedSizes, function ($a, $b) use ($standardOrder) {
                $orderA = $standardOrder[$a['size_name']] ?? 99;
                $orderB = $standardOrder[$b['size_name']] ?? 99;

                if ($orderA === $orderB) {
                    return strcmp($a['size_name'], $b['size_name']);
                }

                return $orderA <=> $orderB;
            });

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

            foreach ($consolidatedSizes as $sizeName => $info) {
                $short = $eventJerseyMatrix[$sizeName]['short'] ?? 0;
                $long = $eventJerseyMatrix[$sizeName]['long'] ?? 0;
                $singlet = $eventJerseyMatrix[$sizeName]['singlet'] ?? 0;
                $subtotal = $short + $long + $singlet;

                $totalEventShort += $short;
                $totalEventLong += $long;
                $totalEventSinglet += $singlet;

                fputcsv($handle, [
                    $info['size_name'],
                    $info['gender_type'],
                    $info['chest_width_cm'],
                    $info['body_length_cm'],
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

            foreach ($consolidatedSizes as $sizeName => $info) {
                $short = $finisherJerseyMatrix[$sizeName]['short'] ?? 0;
                $long = $finisherJerseyMatrix[$sizeName]['long'] ?? 0;
                $singlet = $finisherJerseyMatrix[$sizeName]['singlet'] ?? 0;
                $subtotal = $short + $long + $singlet;

                $totalFinisherShort += $short;
                $totalFinisherLong += $long;
                $totalFinisherSinglet += $singlet;

                fputcsv($handle, [
                    $info['size_name'],
                    $info['gender_type'],
                    $info['chest_width_cm'],
                    $info['body_length_cm'],
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

                $fullAddress = $shipping ? ($shipping->address_detail ?: $p->address) : $p->address;
                $city = $shipping ? ($shipping->destination_city ?: $p->city) : $p->city;
                $district = $shipping ? $shipping->destination_district : null;
                $cityDisplay = $district ? "{$district}, {$city}" : ($city ?: '-');
                $province = $shipping ? ($shipping->province ?: '-') : '-';
                $postalCode = $shipping ? ($shipping->postal_code ?: '-') : '-';

                fputcsv($handle, [
                    $no++,
                    $order?->order_code ?: '-',
                    $p->bib_number ?: 'PROSES',
                    $p->full_name,
                    "'".($p->phone ?: '-'),
                    $fullAddress ?: '-',
                    $cityDisplay,
                    $province,
                    $postalCode,
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
