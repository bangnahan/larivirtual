<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Services\MetaCapiService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationLabel = 'Pesanan & Transaksi';

    protected static ?string $modelLabel = 'Pesanan';

    protected static ?string $pluralModelLabel = 'Daftar Pesanan & Transaksi';

    protected static ?string $navigationGroup = 'Pendaftaran & Peserta';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pesanan & Pendaftar')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('order_code')
                            ->label('Kode Order')
                            ->disabled(),
                        Forms\Components\TextInput::make('customer_name')
                            ->label('Nama Pembeli / PIC')
                            ->required(),
                        Forms\Components\TextInput::make('customer_email')
                            ->label('Email')
                            ->email()
                            ->required(),
                        Forms\Components\TextInput::make('customer_phone')
                            ->label('Nomor WhatsApp / HP')
                            ->tel()
                            ->required(),
                        Forms\Components\Select::make('event_id')
                            ->label('Event')
                            ->relationship('event', 'title')
                            ->disabled(),
                        Forms\Components\TextInput::make('grand_total')
                            ->label('Total Bayar (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled(),
                    ]),

                Forms\Components\Section::make('Status Pembayaran (Tripay Gateway)')
                    ->columns(3)
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status Pembayaran')
                            ->options([
                                'pending' => 'Pending (Menunggu Bayar)',
                                'paid' => 'Lunas (Paid)',
                                'expired' => 'Kadaluarsa (Expired)',
                                'failed' => 'Gagal (Failed)',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('tripay_payment_method')
                            ->label('Metode Bayar Tripay')
                            ->disabled(),
                        Forms\Components\TextInput::make('tripay_reference')
                            ->label('Referensi Tripay')
                            ->disabled(),
                        Forms\Components\TextInput::make('tripay_pay_code')
                            ->label('Kode Bayar / No. VA')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('paid_at')
                            ->label('Waktu Lunas'),
                        Forms\Components\DateTimePicker::make('expired_at')
                            ->label('Batas Waktu Bayar')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('meta_capi_purchase_sent_at')
                            ->label('Meta CAPI Purchase Terkirim')
                            ->helperText('Waktu sukses event Purchase diterima oleh Meta Graph API.')
                            ->disabled(),
                    ]),

                Forms\Components\Section::make('Pengiriman Race Pack (SPX Express)')
                    ->relationship('shippingAddress')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('recipient_name')
                            ->label('Nama Penerima'),
                        Forms\Components\TextInput::make('recipient_phone')
                            ->label('No. HP Penerima'),
                        Forms\Components\Select::make('shipping_status')
                            ->label('Status Ekspedisi SPX')
                            ->options([
                                'pending' => 'Pending (Menunggu)',
                                'processing' => 'Diproses (Packing)',
                                'shipped' => 'Dikirim (Dalam Perjalanan)',
                                'delivered' => 'Terkirim (Sampai)',
                            ]),
                        Forms\Components\TextInput::make('tracking_number')
                            ->label('No. Resi SPX Express')
                            ->placeholder('Contoh: SPXID029482910'),
                        Forms\Components\TextInput::make('destination_city')
                            ->label('Kota / Kabupaten'),
                        Forms\Components\TextInput::make('destination_district')
                            ->label('Kecamatan'),
                        Forms\Components\Textarea::make('address_detail')
                            ->label('Alamat Lengkap')
                            ->rows(2)
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Add-on & Merchandise yang Dipesan')
                    ->schema([
                        Forms\Components\Repeater::make('addons')
                            ->relationship('addons')
                            ->label('Daftar Add-on')
                            ->disabled()
                            ->deletable(false)
                            ->addable(false)
                            ->schema([
                                Forms\Components\TextInput::make('addon_name')
                                    ->label('Nama Add-on')
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('quantity')
                                    ->label('Qty')
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('unit_price')
                                    ->label('Harga Satuan')
                                    ->prefix('Rp')
                                    ->numeric()
                                    ->columnSpan(1),
                                Forms\Components\TextInput::make('total_price')
                                    ->label('Total Harga')
                                    ->prefix('Rp')
                                    ->numeric()
                                    ->columnSpan(1),
                            ])
                            ->columns(5)
                            ->columnSpanFull(),
                    ]),
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
                    ->weight('bold')
                    ->copyable(),

                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Pendaftar')
                    ->searchable()
                    ->description(fn (Order $record) => $record->customer_email),

                Tables\Columns\TextColumn::make('event.title')
                    ->label('Event')
                    ->limit(22)
                    ->toggleable(),

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
                    ->label('Status Bayar')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'expired', 'failed' => 'danger',
                        default => 'secondary',
                    }),

                Tables\Columns\TextColumn::make('shippingAddress.shipping_status')
                    ->label('Status SPX')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'shipped', 'delivered' => 'info',
                        'processing' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('shippingAddress.tracking_number')
                    ->label('No. Resi SPX')
                    ->searchable()
                    ->fontFamily('mono')
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
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status Pembayaran')
                    ->options([
                        'paid' => 'Lunas (Paid)',
                        'pending' => 'Pending (Menunggu Bayar)',
                        'expired' => 'Kadaluarsa (Expired)',
                    ]),
                Tables\Filters\SelectFilter::make('event_id')
                    ->label('Filter Event')
                    ->relationship('event', 'title'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('retry_meta_capi')
                    ->label('Kirim Ulang CAPI')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->visible(fn (Order $record): bool => $record->isPaid() && (bool) $record->event?->hasMetaCapi())
                    ->requiresConfirmation()
                    ->modalHeading('Kirim Ulang Event Purchase ke Meta CAPI')
                    ->action(function (Order $record) {
                        $record->update(['meta_capi_purchase_sent_at' => null]);
                        $result = app(MetaCapiService::class)->sendPurchaseEvent($record);

                        if ($result['success']) {
                            Notification::make()
                                ->title('Event Purchase Terkirim!')
                                ->body('Event Purchase berhasil diterima Meta CAPI.')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Gagal Mengirim Meta CAPI')
                                ->body($result['error'] ?? 'Terjadi kesalahan saat mengirim ke Meta.')
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export_spx_manifest')
                    ->label('Export Manifest SPX (CSV)')
                    ->icon('heroicon-o-truck')
                    ->color('success')
                    ->action(function (): StreamedResponse {
                        $filename = 'manifest-spx-virun-'.date('Ymd-His').'.csv';
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

                            $query->chunk(100, function ($orders) use ($handle) {
                                foreach ($orders as $order) {
                                    static::writeSpxCsvRow($handle, $order);
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
                                static::writeSpxCsvRow($handle, $order);
                            }

                            fclose($handle);
                        }, $filename, $headers);
                    }),
            ]);
    }

    /**
     * Tulis 1 baris manifest logistik SPX per pesanan (order)
     */
    public static function writeSpxCsvRow($handle, Order $order): void
    {
        $addr = $order->shippingAddress;
        $ticketCount = $order->participants->count();
        $addonCount = $order->addons->sum('quantity');
        $weight = $addr?->total_weight_grams ?: max(250, ($ticketCount * 250) + ($addonCount * 150));

        fputcsv($handle, [
            $order->order_code,
            $addr?->recipient_name ?: $order->customer_name,
            $addr?->recipient_phone ?: $order->customer_phone,
            $addr?->province ?: '',
            $addr?->destination_city ?: '',
            $addr?->destination_district ?: '',
            $addr?->postal_code ?: '',
            $addr?->address_detail ?: '',
            $weight,
            $ticketCount,
            $addonCount,
            $order->getPackageContentsSummary(),
            $addr?->courier_name ?: 'SPX Express',
            $addr?->service_type ?: 'Standard',
            $addr?->shipping_status ?: 'pending',
            $addr?->tracking_number ?: '',
            $order->grand_total,
            $order->paid_at?->format('Y-m-d H:i:s') ?: '',
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
