# Product Requirement Document (PRD) — Virtual Run Platform
**Proyek:** Platform Event Lari Virtual (Transisi dari Jelatix Ticketing)  
**Model Operasional:** Single Platform / Direct Brand  
**Origin Pengiriman Racepack:** KAB. TANGERANG (Official SPX Partner)  
**Versi:** 2.0 (Virtual Run Edition)  
**Tanggal:** 12 Oktober 2026  
**Status:** Approved & Ready for Migration  

---

## 1. Eksekutif & Ringkasan Perubahan

### 1.1 Latar Belakang & Visi Produk
Proyek ini bertransformasi dari platform tiketing event lari konvensional (offline) menjadi **Platform Event Lari Virtual terintegrasi**. Pada event virtual:
- Peserta dapat berlari di mana saja dan kapan saja selama periode event berlangsung.
- Seluruh peserta mendapatkan **Race Pack fisik** (Jersey, Medali Finisher, Paket Perlengkapan Lari) yang **dikirim langsung ke alamat rumah peserta**.
- Biaya ongkos kirim dihitung secara akurat menggunakan **Database Resmi SPX (Shopee Xpress)** dengan origin titik pengiriman dari **KAB. TANGERANG**.
- Peserta mencatat dan mengunggah bukti lari (Strava, Garmin, Google Drive) melalui **Universal Submission Portal** untuk memantau progres jarak dan meraih **E-Certificate Finisher**.

### 1.2 Ringkasan Keputusan Strategis & Transformasi Sistem
Berdasarkan arahan kebutuhan spesifik proyek:
1. **Model Platform: Single Platform / Direct Brand**  
   Menghapus kompleksitas multi-organizer dan sistem bagi hasil EO eksternal. Seluruh event dikelola langsung oleh tim internal melalui **Filament Admin Panel** (`/admin`).
2. **Paket Pendaftaran: 100% Pengiriman Fisik**  
   Setiap pendaftaran kategori wajib memilih ukuran jersey dan menyertakan alamat pengiriman lengkap beserta pembayaran ongkir SPX.
3. **Arsitektur Ongkir SPX: Embedded Database Laravel**  
   Data 7.100 baris tarif SPX disimpan langsung di dalam database proyek pada tabel `spx_shipping_rates` (dari origin Kab. Tangerang) untuk pencarian instan (<10 ms) tanpa ketergantungan HTTP service eksternal.
4. **Layanan Ekspedisi: SPX Regular**  
   Menggunakan tarif dan SLA resmi layanan SPX Regular ke seluruh kecamatan se-Indonesia.
5. **Penghapusan Fitur Kru Scanner Venue (RPC)**  
   Fitur scanner tiket fisik di venue (`/crew`, `CrewScannerController`, dan proxy claim) digantikan sepenuhnya oleh **Modul Fulfillment & Pengiriman** (input resi SPX, cetak invoice & label kirim, tracking status barang).
6. **Alur Checkout: Multi-Peserta per Transaksi**  
   Satu transaksi pemesanan dapat mendaftarkan beberapa peserta sekaligus dengan **satu alamat pengiriman terpusat**, di mana berat paket dihitung secara akumulatif.
7. **Modul Virtual Run: Submission, Leaderboard & E-Certificate**  
   Mendukung pencatatan lari (Single session maupun Cumulative cicil jarak), live ranking publik, e-BIB generator, dan sertifikat finisher otomatis.

---

## 2. Aktor & Peran Sistem

| Peran | Deskripsi | Hak Akses Utama |
| :--- | :--- | :--- |
| **Peserta / Pendaftar (Public)** | Pelari yang mendaftar secara individu atau mendaftarkan teman/komunitas. | Melihat katalog event virtual, checkout multi-peserta, cek ongkir SPX, bayar via Tripay, download e-BIB, submit lari, pantau leaderboard, download E-Certificate. |
| **Admin Logistik & Race (Internal)** | Tim operasional pengiriman dan verifikasi event. | Mengelola data event & kategori, memantau pesanan masuk, memverifikasi submission lari peserta, input nomor resi SPX, update status pengiriman (*Packed / Shipped / Delivered*). |
| **Super Admin** | Penanggung jawab teknis platform. | Akses penuh sistem, konfigurasi payment gateway Tripay, manajemen user admin, update master tarif SPX. |

---

## 3. Fitur Utama & Alur Bisnis (Functional Requirements)

### 3.1 Manajemen Event Virtual (Admin)
- **Informasi Event:** Judul event, slug, deskripsi, banner, rules, periode registrasi, dan periode lari (*race start & race end*).
- **Tipe Aktivitas:** `RUN`, `RIDE`, `WALK`.
- **Mode Submission:**
  - `SINGLE`: Peserta wajib menuntaskan target jarak kategori dalam 1 sesi lari.
  - `CUMULATIVE`: Peserta bebas mencicil jarak lari berkali-kali hingga mencapai target.
- **Kategori Event:**
  - Pilihan jarak (misal: 5K, 10K, 21K, 42K, atau Custom Target).
  - Harga pendaftaran (sudah mencakup medali & jersey fisik).
  - Bobot dasar paket per peserta (default 500 gram / peserta untuk kalkulasi ongkir SPX).
  - Prefix BIB otomatis (contoh: `VR5K-`, `VR10K-`).
- **Add-on & Merchandise Event (Kustom per Event):**
  - Merchandise tambahan opsional yang dapat diatur oleh admin di Filament: jersey finisher, enamel pin, soft flask (botol minum), topi lari, dll.
  - Setiap add-on memiliki nama, harga, deskripsi, foto produk, berat (gram untuk SPX), batas stok / unlimited, dan toggle aktif.
  - Ditampilkan di Step 1 (Pilih Tiket) dengan stepper kuantitas dan terintegrasi ke kalkulasi total pesanan, SPX, dan Tripay.

### 3.2 Form Registrasi & Checkout Multi-Peserta
1. **Langkah 1 — Data Pemesan (Buyer Information):**
   - Nama Pemesan, Email, Nomor WhatsApp (digunakan untuk menerima invoice & notifikasi Tripay).
2. **Langkah 2 — Data Peserta (Bisa 1 atau Banyak Peserta):**
   - Untuk setiap peserta:
     - Pilihan Kategori Lari.
     - Nama Lengkap & Nama di BIB (*BIB Name*).
     - Jenis Kelamin & Tanggal Lahir.
     - Nomor Handphone & Golongan Darah.
     - Pilihan Ukuran Jersey (XS, S, M, L, XL, XXL, 3XL, 4XL, 5XL).
3. **Langkah 3 — Alamat Pengiriman Racepack (Satu Alamat Gabungan):**
   - Nama Penerima & Nomor Telepon Penerima.
   - Autocomplete Kota/Kabupaten & Kecamatan (langsung mencari ke tabel `spx_shipping_rates`).
   - Alamat Lengkap (Jalan, No. Rumah, RT/RW, Patokan) & Kode Pos.
4. **Langkah 4 — Perhitungan Otomatis Ongkir SPX Regular:**
   - Total Berat Paket = $\text{Jumlah Peserta} \times \text{Berat Paket Dasar (500 gr)}$.
   - Pembulatan Berat: Berdasarkan aturan SPX (pembulatan ke kg terdekat).
   - Ongkir = $\lceil \text{Berat (kg)} \rceil \times \text{Tarif Regular SPX per kg}$.
   - Ditampilkan estimasi waktu pengiriman (*SLA Days*).
5. **Langkah 5 — Pembayaran Tripay:**
   - Mendukung QRIS, Virtual Account (BCA, Mandiri, BRI, BNI), E-Wallet, dan Gerai Retail.
   - Grand Total = Subtotal Kategori + Ongkir SPX + Kode Unik / Fee.
   - Webhook Callback Tripay: Begitu status `PAID`, sistem otomatis mengubah status pesanan menjadi `paid`, meng-generate e-BIB untuk seluruh peserta, dan mengirimkan email konfirmasi.

### 3.3 Otomatisasi Generate E-BIB per Tiket
1. **Otomasi Instan Setiap Tiket**:
   - Setiap tiket yang dipilih/didaftarkan di dalam form pemesanan (baik 1 peserta maupun multi-peserta) **otomatis di-generate-kan nomor E-BIB unik dan kartu digital E-BIB** untuk masing-masing peserta.
   - Pemicu (*Trigger*): Diterbitkan otomatis begitu pembayaran Tripay dinyatakan lunas (`PAID`), atau saat admin mengonfirmasi pesanan secara manual.
2. **Format & Logika Penomoran E-BIB (Global Auto-Increment)**:
   - Format penomoran dibuat **sederhana berupa angka numerik murni tanpa prefix** (contoh: `1001`, `1002`, `1003`, dst.).
   - Dimulai dari angka **`1001`** dan terus bertambah secara global di seluruh sistem, **meskipun beda event maupun beda kategori**.
   - Setiap tiket baru yang terbit otomatis mengambil nomor urut berikutnya secara global dengan proteksi *database locking*, menjamin nomor E-BIB bersifat unik di seluruh platform dan sangat mudah diingat oleh peserta saat login di portal `/submit`.
3. **Kartu Digital E-BIB (Digital Asset)**:
   - Sistem merender gambar kartu E-BIB secara visual beresolusi tinggi (PNG) yang memuat:
     - Background resmi event virtual.
     - Nomor E-BIB peserta (ukuran besar & kontras).
     - Nama Lengkap dan Nama di BIB (*BIB Name*).
     - Kategori Jarak (misal: 10K Virtual Run).
     - QR Code unik (`qr_token`) untuk verifikasi identitas lari.
4. **Akses & Distribusi E-BIB ke Peserta**:
   - **Halaman Sukses / Invoice:** Menampilkan kartu preview E-BIB dan tombol "Download E-BIB" untuk setiap peserta dalam pesanan.
   - **Email Konfirmasi:** Melampirkan nomor E-BIB dan tautan unduh kartu E-BIB masing-masing peserta.
   - **Portal Mandiri (`/cek-tiket` & `/submit`):** Peserta dapat memasukkan nomor telepon/email/BIB untuk melihat kembali kartu E-BIB dan mengakses form submit lari.
   - E-BIB berfungsi ganda: sebagai materi pamer media sosial (*social media badge*) sebelum lari, dan sebagai identitas login pelaporan aktivitas lari.

### 3.4 Universal Submission Portal (`/submit`) — Akses Tanpa Login (Zero-Login)
1. **Akses Instan Cukup Nomor E-BIB (Tanpa Login / Tanpa Akun)**:
   - Peserta **tidak perlu membuat akun atau mengingat kata sandi**.
   - Cukup mengakses `/submit` dan memasukkan **Nomor E-BIB** (contoh: `1001`).
   - Sistem seketika mendeteksi identitas peserta: Nama Pelari, Event yang diikuti, Kategori Jarak, Target Waktu, serta Progress Bar akumulasi jarak saat ini.
2. **Form Submit Catatan Lari Ringkas & Cepat**:
   - **Tanggal Aktivitas:** Tanggal sesi lari dilakukan.
   - **Jarak Tempuh:** Format desimal kilometer (contoh: `5.20` km).
   - **Durasi Waktu:** Jam, Menit, Detik (contoh: `00:32:15`).
   - **Link Bukti Rekam Jejak (Wajib):** Link aktivitas publik **Strava** atau link folder/file **Google Drive** (mendukung juga Garmin Connect / Apple Fitness).
   - **Catatan Tambahan:** Opsional.
3. **Progres & Pencapaian Finisher Otomatis**:
   - Setiap kali submit berhasil, sistem mengakumulasikan jarak tempuh dan menampilkan persentase target.
   - Ketika total jarak mencapai target kategori ($\ge 100\%$), status peserta seketika berubah menjadi `finisher`.
   - Tombol unduh **E-Certificate Finisher** langsung aktif dan dapat diunduh tanpa syarat tambahan.
4. **Pop-up Interaktif Kata Semangat & Motivasi Lari (Motivational Celebration Modal)**:
   - Setiap kali peserta selesai menekan tombol submit dan data berhasil disimpan, sistem menampilkan **Pop-up Modal Interaktif yang membakar semangat** pelari untuk terus melanjutkan larinya.
   - **Pesan Dinamis Berdasarkan Progres**:
     - **Tahap Awal (< 30%):** *"Langkah awal yang luar biasa! Perjalanan ribuan kilometer selalu dimulai dari satu langkah pertama. Jaga ritme, nikmati langkahmu, dan teruslah bergerak! 🚀"*
     - **Tahap Pertengahan (30% - 70%):** *"Momentummu makin tak terbendung! Kamu sudah menaklukkan separuh jalan. Napasmu kian teratur, tekadmu kian membaja. Jangan kasih kendor, finish menantimu! 🔥"*
     - **Tahap Akhir (70% - 99%):** *"Sedikit lagi jadi Finisher! Sisa jarak terakhir sudah di depan mata. Kumpulkan sisa energimu dan tuntaskan perjuangan hebat ini! ⚡"*
     - **Tahap Finisher (100%):** *"LUAR BIASA! TARGET TUNTAS 100% — SELAMAT FINISHER! 🏆 Dedikasi dan keringatmu terbayar lunas. Unduh sertifikat kebanggaanmu sekarang dan pamerkan pencapaianmu!"* (Disertai animasi selebrasi konfeti).
   - Menampilkan ringkasan: Jarak baru saja ditambahkan, Total jarak saat ini, Sisa jarak menuju finish, serta tombol **"Siap Lanjutkan Lari!"** atau **"Unduh E-Certificate"**.

### 3.5 Leaderboard Publik (`/events/{slug}/leaderboard`)
- Menampilkan papan peringkat peserta per kategori jarak.
- Parameter pengurutan: Total jarak tempuh, durasi tercepat, dan pace rata-rata.
- Filter berdasarkan Gender (Pria / Wanita) dan Kategori.

### 3.6 Modul Fulfillment Logistik & Resi SPX (Admin)
- Daftar pesanan berstatus lunas yang memerlukan pengiriman racepack.
- Rincian rekap logistik per pesanan: Nama penerima, alamat tujuan, rincian ukuran jersey seluruh peserta dalam paket.
- Input nomor resi pengiriman SPX (*Tracking Number*).
- Pembaruan status pengiriman: `PENDING` $\rightarrow$ `PACKED` $\rightarrow$ `SHIPPED` $\rightarrow$ `DELIVERED`.
- Fitur cetak Label Pengiriman & Packing Slip untuk ditempel pada paket.

---

## 4. Alur Integrasi Database Tarif SPX

1. **Origin Tetap:** `KAB. TANGERANG`
2. **Data Cakupan:** 7.100 baris kombinasi Kota/Kabupaten dan Kecamatan di Indonesia.
3. **Endpoint Pencarian Cepat (Internal AJAX):**
   - URL: `/api/shipping/spx/search?q={keyword}`
   - Mengembalikan daftar kecamatan & kota yang cocok beserta `regular_rate_per_kg` dan `regular_sla_days`.
4. **Endpoint Hitung Ongkir:**
   - URL: `/api/shipping/spx/calculate`
   - Parameter: `destination_city`, `destination_district`, `total_weight_grams`.
   - Mengembalikan nominal ongkos kirim resmi SPX Regular.

---

## 5. Roadmap Pelaksanaan Perubahan

1. **Fase 1 (Arsitektur & Dokumentasi):** Finalisasi PRD dan Skema Database (Tahap Saat Ini).
2. **Fase 2 (Migrasi Database & Seeder):**
   - Migrasi tabel `spx_shipping_rates` dan import data dari file master SPX.
   - Refactor tabel `events`, `event_categories`, `orders`, `participants`, dan `order_shipping_addresses`.
   - Pembuatan tabel `activity_submissions`.
   - Pembersihan tabel / route yang tidak lagi digunakan (organizers, payouts, scanner crew).
3. **Fase 3 (Backend Services & Logika Ongkir):**
   - Implementasi `SpxShippingService` untuk kalkulasi ongkir dan autocomplete wilayah.
   - Modifikasi `PublicRegistrationController` untuk mendukung multi-peserta dan alamat pengiriman SPX.
   - Penyesuaian `TripayService` untuk memasukkan komponen ongkir ke rincian transaksi.
4. **Fase 4 (Antarmuka Pengguna / Frontend):**
   - Pembaruan form checkout publik (multi-peserta + auto-calculate ongkir SPX).
   - Halaman `/submit` (Universal Submission Portal).
   - Halaman `/leaderboard` publik.
5. **Fase 5 (Filament Admin Panel):**
   - Panel Fulfillment Pengiriman & Resi SPX.
   - Verifikasi Submission Aktivitas Lari.
   - Export Data Pengiriman & Rekap Jersey.
