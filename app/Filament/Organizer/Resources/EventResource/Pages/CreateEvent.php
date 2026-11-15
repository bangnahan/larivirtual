<?php

namespace App\Filament\Organizer\Resources\EventResource\Pages;

use App\Filament\Organizer\Resources\EventResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEvent extends CreateRecord
{
    protected static string $resource = EventResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();

        if ($user && ! $user->isSuperAdmin()) {
            $data['organizer_id'] = $user->organizer_id;
        }

        $config = [];

        $ebibMap = [
            'ebib_text_color' => 'text_color',
            'ebib_bib_top_mm' => 'bib_top_mm',
            'ebib_bib_font_size_pt' => 'bib_font_size_pt',
            'ebib_name_top_mm' => 'name_top_mm',
            'ebib_name_font_size_pt' => 'name_font_size_pt',
            'ebib_show_event_title' => 'show_event_title',
            'ebib_show_category_badge' => 'show_category_badge',
            'ebib_show_qr_code' => 'show_qr_code',
            'ebib_show_pin_holes' => 'show_pin_holes',
        ];

        foreach ($ebibMap as $formKey => $configKey) {
            if (array_key_exists($formKey, $data)) {
                $config[$configKey] = $data[$formKey];
                unset($data[$formKey]);
            }
        }

        $data['ebib_layout_config'] = $config;

        return $data;
    }
}
