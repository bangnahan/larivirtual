<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SiteSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static string $view = 'filament.pages.site-settings';

    protected static ?string $navigationLabel = 'Pengaturan Beranda';

    protected static ?string $title = 'Pengaturan Banner & Pengumuman Beranda';

    protected static ?string $slug = 'site-settings';

    protected static ?string $navigationGroup = 'Pengaturan Sistem';

    protected static ?int $navigationSort = 90;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user && ($user->isSuperAdmin() || $user->role === 'admin');
    }

    public function mount(): void
    {
        $this->form->fill([
            'top_bar_enabled' => (bool) SiteSetting::get('top_bar_enabled', true),
            'top_bar_badge' => (string) SiteSetting::get('top_bar_badge', 'LIVE'),
            'top_bar_message' => (string) SiteSetting::get('top_bar_message', 'Pendaftaran Bhinneka Virtual Run 42K Masih Dibuka! Lari bisa dicicil s.d 31 Des 2026.'),
            'top_bar_button_text' => (string) SiteSetting::get('top_bar_button_text', 'Cek Nomor E-BIB Anda'),
            'top_bar_button_url' => (string) SiteSetting::get('top_bar_button_url', '/ticket'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Banner Pengumuman Header Beranda (Top Notification Bar)')
                    ->description('Kelola teks badge berkedip dan pesan pengumuman yang muncul di bagian paling atas halaman beranda publik.')
                    ->schema([
                        Forms\Components\Toggle::make('top_bar_enabled')
                            ->label('Tampilkan Banner Pengumuman di Atas Header')
                            ->helperText('Jika aktif, banner akan tampil di atas header beranda larivirtual.com. Matikan bila ingin disembunyikan.')
                            ->default(true)
                            ->live(),

                        Forms\Components\Grid::make(3)->schema([
                            Forms\Components\TextInput::make('top_bar_badge')
                                ->label('Teks Badge Berkedip (Animasi Pulse)')
                                ->placeholder('Contoh: LIVE, INFO, PROMO, HOT')
                                ->default('LIVE')
                                ->required()
                                ->live(),

                            Forms\Components\TextInput::make('top_bar_button_text')
                                ->label('Teks Tombol Kanan (Opsional)')
                                ->placeholder('Contoh: Cek Nomor E-BIB Anda')
                                ->live(),

                            Forms\Components\TextInput::make('top_bar_button_url')
                                ->label('Link / URL Tombol Kanan')
                                ->placeholder('Contoh: /ticket atau https://...')
                                ->live(),
                        ]),

                        Forms\Components\Textarea::make('top_bar_message')
                            ->label('Teks Pesan Pengumuman')
                            ->rows(3)
                            ->helperText('Teks yang tampil di sebelah badge. Anda dapat menggunakan format teks tebal HTML seperti <strong>Nama Event</strong> bila diinginkan.')
                            ->placeholder('Contoh: Pendaftaran Bhinneka Virtual Run 42K Masih Dibuka! Lari bisa dicicil s.d 31 Des 2026.')
                            ->required()
                            ->columnSpanFull()
                            ->live(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        SiteSetting::set('top_bar_enabled', ! empty($state['top_bar_enabled']) ? '1' : '0');
        SiteSetting::set('top_bar_badge', $state['top_bar_badge'] ?? 'LIVE');
        SiteSetting::set('top_bar_message', $state['top_bar_message'] ?? '');
        SiteSetting::set('top_bar_button_text', $state['top_bar_button_text'] ?? '');
        SiteSetting::set('top_bar_button_url', $state['top_bar_button_url'] ?? '');

        Notification::make()
            ->title('Pengaturan Berhasil Disimpan')
            ->body('Perubahan teks banner beranda telah disimpan dan langsung aktif di halaman depan.')
            ->success()
            ->send();
    }
}
