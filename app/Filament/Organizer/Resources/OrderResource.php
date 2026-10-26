<?php

namespace App\Filament\Organizer\Resources;

use App\Filament\Organizer\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
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
                        'pending' => 'Pending',
                        'paid' => 'Lunas (Paid)',
                        'expired' => 'Kadaluarsa (Expired)',
                        'failed' => 'Gagal (Failed)',
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
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'expired', 'failed' => 'danger',
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
                    ->options([
                        'paid' => 'Lunas',
                        'pending' => 'Pending',
                        'expired' => 'Expired',
                    ]),
            ])
            ->actions([
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
