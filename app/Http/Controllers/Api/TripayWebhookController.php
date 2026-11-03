<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentLog;
use App\Services\BibGeneratorService;
use App\Services\OrderManagementService;
use App\Services\TripayService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TripayWebhookController extends Controller
{
    public function __construct(
        protected TripayService $tripayService,
        protected BibGeneratorService $bibGeneratorService,
        protected OrderManagementService $orderManagementService
    ) {}

    public function handle(Request $request): JsonResponse
    {
        $rawJson = $request->getContent();
        $signature = $request->header('X-Callback-Signature');

        // Validasi keaslian signature webhook Tripay
        if (! $this->tripayService->validateCallbackSignature($rawJson, $signature)) {
            Log::warning('Tripay Webhook: Invalid callback signature received.', [
                'ip' => $request->ip(),
                'signature' => $signature,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Invalid signature',
            ], 403);
        }

        $data = json_decode($rawJson, true);
        if (! $data || ! isset($data['merchant_ref'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid payload format',
            ], 400);
        }

        $merchantRef = $data['merchant_ref'];
        $tripayStatus = strtoupper($data['status'] ?? '');
        $tripayReference = $data['reference'] ?? null;
        $callbackEvent = $request->header('X-Callback-Event', 'payment_status');

        // Dukungan test callback / URL validator dari Dashboard Tripay
        if (in_array(strtoupper($merchantRef), ['TEST', 'TEST_MERCHANT_REF', 'SAMPLE', 'PING', 'TESTING'])) {
            return response()->json([
                'success' => true,
                'message' => 'Tripay webhook connection verified successfully',
            ]);
        }

        $order = Order::where('order_code', $merchantRef)->first();

        // Catat Payment Log untuk audit dan pelacakan
        PaymentLog::create([
            'order_id' => $order?->id,
            'gateway' => 'tripay',
            'event_type' => $callbackEvent,
            'tripay_reference' => $tripayReference,
            'status' => $tripayStatus,
            'payload' => $data,
            'signature' => $signature,
            'ip_address' => $request->ip(),
        ]);

        if (! $order) {
            Log::error("Tripay Webhook: Order {$merchantRef} not found.");

            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        try {
            if ($tripayStatus === 'PAID') {
                if (! empty($data['reference'])) {
                    $order->update(['tripay_reference' => $data['reference']]);
                }

                $this->orderManagementService->updateStatus(
                    order: $order,
                    newStatus: 'paid',
                    reason: 'Webhook Tripay terverifikasi (PAID)',
                    adminName: 'Tripay Payment Gateway',
                    sendTicketEmail: true
                );
            } elseif (in_array($tripayStatus, ['EXPIRED', 'FAILED'], true)) {
                $this->orderManagementService->updateStatus(
                    order: $order,
                    newStatus: strtolower($tripayStatus),
                    reason: "Webhook Tripay ({$tripayStatus})",
                    adminName: 'Tripay Payment Gateway'
                );
            }

            return response()->json(['success' => true]);
        } catch (Exception $e) {
            Log::error("Tripay Webhook Error [{$merchantRef}]: ".$e->getMessage());

            return response()->json(['success' => false, 'message' => 'Server error'], 500);
        }
    }
}
