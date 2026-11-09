<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Models\Event;
use App\Models\Organizer;
use App\Services\MetaCapiService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static ?string $navigationLabel = 'Event Virtual Run';

    protected static ?string $modelLabel = 'Event Virtual Run';

    protected static ?string $pluralModelLabel = 'Daftar Event Virtual Run';

    protected static ?string $navigationGroup = 'Virtual Run & Event';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('EventTabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Informasi Lomba')
                            ->schema([
                                Forms\Components\Hidden::make('organizer_id')
                                    ->default(fn () => Organizer::first()?->id ?? 1),

                                Forms\Components\TextInput::make('title')
                                    ->label('Nama Event Lari')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),

                                Forms\Components\TextInput::make('slug')
                                    ->label('URL Slug')
                                    ->required()
                                    ->unique(Event::class, 'slug', ignoreRecord: true),

                                Forms\Components\FileUpload::make('banner_path')
                                    ->label('Gambar Cover / Banner Event')
                                    ->image()
                                    ->imageEditor()
                                    ->imageResizeMode('cover')
                                    ->imageCropAspectRatio('16:9')
                                    ->disk('public')
                                    ->visibility('public')
                                    ->directory('events/banners')
                                    ->maxSize(3072)
                                    ->helperText('📐 Ukuran Rekomendasi: 1200 x 675 pixel (Rasio aspek 16:9), atau minimal 800 x 450 pixel. Format file: JPG, PNG, atau WebP (Maksimal 3 MB). Gambar ini akan menjadi banner utama pada kartu event di beranda publik serta header halaman pendaftaran.')
                                    ->columnSpanFull(),

                                Forms\Components\Textarea::make('short_description')
                                    ->label('Ringkasan Singkat')
                                    ->rows(3)
                                    ->helperText('Ringkasan ini akan tampil pada kartu event beranda dan kartu header pendaftaran.')
                                    ->columnSpanFull(),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Waktu & Lokasi')
                            ->schema([
                                Forms\Components\DateTimePicker::make('event_start_date')
                                    ->label('Waktu Start Race')
                                    ->required(),

                                Forms\Components\DateTimePicker::make('event_end_date')
                                    ->label('Waktu Selesai Race'),

                                Forms\Components\TextInput::make('race_location_name')
                                    ->label('Nama Lokasi Race / Start Line')
                                    ->required(),

                                Forms\Components\TextInput::make('race_location_map_url')
                                    ->label('Google Maps URL')
                                    ->url(),

                                Forms\Components\Textarea::make('race_location_address')
                                    ->label('Alamat Lengkap Venue')
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Jadwal Pengiriman Race Pack')
                            ->schema([
                                Forms\Components\DatePicker::make('racepack_delivery_start_date')
                                    ->label('Mulai Pengiriman Race Pack (Wajib)')
                                    ->required()
                                    ->helperText('Tanggal dimulainya proses pengiriman medali & jersey kepada peserta via ekspedisi SPX.')
                                    ->default(now()->addDays(7)),

                                Forms\Components\DatePicker::make('racepack_delivery_end_date')
                                    ->label('Selesai / Estimasi Tiba Pengiriman')
                                    ->helperText('Perkiraan batas akhir pengiriman atau paket tiba di alamat peserta.'),

                                Forms\Components\TextInput::make('racepack_delivery_schedule')
                                    ->label('Keterangan Jadwal Khusus (Opsional)')
                                    ->placeholder('Contoh: 20 - 25 November 2026')
                                    ->helperText('Jika diisi, teks ini akan dicantumkan secara resmi pada E-Ticket / Virtual Pass pelari.')
                                    ->columnSpanFull(),

                                Forms\Components\Textarea::make('racepack_delivery_notes')
                                    ->label('Catatan Ekspedisi Pengiriman')
                                    ->placeholder('Contoh: Pengiriman serentak menggunakan SPX Express langsung ke alamat peserta.')
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Pendaftaran & Pengaturan BIB')
                            ->schema([
                                Forms\Components\DateTimePicker::make('registration_open_date')
                                    ->label('Registrasi Dibuka')
                                    ->required(),

                                Forms\Components\DateTimePicker::make('registration_close_date')
                                    ->label('Registrasi Ditutup')
                                    ->required(),

                                Forms\Components\Select::make('status')
                                    ->label('Status Event')
                                    ->options([
                                        'draft' => 'Draft (Belum Tampil)',
                                        'published' => 'Published (Buka Pendaftaran)',
                                        'registration_closed' => 'Pendaftaran Ditutup',
                                        'completed' => 'Lomba Selesai',
                                    ])->default('draft')->required(),

                                Forms\Components\Toggle::make('is_featured')
                                    ->label('⭐ Sorot di Beranda Utama')
                                    ->helperText('Jika aktif, event ini akan menjadi sorotan utama (Featured Card) di halaman beranda.')
                                    ->default(false),

                                Forms\Components\Toggle::make('auto_assign_bib')
                                    ->label('Terbitkan BIB Otomatis Saat Bayar Lunas')
                                    ->helperText('Jika non-aktif, BIB akan di-generate serentak saat registrasi ditutup')
                                    ->default(true),

                                Forms\Components\Textarea::make('waiver_content')
                                    ->label('Isi Pernyataan Pelepasan Tanggung Jawab (Waiver)')
                                    ->rows(4)
                                    ->columnSpanFull()
                                    ->default('Dengan ini saya menyatakan bahwa saya dalam kondisi fisik yang sehat dan siap mengikuti lomba lari ini. Saya membebaskan panitia penyelenggara dan platform tiket dari segala tuntutan atas cedera atau gangguan kesehatan selama perlombaan.'),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Kategori Lari')
                            ->schema([
                                Forms\Components\Repeater::make('categories')
                                    ->relationship('categories')
                                    ->label('Kategori Lomba (5K, 10K, 21K, 42K, dll)')
                                    ->defaultItems(1)
                                    ->minItems(1)
                                    ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Nama Kategori')
                                            ->placeholder('Contoh: 10K Open')
                                            ->required(),
                                        Forms\Components\TextInput::make('distance_km')
                                            ->label('Jarak (KM)')
                                            ->numeric()
                                            ->step(0.01)
                                            ->required(),
                                        Forms\Components\TextInput::make('normal_price')
                                            ->label('Harga Normal (Rp)')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->required(),
                                        Forms\Components\TextInput::make('quota')
                                            ->label('Jumlah Kuota')
                                            ->numeric()
                                            ->default(500)
                                            ->required(),
                                        Forms\Components\TextInput::make('bib_prefix')
                                            ->label('Prefix BIB (Opsional)')
                                            ->placeholder('Kosongkan untuk nomor murni')
                                            ->default(null)
                                            ->maxLength(10)
                                            ->helperText('Biarkan kosong untuk e-BIB nomor saja (misal: 1001, 1002)'),
                                        Forms\Components\TextInput::make('bib_start_number')
                                            ->label('Nomor Awal BIB')
                                            ->numeric()
                                            ->default(1001),
                                        Forms\Components\Toggle::make('includes_jersey')
                                            ->label('Dapat Jersey')
                                            ->helperText('Matikan jika Medali Only')
                                            ->default(true),
                                        Forms\Components\Toggle::make('includes_medal')
                                            ->label('Dapat Medali')
                                            ->helperText('Matikan jika Jersey Only')
                                            ->default(true),
                                        Forms\Components\Toggle::make('is_active')
                                            ->label('Buka')
                                            ->default(true),
                                    ])
                                    ->columns(9)
                                    ->columnSpanFull()
                                    ->addActionLabel('+ Tambah Kategori Lari'),
                            ]),

                        Forms\Components\Tabs\Tab::make('Ukuran Jersey')
                            ->schema([
                                Forms\Components\Repeater::make('jerseySizes')
                                    ->relationship('jerseySizes')
                                    ->label('Pilihan Ukuran Jersey Lari (XS sampai 5XL)')
                                    ->defaultItems(0)
                                    ->itemLabel(fn (array $state): ?string => isset($state['size_name']) ? ($state['size_name'].' ('.($state['gender_type'] ?? 'unisex').') - '.((! isset($state['is_unlimited']) || $state['is_unlimited']) ? 'Unlimited' : ('Batas: '.($state['stock'] ?? 0).' pcs'))) : null)
                                    ->schema([
                                        Forms\Components\Select::make('size_name')
                                            ->label('Ukuran')
                                            ->options([
                                                'XS' => 'XS',
                                                'S' => 'S',
                                                'M' => 'M',
                                                'L' => 'L',
                                                'XL' => 'XL',
                                                'XXL' => 'XXL (2XL)',
                                                '3XL' => '3XL',
                                                '4XL' => '4XL',
                                                '5XL' => '5XL',
                                            ])
                                            ->required(),

                                        Forms\Components\Select::make('gender_type')
                                            ->label('Tipe Gender')
                                            ->options([
                                                'unisex' => 'Unisex',
                                                'male' => 'Pria (Men)',
                                                'female' => 'Wanita (Women)',
                                            ])
                                            ->default('unisex')
                                            ->required(),

                                        Forms\Components\TextInput::make('chest_width_cm')
                                            ->label('Lebar Dada (cm)')
                                            ->numeric(),

                                        Forms\Components\TextInput::make('body_length_cm')
                                            ->label('Panjang Baju (cm)')
                                            ->numeric(),

                                        Forms\Components\TextInput::make('surcharge')
                                            ->label('Biaya Tambahan (Rp)')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->default(0)
                                            ->helperText('Misal: 10000 untuk 4XL/5XL'),

                                        Forms\Components\Toggle::make('is_unlimited')
                                            ->label('Stok Unlimited')
                                            ->helperText('Bebas tanpa batasan kuota')
                                            ->default(true)
                                            ->live(),

                                        Forms\Components\TextInput::make('stock')
                                            ->label('Batas Maksimal Stok')
                                            ->numeric()
                                            ->placeholder('Contoh: 100')
                                            ->visible(fn ($get) => ! $get('is_unlimited'))
                                            ->required(fn ($get) => ! $get('is_unlimited'))
                                            ->helperText('Isi jika ukuran ini dibatasi stoknya'),
                                    ])
                                    ->columns(6)
                                    ->columnSpanFull()
                                    ->addActionLabel('+ Tambah Pilihan Ukuran')
                                    ->helperText('Secara default seluruh ukuran jersey berstatus Unlimited (bebas tanpa batasan stok). Aktifkan pembatasan stok hanya jika kuota jersey untuk ukuran tertentu terbatas.'),
                            ]),

                        Forms\Components\Tabs\Tab::make('Add-on / Merchandise')
                            ->schema([
                                Forms\Components\Repeater::make('addons')
                                    ->relationship('addons')
                                    ->label('Merchandise & Paket Add-on (Finisher Jersey, Enamel Pin, Soft Flask, dll)')
                                    ->defaultItems(0)
                                    ->itemLabel(fn (array $state): ?string => isset($state['name']) ? ($state['name'].' - Rp '.number_format((float) ($state['price'] ?? 0), 0, ',', '.').((! isset($state['is_unlimited']) || $state['is_unlimited']) ? ' (Unlimited)' : (' (Stok: '.($state['stock'] ?? 0).')'))) : null)
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Nama Produk / Add-on')
                                            ->placeholder('Contoh: Jersey Finisher Eksklusif / Enamel Pin / Soft Flask 500ml')
                                            ->required()
                                            ->columnSpan(2),

                                        Forms\Components\TextInput::make('price')
                                            ->label('Harga Tambahan')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->required()
                                            ->columnSpan(1),

                                        Forms\Components\TextInput::make('weight_grams')
                                            ->label('Berat (gram)')
                                            ->numeric()
                                            ->default(150)
                                            ->suffix('gr')
                                            ->helperText('Untuk estimasi ongkir SPX')
                                            ->columnSpan(1),

                                        Forms\Components\FileUpload::make('image_path')
                                            ->label('Foto Mockup Produk')
                                            ->image()
                                            ->disk('public')
                                            ->visibility('public')
                                            ->directory('events/addons')
                                            ->maxSize(2048)
                                            ->columnSpan(2),

                                        Forms\Components\Textarea::make('description')
                                            ->label('Deskripsi / Spesifikasi')
                                            ->placeholder('Contoh: Bahan dryfit premium quick-dry dengan aksen reflektif 3M.')
                                            ->rows(2)
                                            ->columnSpan(4),

                                        Forms\Components\Toggle::make('is_unlimited')
                                            ->label('Stok Unlimited')
                                            ->default(true)
                                            ->live()
                                            ->columnSpan(1),

                                        Forms\Components\TextInput::make('stock')
                                            ->label('Batas Stok')
                                            ->numeric()
                                            ->placeholder('Contoh: 100')
                                            ->visible(fn ($get) => ! $get('is_unlimited'))
                                            ->required(fn ($get) => ! $get('is_unlimited'))
                                            ->columnSpan(1),

                                        Forms\Components\Toggle::make('is_active')
                                            ->label('Buka Penjualan')
                                            ->default(true)
                                            ->columnSpan(1),
                                    ])
                                    ->columns(6)
                                    ->columnSpanFull()
                                    ->addActionLabel('+ Tambah Merchandise / Add-on')
                                    ->helperText('Add-on ini akan muncul sebagai opsi tambahan yang dapat dipilih oleh calon peserta pada halaman pemilihan tiket.'),
                            ]),

                        Forms\Components\Tabs\Tab::make('Desain E-BIB & E-Sertifikat')
                            ->icon('heroicon-o-paint-brush')
                            ->schema([
                                Forms\Components\Section::make('Kustomisasi Background E-BIB & E-Sertifikat')
                                    ->description('Unggah desain grafis kustom untuk latar belakang nomor dada e-BIB pelari dan E-Sertifikat Finisher PDF resmi per event.')
                                    ->schema([
                                        Forms\Components\FileUpload::make('ebib_background_path')
                                            ->label('Background Desain Nomor E-BIB')
                                            ->image()
                                            ->disk('public')
                                            ->visibility('public')
                                            ->directory('events/ebib-backgrounds')
                                            ->maxSize(3072)
                                            ->helperText('Format: JPG / PNG (Maks. 3 MB). Template nomor dada resmi (rasio 2:1 atau landscape, misal 1200x600 px). Nomor BIB, Nama Pelari, dan Kategori akan dicetak di atas background ini.'),

                                        Forms\Components\FileUpload::make('certificate_background_path')
                                            ->label('Background Desain E-Sertifikat Finisher')
                                            ->image()
                                            ->disk('public')
                                            ->visibility('public')
                                            ->directory('events/certificate-backgrounds')
                                            ->maxSize(3072)
                                            ->helperText('Format: JPG / PNG (Maks. 3 MB). Template sertifikat resmi A4 Landscape (297 x 210 mm atau rasio 1.414, misal 2000x1414 px). Data Finisher, Nama, Jarak, Durasi, dan QR Token akan dicetak di atas background ini.'),
                                    ])->columns(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('Meta CAPI & Tracking')
                            ->icon('heroicon-o-chart-bar')
                            ->schema([
                                Forms\Components\Section::make('Konfigurasi Meta Pixel & Conversions API (CAPI)')
                                    ->description('Integrasi server-to-server untuk melacak event Purchase dengan akurasi tinggi dan tahan ad-blocker. Event Purchase hanya dikirim saat pesanan sudah benar-benar lunas (paid).')
                                    ->headerActions([
                                        Forms\Components\Actions\Action::make('test_meta_capi')
                                            ->label('Uji Koneksi Meta CAPI')
                                            ->icon('heroicon-o-paper-airplane')
                                            ->color('info')
                                            ->requiresConfirmation()
                                            ->modalHeading('Tes Pengiriman Event ke Meta CAPI')
                                            ->modalDescription('Sistem akan mengirim 1 test event ke Meta Events Manager menggunakan Pixel ID dan Access Token saat ini untuk memastikan konfigurasi aktif.')
                                            ->action(function ($record, Forms\Set $set, $get) {
                                                $pixelId = $get('meta_pixel_id');
                                                $token = $get('meta_capi_access_token');
                                                $testCode = $get('meta_capi_test_event_code');

                                                if (empty($pixelId) || empty($token)) {
                                                    Notification::make()
                                                        ->title('Konfigurasi Belum Lengkap')
                                                        ->body('Pastikan Pixel ID dan Access Token sudah diisi sebelum melakukan pengujian.')
                                                        ->warning()
                                                        ->send();

                                                    return;
                                                }

                                                $service = app(MetaCapiService::class);
                                                $result = $service->sendTestEvent($pixelId, $token, $testCode);

                                                if ($result['success']) {
                                                    Notification::make()
                                                        ->title('Koneksi Meta CAPI Berhasil!')
                                                        ->body("Event test berhasil diterima oleh Meta Graph API. Event count: {$result['events_received']}")
                                                        ->success()
                                                        ->send();
                                                } else {
                                                    Notification::make()
                                                        ->title('Koneksi Meta CAPI Gagal')
                                                        ->body($result['error'] ?? 'Gagal menghubungi Meta Graph API.')
                                                        ->danger()
                                                        ->send();
                                                }
                                            }),
                                    ])
                                    ->schema([
                                        Forms\Components\Toggle::make('is_meta_capi_enabled')
                                            ->label('Aktifkan Meta Conversions API (CAPI)')
                                            ->helperText('Jika aktif, event Purchase resmi akan dikirimkan otomatis secara server-to-server saat pembayaran berstatus Lunas (paid).')
                                            ->default(true),

                                        Forms\Components\TextInput::make('meta_pixel_id')
                                            ->label('Meta / Facebook Pixel ID')
                                            ->placeholder('Contoh: 128495039201948')
                                            ->helperText('Dapatkan Pixel ID dari Meta Events Manager (Pengelola Peristiwa).')
                                            ->maxLength(50),

                                        Forms\Components\Textarea::make('meta_capi_access_token')
                                            ->label('Conversions API Access Token')
                                            ->placeholder('Contoh: EAAG... (Token permanen dari Events Manager > Settings > Conversions API > Generate Access Token)')
                                            ->helperText('Token akses server Meta untuk mengirim event offline / backend secara aman.')
                                            ->rows(3),

                                        Forms\Components\TextInput::make('meta_capi_test_event_code')
                                            ->label('Test Event Code (Opsional untuk Testing)')
                                            ->placeholder('Contoh: TEST12345')
                                            ->helperText('Isi kode ini saat melakukan uji coba di tab "Test Events" Meta Events Manager. Kosongkan saat sudah siap live/production agar event tercatat sebagai data riil.'),
                                    ])
                                    ->columns(1),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('banner_path')
                    ->label('Cover')
                    ->disk('public')
                    ->width(64)
                    ->height(40)
                    ->extraImgAttributes(['class' => 'rounded-lg object-cover shadow-sm']),

                Tables\Columns\TextColumn::make('title')
                    ->label('Nama Event')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\ToggleColumn::make('is_featured')
                    ->label('⭐ Beranda')
                    ->onColor('warning')
                    ->offColor('gray')
                    ->tooltip('Klik untuk menyorot event ini di beranda utama')
                    ->afterStateUpdated(function (Event $record, bool $state): void {
                        if ($state) {
                            Event::where('id', '!=', $record->id)->update(['is_featured' => false]);
                            Notification::make()
                                ->title('Event Sorotan Beranda')
                                ->body("\"{$record->title}\" kini disorot di halaman utama.")
                                ->success()
                                ->send();
                        }
                    }),

                Tables\Columns\TextColumn::make('race_location_name')
                    ->label('Lokasi')
                    ->searchable(),

                Tables\Columns\TextColumn::make('event_start_date')
                    ->label('Tanggal Race')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('registration_close_date')
                    ->label('Tutup Registrasi')
                    ->dateTime('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'gray',
                        'registration_closed' => 'warning',
                        'completed' => 'info',
                        default => 'secondary',
                    }),

                Tables\Columns\IconColumn::make('auto_assign_bib')
                    ->label('Auto BIB')
                    ->boolean(),

                Tables\Columns\IconColumn::make('has_meta_capi')
                    ->label('Meta CAPI')
                    ->state(fn (Event $record): bool => $record->hasMetaCapi())
                    ->boolean()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'published' => 'Published',
                        'draft' => 'Draft',
                        'registration_closed' => 'Registration Closed',
                        'completed' => 'Completed',
                    ]),
                Tables\Filters\SelectFilter::make('organizer_id')
                    ->label('Penyelenggara')
                    ->relationship('organizer', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
