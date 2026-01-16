<x-layouts.app-layout>

    @section('page-title', 'Laporan & Rekapitulasi')
    @section('page-subtitle', 'Analisis arus kas dan cetak laporan keuangan')

    <div class="space-y-6">

        {{-- SECTION 1: SUMMARY CARDS --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            {{-- Total Pemasukan --}}
            <div
                class="rounded-2xl bg-white/5 border border-white/10 p-5 backdrop-blur-sm hover:bg-white/10 transition-all duration-300 group">
                <div class="flex items-start justify-between mb-3">
                    <div
                        class="p-2.5 bg-emerald-500/20 rounded-xl group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-white mb-1">
                    Rp {{ number_format($totalPemasukan ?? 0, 0, ',', '.') }}
                </h3>
                <p class="text-sm text-white/50">Total Pemasukan</p>
            </div>

            {{-- Total Pengeluaran --}}
            <div
                class="rounded-2xl bg-white/5 border border-white/10 p-5 backdrop-blur-sm hover:bg-white/10 transition-all duration-300 group">
                <div class="flex items-start justify-between mb-3">
                    <div class="p-2.5 bg-red-500/20 rounded-xl group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-white mb-1">
                    Rp {{ number_format($totalPengeluaran ?? 0, 0, ',', '.') }}
                </h3>
                <p class="text-sm text-white/50">Total Pengeluaran</p>
            </div>

            {{-- Saldo Kas --}}
            <div
                class="rounded-2xl bg-white/5 border border-white/10 p-5 backdrop-blur-sm hover:bg-white/10 transition-all duration-300 group">
                <div class="flex items-start justify-between mb-3">
                    <div
                        class="p-2.5 bg-blue-500/20 rounded-xl group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-white mb-1">
                    Rp {{ number_format($saldoKas ?? 0, 0, ',', '.') }}
                </h3>
                <p class="text-sm text-white/50">Saldo Kas Saat Ini</p>
            </div>

            {{-- Total Tunggakan --}}
            <div
                class="rounded-2xl bg-white/5 border border-white/10 p-5 backdrop-blur-sm hover:bg-white/10 transition-all duration-300 group">
                <div class="flex items-start justify-between mb-3">
                    <div
                        class="p-2.5 bg-orange-500/20 rounded-xl group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-white mb-1">
                    Rp {{ number_format($totalTunggakan ?? 0, 0, ',', '.') }}
                </h3>
                <p class="text-sm text-white/50">Total Tunggakan</p>
            </div>
        </div>

        {{-- SECTION 2: HEADER ACTIONS & FILTERS --}}
        <div class="rounded-2xl bg-white/5 border border-white/10 p-4 backdrop-blur-sm relative z-20">
            <div class="flex flex-col lg:flex-row lg:items-center gap-4 w-full">

                {{-- SEARCH FORM (GET) --}}
                <form method="GET" action="{{ url()->current() }}" class="relative flex-1 w-full">
                    <input type="hidden" name="from" value="{{ optional($from)->format('Y-m-d') }}">
                    <input type="hidden" name="to" value="{{ optional($to)->format('Y-m-d') }}">

                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-white/40" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>

                    <input name="q" type="text" placeholder="Cari transaksi, anggota, atau nominal..."
                        value="{{ $q ?? '' }}"
                        class="w-full pl-10 pr-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/50 transition-all text-sm">
                </form>

                <div class="flex items-center gap-3 shrink-0">
                    <div class="relative" x-data="{ open: false, label: 'Filter Periode' }" @click.outside="open = false">
                        <button @click="open = !open"
                            class="flex w-48 items-center justify-between rounded-xl border border-white/10 bg-[#0f172a] px-4 py-2.5 text-left text-sm font-medium text-white shadow-lg transition-all hover:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-purple-500/50">
                            <span x-text="label"></span>
                            <svg class="h-4 w-4 text-white/50 transition-transform duration-200"
                                :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" @click.stop x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                            class="absolute right-0 top-full mt-2 w-72 origin-top-right overflow-hidden rounded-xl border border-white/20 bg-[#0f172a] shadow-[0_10px_40px_-10px_rgba(0,0,0,0.5)] z-50 ring-1 ring-white/5"
                            style="display: none;">
                            <form method="GET" action="{{ url()->current() }}" class="p-4 space-y-4">
                                <input type="hidden" name="q" value="{{ $q ?? '' }}">
                                <div>
                                    <label class="block text-xs font-medium text-white/50 mb-1.5">Dari Tanggal</label>
                                    <input name="from" type="date" onclick="event.stopPropagation()"
                                        value="{{ optional($from)->format('Y-m-d') }}"
                                        class="w-full rounded-lg border border-white/20 bg-white/5 px-3 py-2 text-white text-sm focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-all [color-scheme:dark]">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-white/50 mb-1.5">Sampai
                                        Tanggal</label>
                                    <input name="to" type="date" onclick="event.stopPropagation()"
                                        value="{{ optional($to)->format('Y-m-d') }}"
                                        class="w-full rounded-lg border border-white/20 bg-white/5 px-3 py-2 text-white text-sm focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-all [color-scheme:dark]">
                                </div>
                                <div class="pt-2 border-t border-white/10">
                                    <button type="submit"
                                        class="w-full rounded-lg bg-blue-600 py-2 text-sm font-semibold text-white hover:bg-blue-500 transition-colors">
                                        Terapkan Filter
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Export Button --}}
                    @if (Route::has('laporan.rekapitulasi.pdf'))
                        <a href="{{ route('laporan.rekapitulasi.pdf', request()->query()) }}"
                            class="flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-500 to-purple-600 px-6 py-2.5 text-sm font-medium text-white hover:shadow-lg hover:shadow-blue-500/50 hover:scale-105 transition-all whitespace-nowrap">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Export PDF</span>
                        </a>
                    @else
                        <button
                            class="flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-500 to-purple-600 px-6 py-2.5 text-sm font-medium text-white hover:shadow-lg hover:shadow-blue-500/50 hover:scale-105 transition-all whitespace-nowrap">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Export PDF</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- SECTION 3: DAFTAR ANGGOTA DENGAN TUNGGAKAN --}}
        <div class="rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-white/10 bg-white/5">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        Daftar Anggota dengan Tunggakan
                    </h3>

                    <span
                        class="px-3 py-1 rounded-lg text-xs font-bold bg-orange-500/20 text-orange-400 border border-orange-500/30">
                        {{ $billMenunggak->count() }} Anggota Menunggak
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-white/5 border-b border-white/10">
                        <tr>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-white/70 uppercase tracking-wider">
                                Nama Anggota</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-white/70 uppercase tracking-wider">
                                Total Tunggakan</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-white/70 uppercase tracking-wider">
                                Jumlah Tagihan</th>
                            <th
                                class="px-6 py-4 text-right text-xs font-semibold text-white/70 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-white/10">
                        @forelse($billMenunggak as $bill)
                            @php
                                // $bill = object hasil push dari controller: (id, name, nim, photo, total_amount, jumlah_tagihan)

                                // Nama ditampilkan (hapus isi dalam kurung)
                                $rawName = $bill->name ?? 'Anggota';
                                $displayName = trim(preg_replace('/\s*\(.*?\)\s*/', '', $rawName));
                                $displayName = $displayName !== '' ? $displayName : $rawName;

                                $parts = preg_split('/\s+/', trim($displayName));
                                $initials = strtoupper(
                                    substr($parts[0] ?? 'N', 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''),
                                );

                                // FOTO: ambil dari object tunggakan (hasil controller)
                                $photoPath = $bill->photo ?? null;

                                // bikin url foto (kalau sudah URL, pakai langsung)
                                if (
                                    $photoPath &&
                                    \Illuminate\Support\Str::startsWith($photoPath, ['http://', 'https://', 'data:'])
                                ) {
                                    $photoUrl = $photoPath;
                                } elseif ($photoPath) {
                                    $photoUrl = asset('storage/' . ltrim($photoPath, '/'));
                                } else {
                                    $photoUrl = null;
                                }
                            @endphp



                            <tr>
                                {{-- Nama Anggota --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="h-8 w-8 rounded-full overflow-hidden border border-white/10 shrink-0">
                                            @if ($photoUrl)
                                                <img src="{{ $photoUrl }}" alt="{{ $displayName }}"
                                                    class="h-full w-full object-cover">
                                            @else
                                                <div
                                                    class="h-full w-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-xs">
                                                    {{ $initials ?: 'NA' }}
                                                </div>
                                            @endif
                                        </div>

                                        <div>
                                            <p class="text-sm font-semibold text-white">{{ $displayName }}</p>
                                        </div>
                                    </div>
                                </td>


                                {{-- Total Tunggakan --}}
                                <td class="px-6 py-4">
                                    <p class="text-sm font-bold text-white">
                                        Rp {{ number_format($bill->total_amount, 0, ',', '.') }}
                                    </p>
                                </td>

                                {{-- Jumlah Tagihan --}}
                                <td class="px-6 py-4">
                                    <p class="text-sm text-white">
                                        {{ $bill->jumlah_tagihan }} Tagihan
                                    </p>
                                </td>

                                {{-- Aksi --}}
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('laporan.tunggakan.detail', $bill->id) }}"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-blue-500/50 bg-blue-500/10 text-blue-300 hover:bg-blue-500/20 transition-all text-sm font-medium">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Lihat Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-6 text-center text-sm text-white/60">
                                    Tidak ada anggota menunggak
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>

        {{-- SECTION 4: RIWAYAT TRANSAKSI (PEMASUKAN & PENGELUARAN) --}}
        @php
            // Paginator -> Collection (isi item pada halaman ini)
            $txCollection = method_exists($transactions, 'getCollection')
                ? $transactions->getCollection()
                : collect($transactions);

            $txPemasukan = $txCollection->filter(fn($t) => ($t->type ?? '') === 'pemasukan');
            // selain pemasukan dianggap pengeluaran (biar aman kalau value-nya beda-beda)
            $txPengeluaran = $txCollection->filter(fn($t) => ($t->type ?? '') !== 'pemasukan');

            $countPemasukan = $txPemasukan->count();
            $countPengeluaran = $txPengeluaran->count();
        @endphp

        <div x-data="{ receiptUrl: null, receiptIsImage: false, open: false, tab: 'pemasukan' }" x-cloak class="rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm">

            {{-- MODAL PREVIEW --}}
            <div x-show="open" x-transition.opacity
                class="fixed inset-0 z-[9999] flex items-center justify-center p-4" style="display:none;"
                @keydown.escape.window="open = false">
                <div class="absolute inset-0 bg-black/60" @click="open = false"></div>

                <div class="relative w-full max-w-6xl mx-auto bg-[#0f172a] rounded-xl overflow-hidden shadow-lg">
                    <div class="flex items-center justify-between px-4 py-3 border-b border-white/10">
                        <h3 class="text-sm font-semibold text-white">Preview Bukti Pembayaran</h3>
                        <div class="flex items-center gap-2">
                            <a :href="receiptUrl" target="_blank" x-show="receiptUrl"
                                class="text-sm text-blue-300 hover:underline" x-cloak>Buka di Tab Baru</a>
                            <button @click="open = false" class="text-white/60 hover:text-white px-2">Tutup</button>
                        </div>
                    </div>

                    <div class="p-4 overflow-auto" style="max-height: calc(90vh - 56px);">
                        <template x-if="receiptIsImage">
                            <div class="w-full flex items-center justify-center">
                                <img :src="receiptUrl" alt="Bukti Pembayaran"
                                    class="max-w-full max-h-[80vh] object-contain rounded" />
                            </div>
                        </template>

                        <template x-if="!receiptIsImage">
                            <div class="w-full">
                                <iframe :src="receiptUrl" class="w-full h-[80vh] bg-black rounded"
                                    style="min-height:400px;" frameborder="0"></iframe>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
            {{-- END MODAL --}}

            {{-- Header + Tabs --}}
            <div class="px-6 py-4 border-b border-white/10 bg-white/5">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        Riwayat Transaksi
                    </h3>

                    <div class="inline-flex items-center rounded-xl border border-white/10 bg-white/5 p-1">
                        <button type="button" @click="tab = 'pemasukan'"
                            class="px-4 py-2 rounded-lg text-sm font-semibold transition-all"
                            :class="tab === 'pemasukan'
                                ?
                                'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' :
                                'text-white/70 hover:text-white'">
                            Pemasukan
                            <span
                                class="ml-2 inline-flex items-center justify-center min-w-[24px] h-5 px-1.5 rounded-md text-xs font-bold"
                                :class="tab === 'pemasukan' ? 'bg-emerald-500/20 text-emerald-300' :
                                    'bg-white/10 text-white/70'">
                                {{ $countPemasukan }}
                            </span>
                        </button>

                        <button type="button" @click="tab = 'pengeluaran'"
                            class="px-4 py-2 rounded-lg text-sm font-semibold transition-all"
                            :class="tab === 'pengeluaran'
                                ?
                                'bg-red-500/20 text-red-300 border border-red-500/30' :
                                'text-white/70 hover:text-white'">
                            Pengeluaran
                            <span
                                class="ml-2 inline-flex items-center justify-center min-w-[24px] h-5 px-1.5 rounded-md text-xs font-bold"
                                :class="tab === 'pengeluaran' ? 'bg-red-500/20 text-red-300' : 'bg-white/10 text-white/70'">
                                {{ $countPengeluaran }}
                            </span>
                        </button>
                    </div>
                </div>

                <p class="mt-2 text-xs text-white/50">
                    Catatan: pemisahan ini berdasarkan transaksi pada halaman yang sedang ditampilkan (pagination tetap
                    mengikuti data utama).
                </p>
            </div>

            {{-- TAB: PEMASUKAN --}}
            <div x-show="tab === 'pemasukan'" x-transition.opacity style="display:none;">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-white/5 border-b border-white/10">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-white/70 uppercase tracking-wider">
                                    Tanggal</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-white/70 uppercase tracking-wider">
                                    Nama Anggota</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-white/70 uppercase tracking-wider">
                                    Nama Tagihan</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-white/70 uppercase tracking-wider">
                                    Nominal</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-white/70 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-4 text-right text-xs font-semibold text-white/70 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-white/10">
                            @forelse($txPemasukan as $tx)
                                @php
                                    $user = $tx->user ?? null;
                                    $date = $tx->payment_date ?? ($tx->created_at ?? null);
                                    $amount = $tx->amount ?? 0;
                                    $bill = $tx->bill ?? null;

                                    $rawNameTx = $user->name ?? ($tx->payer_name ?? 'NA');
                                    $displayNameTx = trim(preg_replace('/\s*\(.*?\)\s*/', '', $rawNameTx));
                                    $displayNameTx = $displayNameTx !== '' ? $displayNameTx : $rawNameTx;

                                    $partsTx = preg_split('/\s+/', trim($displayNameTx));
                                    $initialsTx = strtoupper(
                                        substr($partsTx[0] ?? 'N', 0, 1) .
                                            (isset($partsTx[1]) ? substr($partsTx[1], 0, 1) : ''),
                                    );

                                    $photoPathTx = $user->photo ?? null;
                                    $photoUrlTx = $photoPathTx ? asset('storage/' . $photoPathTx) : null;
                                @endphp

                                <tr class="hover:bg-white/5 transition-colors">
                                    <td class="px-6 py-4">
                                        <p class="text-sm text-white">{{ optional($date)->format('d M Y') }}</p>
                                        <p class="text-xs text-white/50">{{ optional($date)->format('H:i') }} WIB</p>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="h-8 w-8 rounded-full overflow-hidden border border-white/10 shrink-0">
                                                @if ($photoUrlTx)
                                                    <img src="{{ $photoUrlTx }}" alt="{{ $displayNameTx }}"
                                                        class="h-full w-full object-cover">
                                                @else
                                                    <div
                                                        class="h-full w-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-xs">
                                                        {{ $initialsTx ?: 'NA' }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-white">{{ $displayNameTx }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <p class="text-sm font-semibold text-white">
                                            {{ $bill->name ?? ($tx->description ?? '-') }}</p>
                                        <p class="text-xs text-white/50">
                                            {{ $bill->period ?? optional($tx->created_at)->format('M Y') }}</p>
                                    </td>

                                    <td class="px-6 py-4">
                                        <p class="text-sm font-bold text-emerald-400">
                                            + Rp {{ number_format($amount, 0, ',', '.') }}
                                        </p>
                                    </td>

                                    <td class="px-6 py-4">
                                        @if (($tx->status ?? '') === \App\Models\PembayaranKas::STATUS_CONFIRMED)
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-green-500/20 text-green-400 border border-green-500/30">
                                                <span class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1.5"></span>
                                                Verified
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-500/20 text-slate-300 border border-slate-500/30">
                                                {{ ucfirst($tx->status ?? '—') }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end">
                                            @if (!empty($tx->receipt_path))
                                                <button
                                                    @click="
                                                receiptUrl = {{ json_encode(route('laporan.receipt.show', $tx->id)) }};
                                                receiptIsImage = {{ preg_match('/\.(jpe?g|png|gif|webp)$/i', $tx->receipt_path) ? 'true' : 'false' }};
                                                open = true;
                                            "
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-blue-500/50 bg-blue-500/10 text-blue-300 hover:bg-blue-500/20 transition-all text-sm font-medium">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                                                        </path>
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                        </path>
                                                    </svg>
                                                    Lihat Bukti
                                                </button>
                                            @else
                                                <span class="text-white/30 text-sm">— Tidak ada bukti</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-6 text-center text-sm text-white/60">
                                        Tidak ada transaksi pemasukan pada halaman ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- TAB: PENGELUARAN --}}
            <div x-show="tab === 'pengeluaran'" x-transition.opacity style="display:none;">
                <div class="overflow-x-auto">
                    <table class="w-full table-fixed">
                        <colgroup>
                            <col class="w-[140px]"> {{-- Tanggal --}}
                            <col class="w-[230px]"> {{-- Nama Anggota --}}
                            <col> {{-- Nama Tagihan (fleksibel) --}}
                            <col class="w-[160px]"> {{-- Nominal --}}
                            <col class="w-[140px]"> {{-- Status --}}
                            <col class="w-[170px]"> {{-- Aksi --}}
                        </colgroup>

                        <thead class="bg-white/5 border-b border-white/10">
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-white/70 uppercase tracking-wider">
                                    Tanggal</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-white/70 uppercase tracking-wider">
                                    Nama Anggota</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-white/70 uppercase tracking-wider">
                                    Keterangan Pengeluaran</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-white/70 uppercase tracking-wider">
                                    Nominal</th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-semibold text-white/70 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-4 text-right text-xs font-semibold text-white/70 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-white/10">
                            @forelse($txPengeluaran as $tx)
                                @php
                                    $user = $tx->user ?? null;
                                    $date = $tx->payment_date ?? ($tx->created_at ?? null);
                                    $amount = $tx->amount ?? 0;
                                    $bill = $tx->bill ?? null;

                                    $rawNameTx = $user->name ?? ($tx->payer_name ?? 'NA');
                                    $displayNameTx = trim(preg_replace('/\s*\(.*?\)\s*/', '', $rawNameTx));
                                    $displayNameTx = $displayNameTx !== '' ? $displayNameTx : $rawNameTx;

                                    $partsTx = preg_split('/\s+/', trim($displayNameTx));
                                    $initialsTx = strtoupper(
                                        substr($partsTx[0] ?? 'N', 0, 1) .
                                            (isset($partsTx[1]) ? substr($partsTx[1], 0, 1) : ''),
                                    );

                                    $photoPathTx = $user->photo ?? null;
                                    $photoUrlTx = $photoPathTx ? asset('storage/' . $photoPathTx) : null;
                                @endphp

                                <tr class="hover:bg-white/5 transition-colors">
                                    <td class="px-6 py-4">
                                        <p class="text-sm text-white">{{ optional($date)->format('d M Y') }}</p>
                                        <p class="text-xs text-white/50">{{ optional($date)->format('H:i') }} WIB</p>
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="h-8 w-8 rounded-full overflow-hidden border border-white/10 shrink-0">
                                                @if ($photoUrlTx)
                                                    <img src="{{ $photoUrlTx }}" alt="{{ $displayNameTx }}"
                                                        class="h-full w-full object-cover">
                                                @else
                                                    <div
                                                        class="h-full w-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-xs">
                                                        {{ $initialsTx ?: 'NA' }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="text-sm font-semibold text-white">{{ $displayNameTx }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <p class="text-sm font-semibold text-white break-all overflow-hidden"
                                            style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">
                                            {{ $bill->name ?? ($tx->description ?? '-') }}
                                        </p>
                                        <p class="text-xs text-white/50">
                                            {{ $bill->period ?? optional($tx->created_at)->format('M Y') }}
                                        </p>
                                    </td>


                                    <td class="px-6 py-4">
                                        <p class="text-sm font-bold text-red-400">
                                            - Rp {{ number_format($amount, 0, ',', '.') }}
                                        </p>
                                    </td>

                                    <td class="px-6 py-4">
                                        @if (($tx->status ?? '') === \App\Models\PembayaranKas::STATUS_CONFIRMED)
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-green-500/20 text-green-400 border border-green-500/30">
                                                <span class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1.5"></span>
                                                Verified
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-500/20 text-slate-300 border border-slate-500/30">
                                                {{ ucfirst($tx->status ?? '—') }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end">
                                            @if (!empty($tx->receipt_path))
                                                <button
                                                    @click="
                                                receiptUrl = {{ json_encode(route('laporan.receipt.show', $tx->id)) }};
                                                receiptIsImage = {{ preg_match('/\.(jpe?g|png|gif|webp)$/i', $tx->receipt_path) ? 'true' : 'false' }};
                                                open = true;
                                            "
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-blue-500/50 bg-blue-500/10 text-blue-300 hover:bg-blue-500/20 transition-all text-sm font-medium">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                                                        </path>
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                        </path>
                                                    </svg>
                                                    Lihat Bukti
                                                </button>
                                            @else
                                                <span class="text-white/30 text-sm">— Tidak ada bukti</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-6 text-center text-sm text-white/60">
                                        Tidak ada transaksi pengeluaran pada halaman ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination (tetap untuk data utama) --}}
            <div class="border-t border-white/10 bg-white/5 px-6 py-4">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-sm text-white/60">
                        Menampilkan
                        <span
                            class="font-medium text-white">{{ $transactions->firstItem() ? $transactions->firstItem() : 0 }}</span>
                        –
                        <span
                            class="font-medium text-white">{{ $transactions->lastItem() ? $transactions->lastItem() : 0 }}</span>
                        dari <span class="font-medium text-white">{{ $transactions->total() }}</span> transaksi
                    </p>
                    <div class="flex gap-2">
                        {!! $transactions->links() !!}
                    </div>
                </div>
            </div>

        </div>


    </div>

    {{-- Alpine.js (hapus kalau layout sudah memuat Alpine) --}}
    @push('scripts')
        <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @endpush

</x-layouts.app-layout>
