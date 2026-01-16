<x-layouts.app-layout-anggota>
    {{-- Header Content --}}
    @section('page-title', 'Riwayat Transaksi')
    @section('page-subtitle', 'Catatan lengkap seluruh pembayaran dan aktivitas keuangan Anda')

    <div x-data="{
        selectedStatus: 'all',
        searchQuery: '',
        selectedTransaction: null,
        showDetailModal: false,
        showFilterDropdown: false,
        showStatusDropdown: false,
        filterLabel: 'Filter Periode',
        statusLabel: 'Semua Status',
        dateFrom: '',
        dateTo: '',
    
        // Data dari Backend
        transactions: @js($transactions),
    
        // ==============================
        // FILTER UTAMA (STATUS + SEARCH + TANGGAL)
        // ==============================
        get filteredTransactions() {
            let filtered = this.transactions;
    
            // 1) Filter by Status
            if (this.selectedStatus !== 'all') {
                filtered = filtered.filter(t => t.status === this.selectedStatus);
            }
    
            // 2) Filter by Search Query
            if (this.searchQuery.trim()) {
                const q = this.searchQuery.toLowerCase();
                filtered = filtered.filter(t =>
                    String(t.id ?? '').toLowerCase().includes(q) ||
                    String(t.charge_name ?? '').toLowerCase().includes(q) ||
                    String(t.category ?? '').toLowerCase().includes(q)
                );
            }
    
            // 3) Filter by Date Range (dateFrom/dateTo) berdasarkan payment_date
            // Asumsi trx.payment_date bisa diparse JS (contoh: 2026-01-03 atau 2026-01-03T10:00:00)
            if (this.dateFrom) {
                const from = new Date(this.dateFrom + 'T00:00:00');
                filtered = filtered.filter(t => {
                    const d = new Date(t.payment_date);
                    return !isNaN(d) && d >= from;
                });
            }
    
            if (this.dateTo) {
                const to = new Date(this.dateTo + 'T23:59:59');
                filtered = filtered.filter(t => {
                    const d = new Date(t.payment_date);
                    return !isNaN(d) && d <= to;
                });
            }
    
            // Optional: urutkan terbaru di atas
            filtered = filtered.sort((a, b) => new Date(b.payment_date) - new Date(a.payment_date));
    
            return filtered;
        },
    
        // ==============================
        // STATISTIK (DARI SEMUA TRANSAKSI, BUKAN YANG TERFILTER)
        // ==============================
        get statsData() {
            return {
                total: this.transactions.length,
                verified: this.transactions.filter(t => t.status === 'verified').length,
                pending: this.transactions.filter(t => t.status === 'pending').length,
                rejected: this.transactions.filter(t => t.status === 'rejected').length,
                verifiedAmount: this.transactions
                    .filter(t => t.status === 'verified')
                    .reduce((s, t) => s + (Number(t.amount) || 0), 0)
            };
        },
    
        // ==============================
        // ACTIONS
        // ==============================
        openDetail(trx) {
            this.selectedTransaction = trx;
            this.showDetailModal = true;
        },
    
        resetAllFilters() {
            this.selectedStatus = 'all';
            this.statusLabel = 'Semua Status';
            this.searchQuery = '';
            this.dateFrom = '';
            this.dateTo = '';
            this.filterLabel = 'Filter Periode';
        },
    
        applyDateFilter() {
            // validasi
            if (this.dateFrom && this.dateTo && new Date(this.dateFrom) > new Date(this.dateTo)) {
                alert('Tanggal mulai tidak boleh lebih besar dari tanggal akhir');
                return;
            }
    
            // label dinamis
            if (this.dateFrom || this.dateTo) {
                const f = this.dateFrom ? this.formatDateShort(this.dateFrom) : '...';
                const t = this.dateTo ? this.formatDateShort(this.dateTo) : '...';
                this.filterLabel = `${f} - ${t}`;
            } else {
                this.filterLabel = 'Filter Periode';
            }
    
            this.showFilterDropdown = false;
        },
    
        resetDateOnly() {
            this.dateFrom = '';
            this.dateTo = '';
            this.filterLabel = 'Filter Periode';
            this.showFilterDropdown = false;
        },
    
        exportPdf() {
            const params = new URLSearchParams();
    
            params.set('status', this.selectedStatus || 'all');
            params.set('q', this.searchQuery || '');
    
            if (this.dateFrom) params.set('from', this.dateFrom);
            if (this.dateTo) params.set('to', this.dateTo);
    
            const url = `{{ route('member.riwayat-transaksi.export-pdf') }}?${params.toString()}`;
            window.open(url, '_blank');
        },
    
        // ==============================
        // HELPERS
        // ==============================
        formatDateShort(date) {
            if (!date) return '-';
            return new Date(date).toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        },
    
        formatDate(date) {
            if (!date) return '-';
            return new Date(date).toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'long',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },
    
        getStatusLabel(status) {
            switch (status) {
                case 'verified':
                    return 'Terverifikasi';
                case 'pending':
                    return 'Menunggu';
                case 'rejected':
                    return 'Ditolak';
                default:
                    return status;
            }
        }
    }">

        {{-- SECTION 1: STATS OVERVIEW --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">

            {{-- Card: Total Transaksi --}}
            <div
                class="rounded-xl border border-white/20 bg-white/10 backdrop-blur-md p-5 shadow-lg hover:bg-white/15 transition-all group">
                <div class="flex items-center justify-between mb-3">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 shadow-lg group-hover:scale-110 transition-transform">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
                <p class="text-sm font-medium text-white/60 mb-1">Total Transaksi</p>
                <p class="text-2xl font-bold text-white" x-text="statsData.total"></p>
                <p class="text-xs text-white/40 mt-1">Seluruh riwayat</p>
            </div>

            {{-- Card: Terverifikasi --}}
            <div
                class="rounded-xl border border-white/20 bg-white/10 backdrop-blur-md p-5 shadow-lg hover:bg-white/15 transition-all group">
                <div class="flex items-center justify-between mb-3">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-green-600 shadow-lg group-hover:scale-110 transition-transform">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <p class="text-sm font-medium text-white/60 mb-1">Terverifikasi</p>
                <p class="text-2xl font-bold text-white" x-text="statsData.verified"></p>
                <p class="text-xs text-emerald-400 mt-1">
                    <span x-text="'Rp ' + statsData.verifiedAmount.toLocaleString('id-ID')"></span>
                </p>
            </div>

            {{-- Card: Pending --}}
            <div
                class="rounded-xl border border-white/20 bg-white/10 backdrop-blur-md p-5 shadow-lg hover:bg-white/15 transition-all group">
                <div class="flex items-center justify-between mb-3">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-yellow-500 to-orange-500 shadow-lg group-hover:scale-110 transition-transform">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <p class="text-sm font-medium text-white/60 mb-1">Menunggu</p>
                <p class="text-2xl font-bold text-white" x-text="statsData.pending"></p>
                <p class="text-xs text-yellow-400 mt-1">Sedang diverifikasi</p>
            </div>

            {{-- Card: Ditolak --}}
            <div
                class="rounded-xl border border-white/20 bg-white/10 backdrop-blur-md p-5 shadow-lg hover:bg-white/15 transition-all group">
                <div class="flex items-center justify-between mb-3">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-red-500 to-pink-600 shadow-lg group-hover:scale-110 transition-transform">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                </div>
                <p class="text-sm font-medium text-white/60 mb-1">Ditolak</p>
                <p class="text-2xl font-bold text-white" x-text="statsData.rejected"></p>
                <p class="text-xs text-red-400 mt-1">Perlu upload ulang</p>
            </div>
        </div>

        {{-- SECTION 2: FILTER & SEARCH --}}
        <div class="rounded-2xl bg-white/5 border border-white/10 p-4 backdrop-blur-sm relative z-20 mb-6">
            <div class="flex flex-col lg:flex-row lg:items-center gap-4 w-full">

                {{-- Search Box --}}
                <div class="relative flex-1 w-full">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-white/40" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" x-model="searchQuery"
                        placeholder="Cari ID transaksi, nama tagihan, atau kategori..."
                        class="w-full pl-10 pr-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/50 transition-all text-sm">
                </div>

                <div class="flex items-center gap-3 shrink-0">

                    {{-- Filter Status Dropdown --}}
                    <div class="relative" @click.outside="showStatusDropdown = false">
                        <button @click="showStatusDropdown = !showStatusDropdown"
                            class="flex w-48 items-center justify-between rounded-xl border border-white/10 bg-[#0f172a] px-4 py-2.5 text-left text-sm font-medium text-white shadow-lg transition-all hover:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-purple-500/50">
                            <span x-text="statusLabel"></span>
                            <svg class="h-4 w-4 text-white/50 transition-transform duration-200"
                                :class="showStatusDropdown ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="showStatusDropdown" @click.stop
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                            class="absolute right-0 top-full mt-2 w-56 origin-top-right overflow-hidden rounded-xl border border-white/20 bg-[#0f172a] shadow-[0_10px_40px_-10px_rgba(0,0,0,0.5)] z-50 ring-1 ring-white/5"
                            style="display: none;">
                            <div class="p-2">
                                <button
                                    @click="selectedStatus = 'all'; statusLabel = 'Semua Status'; showStatusDropdown = false"
                                    class="w-full text-left px-3 py-2 text-sm text-white hover:bg-white/10 rounded-lg transition-colors">
                                    Semua Status
                                </button>
                                <button
                                    @click="selectedStatus = 'verified'; statusLabel = 'Terverifikasi'; showStatusDropdown = false"
                                    class="w-full text-left px-3 py-2 text-sm text-white hover:bg-white/10 rounded-lg transition-colors">
                                    Terverifikasi
                                </button>
                                <button
                                    @click="selectedStatus = 'pending'; statusLabel = 'Menunggu'; showStatusDropdown = false"
                                    class="w-full text-left px-3 py-2 text-sm text-white hover:bg-white/10 rounded-lg transition-colors">
                                    Menunggu
                                </button>
                                <button
                                    @click="selectedStatus = 'rejected'; statusLabel = 'Ditolak'; showStatusDropdown = false"
                                    class="w-full text-left px-3 py-2 text-sm text-white hover:bg-white/10 rounded-lg transition-colors">
                                    Ditolak
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Filter Periode Dropdown --}}
                    <div class="relative" @click.outside="showFilterDropdown = false">
                        <button @click="showFilterDropdown = !showFilterDropdown"
                            class="flex w-48 items-center justify-between rounded-xl border border-white/10 bg-[#0f172a] px-4 py-2.5 text-left text-sm font-medium text-white shadow-lg transition-all hover:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-purple-500/50">
                            <span x-text="filterLabel"></span>
                            <svg class="h-4 w-4 text-white/50 transition-transform duration-200"
                                :class="showFilterDropdown ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="showFilterDropdown" @click.stop
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                            class="absolute right-0 top-full mt-2 w-72 origin-top-right overflow-hidden rounded-xl border border-white/20 bg-[#0f172a] shadow-[0_10px_40px_-10px_rgba(0,0,0,0.5)] z-50 ring-1 ring-white/5"
                            style="display: none;">
                            <div class="p-4 space-y-4">
                                <div>
                                    <label class="block text-xs font-medium text-white/50 mb-1.5">Dari Tanggal</label>
                                    <input type="date" x-model="dateFrom" onclick="event.stopPropagation()"
                                        class="w-full rounded-lg border border-white/20 bg-white/5 px-3 py-2 text-white text-sm focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-all [color-scheme:dark]">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-white/50 mb-1.5">Sampai
                                        Tanggal</label>
                                    <input type="date" x-model="dateTo" onclick="event.stopPropagation()"
                                        class="w-full rounded-lg border border-white/20 bg-white/5 px-3 py-2 text-white text-sm focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-all [color-scheme:dark]">
                                </div>

                                <div class="pt-2 border-t border-white/10">
                                    <button @click="applyDateFilter()"
                                        class="w-full rounded-lg bg-blue-600 py-2 text-sm font-semibold text-white hover:bg-blue-500 transition-colors">
                                        Terapkan Filter
                                    </button>

                                    <button @click="resetDateOnly()"
                                        class="mt-2 w-full rounded-lg bg-white/5 border border-white/10 py-2 text-sm font-semibold text-white hover:bg-white/10 transition-colors">
                                        Reset Tanggal
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Export Button (placeholder) --}}
                    <button @click="exportPdf()"
                        class="flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-500 to-purple-600 px-6 py-2.5 text-sm font-medium text-white hover:shadow-lg hover:shadow-blue-500/50 hover:scale-105 transition-all whitespace-nowrap">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Export PDF</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- SECTION 3: TRANSACTIONS LIST --}}
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <p class="text-sm text-white/60">
                    Menampilkan <span class="font-semibold text-white" x-text="filteredTransactions.length"></span>
                    dari <span x-text="transactions.length"></span> transaksi
                </p>

                <button @click="resetAllFilters()" class="text-xs text-white/60 hover:text-white underline">
                    Reset Semua Filter
                </button>
            </div>

            {{-- Transaction Cards --}}
            <template x-if="filteredTransactions.length > 0">
                <div class="space-y-3">
                    <template x-for="trx in filteredTransactions" :key="trx.id">
                        <div @click="openDetail(trx)"
                            class="rounded-xl border border-white/20 bg-white/10 backdrop-blur-md p-5 shadow-lg hover:bg-white/15 hover:border-blue-500/50 transition-all cursor-pointer group">
                            <div class="flex flex-col md:flex-row md:items-center gap-4">

                                {{-- Left --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start gap-3 mb-3">
                                        <div class="flex-shrink-0 w-12 h-12 rounded-xl flex items-center justify-center transition-all"
                                            :class="{
                                                'bg-emerald-500/20 text-emerald-400': trx.status === 'verified',
                                                'bg-yellow-500/20 text-yellow-400': trx.status === 'pending',
                                                'bg-red-500/20 text-red-400': trx.status === 'rejected'
                                            }">
                                            <template x-if="trx.status === 'verified'">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </template>
                                            <template x-if="trx.status === 'pending'">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </template>
                                            <template x-if="trx.status === 'rejected'">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </template>
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                                <h3 class="font-semibold text-white text-base"
                                                    x-text="trx.charge_name"></h3>
                                                <span
                                                    class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-500/20 text-blue-300"
                                                    x-text="trx.category ?? '-'"></span>
                                            </div>

                                            <p class="text-sm text-white/50 mb-2 flex items-center gap-1.5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                                </svg>
                                                <span class="font-mono" x-text="trx.id"></span>
                                            </p>

                                            <div class="flex items-center gap-4 text-xs text-white/40">
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    <span x-text="formatDateShort(trx.payment_date)"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <template x-if="trx.status === 'rejected' && trx.rejection_reason">
                                        <div class="mt-3 p-3 bg-red-500/10 border border-red-500/30 rounded-lg">
                                            <p class="text-xs text-red-400">
                                                <strong>Alasan:</strong> <span x-text="trx.rejection_reason"></span>
                                            </p>
                                        </div>
                                    </template>
                                </div>

                                {{-- Right --}}
                                <div class="flex flex-col items-end gap-3 flex-shrink-0">
                                    <div class="text-right">
                                        <p class="text-xs text-white/50 mb-1">Nominal</p>
                                        <p class="text-2xl font-bold text-white"
                                            x-text="'Rp ' + (Number(trx.amount) || 0).toLocaleString('id-ID')"></p>
                                    </div>

                                    <span
                                        class="px-4 py-2 rounded-full text-xs font-bold tracking-wide border-2 transition-all duration-300 text-white"
                                        :class="{
                                            'bg-green-600 border-green-500': trx.status === 'verified',
                                            'bg-amber-500 border-amber-400': trx.status === 'pending',
                                            'bg-red-600 border-red-500': trx.status === 'rejected'
                                        }"
                                        x-text="getStatusLabel(trx.status)">
                                    </span>

                                    <div
                                        class="flex items-center gap-2 text-white/40 group-hover:text-blue-400 transition-colors">
                                        <span class="text-xs font-medium">Lihat Detail</span>
                                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </template>
                </div>
            </template>

            {{-- Empty State --}}
            <template x-if="filteredTransactions.length === 0">
                <div class="rounded-xl border border-white/20 bg-white/10 backdrop-blur-md p-12 text-center shadow-lg">
                    <h3 class="text-xl font-bold text-white mb-2">Tidak Ada Transaksi</h3>
                    <p class="text-white/60 mb-6">
                        Tidak ada transaksi yang sesuai dengan filter Anda
                    </p>
                    <button @click="resetAllFilters()"
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl font-semibold hover:from-blue-700 hover:to-purple-700 transition-all shadow-lg">
                        Reset Filter
                    </button>
                </div>
            </template>
        </div>

        {{-- MODAL: Transaction Detail --}}
        <div x-show="showDetailModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click.self="showDetailModal = false"
            class="fixed inset-0 bg-black/80 backdrop-blur-sm z-[100] flex items-center justify-center p-4 overflow-y-auto"
            style="display: none;">

            <div x-show="showDetailModal" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-90"
                x-transition:enter-end="opacity-100 transform scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform scale-100"
                x-transition:leave-end="opacity-0 transform scale-90"
                class="bg-slate-900 border border-white/20 rounded-2xl shadow-2xl max-w-2xl w-full my-8 max-h-[calc(100vh-4rem)] overflow-y-auto custom-scrollbar"
                @click.stop>

                <template x-if="selectedTransaction">
                    <div>
                        <div class="sticky top-0 bg-gradient-to-r from-blue-600 to-purple-600 p-6 rounded-t-2xl z-10">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h2 class="text-2xl font-bold text-white mb-2">Detail Transaksi</h2>
                                    <p class="text-white/80 text-sm font-mono" x-text="selectedTransaction?.id"></p>
                                </div>
                                <button @click="showDetailModal = false"
                                    class="text-white/80 hover:text-white hover:bg-white/10 rounded-lg p-2 transition-all">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="p-6 space-y-6">
                            <div class="flex justify-center">
                                <span
                                    class="px-6 py-3 rounded-full text-sm font-bold border-2 inline-flex items-center gap-2 text-white"
                                    :class="{
                                        'bg-green-600 border-green-500': selectedTransaction?.status === 'verified',
                                        'bg-amber-500 border-amber-400': selectedTransaction?.status === 'pending',
                                        'bg-red-600 border-red-500': selectedTransaction?.status === 'rejected'
                                    }">
                                    <span x-text="getStatusLabel(selectedTransaction?.status)"></span>
                                </span>
                            </div>

                            <div class="p-5 bg-white/5 border border-white/10 rounded-xl space-y-4">
                                <p class="text-xs font-semibold text-white/50 uppercase tracking-wider">Informasi</p>
                                <div class="space-y-3">
                                    <div class="flex justify-between items-start">
                                        <span class="text-sm text-white/60">Nama Tagihan</span>
                                        <span class="text-sm font-semibold text-white text-right"
                                            x-text="selectedTransaction?.charge_name"></span>
                                    </div>
                                    <div class="flex justify-between items-start">
                                        <span class="text-sm text-white/60">Total</span>
                                        <span class="text-2xl font-bold text-white"
                                            x-text="'Rp ' + (Number(selectedTransaction?.amount) || 0).toLocaleString('id-ID')"></span>
                                    </div>
                                    <div class="flex justify-between items-start">
                                        <span class="text-sm text-white/60">Tanggal Bayar</span>
                                        <span class="text-sm font-semibold text-white"
                                            x-text="formatDate(selectedTransaction?.payment_date)"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="p-5 bg-white/5 border border-white/10 rounded-xl space-y-3">
                                <p class="text-xs font-semibold text-white/50 uppercase tracking-wider">Bukti Transfer
                                </p>
                                <div class="bg-slate-800 rounded-lg p-2">
                                    <img :src="selectedTransaction?.proof_image" alt="Bukti Transfer"
                                        class="w-full rounded-lg">
                                </div>
                                <template x-if="selectedTransaction?.notes">
                                    <div class="pt-3 border-t border-white/10">
                                        <p class="text-xs text-white/50 mb-1">Catatan:</p>
                                        <p class="text-sm text-white/80" x-text="selectedTransaction?.notes"></p>
                                    </div>
                                </template>
                            </div>

                            <button @click="showDetailModal = false"
                                class="w-full px-6 py-3 bg-white/5 text-white border border-white/20 rounded-xl font-semibold hover:bg-white/10 transition-all">
                                Tutup
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

    </div>

    @push('scripts')
        <style>
            .custom-scrollbar::-webkit-scrollbar {
                width: 6px;
            }

            .custom-scrollbar::-webkit-scrollbar-track {
                background: rgba(255, 255, 255, 0.05);
                border-radius: 10px;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: rgba(255, 255, 255, 0.2);
                border-radius: 10px;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: rgba(255, 255, 255, 0.3);
            }

            select {
                background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='rgba(255,255,255,0.5)' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
                background-position: right 0.5rem center;
                background-repeat: no-repeat;
                background-size: 1.5em 1.5em;
                padding-right: 2.5rem;
            }
        </style>
    @endpush

</x-layouts.app-layout-anggota>
