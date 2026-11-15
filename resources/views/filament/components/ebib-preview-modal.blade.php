<div class="space-y-4">
    <div class="flex items-center justify-between text-xs text-slate-500 bg-slate-50 p-3 rounded-xl border border-slate-200">
        <div class="flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
            <span>Simulasi Nomor BIB Pelari Contoh: <strong>#1001 (BUDI RUNS)</strong> &bull; Ukuran A5 Landscape (210 x 148 mm)</span>
        </div>
        <a 
            href="{{ $previewUrl }}" 
            target="_blank" 
            class="inline-flex items-center gap-1 font-bold text-orange-600 hover:text-orange-700 hover:underline"
        >
            <span>↗️ Buka di Tab Penuh</span>
        </a>
    </div>

    <div class="w-full bg-slate-900 rounded-2xl overflow-hidden border border-slate-300 shadow-inner" style="aspect-ratio: 210 / 148; min-height: 480px;">
        <iframe 
            src="{{ $previewUrl }}" 
            class="w-full h-full border-0" 
            title="Pratinjau E-BIB"
        ></iframe>
    </div>

    <p class="text-[11px] text-slate-400 text-center">
        💡 <em>Catatan: Jika Anda mengubah nilai pada form, klik tombol <strong>"Simpan Perubahan"</strong> di bagian bawah halaman terlebih dahulu agar pratinjau memuat tata letak terbaru.</em>
    </p>
</div>
