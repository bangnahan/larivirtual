<?php

namespace App\Filament\Organizer\Resources\ParticipantResource\Pages;

use App\Filament\Organizer\Resources\ParticipantResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListParticipants extends ListRecords
{
    protected static string $resource = ParticipantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua Pelari'),
            'finishers' => Tab::make('Finisher 🏅')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('finisher_status', 'finisher'))
                ->badge(fn () => static::getResource()::getEloquentQuery()->where('finisher_status', 'finisher')->count())
                ->badgeColor('success'),
            'in_progress' => Tab::make('In Progress 🏃')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('finisher_status', '!=', 'finisher'))
                ->badge(fn () => static::getResource()::getEloquentQuery()->where('finisher_status', '!=', 'finisher')->count())
                ->badgeColor('warning'),
        ];
    }
}
