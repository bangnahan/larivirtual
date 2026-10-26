<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard — {{ $event->title }}</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Plus Jakarta Sans Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col font-sans antialiased selection:bg-orange-100 selection:text-orange-900">

    <!-- Navbar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-xs">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 font-extrabold text-lg text-slate-900 tracking-tight">
                <div class="w-8 h-8 rounded-lg bg-orange-50 border border-orange-200 p-1 flex items-center justify-center">
                    <span class="text-orange-600 font-black text-sm">VR</span>
                </div>
                <span>Virun Platform</span>
                <span class="hidden sm:inline-block text-[10px] uppercase px-2 py-0.5 rounded-full bg-orange-50 text-orange-700 font-bold border border-orange-200">Leaderboard</span>
            </a>
            <div class="flex items-center gap-3 text-xs sm:text-sm font-semibold">
                <a href="{{ route('public.submit.index') }}" class="text-orange-600 hover:text-orange-700 transition">🏃 Submit Lari</a>
                <a href="{{ route('home') }}" class="text-slate-500 hover:text-slate-800 transition">Beranda</a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-5xl mx-auto px-4 sm:px-6 py-8 sm:py-12 flex-1 w-full">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-orange-50 text-orange-700 border border-orange-200 mb-3">
                Live Ranking
            </span>
            <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-slate-900 mb-2">
                Leaderboard Pelari
            </h1>
            <p class="text-slate-500 max-w-xl mx-auto text-xs sm:text-sm">
                {{ $event->title }} — Pantau catatan jarak dan peringkat pelari secara transparan.
            </p>
        </div>

        <!-- Filter Card -->
        <div class="bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-xs mb-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <form action="{{ route('public.leaderboard.show', $event->slug) }}" method="GET" class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto">
                <select name="category_id" class="bg-slate-50 border border-slate-300 focus:border-orange-500 rounded-xl px-3 py-2 text-xs text-slate-800 font-semibold outline-none transition">
                    <option value="">Semua Kategori</option>
                    @foreach($event->categories as $cat)
                        <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }} ({{ number_format($cat->distance_km, 0) }}K)
                        </option>
                    @endforeach
                </select>

                <select name="gender" class="bg-slate-50 border border-slate-300 focus:border-orange-500 rounded-xl px-3 py-2 text-xs text-slate-800 font-semibold outline-none transition">
                    <option value="">Semua Gender</option>
                    <option value="male" {{ $gender == 'male' ? 'selected' : '' }}>Pria (Male)</option>
                    <option value="female" {{ $gender == 'female' ? 'selected' : '' }}>Wanita (Female)</option>
                </select>

                <button type="submit" class="bg-orange-600 hover:bg-orange-500 active:scale-95 text-white font-bold px-4 py-2 rounded-xl text-xs transition shadow-xs">
                    Terapkan
                </button>
            </form>

            <div class="text-xs text-slate-500 font-semibold self-start sm:self-auto">
                Total: <strong class="text-slate-900">{{ $participants->total() }}</strong> Peserta
            </div>
        </div>

        <!-- Leaderboard Table -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 text-[10px] uppercase font-bold tracking-wider">
                            <th class="py-3 px-4 w-16 text-center">Rank</th>
                            <th class="py-3 px-4">Pelari &amp; E-BIB</th>
                            <th class="py-3 px-4">Kategori</th>
                            <th class="py-3 px-4">Total Jarak</th>
                            <th class="py-3 px-4">Total Durasi</th>
                            <th class="py-3 px-4 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($participants as $idx => $p)
                            @php
                                $rank = $participants->firstItem() + $idx;
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-4 text-center font-bold">
                                    @if($rank == 1)
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-amber-50 text-amber-700 border border-amber-200 font-bold">🥇</span>
                                    @elseif($rank == 2)
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-slate-100 text-slate-700 border border-slate-300 font-bold">🥈</span>
                                    @elseif($rank == 3)
                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-amber-50 text-amber-800 border border-amber-300 font-bold">🥉</span>
                                    @else
                                        <span class="text-slate-400 font-mono font-semibold">#{{ $rank }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900">{{ $p->full_name }}</div>
                                    <div class="text-[11px] text-slate-500 font-mono">E-BIB #{{ $p->bib_number ?: '-' }}</div>
                                </td>
                                <td class="py-3.5 px-4 font-semibold text-slate-700">
                                    {{ $p->category?->name }}
                                </td>
                                <td class="py-3.5 px-4 font-black font-mono text-orange-600 text-sm">
                                    {{ number_format((float) $p->total_distance_km, 2) }} KM
                                </td>
                                <td class="py-3.5 px-4 font-mono text-slate-600">
                                    {{ gmdate('H:i:s', (int) $p->total_duration_seconds) }}
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    @if($p->isFinisher())
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            &check; FINISHER
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200 font-mono">
                                            {{ $p->progressPercentage() }}%
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-12 text-slate-400">
                                    Belum ada peserta terdaftar untuk filter ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($participants->hasPages())
            <div class="mt-6">
                {{ $participants->links() }}
            </div>
        @endif

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500 mt-auto">
        <div class="max-w-5xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
            <p>&copy; {{ date('Y') }} Virun Virtual Run Platform. Hak Cipta Dilindungi.</p>
            <p class="text-slate-400">Didukung oleh Tripay &amp; SPX Express.</p>
        </div>
    </footer>

</body>
</html>
