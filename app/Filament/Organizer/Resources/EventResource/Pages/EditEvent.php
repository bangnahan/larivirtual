<?php

namespace App\Filament\Organizer\Resources\EventResource\Pages;

use App\Filament\Organizer\Resources\EventResource;
use App\Services\ProductionPoExportService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEvent extends EditRecord
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('leaderboard')
                ->label('🏆 Leaderboard')
                ->icon('heroicon-o-trophy')
                ->color('warning')
                ->url(fn () => route('public.leaderboard.show', $this->record->slug))
                ->openUrlInNewTab(),
            Actions\Action::make('export_po_pabrik')
                ->label('📦 Export PO Pabrik')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(fn (ProductionPoExportService $service) => $service->exportVendorPo($this->record)),
            Actions\Action::make('preview_ebib')
                ->label('👁️ Preview E-BIB (PDF)')
                ->icon('heroicon-o-eye')
                ->color('info')
                ->url(fn () => route('admin.events.preview_ebib', $this->record))
                ->openUrlInNewTab(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $config = is_array($this->record->ebib_layout_config) ? $this->record->ebib_layout_config : [];

        $data['ebib_text_color'] = $config['text_color'] ?? '#ffffff';
        $data['ebib_bib_top_mm'] = $config['bib_top_mm'] ?? 45;
        $data['ebib_bib_font_size_pt'] = $config['bib_font_size_pt'] ?? 64;
        $data['ebib_name_top_mm'] = $config['name_top_mm'] ?? 82;
        $data['ebib_name_font_size_pt'] = $config['name_font_size_pt'] ?? 18;
        $data['ebib_show_event_title'] = $config['show_event_title'] ?? false;
        $data['ebib_show_category_badge'] = $config['show_category_badge'] ?? true;
        $data['ebib_show_qr_code'] = $config['show_qr_code'] ?? true;
        $data['ebib_show_pin_holes'] = $config['show_pin_holes'] ?? true;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $user = auth()->user();

        if ($user && ! $user->isSuperAdmin()) {
            $data['organizer_id'] = $user->organizer_id;
        }

        $config = is_array($this->record->ebib_layout_config) ? $this->record->ebib_layout_config : [];

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
