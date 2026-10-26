<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParticipantResource\Pages;
use App\Models\Participant;
use App\Services\MailketingService;
use App\Services\TicketPdfService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ParticipantResource extends Resource
{
    protected static ?string $model = Participant::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Data Peserta & e-BIB';

    protected static ?string $modelLabel = 'Peserta';

    protected static ?string $pluralModelLabel = 'Daftar Peserta & e-BIB';

    protected static ?string $navigationGroup = 'Pendaftaran & Peserta';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Nomor Dada (e-BIB) & Kategori')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('bib_number')
                            ->label('Nomor e-BIB (Angka)')
                            ->placeholder('Contoh: 1001')
                            ->helperText('Nomor dada unik peserta virtual run'),
                        Forms\Components\Select::make('event_category_id')
                            ->label('Kategori Lari')
                            ->relationship('category', 'name')
                            ->required(),
                        Forms\Components\Select::make('jersey_size_id')
                            ->label('Ukuran Jersey')
                            ->relationship('jerseySize', 'size_name')
                            ->required(),
                    ]),

                Forms\Components\Section::make('Data Identitas Pelari')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('bib_name')
                            ->label('Nama di e-BIB')
                            ->required()
                            ->maxLength(30)
                            ->placeholder('Nama pendek di nomor dada'),
                        Forms\Components\TextInput::make('full_name')
                            ->label('Nama Lengkap')
                            ->required(),
                        Forms\Components\Select::make('gender')
                            ->label('Jenis Kelamin')
                            ->options([
                                'male' => 'Laki-laki',
                                'female' => 'Perempuan',
                            ])->required(),
                        Forms\Components\TextInput::make('phone')
                            ->label('Nomor WhatsApp / HP')
                            ->tel()
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->label('Email Peserta')
                            ->email()
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('bib_number')
                    ->label('e-BIB')
                    ->searchable()
                    ->fontFamily('mono')
                    ->weight('bold')
                    ->color('primary')
                    ->sortable(),

                Tables\Columns\TextColumn::make('bib_name')
                    ->label('Nama di e-BIB')
                    ->searchable()
                    ->fontFamily('mono')
                    ->weight('bold')
                    ->description(fn (Participant $record) => $record->full_name),

                Tables\Columns\TextColumn::make('category.event.title')
                    ->label('Event')
                    ->limit(20)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('jerseySize.size_name')
                    ->label('Jersey')
                    ->sortable(),

                Tables\Columns\TextColumn::make('order.status')
                    ->label('Status Bayar')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        default => 'danger',
                    }),

                Tables\Columns\TextColumn::make('order.shippingAddress.tracking_number')
                    ->label('Resi SPX')
                    ->searchable()
                    ->fontFamily('mono')
                    ->placeholder('-')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tgl Daftar')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('bib_number', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('event_category_id')
                    ->label('Kategori Lari')
                    ->relationship('category', 'name'),
                Tables\Filters\SelectFilter::make('jersey_size_id')
                    ->label('Ukuran Jersey')
                    ->relationship('jerseySize', 'size_name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('download_ticket')
                    ->label('E-Ticket PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn (Participant $record): string => route('public.ticket.download', $record->qr_token))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('resend_ticket')
                    ->label('Kirim Email Tiket')
                    ->icon('heroicon-o-envelope')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (Participant $record) {
                        try {
                            $ticketService = app(TicketPdfService::class);
                            $mailketing = app(MailketingService::class);

                            $pdfBinary = $ticketService->generateTicketPdf($record);
                            $pdfPath = 'tickets/ticket-'.$record->id.'-'.time().'.pdf';
                            Storage::disk('public')->put($pdfPath, $pdfBinary);
                            $attachmentUrl = Storage::disk('public')->url($pdfPath);

                            $mailketing->sendTicketConfirmedEmail($record, $attachmentUrl);

                            Notification::make()
                                ->title('E-Ticket Berhasil Dikirimkan')
                                ->body("E-Ticket berhasil dikirim ke {$record->email}")
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Gagal Mengirim E-Ticket')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export_bib_manifest')
                    ->label('Export Cetak Nomor BIB (CSV)')
                    ->icon('heroicon-o-identification')
                    ->color('success')
                    ->action(function (): StreamedResponse {
                        $filename = 'data-cetak-nomor-bib-'.date('Ymd-His').'.csv';
                        $headers = [
                            'Content-Type' => 'text/csv; charset=UTF-8',
                            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                        ];

                        return response()->streamDownload(function () {
                            $handle = fopen('php://output', 'w');
                            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

                            fputcsv($handle, [
                                'BIB_NUMBER',
                                'NAMA_BIB',
                                'NAMA_LENGKAP',
                                'KATEGORI',
                                'JARAK_KM',
                                'GENDER',
                                'TGL_LAHIR',
                                'UKURAN_JERSEY',
                                'NO_HP',
                                'EMAIL',
                                'KODE_ORDER',
                                'STATUS_BAYAR',
                            ]);

                            $query = Participant::with(['category', 'jerseySize', 'order'])
                                ->whereNotNull('bib_number')
                                ->orderBy('bib_number', 'asc');

                            $query->chunk(100, function ($participants) use ($handle) {
                                foreach ($participants as $p) {
                                    static::writeBibCsvRow($handle, $p);
                                }
                            });

                            fclose($handle);
                        }, $filename, $headers);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('export_bib_selected')
                    ->label('Export Cetak BIB Terpilih (CSV)')
                    ->icon('heroicon-o-identification')
                    ->color('success')
                    ->action(function ($records): StreamedResponse {
                        $filename = 'data-cetak-nomor-bib-terpilih-'.date('Ymd-His').'.csv';
                        $headers = [
                            'Content-Type' => 'text/csv; charset=UTF-8',
                            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                        ];

                        return response()->streamDownload(function () use ($records) {
                            $handle = fopen('php://output', 'w');
                            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

                            fputcsv($handle, [
                                'BIB_NUMBER',
                                'NAMA_BIB',
                                'NAMA_LENGKAP',
                                'KATEGORI',
                                'JARAK_KM',
                                'GENDER',
                                'TGL_LAHIR',
                                'UKURAN_JERSEY',
                                'NO_HP',
                                'EMAIL',
                                'KODE_ORDER',
                                'STATUS_BAYAR',
                            ]);

                            $records->loadMissing(['category', 'jerseySize', 'order']);

                            foreach ($records as $p) {
                                static::writeBibCsvRow($handle, $p);
                            }

                            fclose($handle);
                        }, $filename, $headers);
                    }),
            ]);
    }

    /**
     * Tulis 1 baris CSV data cetak nomor BIB untuk vendor produksi
     */
    public static function writeBibCsvRow($handle, Participant $p): void
    {
        fputcsv($handle, [
            $p->bib_number,
            $p->bib_name,
            $p->full_name,
            $p->category?->name,
            $p->category?->distance_km,
            $p->gender,
            $p->birth_date?->format('Y-m-d') ?: '',
            $p->jerseySize?->size_name,
            $p->phone,
            $p->email,
            $p->order?->order_code,
            $p->order?->status,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListParticipants::route('/'),
            'create' => Pages\CreateParticipant::route('/create'),
            'edit' => Pages\EditParticipant::route('/{record}/edit'),
        ];
    }
}
