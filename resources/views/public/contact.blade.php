<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hubungi Kami (Contact Us) — LariVirtual.com</title>
    <meta name="description" content="Pusat bantuan dan layanan pelanggan LariVirtual.com. Hubungi kami via WhatsApp Admin 081916444458 atau email resmi hi@larivirtual.com.">
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS v3 Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        patriotic: {
                            500: '#ef4444',
                            600: '#dc2626',
                            700: '#b91c1c',
                            800: '#991b1b',
                            900: '#7f1d1d',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-950 text-slate-100 font-sans antialiased min-h-screen flex flex-col justify-between selection:bg-red-500 selection:text-white">

    <!-- Top Navigation Bar -->
    <header class="sticky top-0 z-50 bg-slate-950/90 backdrop-blur-md border-b border-slate-800">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <img src="{{ asset('images/bhinneka/logo.png') }}" alt="Budaya Run Logo" class="h-9 w-auto object-contain">
                <div>
                    <span class="text-sm font-black text-white tracking-tight block">Budaya Run</span>
                    <span class="text-[10px] text-amber-400 font-semibold tracking-wide">Pelari Berbudi &amp; Berdaya</span>
                </div>
            </a>
            <a href="{{ route('home') }}" class="px-3.5 py-1.5 rounded-lg text-xs font-bold text-slate-300 hover:text-white bg-slate-900 hover:bg-slate-800 border border-slate-800 transition flex items-center gap-1.5">
                <span>&larr;</span>
                <span>Kembali ke Beranda</span>
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 py-12 sm:py-16">
        <div class="bg-slate-900/80 border border-slate-800/80 rounded-3xl p-6 sm:p-10 shadow-2xl backdrop-blur-sm">
            <div class="flex items-center gap-2 text-xs font-bold text-emerald-400 uppercase tracking-wider mb-2">
                <span>💬 Layanan Pelanggan</span>
                <span>•</span>
                <span>Respon Cepat</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white mb-3 tracking-tight">
                Hubungi Kami (Contact Us)
            </h1>
            <p class="text-sm text-slate-400 leading-relaxed mb-8 pb-6 border-b border-slate-800">
                Ada pertanyaan mengenai pendaftaran, status pembayaran, ukuran jersey, pengiriman racepack, atau validasi hasil lari? Tim panitia Budaya Run siap membantu Anda.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
                <!-- WhatsApp Card -->
                <div class="bg-slate-950/70 border border-emerald-500/30 rounded-2xl p-6 flex flex-col justify-between hover:border-emerald-500/60 transition group">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 flex items-center justify-center text-2xl mb-4 group-hover:scale-105 transition">
                            💬
                        </div>
                        <h2 class="text-lg font-extrabold text-white mb-1">WhatsApp Customer Support</h2>
                        <p class="text-xs text-slate-400 leading-relaxed mb-4">
                            Layanan konsultasi langsung paling cepat. Hubungi admin kami untuk konfirmasi tiket, invoice, dan kendala pengiriman.
                        </p>
                        <div class="text-emerald-400 font-mono font-bold text-sm mb-4">
                            +62 819-1644-4458
                        </div>
                    </div>
                    <a href="https://wa.me/6281916444458?text=Halo%20Admin%20Budaya%20Run,%20saya%20mau%20tanya%20seputar%20Bhinneka%20Tunggal%20Ika%20Virtual%20Run" 
                       target="_blank" 
                       rel="noopener noreferrer" 
                       class="w-full py-3 px-4 rounded-xl text-center font-bold text-xs bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-600/30 active:scale-95 transition flex items-center justify-center gap-2">
                        <span>Chat WhatsApp Sekarang</span>
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                    </a>
                </div>

                <!-- Email & Partnership Card -->
                <div class="bg-slate-950/70 border border-slate-800 rounded-2xl p-6 flex flex-col justify-between hover:border-slate-700 transition group">
                    <div>
                        <div class="w-12 h-12 rounded-xl bg-amber-500/20 text-amber-400 border border-amber-500/40 flex items-center justify-center text-2xl mb-4 group-hover:scale-105 transition">
                            ✉️
                        </div>
                        <h2 class="text-lg font-extrabold text-white mb-1">Email Resmi Panitia</h2>
                        <p class="text-xs text-slate-400 leading-relaxed mb-4">
                            Untuk pertanyaan resmi, kerjasama komunitas lari, sponsorship, penawaran korporasi, atau pengajuan revisi data sertifikat.
                        </p>
                        <div class="text-amber-400 font-mono font-bold text-sm mb-4">
                            admin@budayarun.com
                        </div>
                    </div>
                    <a href="mailto:admin@budayarun.com?subject=Tanya%20Bhinneka%20Virtual%20Run" 
                       class="w-full py-3 px-4 rounded-xl text-center font-bold text-xs bg-slate-800 hover:bg-slate-700 text-white border border-slate-700 active:scale-95 transition flex items-center justify-center gap-2">
                        <span>Kirim Email Resmi</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>

            <!-- Jam Operasional & Alamat -->
            <div class="bg-slate-950/50 rounded-2xl border border-slate-800/80 p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-3 gap-5 text-xs">
                <div>
                    <span class="text-slate-400 block mb-1">🕒 Jam Operasional CS:</span>
                    <strong class="text-white text-sm block">Senin - Minggu</strong>
                    <span class="text-slate-400">Pukul 08.00 - 21.00 WIB</span>
                </div>
                <div>
                    <span class="text-slate-400 block mb-1">📍 Kantor Penyelenggara:</span>
                    <strong class="text-white text-sm block">Budaya Run Indonesia</strong>
                    <span class="text-slate-400">Jakarta, Indonesia</span>
                </div>
                <div>
                    <span class="text-slate-400 block mb-1">⚡ Portal Cepat Pelari:</span>
                    <div class="space-y-1 mt-1">
                        <a href="{{ route('public.ticket.index') }}" class="text-amber-400 hover:underline block font-semibold">› Cek Nomor E-BIB</a>
                        <a href="{{ route('public.submit.index') }}" class="text-emerald-400 hover:underline block font-semibold">› Submit Hasil Lari</a>
                    </div>
                </div>
            </div>

            <div class="mt-10 pt-6 border-t border-slate-800 flex flex-wrap items-center justify-between gap-4">
                <span class="text-xs text-slate-500">&copy; {{ date('Y') }} Budaya Run Indonesia.</span>
                <div class="flex items-center gap-3">
                    <a href="{{ route('public.privacy') }}" class="text-xs text-slate-400 hover:text-white underline underline-offset-2">Privacy Policy</a>
                    <span class="text-slate-700">•</span>
                    <a href="{{ route('public.terms') }}" class="text-xs text-slate-400 hover:text-white underline underline-offset-2">Terms &amp; Conditions</a>
                </div>
            </div>
        </div>
    </main>

    <!-- Simple Footer -->
    <footer class="border-t border-slate-900 py-6 text-center text-xs text-slate-600">
        Budaya Run Indonesia • Pelari yang Berbudi dan Berdaya
    </footer>

</body>
</html>
