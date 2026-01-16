{{-- resources/views/member/admin-payment-log.blade.php --}}

<x-layouts.app-layout-anggota>
    @section('page-title', 'Log Aktivitas Pembayaran Admin')
    @section('page-subtitle', 'Log pembayaran admin untuk pembayaran Anda + transparansi pengeluaran organisasi')

    @push('styles')
        <style>
            /* Pagination styling */
            nav[aria-label="Pagination Navigation"] p {
                color: rgba(255, 255, 255, .85) !important;
            }

            nav[aria-label="Pagination Navigation"] a,
            nav[aria-label="Pagination Navigation"] span[aria-current="page"]>span,
            nav[aria-label="Pagination Navigation"] span[aria-disabled="true"]>span {
                background: rgba(255, 255, 255, .06) !important;
                border-color: rgba(255, 255, 255, .18) !important;
                color: rgba(255, 255, 255, .85) !important;
            }

            nav[aria-label="Pagination Navigation"] span[aria-current="page"]>span {
                background: rgba(255, 255, 255, .18) !important;
                border-color: rgba(255, 255, 255, .28) !important;
                font-weight: 700;
            }

            nav[aria-label="Pagination Navigation"] span[aria-disabled="true"]>span {
                color: rgba(255, 255, 255, .35) !important;
                background: rgba(255, 255, 255, .03) !important;
            }

            nav[aria-label="Pagination Navigation"] svg {
                color: rgba(255, 255, 255, .75) !important;
            }
        </style>
    @endpush

    @php
        $tab = $tab ?? request('tab', 'pembayaran');

        $logs = $logs ?? null;
        $pengeluaran = $pengeluaran ?? null;

        $rupiah = function ($n) {
            return 'Rp ' . number_format((float) $n, 0, ',', '.');
        };

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

        $cleanDesc = function ($row) {
            $desc = (string) ($row->description ?? '');
            $desc = preg_replace('/^\[Kategori:\s*(.*?)\]\s*/', '', $desc);
            return trim($desc) ?: '-';
        };

        $displayDate = function ($row) {
            if (!empty($row->payment_date)) {
                try {
                    return \Carbon\Carbon::parse($row->payment_date)->format('d M Y');
                } catch (\Throwable $e) {
                }
            }
            return optional($row->created_at)->format('d M Y') ?? '-';
        };

        $baseParams = request()->except(['tab', 'page']);
        $makeUrl = function (array $params) {
            $query = http_build_query($params);
            return $query ? url()->current() . '?' . $query : url()->current();
        };

        $urlPembayaran = $makeUrl($baseParams + ['tab' => 'pembayaran']);
        $urlPengeluaran = $makeUrl($baseParams + ['tab' => 'pengeluaran']);

        $activeTotal = $tab === 'pengeluaran' ? $pengeluaran?->total() ?? 0 : $logs?->total() ?? 0;

        $q = request('q');
        $from = request('from');
        $to = request('to');

        $resetTanggalUrl = route(
            'member.admin_payment_log',
            array_filter(
                [
                    'tab' => $tab,
                    'q' => $q,
                ],
                fn($v) => $v !== null && $v !== '',
            ),
        );

        $resetSemuaUrl = route('member.admin_payment_log', ['tab' => $tab]);
    @endphp

    <div class="space-y-6" x-data="{
        receiptOpen: false,
        receiptUrl: null,
        receiptIsImage: false,
        zoom: 1,
        panX: 0,
        panY: 0,
    
        openReceipt(url, isImage) {
            this.receiptUrl = url;
            this.receiptIsImage = !!isImage;
            this.receiptOpen = true;
            this.zoom = 1;
            this.panX = 0;
            this.panY = 0;
        },
        closeReceipt() {
            this.receiptOpen = false;
            setTimeout(() => {
                this.receiptUrl = null;
                this.receiptIsImage = false;
            }, 150);
        },
        zoomIn() { this.zoom = Math.min(this.zoom + 0.2, 4); },
        zoomOut() { this.zoom = Math.max(this.zoom - 0.2, 0.5); },
        resetZoom() {
            this.zoom = 1;
            this.panX = 0;
            this.panY = 0;
        },
    }" @keydown.escape.window="if(receiptOpen) closeReceipt()">

        {{-- SECTION 1: SUMMARY CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Total Pembayaran --}}
            <div
                class="rounded-2xl bg-white/5 border border-white/10 p-5 backdrop-blur-sm hover:bg-white/10 transition-all duration-300 group">
                <div class="flex items-start justify-between mb-3">
                    <div
                        class="p-2.5 bg-emerald-500/20 rounded-xl group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-white mb-1">{{ $countPembayaran ?? 0 }}</h3>
                <p class="text-sm text-white/50">Log Pembayaran</p>
            </div>

            {{-- Total Pengeluaran --}}
            <div
                class="rounded-2xl bg-white/5 border border-white/10 p-5 backdrop-blur-sm hover:bg-white/10 transition-all duration-300 group">
                <div class="flex items-start justify-between mb-3">
                    <div
                        class="p-2.5 bg-purple-500/20 rounded-xl group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-white mb-1">{{ $countPengeluaran ?? 0 }}</h3>
                <p class="text-sm text-white/50">Pengeluaran Organisasi</p>
            </div>

            {{-- Tab Aktif --}}
            <div
                class="rounded-2xl bg-white/5 border border-white/10 p-5 backdrop-blur-sm hover:bg-white/10 transition-all duration-300 group">
                <div class="flex items-start justify-between mb-3">
                    <div
                        class="p-2.5 bg-blue-500/20 rounded-xl group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-white mb-1">{{ $activeTotal }}</h3>
                <p class="text-sm text-white/50">Data Ditampilkan</p>
            </div>
        </div>

        {{-- SECTION 2: TABS --}}
        {{-- Cleaned tab section for admin-payment-log.blade.php --}}
        <div class="rounded-2xl bg-white/5 border border-white/10 p-1">
            {{-- Tab list: accessible, responsive, equal-width buttons --}}
            <nav role="tablist" aria-label="Filter log by type" class="flex gap-2">
                <a href="{{ $urlPembayaran }}" role="tab"
                    aria-selected="{{ $tab === 'pembayaran' ? 'true' : 'false' }}"
                    @if ($tab === 'pembayaran') aria-current="page" @endif
                    class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all text-center
        {{ $tab === 'pembayaran'
            ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30'
            : 'text-white/70 hover:text-white hover:bg-white/5' }}">
                    <span class="sr-only">Pembayaran</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>

                    <span class="truncate">Pembayaran</span>

                    <span
                        class="inline-flex items-center justify-center min-w-[24px] h-5 px-1.5 rounded-md text-xs font-bold
             {{ $tab === 'pembayaran' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-white/10 text-white/70' }}">
                        {{ $countPembayaran ?? 0 }}
                    </span>
                </a>

                <a href="{{ $urlPengeluaran }}" role="tab"
                    aria-selected="{{ $tab === 'pengeluaran' ? 'true' : 'false' }}"
                    @if ($tab === 'pengeluaran') aria-current="page" @endif
                    class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold transition-all text-center
        {{ $tab === 'pengeluaran'
            ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30'
            : 'text-white/70 hover:text-white hover:bg-white/5' }}">
                    <span class="sr-only">Pengeluaran</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                    </svg>

                    <span class="truncate">Pengeluaran</span>

                    <span
                        class="inline-flex items-center justify-center min-w-[24px] h-5 px-1.5 rounded-md text-xs font-bold
             {{ $tab === 'pengeluaran' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-white/10 text-white/70' }}">
                        {{ $countPengeluaran ?? 0 }}
                    </span>
                </a>
            </nav>

        </div>

        {{-- Notes:
 - Uses role="tablist" and role="tab" with aria-selected for accessibility.
 - Ensures equal-width tabs with flex-1 and consistent padding/height.
 - Keeps active styles and badges, preserves existing Blade variables.
 - Replace the old tab block with this snippet.
--}}


        {{-- SECTION 3: SEARCH & FILTERS --}}
        <div class="rounded-2xl bg-white/5 border border-white/10 p-4 backdrop-blur-sm relative z-20">
            <div class="flex flex-col lg:flex-row lg:items-center gap-4 w-full">

                {{-- Search Input --}}
                <form method="GET" action="{{ url()->current() }}" class="relative flex-1 w-full">
                    <input type="hidden" name="tab" value="{{ $tab }}">
                    <input type="hidden" name="from" value="{{ $from }}">
                    <input type="hidden" name="to" value="{{ $to }}">

                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-white/40" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>

                    <input type="text" name="q" value="{{ $q }}"
                        placeholder="{{ $tab === 'pengeluaran'
                            ? 'Cari pengeluaran (kategori/keterangan/jumlah/bendahara)...'
                            : 'Cari log pembayaran (admin/tagihan/status/nominal)...' }}"
                        class="w-full pl-10 pr-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/50 transition-all text-sm">
                </form>

                {{-- Filter Periode Dropdown --}}
                {{-- Filter Periode Dropdown (rapi + tidak lari) --}}
                <div class="flex items-center gap-3 shrink-0">
                    <div x-data="{
                        open: false,
                        top: 0,
                        left: 0,
                        w: 360,
                    
                        place() {
                            const r = this.$refs.btn.getBoundingClientRect();
                            const gap = 10;
                    
                            // ukuran panel (estimasi) untuk flip kalau kepentok bawah
                            const estH = 280;
                    
                            // default: taruh di bawah tombol
                            this.top = r.bottom + gap + window.scrollY;
                    
                            // width responsive
                            const vw = window.innerWidth;
                            if (vw < 640) {
                                this.w = Math.min(420, vw - 24);
                                this.left = 12 + window.scrollX;
                            } else {
                                this.w = 360;
                                this.left = (r.right - this.w) + window.scrollX;
                            }
                    
                            // clamp kiri-kanan biar gak keluar layar
                            const minLeft = 12 + window.scrollX;
                            const maxLeft = window.scrollX + vw - this.w - 12;
                            if (this.left < minLeft) this.left = minLeft;
                            if (this.left > maxLeft) this.left = maxLeft;
                    
                            // kalau panel kepentok bawah viewport, taruh di atas tombol
                            const viewportBottom = window.scrollY + window.innerHeight;
                            const bottomEdge = this.top + estH;
                            if (bottomEdge > viewportBottom) {
                                this.top = r.top - estH - gap + window.scrollY;
                            }
                        }
                    }" class="relative">
                        <button type="button" x-ref="btn" @click.stop="place(); open = !open"
                            class="flex w-48 items-center justify-between rounded-xl border border-purple-400/40
                   bg-[#0b1220]/80 px-4 py-2.5 text-left text-sm font-semibold text-white
                   shadow-[0_0_0_1px_rgba(168,85,247,0.35),0_10px_25px_-12px_rgba(0,0,0,0.65)]
                   hover:bg-[#0b1220] hover:border-purple-400/60
                   transition focus:outline-none focus:ring-2 focus:ring-purple-500/50">
                            <span>Filter Periode</span>
                            <svg class="h-4 w-4 text-white/70 transition-transform duration-200"
                                :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        {{-- PANEL: Teleport ke body biar posisinya selalu benar --}}
                        <template x-teleport="body">
                            <div x-show="open" x-cloak class="fixed inset-0 z-[99999]" style="display:none;"
                                @keydown.escape.window="open=false">
                                {{-- klik area luar menutup --}}
                                <div class="absolute inset-0 bg-transparent" @click="open=false"></div>

                                <div class="absolute" :style="`top:${top}px; left:${left}px; width:${w}px;`"
                                    @click.stop x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                    x-transition:leave-end="opacity-0 translate-y-2 scale-95">
                                    <div
                                        class="rounded-xl border border-white/20 bg-[#0f172a]
                                shadow-[0_10px_40px_-10px_rgba(0,0,0,0.5)] ring-1 ring-white/5 overflow-hidden">
                                        <form method="GET" action="{{ url()->current() }}" class="p-4 space-y-4">
                                            <input type="hidden" name="tab" value="{{ $tab }}">
                                            <input type="hidden" name="q" value="{{ $q }}">

                                            <div>
                                                <label class="block text-xs font-medium text-white/50 mb-1.5">Dari
                                                    Tanggal</label>
                                                <input type="date" name="from" value="{{ $from }}"
                                                    class="w-full rounded-lg border border-white/20 bg-white/5 px-3 py-2 text-white text-sm
                                              focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-all [color-scheme:dark]">
                                            </div>

                                            <div>
                                                <label class="block text-xs font-medium text-white/50 mb-1.5">Sampai
                                                    Tanggal</label>
                                                <input type="date" name="to" value="{{ $to }}"
                                                    class="w-full rounded-lg border border-white/20 bg-white/5 px-3 py-2 text-white text-sm
                                              focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-all [color-scheme:dark]">
                                            </div>

                                            <div class="pt-2 border-t border-white/10 space-y-2">
                                                <button type="submit"
                                                    class="w-full rounded-lg bg-gradient-to-r from-blue-500 to-purple-600 py-2.5 text-sm
                                               font-semibold text-white hover:shadow-lg hover:shadow-blue-500/25 transition-all">
                                                    Terapkan Filter
                                                </button>

                                                <a href="{{ $resetTanggalUrl }}"
                                                    class="block w-full text-center rounded-lg border border-white/10 bg-white/5 py-2.5 text-sm
                                          font-medium text-white/80 hover:bg-white/10 transition-all">
                                                    Reset Tanggal
                                                </a>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

            </div>
        </div>

        {{-- SECTION 4: TABLE --}}
        <div class="rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm overflow-hidden">
            {{-- Table Header --}}
            <div class="px-6 py-4 border-b border-white/10 bg-white/5">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        @if ($tab === 'pengeluaran')
                            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                            </svg>
                            Pengeluaran Organisasi
                        @else
                            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Log Pembayaran Admin
                        @endif
                    </h3>

                    <span
                        class="px-3 py-1 rounded-lg text-xs font-bold 
                        {{ $tab === 'pengeluaran'
                            ? 'bg-purple-500/20 text-purple-400 border border-purple-500/30'
                            : 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' }}">
                        {{ $activeTotal }} Data
                    </span>
                </div>
            </div>

            @if ($tab === 'pengeluaran')
                {{-- TAB PENGELUARAN --}}
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-white/5 border-b border-white/10">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-white/70 uppercase tracking-wider">
                                    Tanggal</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-white/70 uppercase tracking-wider">
                                    Kategori</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-white/70 uppercase tracking-wider">
                                    Keterangan</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-white/70 uppercase tracking-wider">
                                    Nominal</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-white/70 uppercase tracking-wider">
                                    Bendahara</th>
                                <th
                                    class="px-6 py-4 text-right text-xs font-semibold text-white/70 uppercase tracking-wider">
                                    Bukti</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @forelse(($pengeluaran ?? collect()) as $row)
                                @php
                                    $path = $row->receipt_path;
                                    $ext = $path ? strtolower(pathinfo($path, PATHINFO_EXTENSION)) : '';
                                    $isImg = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true);

                                    $adminName = $row->user->name ?? 'Admin';
                                    $adminParts = preg_split('/\s+/', trim($adminName));
                                    $adminInitials = strtoupper(
                                        substr($adminParts[0] ?? 'A', 0, 1) .
                                            (isset($adminParts[1]) ? substr($adminParts[1], 0, 1) : ''),
                                    );
                                @endphp

                                <tr class="hover:bg-white/5 transition-colors">
                                    <td class="px-6 py-4">
                                        <p class="text-sm text-white">{{ $displayDate($row) }}</p>
                                    </td>

                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-purple-500/20 text-purple-400 border border-purple-500/30">
                                            {{ $categoryOf($row) }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4">
                                        <p class="text-sm text-white/80">{{ $cleanDesc($row) }}</p>
                                    </td>

                                    <td class="px-6 py-4">
                                        <p class="text-sm font-bold text-red-400">
                                            - {{ $rupiah($row->amount) }}
                                        </p>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="h-8 w-8 rounded-full overflow-hidden border border-white/10 shrink-0">
                                                <div
                                                    class="h-full w-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-xs">
                                                    {{ $adminInitials ?: 'AD' }}
                                                </div>
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-white">
                                                    {{ $row->user->name ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-right">
                                        @if ($row->receipt_path)
                                            <button type="button"
                                                @click="openReceipt('{{ route('laporan.receipt.show', $row) }}', {{ $isImg ? 'true' : 'false' }})"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-blue-500/50 bg-blue-500/10 text-blue-300 hover:bg-blue-500/20 transition-all text-sm font-medium">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                Lihat
                                            </button>
                                        @else
                                            <span class="text-white/30 text-sm">— Tidak ada</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="p-3 bg-white/5 rounded-full">
                                                <svg class="w-8 h-8 text-white/30" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                </svg>
                                            </div>
                                            <p class="text-sm text-white/50">Tidak ada data pengeluaran pada filter
                                                ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="border-t border-white/10 bg-white/5 px-6 py-4">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <p class="text-sm text-white/60">
                            Menampilkan
                            <span class="font-medium text-white">{{ $pengeluaran?->firstItem() ?? 0 }}</span>
                            –
                            <span class="font-medium text-white">{{ $pengeluaran?->lastItem() ?? 0 }}</span>
                            dari <span class="font-medium text-white">{{ $pengeluaran?->total() ?? 0 }}</span> data
                        </p>
                        <div class="flex gap-2">
                            {{ $pengeluaran?->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            @else
                {{-- TAB PEMBAYARAN --}}
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-white/5 border-b border-white/10">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-white/70 uppercase tracking-wider">
                                    Waktu</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-white/70 uppercase tracking-wider">
                                    Admin</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-white/70 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-white/70 uppercase tracking-wider">
                                    Tagihan</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-white/70 uppercase tracking-wider">
                                    Nominal</th>
                                <th
                                    class="px-6 py-4 text-right text-xs font-semibold text-white/70 uppercase tracking-wider">
                                    Bukti</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/10">
                            @forelse(($logs ?? collect()) as $log)
                                @php
                                    $tx = $log->pembayaranKas;

                                    $new = is_array($log->new_values)
                                        ? $log->new_values
                                        : (json_decode($log->new_values ?? '[]', true) ?:
                                        []);
                                    $statusRaw = $new['status'] ?? ($tx->status ?? null);

                                    if ($statusRaw === \App\Models\PembayaranKas::STATUS_PENDING) {
                                        continue;
                                    }

                                    $statusLabel = strtoupper((string) $statusRaw);
                                    $isConfirmed = $statusRaw === \App\Models\PembayaranKas::STATUS_CONFIRMED;

                                    $billName = $tx?->bill?->name ?? 'Tagihan Kas';

                                    $path = $tx?->receipt_path;
                                    $ext = $path ? strtolower(pathinfo($path, PATHINFO_EXTENSION)) : '';
                                    $isImg = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true);

                                    $adminName = $log->actor->name ?? 'Admin';
                                    $adminParts = preg_split('/\s+/', trim($adminName));
                                    $adminInitials = strtoupper(
                                        substr($adminParts[0] ?? 'A', 0, 1) .
                                            (isset($adminParts[1]) ? substr($adminParts[1], 0, 1) : ''),
                                    );
                                @endphp

                                <tr class="hover:bg-white/5 transition-colors">
                                    <td class="px-6 py-4">
                                        <p class="text-sm text-white">
                                            {{ optional($log->created_at)->format('d M Y') }}</p>
                                        <p class="text-xs text-white/50">
                                            {{ optional($log->created_at)->format('H:i') }} WIB</p>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="h-8 w-8 rounded-full overflow-hidden border border-white/10 shrink-0">
                                                <div
                                                    class="h-full w-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-xs">
                                                    {{ $adminInitials ?: 'AD' }}
                                                </div>
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-white">
                                                    {{ $log->actor->name ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        @if ($isConfirmed)
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-green-500/20 text-green-400 border border-green-500/30">
                                                <span class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1.5"></span>
                                                {{ $statusLabel }}
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-red-500/20 text-red-400 border border-red-500/30">
                                                <span class="w-1.5 h-1.5 bg-red-400 rounded-full mr-1.5"></span>
                                                {{ $statusLabel }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4">
                                        <p class="text-sm font-semibold text-white">{{ $billName }}</p>
                                        @php $desc = trim((string) ($tx?->description ?? '')); @endphp
                                        @if ($desc !== '' && $desc !== '-')
                                            <p class="text-xs text-white/50">{{ $desc }}</p>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4">
                                        <p
                                            class="text-sm font-bold {{ $isConfirmed ? 'text-emerald-400' : 'text-white' }}">
                                            {{ $rupiah($tx?->amount ?? 0) }}
                                        </p>
                                    </td>

                                    <td class="px-6 py-4 text-right">
                                        @if ($tx?->receipt_path)
                                            <button type="button"
                                                @click="openReceipt('{{ route('laporan.receipt.show', $tx) }}', {{ $isImg ? 'true' : 'false' }})"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-blue-500/50 bg-blue-500/10 text-blue-300 hover:bg-blue-500/20 transition-all text-sm font-medium">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                Lihat
                                            </button>
                                        @else
                                            <span class="text-white/30 text-sm">— Tidak ada</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="p-3 bg-white/5 rounded-full">
                                                <svg class="w-8 h-8 text-white/30" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            <p class="text-sm text-white/50">Tidak ada log pembayaran pada filter ini.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="border-t border-white/10 bg-white/5 px-6 py-4">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <p class="text-sm text-white/60">
                            Menampilkan
                            <span class="font-medium text-white">{{ $logs?->firstItem() ?? 0 }}</span>
                            –
                            <span class="font-medium text-white">{{ $logs?->lastItem() ?? 0 }}</span>
                            dari <span class="font-medium text-white">{{ $logs?->total() ?? 0 }}</span> data
                        </p>
                        <div class="flex gap-2">
                            {{ $logs?->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- MODAL PREVIEW BUKTI --}}
        <div x-cloak x-show="receiptOpen" x-transition.opacity
            class="fixed inset-0 z-[9999] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/60" @click="closeReceipt()"></div>

            <div
                class="relative w-full max-w-6xl mx-auto bg-[#0f172a] rounded-2xl border border-white/10 overflow-hidden shadow-2xl">
                {{-- Modal Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-white/10 bg-white/5">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Bukti Transaksi
                    </h3>

                    <div class="flex items-center gap-2">
                        {{-- Zoom Controls (hanya untuk gambar) --}}
                        <template x-if="receiptIsImage">
                            <div class="flex items-center gap-1 mr-2">
                                <button type="button" @click="zoomOut()"
                                    class="p-2 rounded-lg bg-white/10 border border-white/10 text-white/80 hover:bg-white/20 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 12H4" />
                                    </svg>
                                </button>
                                <button type="button" @click="resetZoom()"
                                    class="px-3 py-2 rounded-lg bg-white/10 border border-white/10 text-white/80 hover:bg-white/20 transition-all text-xs font-medium">
                                    Reset
                                </button>
                                <button type="button" @click="zoomIn()"
                                    class="p-2 rounded-lg bg-white/10 border border-white/10 text-white/80 hover:bg-white/20 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                </button>
                            </div>
                        </template>

                        {{-- Open in New Tab --}}
                        <a :href="receiptUrl" target="_blank" x-show="receiptUrl"
                            class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg border border-blue-500/50 bg-blue-500/10 text-blue-300 hover:bg-blue-500/20 transition-all text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            Tab Baru
                        </a>

                        {{-- Close Button --}}
                        <button type="button" @click="closeReceipt()"
                            class="p-2 rounded-lg bg-red-500/10 border border-red-500/30 text-red-400 hover:bg-red-500/20 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Modal Body --}}
                <div class="p-4 bg-black/20" style="max-height: calc(90vh - 80px); overflow: auto;">
                    <template x-if="receiptUrl">
                        <div>
                            {{-- Image Preview --}}
                            <template x-if="receiptIsImage">
                                <div
                                    class="w-full h-[70vh] overflow-auto rounded-xl border border-white/10 bg-black/30">
                                    <div class="min-h-full min-w-full flex items-center justify-center p-4">
                                        <img :src="receiptUrl" alt="Bukti Transaksi"
                                            class="max-w-none select-none rounded-lg"
                                            :style="`transform: translate(${panX}px, ${panY}px) scale(${zoom}); transform-origin: center;`"
                                            draggable="false">
                                    </div>
                                </div>
                            </template>

                            {{-- PDF/Document Preview --}}
                            <template x-if="!receiptIsImage">
                                <iframe :src="receiptUrl"
                                    class="w-full h-[70vh] rounded-xl border border-white/10 bg-black/30"
                                    style="min-height: 400px;" frameborder="0"></iframe>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>

    </div>
</x-layouts.app-layout-anggota>
