<?php

namespace App\Filament\Organizer\Resources;

use App\Filament\Organizer\Resources\ParticipantResource\Pages;
use App\Models\EventCategory;
use App\Models\Participant;
use App\Services\BibGeneratorService;
use App\Services\MailketingService;
use App\Services\TicketPdfService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ParticipantResource extends Resource
{
    protected static ?string $model = Participant::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Data Pelari & BIB';

    protected static ?string $navigationGroup = 'Manajemen Event Lari';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Nomor Dada & Status VIP')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('bib_number')
                            ->label('Nomor BIB')
                            ->placeholder('Contoh: 10K1001 atau 0001')
                            ->helperText('Nomor dada unik peserta'),
                    ]),

                Forms\Components\Section::make('Data Identitas Pelari')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('event_category_id')
                            ->label('Kategori Lari')
                            ->relationship('category', 'name', function (Builder $query) {
                                $user = auth()->user();
                                if ($user && ! $user->isSuperAdmin()) {
                                    if ($user->organizer_id) {
                                        $query->whereHas('event', fn ($q) => $q->where('organizer_id', $user->organizer_id));
                                    } else {
                                        $query->whereRaw('1 = 0');
                                    }
                                }
                            })
                            ->required(),
                        Forms\Components\Select::make('jersey_size_id')
                            ->label('Ukuran Jersey')
                            ->relationship('jerseySize', 'size_name', function (Builder $query) {
                                $user = auth()->user();
                                if ($user && ! $user->isSuperAdmin()) {
                                    if ($user->organizer_id) {
                                        $query->whereHas('event', fn ($q) => $q->where('organizer_id', $user->organizer_id));
                                    } else {
                                        $query->whereRaw('1 = 0');
                                    }
                                }
                            })
                            ->required(),
                        Forms\Components\TextInput::make('full_name')
                            ->label('Nama Lengkap')
                            ->required(),
                        Forms\Components\TextInput::make('bib_name')
                            ->label('Nama di BIB (Max 14 Karakter)')
                            ->maxLength(14)
                            ->placeholder('Nama pendek di nomor dada'),
                        Forms\Components\Select::make('id_type')
                            ->label('Jenis Identitas')
                            ->options([
                                'KTP' => 'KTP',
                                'SIM' => 'SIM',
                                'Passport' => 'Passport',
                                'KIA' => 'KIA',
                            ])->default('KTP'),
                        Forms\Components\TextInput::make('id_number')
                            ->label('Nomor NIK / Paspor')
                            ->required(),
                        Forms\Components\Select::make('gender')
                            ->label('Jenis Kelamin')
                            ->options([
                                'male' => 'Laki-laki',
                                'female' => 'Perempuan',
                            ])->required(),
                        Forms\Components\DatePicker::make('birth_date')
                            ->label('Tanggal Lahir')
                            ->required(),
                        Forms\Components\TextInput::make('phone')
                            ->label('Nomor WhatsApp/HP')
                            ->tel()
                            ->required(),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
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
                    ->label('BIB')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->fontFamily('mono')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('bib_name')
                    ->label('Nama di BIB')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => strtoupper((string) $state)),

                Tables\Columns\TextColumn::make('full_name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->description(fn (Participant $record) => $record->email.' | '.$record->phone),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                Tables\Columns\TextColumn::make('jerseySize.size_name')
                    ->label('Jersey')
                    ->badge(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tgl Daftar')
                    ->dateTime('d M Y, H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('event_category_id')
                    ->label('Kategori Lari')
                    ->relationship('category', 'name', function (Builder $query) {
                        $user = auth()->user();
                        if ($user && ! $user->isSuperAdmin()) {
                            if ($user->organizer_id) {
                                $query->whereHas('event', fn ($q) => $q->where('organizer_id', $user->organizer_id));
                            } else {
                                $query->whereRaw('1 = 0');
                            }
                        }
                    }),

            ])
            ->actions([

                // 2. Action: Download E-Ticket PDF
                Tables\Actions\Action::make('download_ticket')
                    ->label('PDF Tiket')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->action(function (Participant $record, TicketPdfService $ticketService): StreamedResponse {
                        $pdfBinary = $ticketService->generateTicketPdf($record);
                        $filename = 'E-Ticket-'.($record->bib_number ?: $record->id).'.pdf';

                        return response()->streamDownload(function () use ($pdfBinary) {
                            echo $pdfBinary;
                        }, $filename, ['Content-Type' => 'application/pdf']);
                    }),

                // 3. Action: Kirim Ulang Email Tiket via Mailketing
                Tables\Actions\Action::make('resend_email')
                    ->label('Kirim Ulang Email')
                    ->icon('heroicon-o-envelope')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Kirim Ulang E-Ticket ke Peserta')
                    ->modalDescription(fn (Participant $record) => "Apakah Anda yakin ingin mengirim ulang tiket ke {$record->email}?")
                    ->action(function (Participant $record, MailketingService $mailketing, TicketPdfService $ticketService) {
                        $pdfBinary = $ticketService->generateTicketPdf($record);
                        $pdfBase64 = base64_encode($pdfBinary);

                        $subject = "E-Ticket & Nomor BIB: {$record->event?->title} - {$record->bib_number}";
                        $html = view('emails.ticket_confirmed', [
                            'participant' => $record,
                            'event' => $record->event,
                            'order' => $record->order,
                        ])->render();

                        $sent = $mailketing->sendEmail(
                            recipientEmail: $record->email,
                            recipientName: $record->full_name,
                            subject: $subject,
                            htmlContent: $html,
                            attachmentBase64: $pdfBase64,
                            attachmentName: "E-Ticket-{$record->bib_number}.pdf",
                            attachmentUrl: route('public.ticket.download', $record->qr_token)
                        );

                        if ($sent) {
                            Notification::make()
                                ->title('Email Berhasil Dikirim')
                                ->body("E-Ticket telah dikirim ke {$record->email}.")
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Pengiriman Email Gagal')
                                ->body('Periksa log sistem atau konfigurasi API Token Mailketing.')
                                ->danger()
                                ->send();
                        }
                    }),

                Tables\Actions\EditAction::make(),
            ])
            ->headerActions([
                // Header Action 1: Export CSV untuk Vendor Cetak Nomor BIB (Virtual Run)
                Tables\Actions\Action::make('export_vendor_bib_csv')
                    ->label('Export Cetak Nomor BIB (CSV)')
                    ->icon('heroicon-o-identification')
                    ->color('success')
                    ->action(function (): StreamedResponse {
                        $filename = 'data-peserta-cetak-bib-'.date('Ymd-His').'.csv';
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

                            $user = auth()->user();
                            if ($user && ! $user->isSuperAdmin()) {
                                if ($user->organizer_id) {
                                    $query->whereHas('category.event', fn ($q) => $q->where('organizer_id', $user->organizer_id));
                                } else {
                                    $query->whereRaw('1 = 0');
                                }
                            }

                            $query->chunk(100, function ($participants) use ($handle) {
                                foreach ($participants as $p) {
                                    \App\Filament\Resources\ParticipantResource::writeBibCsvRow($handle, $p);
                                }
                            });

                            fclose($handle);
                        }, $filename, $headers);
                    }),

                // Header Action 2: Bulk Re-map BIBs (Aman dari VVIP)
                Tables\Actions\Action::make('bulk_remap')
                    ->label('Urutkan Ulang BIB (Bulk Remap)')
                    ->icon('heroicon-o-arrows-up-down')
                    ->color('primary')
                    ->form([
                        Forms\Components\Select::make('category_id')
                            ->label('Pilih Kategori Lari')
                            ->options(function () {
                                $user = auth()->user();
                                $query = EventCategory::query();
                                if ($user && ! $user->isSuperAdmin()) {
                                    if ($user->organizer_id) {
                                        $query->whereHas('event', fn ($q) => $q->where('organizer_id', $user->organizer_id));
                                    } else {
                                        $query->whereRaw('1 = 0');
                                    }
                                }

                                return $query->pluck('name', 'id');
                            })
                            ->required(),
                        Forms\Components\Select::make('sort_by')
                            ->label('Kriteria Pengurutan')
                            ->options([
                                'created_at' => 'Waktu Pendaftaran Awal',
                                'gender' => 'Jenis Kelamin',
                                'birth_date' => 'Usia Peserta',
                            ])->default('created_at')->required(),
                        Forms\Components\Select::make('direction')
                            ->label('Arah Urutan')
                            ->options([
                                'asc' => 'Ascending (A-Z / Waktu Tercepat ke Terlambat)',
                                'desc' => 'Descending',
                            ])->default('asc')->required(),
                    ])
                    ->action(function (array $data, BibGeneratorService $bibService) {
                        $user = auth()->user();
                        $categoryQuery = EventCategory::query();
                        if ($user && ! $user->isSuperAdmin()) {
                            if ($user->organizer_id) {
                                $categoryQuery->whereHas('event', fn ($q) => $q->where('organizer_id', $user->organizer_id));
                            } else {
                                $categoryQuery->whereRaw('1 = 0');
                            }
                        }
                        $category = $categoryQuery->findOrFail($data['category_id']);
                        $remapped = $bibService->bulkRemapBibs($category, $data['sort_by'], $data['direction']);

                        Notification::make()
                            ->title('Penomoran BIB Selesai')
                            ->body("Berhasil mengurutkan {$remapped} pelari reguler. Nomor peserta VVIP tetap terlindungi.")
                            ->success()
                            ->send();
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
                                \App\Filament\Resources\ParticipantResource::writeBibCsvRow($handle, $p);
                            }

                            fclose($handle);
                        }, $filename, $headers);
                    }),
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user && ! $user->isSuperAdmin()) {
            if ($user->organizer_id) {
                $query->whereHas('category.event', function (Builder $q) use ($user) {
                    $q->where('organizer_id', $user->organizer_id);
                });
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        return $query;
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
