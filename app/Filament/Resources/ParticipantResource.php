<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParticipantResource\Pages;
use App\Jobs\SendCertificateEmailJob;
use App\Models\Participant;
use App\Services\CertificatePdfService;
use App\Services\MailketingService;
use App\Services\TicketPdfService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
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
                        Forms\Components\Select::make('jersey_type')
                            ->label('Model Lengan Jersey')
                            ->options([
                                'short' => 'Lengan Pendek (Standar)',
                                'singlet' => 'Singlet (Tanpa Lengan)',
                                'long' => 'Lengan Panjang (+Rp 20.000)',
                            ])
                            ->placeholder('Tanpa Jersey (Medali Only)')
                            ->nullable(),
                        Forms\Components\Select::make('jersey_size_id')
                            ->label('Ukuran Jersey')
                            ->relationship('jerseySize', 'size_name')
                            ->placeholder('Tanpa Jersey (Medali Only)')
                            ->nullable(),
                        Forms\Components\TextInput::make('jersey_surcharge')
                            ->label('Total Surcharge Jersey (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),
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
                    ->description(fn (Participant $record) => $record->category?->package_type_label)
                    ->color('gray'),

                Tables\Columns\TextColumn::make('jersey_full_spec')
                    ->label('Jersey')
                    ->description(fn (Participant $record) => $record->category && ! $record->category->includes_medal ? 'Tanpa Medali' : ($record->jersey_surcharge > 0 ? '+Rp '.number_format($record->jersey_surcharge, 0, ',', '.') : null))
                    ->sortable(),

                Tables\Columns\TextColumn::make('finisher_status')
                    ->label('Status Finisher')
                    ->badge()
                    ->state(fn (Participant $record): string => $record->isFinisher() ? 'Finisher 🏅' : 'In Progress')
                    ->description(fn (Participant $record): string => number_format((float) $record->total_distance_km, 1).' / '.$record->targetDistance().' KM')
                    ->color(fn (Participant $record): string => $record->isFinisher() ? 'success' : 'warning')
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
                Tables\Filters\SelectFilter::make('finisher_status')
                    ->label('Status Finisher')
                    ->options([
                        'finisher' => 'Finisher 🏅',
                        'in_progress' => 'In Progress 🏃',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('download_ticket')
                    ->label('E-Ticket PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->url(fn (Participant $record): string => route('public.ticket.download', $record->qr_token))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('resend_ticket')
                    ->label('Kirim Email Tiket')
                    ->icon('heroicon-o-envelope')
                    ->color('info')
                    ->form([
                        Forms\Components\TextInput::make('recipient_email')
                            ->label('Email Tujuan')
                            ->email()
                            ->default(fn (Participant $record) => $record->email ?: $record->order?->customer_email)
                            ->required(),
                    ])
                    ->action(function (Participant $record, array $data, MailketingService $mailketing, TicketPdfService $ticketService) {
                        try {
                            $pdfBinary = $ticketService->generateTicketPdf($record);
                            $pdfBase64 = base64_encode($pdfBinary);

                            $subject = "E-Ticket & Nomor BIB: {$record->event?->title} - {$record->bib_number}";
                            $html = view('emails.ticket_confirmed', [
                                'participant' => $record,
                                'event' => $record->event,
                                'order' => $record->order,
                            ])->render();

                            $sent = $mailketing->sendEmail(
                                recipientEmail: $data['recipient_email'],
                                recipientName: $record->full_name,
                                subject: $subject,
                                htmlContent: $html,
                                attachmentBase64: $pdfBase64,
                                attachmentName: "E-Ticket-{$record->bib_number}.pdf",
                                attachmentUrl: route('public.ticket.download', $record->qr_token)
                            );

                            if ($sent) {
                                Notification::make()
                                    ->title('E-Ticket Berhasil Dikirimkan')
                                    ->body("E-Ticket berhasil dikirim ke {$data['recipient_email']}")
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Pengiriman E-Ticket Gagal')
                                    ->body('Periksa log sistem atau konfigurasi API Token Mailketing.')
                                    ->danger()
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Gagal Mengirim E-Ticket')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Tables\Actions\Action::make('download_certificate')
                    ->label('Unduh Sertifikat')
                    ->icon('heroicon-o-academic-cap')
                    ->color('success')
                    ->visible(fn (Participant $record): bool => $record->isFinisher())
                    ->action(fn (Participant $record, CertificatePdfService $certService): StreamedResponse => response()->streamDownload(function () use ($record, $certService) {
                        echo $certService->generateCertificatePdf($record);
                    }, 'E-Certificate-Finisher-'.($record->bib_number ?: $record->id).'.pdf', ['Content-Type' => 'application/pdf'])
                    ),

                Tables\Actions\Action::make('resend_certificate')
                    ->label('Kirim E-Sertifikat')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('warning')
                    ->visible(fn (Participant $record): bool => $record->isFinisher())
                    ->form([
                        Forms\Components\TextInput::make('recipient_email')
                            ->label('Kirim Sertifikat ke Email')
                            ->email()
                            ->default(fn (Participant $record) => $record->email ?: $record->order?->customer_email)
                            ->required()
                            ->helperText('Default: email peserta saat mendaftar. Dapat diubah jika diminta dikirim ke email lain.'),
                    ])
                    ->action(function (Participant $record, array $data) {
                        SendCertificateEmailJob::dispatch($record, $data['recipient_email']);

                        Notification::make()
                            ->title('E-Sertifikat Dijadwalkan')
                            ->body("E-Sertifikat finisher sedang dikirimkan ke {$data['recipient_email']}")
                            ->success()
                            ->send();
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
                                'MODEL_JERSEY',
                                'UKURAN_JERSEY',
                                'SPESIFIKASI_JERSEY',
                                'BIAYA_UPGRADE_JERSEY',
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
                                'MODEL_JERSEY',
                                'UKURAN_JERSEY',
                                'SPESIFIKASI_JERSEY',
                                'BIAYA_UPGRADE_JERSEY',
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

                Tables\Actions\BulkAction::make('bulk_send_certificates')
                    ->label('Kirim E-Sertifikat (Finisher Terpilih)')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Kirim E-Sertifikat ke Finisher Terpilih')
                    ->modalDescription('Sistem akan mengirimkan email E-Sertifikat resmi hanya kepada peserta yang berstatus Finisher dari data yang dipilih.')
                    ->action(function (Collection $records) {
                        $finishers = $records->filter(fn (Participant $p) => $p->isFinisher());

                        if ($finishers->isEmpty()) {
                            Notification::make()
                                ->title('Tidak Ada Finisher')
                                ->body('Tidak ada peserta terpilih yang berstatus Finisher.')
                                ->warning()
                                ->send();

                            return;
                        }

                        foreach ($finishers as $finisher) {
                            SendCertificateEmailJob::dispatch($finisher);
                        }

                        Notification::make()
                            ->title('E-Sertifikat Terkirim')
                            ->body("E-Sertifikat berhasil dijadwalkan untuk dikirim ke {$finishers->count()} finisher.")
                            ->success()
                            ->send();
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
            $p->jersey_type_label,
            $p->jerseySize?->size_name,
            $p->jersey_full_spec,
            $p->jersey_surcharge,
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
