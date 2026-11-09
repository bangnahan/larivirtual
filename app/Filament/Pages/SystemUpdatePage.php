<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\App;
use Symfony\Component\Process\Process;

class SystemUpdatePage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    protected static string $view = 'filament.pages.system-update';

    protected static ?string $navigationLabel = 'Pembaruan Sistem';

    protected static ?string $title = 'Pembaruan Sistem & GitHub Sync';

    protected static ?string $slug = 'system-update';

    protected static ?string $navigationGroup = 'Pengaturan Sistem';

    protected static ?int $navigationSort = 99;

    public ?string $outputLog = null;

    public ?string $lastCheckedAt = null;

    public int $commitsBehind = 0;

    public ?string $statusMessage = null;

    public static function canAccess(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public function mount(): void
    {
        $this->refreshGitStatus();
    }

    public function getGitInfo(): array
    {
        return [
            'branch' => $this->runCommand('git rev-parse --abbrev-ref HEAD') ?: 'main',
            'commit_hash' => $this->runCommand('git log -1 --format="%h"') ?: '-',
            'commit_title' => $this->runCommand('git log -1 --format="%s"') ?: '-',
            'commit_author' => $this->runCommand('git log -1 --format="%an"') ?: '-',
            'commit_date' => $this->runCommand('git log -1 --format="%cd" --date=relative') ?: '-',
            'remote_url' => $this->runCommand('git remote get-url origin') ?: 'origin',
            'laravel_version' => App::version(),
            'php_version' => PHP_VERSION,
        ];
    }

    public function refreshGitStatus(): void
    {
        $behind = $this->runCommand('git rev-list HEAD..origin/main --count 2>&1');
        $this->commitsBehind = is_numeric($behind) ? (int) $behind : 0;
        $this->lastCheckedAt = now()->format('H:i:s d/m/Y');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('checkUpdates')
                ->label('Cek Pembaruan GitHub')
                ->icon('heroicon-m-magnifying-glass')
                ->color(Color::Gray)
                ->action(function (): void {
                    $this->checkForUpdates();
                }),

            Action::make('runUpdate')
                ->label('Tarik & Perbarui Sistem')
                ->icon('heroicon-m-arrow-down-tray')
                ->color(Color::Orange)
                ->requiresConfirmation()
                ->modalHeading('Konfirmasi Pembaruan Sistem')
                ->modalDescription('Apakah Anda yakin ingin memperbarui kode aplikasi ke versi terbaru dari GitHub (branch main)? Proses ini akan menjalankan git pull, database migration, dan rebuild cache aplikasi.')
                ->modalSubmitActionLabel('Ya, Perbarui Sekarang')
                ->action(function (): void {
                    $this->executeSystemUpdate();
                }),
        ];
    }

    public function checkForUpdates(): void
    {
        $fetchOutput = $this->runCommand('git fetch origin main 2>&1');
        $this->refreshGitStatus();

        if ($this->commitsBehind > 0) {
            $this->statusMessage = "Terdapat {$this->commitsBehind} pembaruan commit baru di GitHub yang siap dipasang.";
            Notification::make()
                ->title('Pembaruan Tersedia!')
                ->body($this->statusMessage)
                ->warning()
                ->send();
        } else {
            $this->statusMessage = 'Sistem sudah menggunakan versi terbaru (Up to date).';
            Notification::make()
                ->title('Sistem Up-to-Date')
                ->body('Tidak ada commit baru di branch main.')
                ->success()
                ->send();
        }

        $this->outputLog = '[INFO '.now()->format('H:i:s')."] Cek pembaruan ke GitHub selesai.\n".$fetchOutput."\n".$this->statusMessage;
    }

    public function executeSystemUpdate(): void
    {
        @set_time_limit(180);

        $commands = [
            'git fetch origin main 2>&1',
            'git pull origin main 2>&1',
            'php artisan migrate --force 2>&1',
            'php artisan optimize:clear 2>&1',
            'php artisan optimize 2>&1',
            'php artisan view:clear 2>&1',
        ];

        $logs = [];
        $logs[] = '==================================================';
        $logs[] = 'MEMULAI PROSES PEMBARUAN SISTEM - '.now()->format('Y-m-d H:i:s');
        $logs[] = '==================================================';

        $hasError = false;

        foreach ($commands as $cmd) {
            $logs[] = "\n$ {$cmd}";
            $output = $this->runCommand($cmd);
            $logs[] = $output ?: '(tidak ada output)';

            if (str_contains(strtolower($output), 'fatal:') || str_contains(strtolower($output), 'error:')) {
                $hasError = true;
            }
        }

        $logs[] = "\n==================================================";
        $logs[] = $hasError ? 'SELESAI DENGAN CATATAN/PERINGATAN' : 'PROSES PEMBARUAN SELESAI DENGAN SUKSES';
        $logs[] = '==================================================';

        $this->outputLog = implode("\n", $logs);
        $this->refreshGitStatus();

        if ($hasError) {
            Notification::make()
                ->title('Pembaruan Selesai dengan Catatan')
                ->body('Periksa log output di bawah untuk melihat detail pesan dari git/artisan.')
                ->warning()
                ->send();
        } else {
            Notification::make()
                ->title('Pembaruan Berhasil!')
                ->body('Aplikasi berhasil diperbarui dan cache telah disegarkan.')
                ->success()
                ->send();
        }
    }

    protected function runCommand(string $command): string
    {
        try {
            $env = [
                'GIT_SSH_COMMAND' => 'ssh -o StrictHostKeyChecking=no',
            ];
            $process = Process::fromShellCommandline($command, base_path(), $env, null, 120);
            $process->run();

            return trim($process->getOutput().$process->getErrorOutput());
        } catch (\Throwable $e) {
            return 'Gagal mengeksekusi perintah: '.$e->getMessage();
        }
    }
}
