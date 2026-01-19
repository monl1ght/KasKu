{{-- resources/views/PengeluaranKas/index.blade.php --}}
<x-layouts.app-layout>

    @section('page-title', 'Pengeluaran Kas')
    @section('page-subtitle', 'Catat setiap kas keluar (nota/kwitansi) dan saldo akan otomatis berkurang')

    @push('styles')
        <style>
            [x-cloak] {
                display: none !important;
            }

            /*
                     |------------------------------------------------------------------
                     | IMPORTANT
                     |------------------------------------------------------------------
                     | Jangan menyembunyikan scrollbar secara GLOBAL di halaman.
                     | Ini bikin layout "mental" (shift) saat pindah halaman karena
                     | lebar viewport berubah ketika scrollbar browser hilang.
                     |
                     | Solusi: sembunyikan scrollbar hanya pada container overflow
                     | tertentu (mis. wrapper tabel) dengan class .no-scrollbar.
                     */

            .no-scrollbar {
                -ms-overflow-style: none;
                /* IE/Edge legacy */
                scrollbar-width: none;
                /* Firefox */
            }

            /* Icon kalender input date jadi putih */
            input[type="date"]::-webkit-calendar-picker-indicator {
                filter: invert(1) brightness(2);
                opacity: 1;
                cursor: pointer;
            }

            /* Chrome/Edge/Safari */
            .date-input::-webkit-calendar-picker-indicator {
                opacity: 0;
                /* sembunyikan icon native */
                cursor: pointer;
                width: 2.75rem;
                /* area klik icon tetap ada */
                height: 100%;
            }

            /* Firefox (kalau didukung) */
            .date-input::-moz-calendar-picker-indicator {
                opacity: 0;
                cursor: pointer;
            }
        </style>
    @endpush

    @php
        // Helper: format rupiah
        $rupiah = function ($n) {
            return 'Rp ' . number_format((float) $n, 0, ',', '.');
        };

        // Helper: ambil kategori (kalau belum ada kolom category, dibaca dari prefix description: [Kategori: ...])
        $categoryOf = function ($row) {
            if (isset($row->category) && $row->category) {
                return $row->category;
            }
            $desc = (string) ($row->description ?? '');
            if (preg_match('/^\[Kategori:\s*(.*?)\]\s*/', $desc, $m)) {
                return trim($m[1]);
            }
            return '-';
        };

        // Helper: bersihkan description dari prefix kategori
        $cleanDesc = function ($row) {
            $desc = (string) ($row->description ?? '');
            $desc = preg_replace('/^\[Kategori:\s*(.*?)\]\s*/', '', $desc);
            return trim($desc) ?: '-';
        };

        // Helper: tanggal tampil (pakai payment_date kalau ada, fallback created_at)
        $displayDate = function ($row) {
            if (!empty($row->payment_date)) {
                try {
                    return \Carbon\Carbon::parse($row->payment_date)->format('d M Y');
                } catch (\Throwable $e) {
                }
            }
            return optional($row->created_at)->format('d M Y') ?? '-';
        };
    @endphp

    <div class="space-y-6" x-data="{
        showFilters: true,
        detailOpen: false,
        detail: null,
        openDetail(payload) {
            this.detail = payload;
            this.detailOpen = true;
        },
        closeDetail() {
            this.detailOpen = false;
            setTimeout(() => this.detail = null, 150);
        },
    }" @keydown.escape.window="closeDetail()">



        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Saldo Kas --}}
            <div
                class="rounded-2xl bg-white/5 border border-white/10 p-5 backdrop-blur-sm hover:bg-white/10 transition-all duration-300">
                <div class="flex items-start justify-between mb-3">
                    <div class="p-2.5 bg-purple-500/20 rounded-xl">
                        <svg class="w-6 h-6 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <p class="text-sm text-white/60">Saldo Kas (berdasarkan transaksi confirmed)</p>
                <p class="text-2xl font-bold text-white mt-1">{{ $rupiah($saldoKas ?? 0) }}</p>
            </div>

            {{-- Total Pengeluaran (total record) --}}
            <div
                class="rounded-2xl bg-white/5 border border-white/10 p-5 backdrop-blur-sm hover:bg-white/10 transition-all duration-300">
                <div class="flex items-start justify-between mb-3">
                    <div class="p-2.5 bg-red-500/20 rounded-xl">
                        <svg class="w-6 h-6 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <p class="text-sm text-white/60">Total Transaksi Pengeluaran</p>
                <p class="text-2xl font-bold text-white mt-1">{{ number_format($pengeluaran->total() ?? 0) }}</p>
            </div>

            {{-- Periode --}}
            <div
                class="rounded-2xl bg-white/5 border border-white/10 p-5 backdrop-blur-sm hover:bg-white/10 transition-all duration-300">
                <div class="flex items-start justify-between mb-3">
                    <div class="p-2.5 bg-sky-500/20 rounded-xl">
                        <svg class="w-6 h-6 text-sky-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <button @click="showFilters = !showFilters" class="text-xs text-white/60 hover:text-white">
                        <span x-show="showFilters">Sembunyikan</span>
                        <span x-show="!showFilters" x-cloak>Tampilkan</span>
                    </button>
                </div>
                <p class="text-sm text-white/60">Filter Periode (berdasarkan created_at)</p>
                <p class="text-sm font-semibold text-white mt-2">
                    {{ $filters['from'] ? $filters['from'] : '—' }} s/d {{ $filters['to'] ? $filters['to'] : '—' }}
                </p>
            </div>
        </div>



        {{-- TOOLBAR (Search + Filter + Tambah) --}}
        <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur-md shadow-lg overflow-visible">
            <div class="p-6 space-y-4" x-show="showFilters" x-cloak>

                <form id="pengeluaranFilterForm" method="GET" action="{{ route('pengeluaran-kas.index') }}"
                    class="grid grid-cols-1 md:grid-cols-[1fr_220px_240px] gap-4 items-end">

                    {{-- Search --}}
                    <div class="min-w-0">
                        <div class="relative">
                            <input type="text" name="q" value="{{ $filters['q'] ?? '' }}"
                                placeholder="Cari keterangan / jumlah..."
                                class="w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-purple-500/40" />
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 text-white/40">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Filter Periode (Dropdown) --}}
                    <div class="w-full">
                        <div x-data="{
                            open: false,
                            panelTop: 0,
                            panelLeft: 0,
                            panelW: 460,
                            place() {
                                const r = this.$refs.btn.getBoundingClientRect();
                                const gap = 12;
                        
                                this.panelTop = r.bottom + gap + window.scrollY;
                        
                                const vw = window.innerWidth;
                                if (vw < 768) {
                                    const w = Math.min(420, vw - 24);
                                    this.panelW = w;
                                    this.panelLeft = 12 + window.scrollX;
                                } else {
                                    this.panelW = 460;
                                    this.panelLeft = (r.right - this.panelW) + window.scrollX;
                                }
                        
                                const minLeft = 12 + window.scrollX;
                                const maxLeft = window.scrollX + vw - this.panelW - 12;
                                if (this.panelLeft < minLeft) this.panelLeft = minLeft;
                                if (this.panelLeft > maxLeft) this.panelLeft = maxLeft;
                            }
                        }" class="relative w-full">

                            <button type="button" x-ref="btn" @click="place(); open = !open"
                                class="w-full rounded-xl px-4 py-3 text-sm font-semibold text-white
           bg-[#0b1220]/80 border border-white/10
           shadow-[0_0_0_1px_rgba(168,85,247,0.35),0_10px_25px_-12px_rgba(0,0,0,0.65)]
           hover:bg-[#0b1220] hover:border-purple-400/30
           transition flex items-center justify-between gap-3">

                                <span>Filter Periode</span>
                                <svg class="w-4 h-4 text-white/70 transition-transform"
                                    :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>

                            </button>

                            {{-- PANEL (Teleport biar gak ketutupan table) --}}
                            <template x-teleport="body">
                                <div x-show="open" x-cloak class="fixed inset-0 z-[99999]" style="display:none;"
                                    @keydown.escape.window="open=false">
                                    {{-- klik luar nutup --}}
                                    <div class="absolute inset-0 bg-transparent" @click="open=false"></div>

                                    <div class="absolute"
                                        :style="`top:${panelTop}px; left:${panelLeft}px; width:${panelW}px;`"
                                        @click.stop x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                        x-transition:leave="transition ease-in duration-150"
                                        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                        x-transition:leave-end="opacity-0 translate-y-2 scale-95">

                                        <div
                                            class="rounded-2xl border border-white/15 bg-[#0f172a]/92 backdrop-blur-md
                                                   shadow-[0_20px_60px_-20px_rgba(0,0,0,0.85)] ring-1 ring-white/5 p-5">
                                            <div class="space-y-4">

                                                <div>
                                                    <label class="block text-xs font-medium text-white/60 mb-2">Dari
                                                        Tanggal</label>

                                                    <div class="relative">
                                                        <input x-ref="fromDate" type="date" name="from"
                                                            value="{{ $filters['from'] ?? '' }}"
                                                            form="pengeluaranFilterForm"
                                                            class="date-input w-full pr-12 rounded-xl border border-white/15 bg-[#0b1220]/70
                   px-4 py-3 text-sm text-white
                   focus:outline-none focus:ring-2 focus:ring-purple-500/40 focus:border-purple-500
                   transition [color-scheme:dark]" />

                                                        {{-- Icon kalender putih (bisa diklik) --}}
                                                        <button type="button"
                                                            class="absolute right-3 top-1/2 -translate-y-1/2 rounded-lg p-2 text-white/80 hover:bg-white/10 transition"
                                                            @click="$refs.fromDate.showPicker ? $refs.fromDate.showPicker() : $refs.fromDate.focus()">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>


                                                <div>
                                                    <label class="block text-xs font-medium text-white/60 mb-2">Sampai
                                                        Tanggal</label>

                                                    <div class="relative">
                                                        <input x-ref="toDate" type="date" name="to"
                                                            value="{{ $filters['to'] ?? '' }}"
                                                            form="pengeluaranFilterForm"
                                                            class="date-input w-full pr-12 rounded-xl border border-white/15 bg-[#0b1220]/70
                   px-4 py-3 text-sm text-white
                   focus:outline-none focus:ring-2 focus:ring-purple-500/40 focus:border-purple-500
                   transition [color-scheme:dark]" />

                                                        {{-- Icon kalender putih (bisa diklik) --}}
                                                        <button type="button"
                                                            class="absolute right-3 top-1/2 -translate-y-1/2 rounded-lg p-2 text-white/80 hover:bg-white/10 transition"
                                                            @click="$refs.toDate.showPicker ? $refs.toDate.showPicker() : $refs.toDate.focus()">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>


                                                <div class="pt-2 border-t border-white/10">
                                                    <button type="submit" form="pengeluaranFilterForm"
                                                        @click="open=false"
                                                        class="w-full rounded-2xl bg-gradient-to-r from-blue-600 to-purple-600 px-5 py-3.5
                   text-sm font-semibold text-white shadow-lg hover:opacity-95 transition">
                                                        Terapkan Filter
                                                    </button>
                                                </div>

                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Tambah Pengeluaran --}}
                    <div class="w-full">
                        <a href="{{ route('pengeluaran-kas.create') }}"
                            class="w-full inline-flex items-center justify-center gap-2 rounded-xl
           bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-3 text-sm font-semibold text-white
           shadow-lg hover:opacity-95 transition whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Pengeluaran
                        </a>

                    </div>

                </form>
            </div>
        </div>



        {{-- TABLE (Desktop) --}}
        <div
            class="hidden md:block rounded-2xl border border-white/10 bg-white/5 backdrop-blur-md shadow-lg overflow-hidden">

            <div class="overflow-x-auto no-scrollbar">
                <table class="min-w-full text-left">
                    <thead class="bg-white/5 border-b border-white/10">
                        <tr class="text-xs font-semibold text-white/70">
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Kategori</th>
                            <th class="px-6 py-4">Keterangan</th>
                            <th class="px-6 py-4">Jumlah</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse ($pengeluaran as $row)
                            @php
                                $detailPayload = [
                                    'tanggal' => $displayDate($row),
                                    'input_at' => optional($row->created_at)->format('d M Y H:i'),
                                    'kategori' => $categoryOf($row),
                                    'keterangan' => $cleanDesc($row),
                                    'jumlah' => $rupiah($row->amount),
                                    'oleh' => $row->user->name ?? '—',
                                    'receipt_url' => !empty($row->receipt_path)
                                        ? route('pengeluaran-kas.receipt', $row)
                                        : null,
                                    'edit_url' => \Illuminate\Support\Facades\Route::has('pengeluaran-kas.edit')
                                        ? route('pengeluaran-kas.edit', $row)
                                        : null,
                                    'destroy_url' => \Illuminate\Support\Facades\Route::has('pengeluaran-kas.destroy')
                                        ? route('pengeluaran-kas.destroy', $row)
                                        : null,
                                ];
                            @endphp

                            <tr class="hover:bg-white/5 transition cursor-pointer"
                                @click="openDetail(@js($detailPayload))">
                                <td class="px-6 py-4 text-sm text-white">
                                    <div class="font-semibold">{{ $displayDate($row) }}</div>
                                    <div class="text-xs text-white/50">
                                        Input: {{ optional($row->created_at)->format('d M Y H:i') }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-sm text-white/80">
                                    <span
                                        class="inline-flex items-center rounded-lg bg-white/5 px-2.5 py-1 text-xs font-semibold text-white/80 ring-1 ring-inset ring-white/10">
                                        {{ $categoryOf($row) }}
                                    </span>
                                </td>

                                @php
                                    $detailPayload = [
                                        'tanggal' => $displayDate($row),
                                        'input_at' => optional($row->created_at)->format('d M Y H:i'),
                                        'kategori' => $categoryOf($row),
                                        'keterangan' => $cleanDesc($row),
                                        'jumlah' => $rupiah($row->amount),
                                        'oleh' => $row->user->name ?? '—',
                                        'receipt_url' => !empty($row->receipt_path)
                                            ? route('pengeluaran-kas.receipt', $row)
                                            : null,
                                        'edit_url' => \Illuminate\Support\Facades\Route::has('pengeluaran-kas.edit')
                                            ? route('pengeluaran-kas.edit', $row)
                                            : null,
                                        'destroy_url' => \Illuminate\Support\Facades\Route::has(
                                            'pengeluaran-kas.destroy',
                                        )
                                            ? route('pengeluaran-kas.destroy', $row)
                                            : null,
                                    ];
                                @endphp

                                <td class="px-6 py-4 text-sm text-white/80">
                                    <button type="button" class="w-full text-left cursor-pointer"
                                        @click="openDetail(@js($detailPayload))">
                                        <div
                                            class="max-w-[520px] whitespace-normal break-words line-clamp-2 text-white/90 hover:text-white transition">
                                            {{ $cleanDesc($row) }}
                                        </div>
                                        <div class="mt-1 text-xs text-white/50">
                                            Oleh: {{ $row->user->name ?? '—' }}
                                        </div>
                                    </button>
                                </td>

                                <td class="px-6 py-4 text-sm font-bold text-white">
                                    {{ $rupiah($row->amount) }}
                                </td>

                                <td class="px-6 py-4 text-right" @click.stop>
                                    <div class="flex items-center justify-end">
                                        <div x-data="{
                                            open: false,
                                            top: 0,
                                            left: 0,
                                            panelW: 208,
                                            place() {
                                                const r = this.$refs.btn.getBoundingClientRect();
                                                const gap = 10;
                                        
                                                this.top = r.bottom + gap + window.scrollY;
                                                this.left = (r.right - this.panelW) + window.scrollX;
                                        
                                                const minLeft = 12 + window.scrollX;
                                                if (this.left < minLeft) this.left = minLeft;
                                        
                                                const estH = 180;
                                                const bottomEdge = this.top + estH;
                                                const viewportBottom = window.scrollY + window.innerHeight - 12;
                                                if (bottomEdge > viewportBottom) {
                                                    this.top = r.top - estH - gap + window.scrollY;
                                                }
                                            }
                                        }">
                                            <button type="button" x-ref="btn" @click.stop="place(); open = true"
                                                class="inline-flex items-center gap-2 rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-xs font-semibold text-white hover:bg-white/10 transition">
                                                Aksi
                                                <svg class="w-4 h-4 text-white/70" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </button>

                                            <template x-teleport="body">
                                                <div x-show="open" x-cloak class="fixed inset-0 z-[99999]"
                                                    @keydown.escape.window="open=false" style="display:none;">
                                                    <div class="absolute inset-0 bg-transparent" @click="open=false">
                                                    </div>

                                                    <div class="absolute"
                                                        :style="`top:${top}px; left:${left}px; width:${panelW}px;`"
                                                        @click.stop
                                                        x-transition:enter="transition ease-out duration-200"
                                                        x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                                        x-transition:leave="transition ease-in duration-150"
                                                        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                                        x-transition:leave-end="opacity-0 translate-y-2 scale-95">
                                                        <div
                                                            class="overflow-hidden rounded-2xl border border-white/10 bg-[#0f172a] shadow-[0_0_20px_rgba(0,0,0,0.45)] ring-1 ring-white/5">
                                                            <div class="py-2">
                                                                @if (!empty($row->receipt_path))
                                                                    <a href="{{ route('pengeluaran-kas.receipt', $row) }}"
                                                                        target="_blank" @click="open=false"
                                                                        class="flex items-center gap-2 px-4 py-2.5 text-sm text-white/80 hover:bg-white/10 hover:text-white transition">
                                                                        Lihat Bukti
                                                                    </a>
                                                                @endif

                                                                <a href="{{ route('pengeluaran-kas.edit', $row) }}"
                                                                    @click="open=false"
                                                                    class="flex items-center gap-2 px-4 py-2.5 text-sm text-white/80 hover:bg-white/10 hover:text-white transition">
                                                                    Edit
                                                                </a>

                                                                <div class="my-2 border-t border-white/10"></div>

                                                                <form method="POST"
                                                                    action="{{ route('pengeluaran-kas.destroy', $row) }}"
                                                                    onsubmit="return confirm('Yakin hapus transaksi pengeluaran ini?');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="flex w-full items-center gap-2 px-4 py-2.5 text-sm text-red-300 hover:bg-red-500/10 hover:text-red-200 transition text-left">
                                                                        Hapus
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-sm text-white/60">
                                    Belum ada transaksi pengeluaran.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                {{-- HEADER INFO (gabung di card table) --}}
                <div class="px-6 py-4 border-b border-white/10 bg-white/5">
                    <div class="text-sm text-white/60">
                        Menampilkan
                        <span class="font-medium text-white">
                            {{ $pengeluaran->firstItem() ?? 0 }}–{{ $pengeluaran->lastItem() ?? 0 }}
                        </span>
                        dari
                        <span class="font-medium text-white">{{ $pengeluaran->total() ?? 0 }}</span>
                        transaksi
                    </div>
                </div>
            </div>

            {{-- Pagination --}}
            @if ($pengeluaran->hasPages())
                <div class="border-t border-white/20 bg-white/5 px-6 py-4">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <p class="text-sm text-white/60">
                            Menampilkan
                            <span class="font-medium text-white">
                                {{ $pengeluaran->firstItem() ?? 0 }}–{{ $pengeluaran->lastItem() ?? 0 }}
                            </span>
                            dari
                            <span class="font-medium text-white">{{ $pengeluaran->total() ?? 0 }}</span>
                        </p>

                        <nav class="flex items-center gap-2" aria-label="Pagination">
                            @if ($pengeluaran->onFirstPage())
                                <span
                                    class="rounded-lg border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium text-white/60 opacity-50 cursor-not-allowed">
                                    Sebelumnya
                                </span>
                            @else
                                <a href="{{ $pengeluaran->previousPageUrl() }}"
                                    class="rounded-lg border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium text-white hover:bg-white/10 transition-all">
                                    Sebelumnya
                                </a>
                            @endif

                            @php
                                $current = $pengeluaran->currentPage();
                                $last = $pengeluaran->lastPage();
                                $start = max(1, $current - 2);
                                $end = min($last, $current + 2);
                            @endphp

                            <div class="hidden sm:flex items-center gap-2">
                                @for ($p = $start; $p <= $end; $p++)
                                    @if ($p == $current)
                                        <span
                                            class="rounded-lg border border-blue-500/30 bg-blue-500/15 px-3 py-2 text-sm font-semibold text-white shadow-sm">
                                            {{ $p }}
                                        </span>
                                    @else
                                        <a href="{{ $pengeluaran->url($p) }}"
                                            class="rounded-lg border border-white/10 bg-white/5 px-3 py-2 text-sm font-medium text-white hover:bg-white/10 transition-all">
                                            {{ $p }}
                                        </a>
                                    @endif
                                @endfor
                            </div>

                            @if ($pengeluaran->hasMorePages())
                                <a href="{{ $pengeluaran->nextPageUrl() }}"
                                    class="rounded-lg border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium text-white hover:bg-white/10 transition-all">
                                    Selanjutnya
                                </a>
                            @else
                                <span
                                    class="rounded-lg border border-white/10 bg-white/5 px-4 py-2 text-sm font-medium text-white/60 opacity-50 cursor-not-allowed">
                                    Selanjutnya
                                </span>
                            @endif
                        </nav>
                    </div>
                </div>
            @endif
        </div>



        {{-- MOBILE CARDS --}}
        <div class="md:hidden space-y-4">
            @forelse ($pengeluaran as $row)
                <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur-md shadow-lg p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-bold text-white">{{ $rupiah($row->amount) }}</p>
                            <p class="text-xs text-white/60 mt-0.5">{{ $displayDate($row) }}</p>
                        </div>
                        <span
                            class="inline-flex items-center rounded-lg bg-emerald-500/15 px-2.5 py-1 text-xs font-semibold text-emerald-300 ring-1 ring-inset ring-emerald-400/20">
                            CONFIRMED
                        </span>
                    </div>

                    <div class="mt-4 space-y-2 text-sm text-white/80">
                        <div class="flex items-center justify-between">
                            <span class="text-white/60">Kategori</span>
                            <span class="font-semibold">{{ $categoryOf($row) }}</span>
                        </div>
                        <div class="text-white/60 text-xs">Keterangan</div>
                        <div class="text-sm text-white">{{ $cleanDesc($row) }}</div>

                        <div class="flex items-center justify-between pt-2 text-xs text-white/50">
                            <span>Oleh: {{ $row->user->name ?? '—' }}</span>
                            <span>{{ optional($row->created_at)->format('d M Y H:i') }}</span>
                        </div>
                    </div>

                    <div class="mt-4 flex flex-wrap items-center justify-end gap-2">
                        @if (!empty($row->receipt_path))
                            <a href="{{ route('pengeluaran-kas.receipt', $row) }}" target="_blank"
                                class="shrink-0 rounded-xl border border-white/10 bg-white/5 px-3 py-2 text-xs font-semibold text-white hover:bg-white/10 transition whitespace-nowrap">
                                Lihat Bukti
                            </a>
                        @endif

                        <a href="{{ route('pengeluaran-kas.edit', $row) }}"
                            class="shrink-0 rounded-xl border border-blue-500/20 bg-blue-500/10 px-3 py-2 text-xs font-semibold text-blue-200 hover:bg-blue-500/15 transition whitespace-nowrap">
                            Edit
                        </a>

                        <form method="POST" action="{{ route('pengeluaran-kas.destroy', $row) }}"
                            onsubmit="return confirm('Yakin hapus transaksi pengeluaran ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="shrink-0 rounded-xl border border-red-500/20 bg-red-500/10 px-3 py-2 text-xs font-semibold text-red-200 hover:bg-red-500/15 transition whitespace-nowrap">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div
                    class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur-md shadow-lg p-6 text-center text-sm text-white/60">
                    Belum ada transaksi pengeluaran.
                </div>
            @endforelse
        </div>



        {{-- MODAL DETAIL --}}
        <div x-show="detailOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4"
            aria-modal="true" role="dialog">

            {{-- Overlay --}}
            <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-sm" @click="closeDetail()"
                x-transition:enter="transition-opacity ease-out duration-200" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-in duration-150"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

            {{-- Panel --}}
            <div class="relative w-full max-w-2xl overflow-hidden rounded-2xl border border-white/15 bg-[#0f172a]/95 shadow-2xl"
                @click.outside="closeDetail()" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-2 scale-95">

                <div class="p-6 border-b border-white/10 bg-white/5">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-bold text-white">Detail Pengeluaran</h3>
                            <p class="text-sm text-white/60 mt-1"
                                x-text="detail ? (detail.tanggal + ' • Input: ' + detail.input_at) : ''"></p>
                        </div>
                        <button type="button" class="rounded-lg p-2 text-white/70 hover:bg-white/10 hover:text-white"
                            @click="closeDetail()">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="p-6 space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                            <p class="text-xs text-white/50">Kategori</p>
                            <p class="mt-1 text-sm font-semibold text-white" x-text="detail?.kategori ?? '—'"></p>
                        </div>
                        <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                            <p class="text-xs text-white/50">Jumlah</p>
                            <p class="mt-1 text-sm font-bold text-white" x-text="detail?.jumlah ?? '—'"></p>
                        </div>
                    </div>

                    <div class="rounded-xl border border-white/10 bg-white/5 p-4">
                        <p class="text-xs text-white/50">Keterangan</p>
                        <p class="mt-2 text-sm text-white/90 whitespace-pre-wrap break-words"
                            x-text="detail?.keterangan ?? '—'"></p>
                        <p class="mt-3 text-xs text-white/50" x-text="detail ? ('Oleh: ' + detail.oleh) : ''"></p>
                    </div>
                </div>

                <div
                    class="px-6 py-4 border-t border-white/10 bg-white/5 flex flex-col sm:flex-row gap-2 sm:justify-end">
                    <button type="button"
                        class="rounded-xl border border-white/10 bg-white/5 px-4 py-2.5 text-sm font-semibold text-white/80 hover:bg-white/10 hover:text-white transition"
                        @click="closeDetail()">
                        Tutup
                    </button>

                    <template x-if="detail?.receipt_url">
                        <a :href="detail.receipt_url" target="_blank"
                            class="inline-flex items-center justify-center rounded-xl border border-white/10 bg-white/10 px-4 py-2.5 text-sm font-semibold text-white hover:bg-white/15 transition">
                            Lihat Bukti
                        </a>
                    </template>

                    <template x-if="detail?.edit_url">
                        <a :href="detail.edit_url"
                            class="inline-flex items-center justify-center rounded-xl border border-blue-500/25 bg-blue-500/10 px-4 py-2.5 text-sm font-semibold text-blue-100 hover:bg-blue-500/15 transition">
                            Edit
                        </a>
                    </template>

                    <template x-if="detail?.destroy_url">
                        <form method="POST" :action="detail.destroy_url"
                            onsubmit="return confirm('Yakin hapus transaksi pengeluaran ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="rounded-xl border border-red-500/25 bg-red-500/10 px-4 py-2.5 text-sm font-semibold text-red-100 hover:bg-red-500/15 transition">
                                Hapus
                            </button>
                        </form>
                    </template>
                </div>
            </div>
        </div>

    </div>

</x-layouts.app-layout>
