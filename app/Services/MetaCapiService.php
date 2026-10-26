<?php

namespace App\Services;

use App\Models\Order;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetaCapiService
{
    /**
     * Kirim event Purchase ke Meta Conversions API (CAPI)
     * Hanya dieksekusi jika status order sudah benar-benar 'paid'
     */
    public function sendPurchaseEvent(Order $order): array
    {
        // 1. Validasi ketat: Purchase HANYA boleh dikirim jika order sudah benar-benar LUNAS (paid)
        if ($order->status !== 'paid') {
            Log::warning("Meta CAPI Skipped: Order {$order->order_code} belum berstatus 'paid' (status saat ini: {$order->status})");

            return [
                'success' => false,
                'message' => "Order belum berstatus 'paid' (status saat ini: {$order->status})",
            ];
        }

        // 2. Cegah duplikasi (Idempotency): Jangan kirim ulang jika sudah pernah sukses terkirim
        if (! is_null($order->meta_capi_purchase_sent_at)) {
            Log::info("Meta CAPI Skipped: Event Purchase untuk order {$order->order_code} sudah pernah dikirim pada {$order->meta_capi_purchase_sent_at}");

            return [
                'success' => true,
                'message' => 'Event Purchase sudah pernah dikirim sebelumnya.',
                'already_sent' => true,
            ];
        }

        // 3. Ambil event dan cek apakah Meta CAPI aktif serta terkonfigurasi
        $event = $order->event;
        if (! $event || ! $event->hasMetaCapi()) {
            Log::info("Meta CAPI Skipped: Event {$event?->title} tidak mengaktifkan atau belum mengonfigurasi Meta CAPI.");

            return [
                'success' => false,
                'message' => 'Meta CAPI tidak aktif atau konfigurasi Pixel ID / Access Token belum lengkap pada event ini.',
            ];
        }

        $pixelId = trim($event->meta_pixel_id);
        $accessToken = trim($event->meta_capi_access_token);
        $testEventCode = ! empty($event->meta_capi_test_event_code) ? trim($event->meta_capi_test_event_code) : null;

        // 4. Bangun user_data dengan hashing SHA-256 sesuai standar Meta Conversions API
        $userData = $this->buildUserData($order);

        // 5. Bangun custom_data untuk event Purchase
        $customData = $this->buildCustomData($order);

        // 6. Bangun single event data
        $eventData = [
            'event_name' => 'Purchase',
            'event_time' => $order->paid_at ? $order->paid_at->timestamp : now()->timestamp,
            'event_id' => $order->order_code, // Digunakan untuk deduplikasi dengan Browser Pixel jika ada
            'event_source_url' => url("/orders/{$order->order_code}"),
            'action_source' => 'website',
            'user_data' => $userData,
            'custom_data' => $customData,
        ];

        $payload = [
            'data' => [$eventData],
        ];

        if ($testEventCode) {
            $payload['test_event_code'] = $testEventCode;
        }

        // 7. Kirim permintaan POST ke Meta Graph API v21.0
        try {
            $endpoint = "https://graph.facebook.com/v21.0/{$pixelId}/events";
            $response = Http::timeout(10)->post($endpoint.'?access_token='.urlencode($accessToken), $payload);

            if ($response->successful()) {
                $responseData = $response->json();

                // Tandai waktu berhasil dikirim untuk mencegah duplikasi
                $order->update([
                    'meta_capi_purchase_sent_at' => now(),
                ]);

                Log::info("Meta CAPI Success: Event Purchase berhasil dikirim untuk order {$order->order_code}", [
                    'pixel_id' => $pixelId,
                    'events_received' => $responseData['events_received'] ?? 1,
                    'fbtrace_id' => $responseData['fbtrace_id'] ?? null,
                ]);

                return [
                    'success' => true,
                    'data' => $responseData,
                ];
            }

            Log::error("Meta CAPI HTTP Error untuk order {$order->order_code}: ".$response->body(), [
                'status' => $response->status(),
                'pixel_id' => $pixelId,
            ]);

            return [
                'success' => false,
                'error' => $response->json('error.message') ?? $response->body(),
                'status' => $response->status(),
            ];
        } catch (Exception $e) {
            Log::error("Meta CAPI Exception untuk order {$order->order_code}: ".$e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Uji coba koneksi Meta CAPI dengan mengirim 1 test event
     */
    public function sendTestEvent(string $pixelId, string $accessToken, ?string $testEventCode = null): array
    {
        $payload = [
            'data' => [
                [
                    'event_name' => 'TestEvent',
                    'event_time' => now()->timestamp,
                    'event_id' => 'TEST-'.uniqid(),
                    'event_source_url' => url('/'),
                    'action_source' => 'website',
                    'user_data' => [
                        'em' => [hash('sha256', 'test@virun.id')],
                        'client_ip_address' => request()->ip() ?: '127.0.0.1',
                        'client_user_agent' => request()->userAgent() ?: 'Virun-Server/1.0',
                    ],
                    'custom_data' => [
                        'currency' => 'IDR',
                        'value' => 10000,
                    ],
                ],
            ],
        ];

        if ($testEventCode) {
            $payload['test_event_code'] = $testEventCode;
        }

        try {
            $endpoint = "https://graph.facebook.com/v21.0/{$pixelId}/events";
            $response = Http::timeout(10)->post($endpoint.'?access_token='.urlencode($accessToken), $payload);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'events_received' => $data['events_received'] ?? 1,
                    'fbtrace_id' => $data['fbtrace_id'] ?? null,
                ];
            }

            return [
                'success' => false,
                'error' => $response->json('error.message') ?? $response->body(),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Bangun objek user_data dengan SHA-256 hashing
     */
    protected function buildUserData(Order $order): array
    {
        $userData = [];

        // Email (wajib lowercase, trim, SHA-256)
        if (! empty($order->customer_email)) {
            $userData['em'] = [hash('sha256', strtolower(trim($order->customer_email)))];
        }

        // Nomor Telepon (hanya angka dengan kode negara 62, SHA-256)
        if (! empty($order->customer_phone)) {
            $normalizedPhone = $this->normalizePhone($order->customer_phone);
            if ($normalizedPhone) {
                $userData['ph'] = [hash('sha256', $normalizedPhone)];
            }
        }

        // Nama Depan & Belakang
        if (! empty($order->customer_name)) {
            $nameParts = explode(' ', trim($order->customer_name), 2);
            $firstName = strtolower(trim($nameParts[0]));
            $userData['fn'] = [hash('sha256', $firstName)];

            if (! empty($nameParts[1])) {
                $lastName = strtolower(trim($nameParts[1]));
                $userData['ln'] = [hash('sha256', $lastName)];
            }
        }

        // Data Geografis dari alamat pengiriman SPX (jika ada)
        $shipping = $order->shippingAddress;
        if ($shipping) {
            if (! empty($shipping->destination_city)) {
                $userData['ct'] = [hash('sha256', strtolower(trim($shipping->destination_city)))];
            }
            if (! empty($shipping->province)) {
                $userData['st'] = [hash('sha256', strtolower(trim($shipping->province)))];
            }
            if (! empty($shipping->postal_code)) {
                $userData['zp'] = [hash('sha256', strtolower(trim($shipping->postal_code)))];
            }
            $userData['country'] = [hash('sha256', 'id')];
        }

        // Client IP & User Agent (raw strings, tidak di-hash)
        $ip = $order->ip_address ?: request()->ip();
        if ($ip && $ip !== '127.0.0.1' && $ip !== '::1') {
            $userData['client_ip_address'] = $ip;
        }

        $userAgent = $order->user_agent ?: request()->userAgent();
        if ($userAgent) {
            $userData['client_user_agent'] = $userAgent;
        }

        // Browser Pixel Cookie IDs untuk deduplikasi dan pencocokan tingkat tinggi
        if (! empty($order->fbp)) {
            $userData['fbp'] = $order->fbp;
        }
        if (! empty($order->fbc)) {
            $userData['fbc'] = $order->fbc;
        }

        return $userData;
    }

    /**
     * Bangun custom_data untuk nilai belanja dan rincian produk
     */
    protected function buildCustomData(Order $order): array
    {
        $order->loadMissing(['participants.category', 'addons']);

        $contents = [];

        // Tiket per peserta
        foreach ($order->participants as $p) {
            $catId = $p->event_category_id ? 'cat-'.$p->event_category_id : 'ticket';
            $contents[] = [
                'id' => $catId,
                'quantity' => 1,
                'item_price' => (float) ($p->category?->normal_price ?? 0),
                'title' => $p->category?->name ?? 'Tiket Virtual Run',
            ];
        }

        // Add-on / Merchandise
        if ($order->addons) {
            foreach ($order->addons as $a) {
                $contents[] = [
                    'id' => 'addon-'.($a->event_addon_id ?? $a->id),
                    'quantity' => (int) $a->quantity,
                    'item_price' => (float) $a->unit_price,
                    'title' => $a->addon_name,
                ];
            }
        }

        $ticketCount = $order->participants->count();
        $addonCount = $order->addons ? $order->addons->sum('quantity') : 0;

        return [
            'currency' => 'IDR',
            'value' => (float) $order->grand_total,
            'content_type' => 'product',
            'order_id' => $order->order_code,
            'contents' => $contents,
            'num_items' => $ticketCount + $addonCount,
        ];
    }

    /**
     * Normalisasi nomor telepon format E.164 untuk Meta CAPI (contoh: 08123456789 -> 628123456789)
     */
    public function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        } elseif (str_starts_with($digits, '8')) {
            $digits = '62'.$digits;
        }

        return $digits;
    }
}
