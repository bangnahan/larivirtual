<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Services\OrderManagementService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $oldStatus = $record->status;
        $newStatus = $data['status'] ?? $oldStatus;

        if ($oldStatus !== $newStatus) {
            try {
                app(OrderManagementService::class)->updateStatus(
                    order: $record,
                    newStatus: $newStatus,
                    reason: 'Diubah melalui form Edit Pesanan oleh '.(auth()->user()?->name ?? 'Admin'),
                    adminName: auth()->user()?->name ?? 'Admin'
                );
                unset($data['status']);
            } catch (\Throwable $e) {
                Log::error('Gagal mengubah status di EditOrder: '.$e->getMessage(), [
                    'order_id' => $record->id,
                    'trace' => $e->getTraceAsString(),
                ]);

                Notification::make()
                    ->title('Gagal Memperbarui Status Pesanan')
                    ->body('Terjadi kesalahan: '.$e->getMessage())
                    ->danger()
                    ->persistent()
                    ->send();

                $this->halt();
            }
        }

        $record->update($data);

        return $record;
    }
}
