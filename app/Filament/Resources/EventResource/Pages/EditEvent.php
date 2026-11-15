<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEvent extends EditRecord
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('preview_ebib')
                ->label('👁️ Preview E-BIB (PDF)')
                ->icon('heroicon-o-eye')
                ->color('warning')
                ->url(fn () => route('admin.events.preview_ebib', $this->record))
                ->openUrlInNewTab(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (empty($data['ebib_layout_config']) && ! empty($this->record->ebib_layout_config)) {
            $data['ebib_layout_config'] = $this->record->ebib_layout_config;
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['ebib_layout_config']) && is_array($data['ebib_layout_config'])) {
            $existing = is_array($this->record->ebib_layout_config) ? $this->record->ebib_layout_config : [];
            $data['ebib_layout_config'] = array_merge($existing, $data['ebib_layout_config']);
        }

        return $data;
    }
}
