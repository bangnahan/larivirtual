<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\MetaCapiService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendMetaCapiPurchaseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 10;

    public function __construct(public Order $order) {}

    public function handle(MetaCapiService $metaCapiService): void
    {
        $order = $this->order->fresh(['event', 'participants.category', 'addons', 'shippingAddress']);

        if (! $order) {
            Log::warning("SendMetaCapiPurchaseJob skipped: Order #{$this->order->id} not found.");

            return;
        }

        // Pastikan order benar-benar PAID
        if (! $order->isPaid()) {
            Log::info("SendMetaCapiPurchaseJob skipped: Order #{$order->id} ({$order->order_code}) status is {$order->status}, not paid.");

            return;
        }

        // Cegah pengiriman ganda
        if ($order->isMetaCapiPurchaseSent()) {
            Log::info("SendMetaCapiPurchaseJob skipped: Order #{$order->id} ({$order->order_code}) already sent at {$order->meta_capi_purchase_sent_at}");

            return;
        }

        try {
            $metaCapiService->sendPurchaseEvent($order);
        } catch (Exception $e) {
            Log::error("SendMetaCapiPurchaseJob Exception for Order #{$order->id}: ".$e->getMessage());
        }
    }
}
