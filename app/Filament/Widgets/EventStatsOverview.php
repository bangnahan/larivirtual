<?php

namespace App\Filament\Widgets;

use App\Models\ActivitySubmission;
use App\Models\Order;
use App\Models\Participant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EventStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalParticipants = Participant::count();
        $totalPaidOrders = Order::where('status', 'paid')->count();
        $totalRevenue = Order::where('status', 'paid')->sum('grand_total');
        $pendingSubmissions = ActivitySubmission::where('validation_status', 'pending')->count();
        $approvedSubmissions = ActivitySubmission::where('validation_status', 'approved')->count();
        $totalJerseyOrdered = Participant::whereNotNull('jersey_size_id')->count();

        return [
            Stat::make('Total Pelari Terdaftar', number_format($totalParticipants, 0, ',', '.'))
                ->description("{$totalPaidOrders} Transaksi Sukses Lunas")
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),

            Stat::make('Total Pendapatan Tiket', 'Rp '.number_format($totalRevenue, 0, ',', '.'))
                ->description('Total Pembayaran Terkonfirmasi')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Validasi Aktivitas Lari', "{$approvedSubmissions} Disetujui")
                ->description($pendingSubmissions > 0 ? "{$pendingSubmissions} Menunggu Verifikasi" : 'Semua Sudah Diverifikasi')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color($pendingSubmissions > 0 ? 'warning' : 'success'),

            Stat::make('Total Jersey Dipesan', number_format($totalJerseyOrdered, 0, ',', '.'))
                ->description('Data Siap Produksi Vendor')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('info'),
        ];
    }
}
