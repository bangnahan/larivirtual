<?php

namespace App\Services;

use App\Jobs\SendMetaCapiPurchaseJob;
use App\Jobs\SendTicketEmailJob;
use App\Models\Order;
use App\Models\PaymentLog;
use Exception;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class OrderManagementService
{
    public const VALID_STATUSES = [
        'paid',
        'pending',
        'cancelled',
        'refunded',
        'expired',
        'failed',
    ];

    public function __construct(
        protected BibGeneratorService $bibGeneratorService
    ) {}

    /**
     * Update status pesanan / transaksi secara aman dengan penyesuaian kuota, stok, nomor BIB, dan audit log
     *
     * @throws Exception
     */
    public function updateStatus(
        Order $order,
        string $newStatus,
        ?string $reason = null,
        ?string $adminName = null,
        bool $sendTicketEmail = true
    ): Order {
        if (! in_array($newStatus, self::VALID_STATUSES, true)) {
            throw new InvalidArgumentException("Status '{$newStatus}' tidak valid untuk pesanan.");
        }

        return DB::transaction(function () use ($order, $newStatus, $reason, $adminName, $sendTicketEmail) {
            /** @var Order $lockedOrder */
            $lockedOrder = Order::where('id', $order->id)->lockForUpdate()->firstOrFail();
            $oldStatus = $lockedOrder->status;

            if ($oldStatus === $newStatus) {
                return $lockedOrder;
            }

            // 1. Logika Transisi Menuju 'PAID' (Lunas)
            if ($newStatus === 'paid') {
                // Jika sebelumnya dibatalkan, refund, atau expired, alokasikan kembali kuota & stok
                if (in_array($oldStatus, ['cancelled', 'refunded', 'expired', 'failed'], true)) {
                    $this->allocateSlotsAndStock($lockedOrder);
                }

                $lockedOrder->update([
                    'status' => 'paid',
                    'paid_at' => $lockedOrder->paid_at ?: now(),
                ]);

                // Auto-assign nomor BIB jika belum ada
                if ($lockedOrder->event?->auto_assign_bib) {
                    foreach ($lockedOrder->participants as $participant) {
                        if (empty($participant->bib_number)) {
                            $this->bibGeneratorService->autoAssignBib($participant);
                        }
                    }
                }

                // Kirim E-Ticket via email
                if ($sendTicketEmail) {
                    SendTicketEmailJob::dispatchAfterResponse($lockedOrder);
                }

                // Kirim event Purchase ke Meta CAPI jika belum pernah terkirim
                if (! $lockedOrder->isMetaCapiPurchaseSent()) {
                    SendMetaCapiPurchaseJob::dispatchAfterResponse($lockedOrder);
                }
            }
            // 2. Logika Transisi Menuju Pembatalan / Refund / Expired / Gagal
            elseif (in_array($newStatus, ['cancelled', 'refunded', 'expired', 'failed'], true)) {
                // Jika sebelumnya aktif (pending / paid), kembalikan kuota kategori dan stok
                if (in_array($oldStatus, ['pending', 'paid'], true)) {
                    $this->releaseSlotsAndStock($lockedOrder);
                }

                $lockedOrder->update([
                    'status' => $newStatus,
                ]);
            }
            // 3. Logika Transisi Menuju 'PENDING'
            elseif ($newStatus === 'pending') {
                // Jika sebelumnya dibatalkan / refund / expired, ambil kembali kuota & stok
                if (in_array($oldStatus, ['cancelled', 'refunded', 'expired', 'failed'], true)) {
                    $this->allocateSlotsAndStock($lockedOrder);
                }

                $lockedOrder->update([
                    'status' => 'pending',
                ]);
            } else {
                $lockedOrder->update([
                    'status' => $newStatus,
                ]);
            }

            // Catat Log Perubahan Status untuk Keperluan Audit Keuangan
            PaymentLog::create([
                'order_id' => $lockedOrder->id,
                'gateway' => 'manual_admin',
                'event_type' => 'status_change',
                'status' => $newStatus,
                'tripay_reference' => $lockedOrder->tripay_reference,
                'payload' => [
                    'changed_by' => $adminName ?: (auth()->user()?->name ?: 'Admin / Operator'),
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'reason' => $reason ?: 'Perubahan status transaksi secara manual oleh admin.',
                    'timestamp' => now()->toIso8601String(),
                ],
            ]);

            return $lockedOrder->fresh(['participants', 'items', 'addons']);
        });
    }

    /**
     * Kurangi kuota yang diambil (kembalikan kuota lomba & stok kaos/add-on)
     */
    protected function releaseSlotsAndStock(Order $order): void
    {
        foreach ($order->participants as $participant) {
            if ($participant->category) {
                $participant->category->decrement('slots_taken', 1);
            }
            if ($participant->jerseySize) {
                $participant->jerseySize->decrement('allocated_stock', 1);
            }
        }

        foreach ($order->addons as $addonItem) {
            if ($addonItem->eventAddon) {
                $addonItem->eventAddon->decrement('allocated_stock', $addonItem->quantity);
            }
        }
    }

    /**
     * Tambah kuota yang diambil (alokasikan kuota lomba & stok kaos/add-on)
     */
    protected function allocateSlotsAndStock(Order $order): void
    {
        foreach ($order->participants as $participant) {
            if ($participant->category) {
                $participant->category->increment('slots_taken', 1);
            }
            if ($participant->jerseySize) {
                $participant->jerseySize->increment('allocated_stock', 1);
            }
        }

        foreach ($order->addons as $addonItem) {
            if ($addonItem->eventAddon) {
                $addonItem->eventAddon->increment('allocated_stock', $addonItem->quantity);
            }
        }
    }
}
