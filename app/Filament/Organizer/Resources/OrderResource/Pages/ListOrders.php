<?php

namespace App\Filament\Organizer\Resources\OrderResource\Pages;

use App\Filament\Organizer\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua Pesanan'),
            'pending' => Tab::make('Menunggu Bayar')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending'))
                ->badge(fn () => $this->getTableQuery()->where('status', 'pending')->count() ?: null)
                ->badgeColor('warning'),
            'paid' => Tab::make('Lunas')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'paid'))
                ->badge(fn () => $this->getTableQuery()->where('status', 'paid')->count() ?: null)
                ->badgeColor('success'),
            'cancelled' => Tab::make('Dibatalkan')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'cancelled'))
                ->badge(fn () => $this->getTableQuery()->where('status', 'cancelled')->count() ?: null)
                ->badgeColor('danger'),
            'refunded' => Tab::make('Refund')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'refunded'))
                ->badge(fn () => $this->getTableQuery()->where('status', 'refunded')->count() ?: null)
                ->badgeColor('info'),
            'expired' => Tab::make('Expired')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'expired'))
                ->badge(fn () => $this->getTableQuery()->where('status', 'expired')->count() ?: null)
                ->badgeColor('gray'),
        ];
    }
}
