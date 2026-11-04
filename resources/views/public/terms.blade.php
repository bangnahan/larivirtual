<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Syarat &amp; Ketentuan (Terms &amp; Conditions) — LariVirtual.com</title>
    <meta name="description" content="Syarat dan Ketentuan resmi kepesertaan event virtual run LariVirtual.com. Aturan lomba, submission hasil lari, dan pengiriman racepack.">
    
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
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-purple-800 to-purple-950 border border-purple-700/80 p-1.5 flex items-center justify-center shadow-lg shadow-purple-950/50 group-hover:scale-105 transition">
                    <img src="{{ asset('images/larivirtual/logo-icon-white.svg') }}" alt="LariVirtual Icon" class="w-full h-full object-contain">
                </div>
                <div class="flex flex-col">
                    <div class="flex items-baseline">
                        <span class="text-lg font-black italic tracking-tight text-white">lari<span class="text-orange-500">virtual</span></span>
                        <span class="text-xs font-extrabold text-purple-400">.com</span>
                    </div>
                    <span class="text-[9px] uppercase tracking-widest font-bold text-slate-400 -mt-1">Run Anywhere. Race Together.</span>
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
            <div class="flex items-center gap-2 text-xs font-bold text-amber-400 uppercase tracking-wider mb-2">
                <span>📜 Regulasi Resmi Lomba</span>
                <span>•</span>
                <span>Berlaku 2026</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white mb-6 tracking-tight">
                Syarat &amp; Ketentuan (Terms &amp; Conditions)
            </h1>
            <p class="text-sm text-slate-400 leading-relaxed mb-8 pb-6 border-b border-slate-800">
                Dengan mendaftarkan diri pada event <strong>Bhinneka Tunggal Ika Virtual Run 42.195 KM</strong>, peserta menyatakan telah membaca, memahami, menyetujui, dan bersedia mematuhi seluruh syarat dan ketentuan berikut:
            </p>

            <div class="space-y-8 text-xs sm:text-sm text-slate-300 leading-relaxed">
                <section class="space-y-2.5">
                    <h2 class="text-base font-extrabold text-white flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-amber-600/20 text-amber-400 flex items-center justify-center text-xs">1</span>
                        Ketentuan Pendaftaran &amp; Pembayaran
                    </h2>
                    <ul class="list-disc list-inside space-y-1.5 text-slate-300 pl-2">
                        <li>Pendaftaran terbuka untuk umum, baik warga negara Indonesia maupun mancanegara tanpa batasan usia minimal, selama peserta memiliki kondisi fisik yang memadai.</li>
                        <li>Peserta wajib mengisi data diri secara jujur dan akurat. Kesalahan penulisan nama di e-BIB atau alamat pengiriman paket berada di luar tanggung jawab penyelenggara.</li>
                        <li>Pendaftaran dianggap sah setelah pembayaran berhasil diverifikasi oleh sistem payment gateway Tripay.</li>
                        <li>Biaya pendaftaran yang telah dibayarkan <strong>bersifat final dan tidak dapat dikembalikan (non-refundable)</strong> dengan alasan apa pun, karena alokasi produksi jersey kustom dan pencetakan medali langsung diproses.</li>
                    </ul>
                </section>

                <section class="space-y-2.5">
                    <h2 class="text-base font-extrabold text-white flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-amber-600/20 text-amber-400 flex items-center justify-center text-xs">2</span>
                        Aturan Aktivitas Lari &amp; Pengiriman Hasil (Submission)
                    </h2>
                    <ul class="list-disc list-inside space-y-1.5 text-slate-300 pl-2">
                        <li>Jarak target kategori 42.195 KM dapat diselesaikan dengan 2 mode pilihan:
                            <ul class="list-circle list-inside pl-5 mt-1 space-y-1 text-slate-400">
                                <li><strong>Mode Sekali Lari (Single Run):</strong> Menyelesaikan 42.195 KM dalam 1 sesi lari/jalan.</li>
                                <li><strong>Mode Cicil (Cumulative Run):</strong> Menempuh jarak secara bertahap beberapa kali (misal: 5 KM x 8 kali) selama rentang periode lomba (September s.d. 31 Desember 2026).</li>
                            </ul>
                        </li>
                        <li>Aktivitas dapat dilakukan di mana saja: jalan raya, jogging track, taman, perumahan, maupun treadmill indoor.</li>
                        <li>Peserta dapat mencatat aktivitas menggunakan aplikasi GPS apa saja (Strava, Garmin Connect, Nike Run Club, Polar, Suunto, Apple Fitness, Coros, dll) atau foto display treadmill yang jelas.</li>
                        <li>Seluruh hasil lari wajib disubmit melalui portal resmi <a href="{{ route('public.submit.index') }}" class="text-amber-400 underline font-semibold">budayarun.com/submit</a> paling lambat tanggal <strong>31 Desember 2026 pukul 23:59 WIB</strong>.</li>
                    </ul>
                </section>

                <section class="space-y-2.5">
                    <h2 class="text-base font-extrabold text-white flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-amber-600/20 text-amber-400 flex items-center justify-center text-xs">3</span>
                        Produksi &amp; Pengiriman Race Pack
                    </h2>
                    <ul class="list-disc list-inside space-y-1.5 text-slate-300 pl-2">
                        <li>Paket Race Pack (Medali Logam Zinc Alloy Asli dan Jersey Adidas by 06 AM RUNNING) akan diproduksi sesuai spesifikasi ukuran yang dipilih peserta saat pendaftaran.</li>
                        <li>Pengiriman Race Pack dilakukan secara bertahap dan serentak mulai <strong>25 Oktober 2026</strong> menggunakan ekspedisi resmi <strong>SPX Express</strong>.</li>
                        <li>Nomor resi pengiriman akan diinformasikan ke WhatsApp atau email peserta saat paket telah diserahkan ke pihak ekspedisi.</li>
                        <li>Keterlambatan pengantaran yang disebabkan oleh alamat yang tidak lengkap, rumah kosong, atau kendala operasional kurir dapat dikoordinasikan langsung melalui WhatsApp CS kami.</li>
                    </ul>
                </section>

                <section class="space-y-2.5">
                    <h2 class="text-base font-extrabold text-white flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-amber-600/20 text-amber-400 flex items-center justify-center text-xs">4</span>
                        Pelepasan Tanggung Jawab Kesehatan (Waiver of Liability)
                    </h2>
                    <p class="text-slate-400">
                        Setiap peserta menyatakan bahwa dirinya dalam kondisi fisik dan mental yang sehat untuk menjalani kegiatan olahraga lari atau jalan. Penyelenggara Budaya Run Indonesia tidak bertanggung jawab atas cedera fisik, kecelakaan, gangguan kesehatan, maupun kehilangan barang berharga yang dialami peserta selama melakukan aktivitas lari mandiri.
                    </p>
                </section>

                <section class="space-y-2.5">
                    <h2 class="text-base font-extrabold text-white flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-amber-600/20 text-amber-400 flex items-center justify-center text-xs">5</span>
                        Hak Penyelenggara
                    </h2>
                    <p class="text-slate-400">
                        Penyelenggara berhak membatalkan atau menganulir data submission pelari yang terbukti melakukan manipulasi data GPS atau kecurangan yang tidak mencerminkan semangat sportivitas pelari berbudi dan berdaya.
                    </p>
                </section>
            </div>

            <div class="mt-10 pt-6 border-t border-slate-800 flex flex-wrap items-center justify-between gap-4">
                <span class="text-xs text-slate-500">&copy; {{ date('Y') }} LariVirtual.com. All Rights Reserved.</span>
                <div class="flex items-center gap-3">
                    <a href="{{ route('public.privacy') }}" class="text-xs text-slate-400 hover:text-white underline underline-offset-2">Privacy Policy</a>
                    <span class="text-slate-700">•</span>
                    <a href="{{ route('public.contact') }}" class="text-xs text-slate-400 hover:text-white underline underline-offset-2">Contact Us</a>
                </div>
            </div>
        </div>
    </main>

    <!-- Simple Footer -->
    <footer class="border-t border-slate-900 py-6 text-center text-xs text-slate-500">
        LariVirtual.com • Run Anywhere. Race Together.
    </footer>

</body>
</html>
