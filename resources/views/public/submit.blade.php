<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Hasil Lari — LariVirtual.com</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Plus Jakarta Sans Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col font-sans antialiased selection:bg-orange-100 selection:text-orange-900">

    <!-- Navbar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                <div class="w-8 h-8 rounded-lg bg-purple-950 border border-purple-800 p-1 flex items-center justify-center shadow-xs group-hover:scale-105 transition">
                    <img src="{{ asset('images/larivirtual/logo-icon-white.svg') }}" alt="LariVirtual" class="w-full h-full object-contain">
                </div>
                <div class="flex items-baseline">
                    <span class="text-lg font-black italic tracking-tight text-slate-900">lari<span class="text-orange-600">virtual</span></span>
                    <span class="text-xs font-extrabold text-purple-700">.com</span>
                </div>
            </a>
            <div class="flex items-center gap-3 text-xs sm:text-sm font-semibold">
                <a href="{{ route('public.ticket.index') }}" class="text-slate-600 hover:text-orange-600 transition">Cek E-BIB</a>
                @if($participant && $participant->category?->event)
                    <a href="{{ route('public.leaderboard.show', $participant->category->event->slug) }}" class="text-orange-600 hover:text-orange-700 transition">🏆 Leaderboard</a>
                @endif
                <a href="{{ route('home') }}" class="text-slate-500 hover:text-slate-800 transition">Beranda</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 py-8 sm:py-12 flex-1 w-full">
        
        <!-- Search Hero Section -->
        <div class="text-center mb-8 sm:mb-10">
            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-orange-50 text-orange-700 border border-orange-200 mb-3">
                Lapor Hasil Lari Mandiri
            </span>
            <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-slate-900 mb-2">
                Portal Submit Lari Virtual
            </h1>
            <p class="text-slate-500 max-w-xl mx-auto text-xs sm:text-sm">
                Cukup masukkan Nomor Dada (E-BIB) Anda tanpa perlu repot login untuk mengirimkan catatan kilometer lari dan memantau progres finisher.
            </p>
        </div>

        <!-- Search Box -->
        <div class="bg-white border border-slate-200 rounded-3xl p-4 sm:p-6 shadow-xs mb-8">
            <form action="{{ route('public.submit.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <input 
                        type="text" 
                        name="bib" 
                        value="{{ $bib }}" 
                        required 
                        autofocus
                        placeholder="Ketik Nomor E-BIB Anda (contoh: 1001)..." 
                        class="w-full bg-slate-50 border border-slate-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 rounded-2xl px-4 py-3.5 text-slate-900 placeholder-slate-400 text-xs sm:text-sm outline-none transition font-semibold"
                    >
                </div>
                <button 
                    type="submit" 
                    class="bg-orange-600 hover:bg-orange-500 active:scale-95 text-white font-bold px-6 py-3.5 rounded-2xl text-xs sm:text-sm transition shadow-md shadow-orange-600/20 flex items-center justify-center gap-2 cursor-pointer shrink-0"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <span>Cari Peserta</span>
                </button>
            </form>

            @if(!empty($error))
                <div class="mt-4 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-2xl text-xs font-semibold text-center">
                    {{ $error }}
                </div>
            @endif
        </div>

        @if($participant)
            <!-- Participant Info Card -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xs mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-2xl bg-orange-50 border border-orange-200 flex flex-col items-center justify-center shrink-0">
                            <span class="text-[9px] font-bold text-orange-500 tracking-wider">E-BIB</span>
                            <span class="text-xl font-black font-mono text-orange-600 leading-none mt-0.5">{{ $participant->bib_number }}</span>
                        </div>
                        <div>
                            <h2 class="text-lg sm:text-xl font-extrabold text-slate-900">{{ $participant->bib_name ?: $participant->full_name }}</h2>
                            <p class="text-xs text-slate-500 mt-0.5">
                                {{ $participant->category?->event?->title }} &bull; <strong class="text-orange-600">{{ $participant->category?->name }} (Target: {{ number_format($participant->targetDistance(), 2) }} KM)</strong>
                            </p>
                        </div>
                    </div>

                    <div>
                        @if($participant->isFinisher())
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                                &check; OFFICIAL FINISHER
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200">
                                🏃 DALAM PROGRES
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="mt-6 bg-slate-50 p-5 rounded-2xl border border-slate-200">
                    <div class="flex justify-between items-baseline mb-2">
                        <div class="text-sm sm:text-base font-extrabold text-slate-900 font-mono">
                            {{ number_format((float) $participant->total_distance_km, 2) }} KM
                            <span class="text-xs font-normal text-slate-500">/ {{ number_format($participant->targetDistance(), 2) }} KM Target</span>
                        </div>
                        <div class="text-base sm:text-lg font-black font-mono text-orange-600">
                            {{ $participant->progressPercentage() }}%
                        </div>
                    </div>

                    <div class="h-3 w-full bg-slate-200 rounded-full overflow-hidden">
                        <div class="h-full bg-orange-600 rounded-full transition-all duration-700" style="width: {{ $participant->progressPercentage() }}%;"></div>
                    </div>

                    @if(!$participant->isFinisher())
                        <div class="flex justify-between text-xs text-slate-500 mt-3 pt-2 border-t border-slate-200/60 font-medium">
                            <span>Sisa Jarak: <strong class="text-slate-800">{{ number_format($participant->remainingDistance(), 2) }} KM</strong></span>
                            <span>Total Durasi: <strong class="text-slate-800 font-mono">{{ gmdate('H:i:s', (int) $participant->total_duration_seconds) }}</strong></span>
                        </div>
                    @endif
                </div>

                @if($participant->isFinisher())
                    <div class="mt-5 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div>
                            <div class="font-bold text-sm text-emerald-900">🎉 Selamat! Anda Resmi Menjadi Finisher!</div>
                            <div class="text-xs text-emerald-700 mt-0.5">E-Sertifikat Anda telah terbit dan siap diunduh secara resmi.</div>
                        </div>
                        <a 
                            href="{{ route('public.certificate.download', $participant->bib_number) }}" 
                            class="bg-emerald-600 hover:bg-emerald-500 active:scale-95 text-white font-bold px-5 py-2.5 rounded-xl text-xs transition shadow-sm flex items-center gap-1.5 shrink-0"
                        >
                            <span>Unduh E-Sertifikat PDF 📥</span>
                        </a>
                    </div>
                @endif
            </div>

            @if($errors->any())
                <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 p-4 rounded-2xl text-xs">
                    <div class="flex items-center gap-2 font-bold text-rose-900 mb-1.5">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Gagal Menyimpan Catatan Lari:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 text-rose-700">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl text-xs font-semibold flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Activity Submission Form -->
            <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xs mb-8">
                <h3 class="text-base sm:text-lg font-extrabold text-slate-900 mb-1 flex items-center gap-2">
                    <span>📝 Lapor Catatan Lari Baru</span>
                </h3>
                <p class="text-xs text-slate-500 mb-6">Unggah tangkapan layar (screenshot GPS/treadmill) atau masukkan tautan aktivitas publik Strava/Garmin/Drive.</p>

                <form action="{{ route('public.submit.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <input type="hidden" name="bib_number" value="{{ $participant->bib_number }}">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Tanggal Lari *</label>
                            <input 
                                type="date" 
                                name="activity_date" 
                                value="{{ old('activity_date', date('Y-m-d')) }}" 
                                class="w-full bg-slate-50 border border-slate-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 outline-none transition" 
                                required
                            >
                            @error('activity_date')
                                <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Jarak Tempuh (Kilometer) *</label>
                            <input 
                                type="text" 
                                inputmode="decimal"
                                name="distance_km" 
                                value="{{ old('distance_km') }}" 
                                placeholder="Contoh: 5.25 atau 5,25" 
                                class="w-full bg-slate-50 border border-slate-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 outline-none transition font-semibold" 
                                required
                            >
                            @error('distance_km')
                                <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-[11px] text-slate-400 mt-1">Mendukung format titik maupun koma.</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Durasi Waktu Tempuh (Jam : Menit : Detik)</label>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <input type="number" name="hours" min="0" max="99" placeholder="00 Jam" value="{{ old('hours', 0) }}" class="w-full bg-slate-50 border border-slate-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 rounded-xl px-3 py-2.5 text-xs text-slate-900 outline-none transition text-center font-mono">
                                <span class="block text-[10px] text-slate-400 text-center mt-1">Jam</span>
                            </div>
                            <div>
                                <input type="number" name="minutes" min="0" max="59" placeholder="30 Menit" value="{{ old('minutes', old('minutes') !== null ? old('minutes') : 30) }}" class="w-full bg-slate-50 border border-slate-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 rounded-xl px-3 py-2.5 text-xs text-slate-900 outline-none transition text-center font-mono">
                                <span class="block text-[10px] text-slate-400 text-center mt-1">Menit</span>
                            </div>
                            <div>
                                <input type="number" name="seconds" min="0" max="59" placeholder="00 Detik" value="{{ old('seconds', 0) }}" class="w-full bg-slate-50 border border-slate-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 rounded-xl px-3 py-2.5 text-xs text-slate-900 outline-none transition text-center font-mono">
                                <span class="block text-[10px] text-slate-400 text-center mt-1">Detik</span>
                            </div>
                        </div>
                    </div>

                    <!-- Bukti Aktivitas: Foto atau Link -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
                        <div class="text-xs font-extrabold text-slate-800 flex items-center justify-between">
                            <span>📷 Bukti Aktivitas Lari (Wajib Pilih Salah Satu) *</span>
                            <span class="text-[10px] font-semibold text-orange-600 bg-orange-100/60 px-2 py-0.5 rounded-full">Foto atau Link</span>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Opsi 1: Unggah Tangkapan Layar / Foto Bukti (Rekomendasi)</label>
                            <input 
                                type="file" 
                                name="proof_image" 
                                accept="image/jpeg,image/png,image/jpg,image/webp" 
                                class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100 cursor-pointer bg-white border border-slate-200 rounded-xl p-1.5"
                            >
                            @error('proof_image')
                                <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-[11px] text-slate-400 mt-1">Screenshot aplikasi Strava, Garmin, Apple Fitness, Nike Run Club, atau foto layar treadmill (Maks. 1 MB).</p>
                        </div>

                        <div class="pt-2 border-t border-slate-200/70">
                            <label class="block text-xs font-bold text-slate-700 mb-1">Opsi 2: Atau Masukkan Link Tautan Publik</label>
                            <input 
                                type="text" 
                                name="proof_url" 
                                value="{{ old('proof_url') }}"
                                placeholder="Contoh: strava.app.link/... atau drive.google.com/..." 
                                class="w-full bg-white border border-slate-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 outline-none transition" 
                            >
                            @error('proof_url')
                                <p class="text-rose-600 text-[11px] font-semibold mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-[11px] text-slate-400 mt-1">Tautan aktivitas publik (bisa langsung paste tanpa awalan https://).</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1.5">Catatan Tambahan (Opsional)</label>
                        <input 
                            type="text" 
                            name="notes" 
                            value="{{ old('notes') }}"
                            placeholder="Misal: Lari pagi santai rute perumahan" 
                            class="w-full bg-slate-50 border border-slate-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 rounded-xl px-3.5 py-2.5 text-xs text-slate-900 outline-none transition"
                        >
                    </div>

                    <button 
                        type="submit" 
                        class="w-full bg-orange-600 hover:bg-orange-500 active:scale-95 text-white font-bold py-3.5 rounded-2xl text-xs sm:text-sm transition shadow-md shadow-orange-600/25 flex items-center justify-center gap-2 cursor-pointer mt-4"
                    >
                        <span>Kirim Catatan Lari 🔥</span>
                    </button>
                </form>
            </div>

            <!-- Submission History Table -->
            @if($participant->submissions->isNotEmpty())
                <div class="bg-white border border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xs">
                    <h3 class="text-base font-extrabold text-slate-900 mb-4">
                        📊 Riwayat Sesi Lari ({{ $participant->submissions->count() }} Sesi)
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="border-b border-slate-200 text-slate-500 text-[10px] uppercase font-bold tracking-wider">
                                    <th class="py-2.5 px-3">Tanggal</th>
                                    <th class="py-2.5 px-3">Jarak</th>
                                    <th class="py-2.5 px-3">Durasi</th>
                                    <th class="py-2.5 px-3">Pace</th>
                                    <th class="py-2.5 px-3 text-right">Bukti</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($participant->submissions as $sub)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-3 px-3 font-semibold text-slate-700">{{ $sub->activity_date->format('d M Y') }}</td>
                                        <td class="py-3 px-3 font-bold font-mono text-orange-600">{{ number_format((float) $sub->distance_km, 2) }} KM</td>
                                        <td class="py-3 px-3 font-mono text-slate-600">{{ gmdate('H:i:s', (int) $sub->duration_seconds) }}</td>
                                        <td class="py-3 px-3 font-mono text-slate-500">{{ $sub->calculated_pace ? number_format((float) $sub->calculated_pace, 2) . " '/km" : '-' }}</td>
                                        <td class="py-3 px-3 text-right space-x-1">
                                            @if($sub->proof_image_path)
                                                <a href="{{ asset('storage/' . $sub->proof_image_path) }}" target="_blank" class="text-emerald-600 hover:text-emerald-700 font-bold inline-flex items-center gap-1 text-[11px] bg-emerald-50 px-2 py-1 rounded-lg border border-emerald-200">
                                                    <span>📷 Foto</span>
                                                </a>
                                            @endif
                                            @if($sub->proof_url)
                                                <a href="{{ $sub->proof_url }}" target="_blank" class="text-orange-600 hover:text-orange-700 font-bold inline-flex items-center gap-1 text-[11px] bg-orange-50 px-2 py-1 rounded-lg border border-orange-200">
                                                    <span>🔗 Link</span>
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        @endif

    </main>

    <!-- Motivational Celebration Modal -->
    <div id="motivationalModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs {{ session('motivational_modal') ? 'flex' : 'hidden' }} items-center justify-center z-50 p-4">
        <div class="bg-white border border-slate-200 rounded-3xl max-w-md w-full p-6 sm:p-8 text-center shadow-2xl relative">
            @php $modal = session('motivational_modal', []); @endphp

            <div class="text-5xl mb-3">
                @if(($modal['tier'] ?? '') === 'FINISHER') 🏆
                @elseif(($modal['tier'] ?? '') === 'FINAL_PUSH') ⚡
                @elseif(($modal['tier'] ?? '') === 'BUILDING_RHYTHM') 🔥
                @else 🚀
                @endif
            </div>

            <h3 class="text-xl font-extrabold text-slate-900 mb-1.5">{{ $modal['title'] ?? 'Langkah Hebat!' }}</h3>
            <p class="text-xs sm:text-sm text-slate-500 leading-relaxed mb-5">{{ $modal['message'] ?? 'Catatan lari Anda berhasil ditambahkan. Terus tingkatkan progres Anda!' }}</p>

            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 grid grid-cols-2 gap-3 mb-5">
                <div>
                    <span class="block text-[10px] font-bold text-slate-400 uppercase">Sesi Ini</span>
                    <span class="text-base font-black font-mono text-orange-600">+{{ $modal['added_km'] ?? '0.00' }} KM</span>
                </div>
                <div>
                    <span class="block text-[10px] font-bold text-slate-400 uppercase">Total Akumulasi</span>
                    <span class="text-base font-black font-mono text-slate-900">{{ $modal['total_km'] ?? '0.00' }} KM</span>
                </div>
            </div>

            <div class="space-y-2">
                @if(!empty($modal['is_finisher']))
                    <a href="{{ $modal['certificate_url'] ?? '#' }}" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3 rounded-xl text-xs transition shadow-md flex items-center justify-center gap-1.5">
                        <span>Unduh E-Sertifikat Finisher 📥</span>
                    </a>
                @else
                    <button type="button" class="w-full bg-orange-600 hover:bg-orange-500 text-white font-bold py-3 rounded-xl text-xs transition shadow-md" onclick="closeMotivationalModal()">
                        Siap Lanjutkan Lari! 🔥
                    </button>
                @endif

                <button type="button" class="w-full text-slate-400 hover:text-slate-600 font-semibold py-2 text-xs transition" onclick="closeMotivationalModal()">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500 mt-auto">
        <div class="max-w-4xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
            <p>&copy; {{ date('Y') }} LariVirtual.com Platform. Hak Cipta Dilindungi.</p>
            <p class="text-slate-400">Didukung oleh Tripay &amp; SPX Express.</p>
        </div>
    </footer>

    <script>
        function closeMotivationalModal() {
            document.getElementById('motivationalModal').classList.add('hidden');
            document.getElementById('motivationalModal').classList.remove('flex');
        }

        @if(session('motivational_modal') && !empty(session('motivational_modal')['is_finisher']))
            window.addEventListener('DOMContentLoaded', () => {
                confetti({
                    particleCount: 120,
                    spread: 80,
                    origin: { y: 0.6 }
                });
            });
        @endif
    </script>
</body>
</html>
