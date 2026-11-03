<?php

namespace App\Filament\Organizer\Resources;

use App\Filament\Organizer\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Services\OrderManagementService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationLabel = 'Transaksi & Invoice';

    protected static ?string $navigationGroup = 'Keuangan & Transaksi';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('order_code')
                    ->label('Kode Order')
                    ->disabled(),
                Forms\Components\TextInput::make('customer_name')
                    ->label('Nama Pembeli')
                    ->disabled(),
                Forms\Components\TextInput::make('customer_email')
                    ->label('Email')
                    ->disabled(),
                Forms\Components\TextInput::make('customer_phone')
                    ->label('No. Telepon')
                    ->disabled(),
                Forms\Components\TextInput::make('tripay_payment_method')
                    ->label('Metode Bayar Tripay')
                    ->disabled(),
                Forms\Components\TextInput::make('tripay_reference')
                    ->label('Tripay Reference')
                    ->disabled(),
                Forms\Components\TextInput::make('grand_total')
                    ->label('Total Bayar (Rp)')
                    ->numeric()
                    ->prefix('Rp')
                    ->disabled(),
                Forms\Components\Select::make('status')
                    ->label('Status Pembayaran')
                    ->options([
                        'pending' => '⏳ Menunggu Bayar (Pending)',
                        'paid' => '✅ Lunas (Paid)',
                        'cancelled' => '❌ Dibatalkan (Cancelled)',
                        'refunded' => '💸 Pengembalian Dana (Refunded)',
                        'expired' => '⏱️ Kadaluarsa (Expired)',
                        'failed' => '⚠️ Gagal (Failed)',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_code')
                    ->label('Kode Order')
                    ->searchable()
                    ->fontFamily('mono')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Nama Pendaftar')
                    ->searchable()
                    ->description(fn (Order $record) => $record->customer_email),

                Tables\Columns\TextColumn::make('event.title')
                    ->label('Event')
                    ->limit(25),

                Tables\Columns\TextColumn::make('tripay_payment_method')
                    ->label('Metode')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('grand_total')
                    ->label('Total (Rp)')
                    ->money('IDR', locale: 'id')
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'paid' => 'Lunas',
                        'pending' => 'Pending',
                        'cancelled' => 'Dibatalkan',
                        'refunded' => 'Refund',
                        'expired' => 'Expired',
                        'failed' => 'Gagal',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'cancelled' => 'danger',
                        'refunded' => 'info',
                        'expired' => 'gray',
                        'failed' => 'danger',
                        default => 'secondary',
                    }),

                Tables\Columns\TextColumn::make('paid_at')
                    ->label('Waktu Lunas')
                    ->dateTime('d M Y, H:i')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tgl Order')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                Tables\Columns\IconColumn::make('meta_capi_purchase_sent_at')
                    ->label('Meta CAPI')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('success')
                    ->falseColor('gray')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status Pembayaran')
                    ->options([
                        'paid' => 'Lunas (Paid)',
                        'pending' => 'Pending (Menunggu Bayar)',
                        'cancelled' => 'Dibatalkan (Cancelled)',
                        'refunded' => 'Refund (Pengembalian Dana)',
                        'expired' => 'Kadaluarsa (Expired)',
                        'failed' => 'Gagal (Failed)',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('change_status')
                    ->label('Ubah Status')
                    ->icon('heroicon-o-arrows-right-left')
                    ->color('warning')
                    ->modalHeading(fn (Order $record) => "Ubah Status Pesanan #{$record->order_code}")
                    ->modalDescription('Perubahan status ke Lunas akan otomatis menerbitkan nomor BIB & E-Ticket. Perubahan ke Batal/Refund akan mengembalikan kuota.')
                    ->modalSubmitActionLabel('Simpan Perubahan Status')
                    ->form([
                        Forms\Components\Select::make('new_status')
                            ->label('Pilih Status Baru *')
                            ->options([
                                'paid' => '✅ Lunas (Paid) - Alokasi kuota, nomor BIB, dan E-Ticket',
                                'pending' => '⏳ Menunggu Pembayaran (Pending)',
                                'cancelled' => '❌ Dibatalkan (Cancelled) - Kembalikan kuota & stok',
                                'refunded' => '💸 Pengembalian Dana (Refunded) - Kembalikan kuota & stok',
                                'expired' => '⏱️ Kadaluarsa (Expired)',
                                'failed' => '⚠️ Gagal (Failed)',
                            ])
                            ->default(fn (Order $record) => $record->status)
                            ->required(),
                        Forms\Components\Textarea::make('reason')
                            ->label('Alasan / Catatan Perubahan *')
                            ->placeholder('Contoh: Peserta membatalkan pendaftaran / pembayaran via transfer manual.')
                            ->required(),
                        Forms\Components\Toggle::make('send_ticket_email')
                            ->label('Kirim E-Ticket ke Email Peserta')
                            ->helperText('Otomatis kirim email E-Ticket jika status diubah ke Lunas.')
                            ->default(true)
                            ->visible(fn (Forms\Get $get) => $get('new_status') === 'paid'),
                    ])
                    ->action(function (Order $record, array $data, OrderManagementService $service) {
                        $service->updateStatus(
                            order: $record,
                            newStatus: $data['new_status'],
                            reason: $data['reason'],
                            adminName: auth()->user()?->name ?? 'Organizer',
                            sendTicketEmail: (bool) ($data['send_ticket_email'] ?? true)
                        );

                        Notification::make()
                            ->title('Status Transaksi Berhasil Diperbarui')
                            ->body("Pesanan #{$record->order_code} kini berstatus: ".strtoupper($data['new_status']))
                            ->success()
                            ->send();
                    }),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export_spx_manifest')
                    ->label('Export Manifest SPX (CSV)')
                    ->icon('heroicon-o-truck')
                    ->color('success')
                    ->action(function (): StreamedResponse {
                        $filename = 'manifest-spx-organizer-'.date('Ymd-His').'.csv';
                        $headers = [
                            'Content-Type' => 'text/csv; charset=UTF-8',
                            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                        ];

                        return response()->streamDownload(function () {
                            $handle = fopen('php://output', 'w');
                            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

                            fputcsv($handle, [
                                'NO_ORDER',
                                'NAMA_PENERIMA',
                                'NO_HP_PENERIMA',
                                'PROVINSI',
                                'KOTA_KABUPATEN',
                                'KECAMATAN',
                                'KODE_POS',
                                'ALAMAT_LENGKAP',
                                'BERAT_GRAM',
                                'JUMLAH_TIKET',
                                'JUMLAH_ADDON',
                                'RINCIAN_ISI_PAKET',
                                'EKSPEDISI',
                                'LAYANAN',
                                'STATUS_PENGIRIMAN',
                                'NO_RESI_SPX',
                                'TOTAL_TAGIHAN',
                                'TGL_LUNAS',
                            ]);

                            $query = Order::with(['shippingAddress', 'participants.category', 'participants.jerseySize', 'addons'])
                                ->where('status', 'paid')
                                ->whereHas('shippingAddress')
                                ->orderBy('paid_at', 'desc');

                            $user = auth()->user();
                            if ($user && ! $user->isSuperAdmin()) {
                                if ($user->organizer_id) {
                                    $query->whereHas('event', fn ($q) => $q->where('organizer_id', $user->organizer_id));
                                } else {
                                    $query->whereRaw('1 = 0');
                                }
                            }

                            $query->chunk(100, function ($orders) use ($handle) {
                                foreach ($orders as $order) {
                                    \App\Filament\Resources\OrderResource::writeSpxCsvRow($handle, $order);
                                }
                            });

                            fclose($handle);
                        }, $filename, $headers);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('export_spx_selected')
                    ->label('Export Manifest SPX Terpilih (CSV)')
                    ->icon('heroicon-o-truck')
                    ->color('success')
                    ->action(function ($records): StreamedResponse {
                        $filename = 'manifest-spx-terpilih-'.date('Ymd-His').'.csv';
                        $headers = [
                            'Content-Type' => 'text/csv; charset=UTF-8',
                            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                        ];

                        return response()->streamDownload(function () use ($records) {
                            $handle = fopen('php://output', 'w');
                            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

                            fputcsv($handle, [
                                'NO_ORDER',
                                'NAMA_PENERIMA',
                                'NO_HP_PENERIMA',
                                'PROVINSI',
                                'KOTA_KABUPATEN',
                                'KECAMATAN',
                                'KODE_POS',
                                'ALAMAT_LENGKAP',
                                'BERAT_GRAM',
                                'JUMLAH_TIKET',
                                'JUMLAH_ADDON',
                                'RINCIAN_ISI_PAKET',
                                'EKSPEDISI',
                                'LAYANAN',
                                'STATUS_PENGIRIMAN',
                                'NO_RESI_SPX',
                                'TOTAL_TAGIHAN',
                                'TGL_LUNAS',
                            ]);

                            $records->loadMissing(['shippingAddress', 'participants.category', 'participants.jerseySize', 'addons']);

                            foreach ($records as $order) {
                                \App\Filament\Resources\OrderResource::writeSpxCsvRow($handle, $order);
                            }

                            fclose($handle);
                        }, $filename, $headers);
                    }),
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
                $query->whereHas('event', function (Builder $q) use ($user) {
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
            'index' => Pages\ListOrders::route('/'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
