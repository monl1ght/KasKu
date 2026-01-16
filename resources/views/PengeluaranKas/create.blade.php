{{-- resources/views/PengeluaranKas/create.blade.php --}}
<x-layouts.app-layout>

    @section('page-title', 'Tambah Pengeluaran Kas')
    @section('page-subtitle', 'Catat uang kas yang keluar beserta bukti nota/kwitansi')

    @push('styles')
        <style>
            [x-cloak] { display: none !important; }
            ::-webkit-scrollbar { width: 0px; background: transparent; }
        </style>
    @endpush

    @php
        $rupiahHint = function ($v) {
            // cuma buat hint tampilan (opsional)
            $n = (float) preg_replace('/[^\d]/', '', (string) $v);
            if ($n <= 0) return '0';
            return number_format($n, 0, ',', '.');
        };
    @endphp

    <div class="space-y-6"
        x-data="{
            previewUrl: null,
            previewType: null, // 'image' | 'pdf' | null
            fileName: null,
            amountRaw: '{{ old('amount') ?? '' }}',
            formatAmount() {
                // format input jadi 1.234.567 (tanpa desimal) agar enak diketik
                let s = (this.amountRaw ?? '').toString();
                s = s.replace(/[^\d]/g, '');
                if (!s) { this.amountRaw=''; return; }
                this.amountRaw = s.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            },
            onFileChange(e) {
                const f = e.target.files && e.target.files[0] ? e.target.files[0] : null;
                if (!f) {
                    this.previewUrl = null;
                    this.previewType = null;
                    this.fileName = null;
                    return;
                }
                this.fileName = f.name;

                const type = (f.type || '').toLowerCase();
                if (type.includes('pdf')) this.previewType = 'pdf';
                else if (type.includes('image')) this.previewType = 'image';
                else this.previewType = null;

                if (this.previewType === 'image') {
                    this.previewUrl = URL.createObjectURL(f);
                } else {
                    this.previewUrl = null;
                }
            }
        }">

        {{-- Breadcrumb / Back --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('pengeluaran-kas.index') }}"
                class="inline-flex items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm font-semibold text-white/80 hover:bg-white/10 hover:text-white transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </a>

            <div class="text-right">
                <p class="text-xs text-white/50">Organisasi Aktif</p>
                <p class="text-sm font-bold text-white">{{ $org->name ?? '—' }}</p>
            </div>
        </div>

        {{-- Card Form --}}
        <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur-md shadow-lg overflow-hidden">

            <div class="p-6 border-b border-white/10 bg-white/5">
                <h2 class="text-lg font-bold text-white">Form Pengeluaran Kas</h2>
                <p class="text-sm text-white/60 mt-1">
                    Pastikan jumlah dan bukti benar. Transaksi ini akan langsung berstatus <span class="font-semibold text-emerald-300">CONFIRMED</span>.
                </p>
            </div>

            <form method="POST" action="{{ route('pengeluaran-kas.store') }}" enctype="multipart/form-data" class="p-6 space-y-6">
                @csrf

                {{-- Errors --}}
                @if ($errors->any())
                    <div class="rounded-xl border border-red-500/30 bg-red-500/10 p-4 text-sm text-red-200">
                        <p class="font-semibold mb-2">Ada kesalahan input:</p>
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Grid fields --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    {{-- Tanggal --}}
                    <div>
                        <label class="block text-xs font-medium text-white/60 mb-1">Tanggal Pengeluaran</label>
                        <input type="date" name="payment_date" value="{{ old('payment_date', now()->format('Y-m-d')) }}"
                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-purple-500/40"
                            required>
                        <p class="mt-2 text-xs text-white/40">
                            *Tanggal ini disimpan di <code class="text-white/60">payment_date</code>.
                            Laporan periode saat ini masih mengikuti <code class="text-white/60">created_at</code>.
                        </p>
                    </div>

                    {{-- Jumlah --}}
                    <div>
                        <label class="block text-xs font-medium text-white/60 mb-1">Jumlah (Rp)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm text-white/40">Rp</span>
                            <input type="text" name="amount"
                                x-model="amountRaw"
                                @input="formatAmount()"
                                placeholder="contoh: 150.000"
                                class="w-full rounded-xl border border-white/10 bg-white/5 pl-12 pr-4 py-2.5 text-sm text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-purple-500/40"
                                required>
                        </div>
                        <p class="mt-2 text-xs text-white/40">
                            Gunakan angka saja. Sistem akan menormalisasi format (contoh <span class="text-white/60">1.000.000</span>).
                        </p>
                    </div>

                    {{-- Kategori --}}
                    <div>
                        <label class="block text-xs font-medium text-white/60 mb-1">Kategori</label>
                        <select name="category"
                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white focus:outline-none focus:ring-2 focus:ring-purple-500/40"
                            required>
                            <option value="" class="bg-slate-900">Pilih kategori…</option>
                            @foreach (($categories ?? []) as $cat)
                                <option value="{{ $cat }}" class="bg-slate-900" @selected(old('category') === $cat)>{{ $cat }}</option>
                            @endforeach
                        </select>
                        <p class="mt-2 text-xs text-white/40">
                            Jika tabel belum punya kolom <code class="text-white/60">category</code>, kategori akan disimpan di awal <code class="text-white/60">description</code> (format: <span class="text-white/60">[Kategori: ...]</span>).
                        </p>
                    </div>

                    {{-- Bukti --}}
                    <div>
                        <label class="block text-xs font-medium text-white/60 mb-1">Bukti Nota/Kwitansi</label>
                        <input type="file" name="receipt" accept="image/*,.pdf"
                            @change="onFileChange($event)"
                            class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm text-white file:mr-4 file:rounded-lg file:border-0 file:bg-white/10 file:px-4 file:py-2 file:text-xs file:font-semibold file:text-white hover:file:bg-white/15 transition"
                            required>
                        <p class="mt-2 text-xs text-white/40">
                            Format: JPG/PNG/WEBP/PDF. Maks 5MB.
                        </p>
                    </div>

                </div>

                {{-- Keterangan --}}
                <div>
                    <label class="block text-xs font-medium text-white/60 mb-1">Keterangan Penggunaan</label>
                    <textarea name="description" rows="4" placeholder="Contoh: Beli ATK untuk kegiatan rapat..."
                        class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-purple-500/40"
                        required>{{ old('description') }}</textarea>
                </div>

                {{-- Preview --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                        <p class="text-sm font-bold text-white">Preview Bukti</p>
                        <p class="text-xs text-white/50 mt-1" x-text="fileName ? ('File: ' + fileName) : 'Belum ada file dipilih.'"></p>

                        <div class="mt-4">
                            {{-- image preview --}}
                            <template x-if="previewType === 'image' && previewUrl">
                                <div class="overflow-hidden rounded-xl border border-white/10 bg-black/20">
                                    <img :src="previewUrl" alt="Preview Bukti" class="w-full h-64 object-contain">
                                </div>
                            </template>

                            {{-- pdf hint --}}
                            <template x-if="previewType === 'pdf'">
                                <div class="rounded-xl border border-white/10 bg-white/5 p-4 text-sm text-white/70">
                                    File PDF terpilih. Setelah disimpan, kamu bisa klik <span class="font-semibold text-white">Lihat Bukti</span> pada daftar pengeluaran.
                                </div>
                            </template>

                            {{-- other --}}
                            <template x-if="previewType === null && fileName">
                                <div class="rounded-xl border border-white/10 bg-white/5 p-4 text-sm text-white/70">
                                    Tipe file tidak dikenali untuk preview.
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                        <p class="text-sm font-bold text-white">Ringkasan</p>
                        <div class="mt-4 space-y-3 text-sm text-white/75">
                            <div class="flex items-center justify-between">
                                <span class="text-white/60">Status</span>
                                <span class="inline-flex items-center rounded-lg bg-emerald-500/15 px-2.5 py-1 text-xs font-semibold text-emerald-300 ring-1 ring-inset ring-emerald-400/20">
                                    CONFIRMED
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-white/60">Bendahara</span>
                                <span class="font-semibold text-white">{{ auth()->user()->name ?? '—' }}</span>
                            </div>
                            <div class="text-xs text-white/50 pt-2">
                                Setelah disimpan, transaksi ini akan langsung mempengaruhi saldo kas karena saldo dihitung dari pemasukan-confirmed dikurangi pengeluaran-confirmed.
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex flex-col sm:flex-row gap-3 sm:justify-end pt-2">
                    <a href="{{ route('pengeluaran-kas.index') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/5 px-5 py-2.5 text-sm font-semibold text-white/80 hover:bg-white/10 hover:text-white transition">
                        Batal
                    </a>
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-purple-600 to-blue-600 px-6 py-2.5 text-sm font-bold text-white shadow-lg hover:opacity-95 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Pengeluaran
                    </button>
                </div>
            </form>
        </div>

    </div>

</x-layouts.app-layout>
