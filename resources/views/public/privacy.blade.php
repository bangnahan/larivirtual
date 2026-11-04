<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kebijakan Privasi (Privacy Policy) — LariVirtual.com</title>
    <meta name="description" content="Kebijakan Privasi resmi LariVirtual.com. Perlindungan data pelari, kerahasiaan informasi pembayaran, dan keamanan pengiriman racepack.">
    
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
            <div class="flex items-center gap-2 text-xs font-bold text-red-400 uppercase tracking-wider mb-2">
                <span>🔒 Kebijakan Resmi</span>
                <span>•</span>
                <span>Diperbarui Oktober 2026</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white mb-6 tracking-tight">
                Kebijakan Privasi (Privacy Policy)
            </h1>
            <p class="text-sm text-slate-400 leading-relaxed mb-8 pb-6 border-b border-slate-800">
                Budaya Run Indonesia berkomitmen tinggi untuk menjaga dan menghormati privasi seluruh peserta virtual run. Kebijakan ini menjelaskan bagaimana data pribadi Anda dikumpulkan, disimpan, digunakan, dan dilindungi saat mendaftar serta berpartisipasi dalam event kami.
            </p>

            <div class="space-y-8 text-xs sm:text-sm text-slate-300 leading-relaxed">
                <section class="space-y-2.5">
                    <h2 class="text-base font-extrabold text-white flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-red-600/20 text-red-400 flex items-center justify-center text-xs">1</span>
                        Informasi yang Kami Kumpulkan
                    </h2>
                    <p class="text-slate-400">
                        Saat mendaftar event virtual run melalui platform Budaya Run, kami mengumpulkan sejumlah informasi yang diperlukan untuk pemrosesan tiket dan logistik:
                    </p>
                    <ul class="list-disc list-inside space-y-1.5 text-slate-300 pl-2">
                        <li><strong>Data Identitas &amp; E-BIB:</strong> Nama lengkap, nama di nomor dada (e-BIB), tanggal lahir, jenis kelamin, dan golongan darah.</li>
                        <li><strong>Data Kontak:</strong> Alamat email aktif dan nomor handphone/WhatsApp untuk konfirmasi tiket dan pembaruan event.</li>
                        <li><strong>Data Pengiriman Racepack:</strong> Alamat lengkap penerima, kota/kabupaten, kecamatan, kode pos, dan nama penerima untuk kurir SPX Express.</li>
                        <li><strong>Spesifikasi Perlengkapan:</strong> Ukuran jersey (XS hingga 5XL) dan model lengan (pendek, singlet, atau panjang).</li>
                        <li><strong>Data Hasil Lari (Submission):</strong> Tangkapan layar aplikasi lari (Strava, Garmin, Nike Run Club, Polar, Treadmill, dll), tanggal aktivitas, jarak tempuh (KM), dan durasi lari.</li>
                    </ul>
                </section>

                <section class="space-y-2.5">
                    <h2 class="text-base font-extrabold text-white flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-red-600/20 text-red-400 flex items-center justify-center text-xs">2</span>
                        Tujuan Penggunaan Data
                    </h2>
                    <p class="text-slate-400">
                        Informasi yang Anda berikan hanya digunakan semata-mata untuk kelancaran kegiatan virtual run:
                    </p>
                    <ul class="list-disc list-inside space-y-1.5 text-slate-300 pl-2">
                        <li>Menerbitkan nomor dada virtual resmi (e-BIB) dan E-Sertifikat Finisher ber-QR Code unik.</li>
                        <li>Memproses pembayaran tiket secara aman melalui payment gateway resmi berlisensi Bank Indonesia (Tripay).</li>
                        <li>Memproduksi medali kustom dan jersey sesuai pesanan ukuran peserta.</li>
                        <li>Mengirimkan paket fisik Race Pack ke alamat tujuan melalui ekspedisi logistik resmi (SPX Express).</li>
                        <li>Menampilkan papan peringkat (Leaderboard) pelari secara transparan.</li>
                        <li>Menghubungi peserta jika terdapat kendala pengiriman atau validasi hasil lari.</li>
                    </ul>
                </section>

                <section class="space-y-2.5">
                    <h2 class="text-base font-extrabold text-white flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-red-600/20 text-red-400 flex items-center justify-center text-xs">3</span>
                        Keamanan &amp; Perlindungan Data
                    </h2>
                    <p class="text-slate-400">
                        Kami menerapkan standar enkripsi dan pengamanan sistem untuk melindungi data Anda dari akses tanpa izin. Kami <strong>tidak pernah dan tidak akan menjual, menyewakan, atau memperdagangkan</strong> data pribadi Anda kepada pihak ketiga mana pun untuk tujuan periklanan di luar ekosistem Budaya Run.
                    </p>
                </section>

                <section class="space-y-2.5">
                    <h2 class="text-base font-extrabold text-white flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-red-600/20 text-red-400 flex items-center justify-center text-xs">4</span>
                        Pihak Ketiga yang Bekerjasama
                    </h2>
                    <p class="text-slate-400">
                        Data hanya diteruskan kepada mitra resmi yang terlibat langsung dalam rantai operasional event:
                    </p>
                    <ul class="list-disc list-inside space-y-1.5 text-slate-300 pl-2">
                        <li><strong>Tripay Payment Gateway:</strong> Memproses transaksi transfer bank, Virtual Account, QRIS, dan retail store secara terenkripsi SSL.</li>
                        <li><strong>SPX Express (Shopee Express):</strong> Memproses resi pengiriman, label paket, dan pengantaran fisik race pack ke alamat Anda.</li>
                    </ul>
                </section>

                <section class="space-y-2.5">
                    <h2 class="text-base font-extrabold text-white flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-red-600/20 text-red-400 flex items-center justify-center text-xs">5</span>
                        Hak dan Kendali Peserta
                    </h2>
                    <p class="text-slate-400">
                        Anda berhak untuk memeriksa status data e-BIB, meminta koreksi alamat pengiriman sebelum batas pengiriman racepack dimulai, atau mengajukan pertanyaan terkait privasi data kepada tim Customer Support kami melalui WhatsApp resmi di <strong>0819-1644-4458</strong> atau email <strong>hi@larivirtual.com</strong>.
                    </p>
                </section>
            </div>

            <div class="mt-10 pt-6 border-t border-slate-800 flex flex-wrap items-center justify-between gap-4">
                <span class="text-xs text-slate-500">&copy; {{ date('Y') }} LariVirtual.com. All Rights Reserved.</span>
                <div class="flex items-center gap-3">
                    <a href="{{ route('public.terms') }}" class="text-xs text-slate-400 hover:text-white underline underline-offset-2">Terms &amp; Conditions</a>
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
