<x-filament-panels::page>
    @php
        $git = $this->getGitInfo();
    @endphp

    <div class="space-y-6">
        <!-- Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Card 1: Branch & Status -->
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-5 shadow-xs">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Branch Aktif</span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-orange-50 text-orange-700 dark:bg-orange-950/60 dark:text-orange-400 border border-orange-200 dark:border-orange-800/60">
                        <span class="w-1.5 h-1.5 rounded-full bg-orange-500 animate-pulse"></span>
                        {{ $git['branch'] }}
                    </span>
                </div>
                <div class="text-xl font-black text-gray-900 dark:text-white font-mono">
                    {{ $git['commit_hash'] }}
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 truncate" title="{{ $git['commit_title'] }}">
                    {{ $git['commit_title'] }}
                </p>
            </div>

            <!-- Card 2: Status Pembaruan -->
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-5 shadow-xs">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status Versi</span>
                    @if($commitsBehind > 0)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-400 border border-amber-200 dark:border-amber-800/60">
                            {{ $commitsBehind }} Update Siap
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/60">
                            ✓ Up to date
                        </span>
                    @endif
                </div>
                <div class="text-sm font-bold text-gray-800 dark:text-gray-200">
                    @if($commitsBehind > 0)
                        Ada commit baru di GitHub
                    @else
                        Sistem sinkron dengan origin
                    @endif
                </div>
                <p class="text-[11px] text-gray-400 mt-1">
                    Cek terakhir: {{ $lastCheckedAt ?? 'Baru saja' }}
                </p>
            </div>

            <!-- Card 3: Rilis & Author -->
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-5 shadow-xs">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block mb-3">Commit Terakhir</span>
                <div class="text-sm font-bold text-gray-900 dark:text-white truncate" title="{{ $git['commit_author'] }}">
                    {{ $git['commit_author'] }}
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ $git['commit_date'] }}
                </p>
            </div>

            <!-- Card 4: Lingkungan Server -->
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-5 shadow-xs">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block mb-3">Stack Server</span>
                <div class="text-sm font-mono font-bold text-gray-900 dark:text-white">
                    PHP {{ $git['php_version'] }}
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-mono">
                    Laravel {{ $git['laravel_version'] }}
                </p>
            </div>
        </div>

        <!-- Panduan & Aksi Cepat -->
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6 shadow-xs">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="space-y-1">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        Pembaruan Otomatis Tanpa Akses SSH
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 max-w-2xl leading-relaxed">
                        Fitur ini mengeksekusi penarikan kode terbaru dari repositori GitHub (<code class="text-xs font-mono bg-gray-100 dark:bg-gray-800 px-1 py-0.5 rounded">{{ $git['remote_url'] }}</code>), menjalankan migrasi database otomatis, serta me-rebuild cache aplikasi secara instan.
                    </p>
                </div>

                <div class="flex items-center gap-2.5 w-full md:w-auto">
                    <button 
                        type="button" 
                        wire:click="checkForUpdates"
                        wire:loading.attr="disabled"
                        class="px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/60 text-gray-700 dark:text-gray-200 text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-2xs"
                    >
                        <span wire:loading.remove wire:target="checkForUpdates">🔍 Cek Pembaruan</span>
                        <span wire:loading wire:target="checkForUpdates">Memeriksa GitHub...</span>
                    </button>

                    <button 
                        type="button" 
                        x-data="{}" 
                        x-on:click="$wire.mountAction('runUpdate')"
                        class="px-4 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-500 active:scale-95 text-white text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-md shadow-orange-600/20"
                    >
                        <span>⬇️ Tarik &amp; Perbarui Sekarang</span>
                    </button>
                </div>
            </div>

            <!-- Loading Indicator for Long Running Update -->
            <div wire:loading wire:target="executeSystemUpdate" class="mt-4 p-4 rounded-xl bg-orange-50 dark:bg-orange-950/40 border border-orange-200 dark:border-orange-800/60 text-orange-800 dark:text-orange-300 text-xs flex items-center gap-3">
                <svg class="animate-spin h-5 w-5 text-orange-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                <div>
                    <span class="font-bold">Sedang memperbarui aplikasi...</span> Mohon jangan menutup halaman ini sampai proses git pull dan migrasi database selesai.
                </div>
            </div>
        </div>

        <!-- Terminal Execution Log Screen -->
        <div class="bg-slate-950 rounded-2xl border border-slate-800 overflow-hidden shadow-lg">
            <div class="px-5 py-3 bg-slate-900 border-b border-slate-800 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-rose-500/80 inline-block"></span>
                        <span class="w-3 h-3 rounded-full bg-amber-500/80 inline-block"></span>
                        <span class="w-3 h-3 rounded-full bg-emerald-500/80 inline-block"></span>
                    </div>
                    <span class="text-xs font-mono text-slate-400 font-bold ml-2">Console Deployment Log</span>
                </div>
                
                @if($outputLog)
                    <button 
                        type="button" 
                        x-data="{ copied: false }" 
                        x-on:click="navigator.clipboard.writeText($el.dataset.log); copied = true; setTimeout(() => copied = false, 2000)"
                        data-log="{{ $outputLog }}"
                        class="text-[11px] text-slate-400 hover:text-white font-mono flex items-center gap-1 transition"
                    >
                        <span x-show="!copied">📋 Salin Log</span>
                        <span x-show="copied" class="text-emerald-400 font-bold">✓ Tersalin!</span>
                    </button>
                @endif
            </div>

            <div class="p-5 font-mono text-xs overflow-x-auto max-h-[420px] select-text">
                @if($outputLog)
                    <pre class="text-emerald-400 whitespace-pre-wrap leading-relaxed">{{ $outputLog }}</pre>
                @else
                    <div class="text-slate-500 py-8 text-center">
                        <p class="font-mono text-xs">Belum ada riwayat proses pembaruan yang dijalankan pada sesi ini.</p>
                        <p class="text-[11px] text-slate-600 mt-1">Klik tombol <strong>"Cek Pembaruan"</strong> atau <strong>"Tarik &amp; Perbarui Sekarang"</strong> untuk memulai.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-filament-panels::page>
