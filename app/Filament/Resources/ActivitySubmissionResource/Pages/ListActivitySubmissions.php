<?php

namespace App\Filament\Resources\ActivitySubmissionResource\Pages;

use App\Filament\Resources\ActivitySubmissionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListActivitySubmissions extends ListRecords
{
    protected static string $resource = ActivitySubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Input Laporan Lari'),
        ];
    }
}
