<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivitySubmissionResource\Pages;
use App\Models\ActivitySubmission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ActivitySubmissionResource extends Resource
{
    protected static ?string $model = ActivitySubmission::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-badge';

    protected static ?string $navigationLabel = 'Validasi Aktivitas Lari';

    protected static ?string $modelLabel = 'Hasil Lari';

    protected static ?string $pluralModelLabel = 'Validasi Hasil Lari Virtual';

    protected static ?string $navigationGroup = 'Virtual Run & Event';

    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        $count = ActivitySubmission::where('validation_status', 'pending')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Pelari & Aktivitas')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('participant_id')
                            ->label('Peserta Virtual Run')
                            ->relationship('participant', 'full_name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "BIB: {$record->bib_number} - {$record->full_name} ({$record->category?->name})")
                            ->searchable(['bib_number', 'full_name', 'bib_name'])
                            ->preload()
                            ->required()
                            ->disabled(fn (string $operation): bool => $operation === 'edit'),
                        Forms\Components\DatePicker::make('activity_date')
                            ->label('Tanggal Lari')
                            ->required(),
                        Forms\Components\TextInput::make('distance_km')
                            ->label('Jarak Tempuh (KM)')
                            ->numeric()
                            ->step(0.01)
                            ->required(),
                        Forms\Components\TextInput::make('duration_seconds')
                            ->label('Durasi Waktu Lari (Detik)')
                            ->numeric()
                            ->helperText(fn (?int $state) => $state ? 'Format: '.gmdate('H:i:s', $state) : null)
                            ->required(),
                        Forms\Components\TextInput::make('calculated_pace')
                            ->label('Pace Rata-rata (Menit/KM)')
                            ->numeric()
                            ->step(0.01),
                        Forms\Components\TextInput::make('proof_url')
                            ->label('Link Strava / Garmin (Opsional)')
                            ->url(),
                    ]),

                Forms\Components\Section::make('Bukti Tangkapan Layar (Screenshot GPS)')
                    ->schema([
                        Forms\Components\FileUpload::make('proof_image_path')
                            ->label('Foto / Screenshot Bukti Lari')
                            ->image()
                            ->maxSize(1024)
                            ->directory('submissions/proofs')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan dari Pelari')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Status Verifikasi Panitia')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('validation_status')
                            ->label('Status Validasi')
                            ->options([
                                'pending' => 'Pending (Menunggu Review)',
                                'approved' => 'Approved (Disetujui)',
                                'rejected' => 'Rejected (Ditolak)',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('admin_feedback')
                            ->label('Catatan / Alasan Penolakan untuk Pelari')
                            ->placeholder('Contoh: Foto screenshot buram atau tanggal lari di luar periode event')
                            ->rows(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('participant.bib_number')
                    ->label('e-BIB')
                    ->searchable()
                    ->fontFamily('mono')
                    ->weight('bold')
                    ->color('primary')
                    ->sortable(),

                Tables\Columns\TextColumn::make('participant.bib_name')
                    ->label('Nama Pelari')
                    ->searchable()
                    ->description(fn (ActivitySubmission $record) => $record->participant?->category?->event?->title),

                Tables\Columns\TextColumn::make('participant.category.name')
                    ->label('Kategori')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('distance_km')
                    ->label('Jarak (KM)')
                    ->formatStateUsing(fn ($state) => number_format((float) $state, 2).' KM')
                    ->weight('bold')
                    ->sortable(),

                Tables\Columns\TextColumn::make('duration_seconds')
                    ->label('Durasi')
                    ->formatStateUsing(fn ($state) => gmdate('H:i:s', (int) $state)),

                Tables\Columns\TextColumn::make('calculated_pace')
                    ->label('Pace')
                    ->formatStateUsing(fn ($state) => $state ? number_format((float) $state, 2).' /km' : '-'),

                Tables\Columns\TextColumn::make('activity_date')
                    ->label('Tgl Lari')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\ImageColumn::make('proof_image_path')
                    ->label('Bukti')
                    ->circular(false)
                    ->square(),

                Tables\Columns\TextColumn::make('validation_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        default => 'secondary',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('validation_status')
                    ->label('Status Validasi')
                    ->options([
                        'pending' => 'Pending (Menunggu Review)',
                        'approved' => 'Approved (Disetujui)',
                        'rejected' => 'Rejected (Ditolak)',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (ActivitySubmission $record) => $record->validation_status !== 'approved')
                    ->action(function (ActivitySubmission $record) {
                        $record->update([
                            'validation_status' => 'approved',
                            'reviewed_by_user_id' => auth()->id(),
                        ]);

                        Notification::make()
                            ->title('Aktivitas Berhasil Disetujui')
                            ->body("Aktivitas pelari {$record->participant?->bib_name} telah disetujui.")
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('admin_feedback')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->placeholder('Contoh: Tangkapan layar tidak terbaca atau jarak tidak memenuhi syarat'),
                    ])
                    ->visible(fn (ActivitySubmission $record) => $record->validation_status !== 'rejected')
                    ->action(function (ActivitySubmission $record, array $data) {
                        $record->update([
                            'validation_status' => 'rejected',
                            'admin_feedback' => $data['admin_feedback'],
                            'reviewed_by_user_id' => auth()->id(),
                        ]);

                        Notification::make()
                            ->title('Aktivitas Ditolak')
                            ->body("Aktivitas telah ditolak dengan alasan: {$data['admin_feedback']}")
                            ->warning()
                            ->send();
                    }),

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivitySubmissions::route('/'),
            'create' => Pages\CreateActivitySubmission::route('/create'),
            'edit' => Pages\EditActivitySubmission::route('/{record}/edit'),
        ];
    }
}
