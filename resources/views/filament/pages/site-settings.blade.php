<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">
        <!-- Live Preview Header Banner -->
        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                    Live Preview Banner Beranda
                </span>
                @if(!empty($data['top_bar_enabled']))
                    <span class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Banner Aktif
                    </span>
                @else
                    <span class="text-xs text-gray-400 font-semibold flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-gray-400"></span> Banner Disembunyikan
                    </span>
                @endif
            </div>

            <div class="rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-800 shadow-xs">
                @if(!empty($data['top_bar_enabled']))
                    <div class="bg-gradient-to-r from-purple-950 via-purple-900 to-orange-700 text-white text-[11px] sm:text-xs py-3 px-4 sm:px-6">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-2.5 font-medium">
                                @if(!empty($data['top_bar_badge']))
                                    <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full bg-orange-500 text-white font-extrabold text-[10px] tracking-wider uppercase animate-pulse shadow-xs">
                                        {{ $data['top_bar_badge'] }}
                                    </span>
                                @endif
                                <span class="leading-relaxed">
                                    {!! strip_tags($data['top_bar_message'] ?: 'Tulis pesan pengumuman...', '<strong><b><em><span><mark>') !!}
                                </span>
                            </div>

                            @if(!empty($data['top_bar_button_text']))
                                <span class="inline-flex items-center gap-1 font-bold text-orange-300 hover:text-white text-xs cursor-default">
                                    <span>{{ $data['top_bar_button_text'] }}</span>
                                    <span>&rarr;</span>
                                </span>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="p-5 text-center text-xs text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-900/40 italic">
                        Banner pengumuman sedang dinonaktifkan (tidak akan tampil di halaman depan beranda).
                    </div>
                @endif
            </div>
        </div>

        {{ $this->form }}

        <div class="flex items-center gap-3 pt-2">
            <x-filament::button type="submit" size="lg" color="primary">
                Simpan Pengaturan
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>

