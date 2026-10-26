<?php

namespace App\Filament\Resources\ActivitySubmissionResource\Pages;

use App\Filament\Resources\ActivitySubmissionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateActivitySubmission extends CreateRecord
{
    protected static string $resource = ActivitySubmissionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['calculated_pace']) && ! empty($data['distance_km']) && ! empty($data['duration_seconds'])) {
            $data['calculated_pace'] = round(($data['duration_seconds'] / 60) / (float) $data['distance_km'], 2);
        }

        if (empty($data['validation_status'])) {
            $data['validation_status'] = 'approved';
        }

        return $data;
    }
}
