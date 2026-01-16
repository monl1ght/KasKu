<x-layouts.app-layout>

    @section('page-title', 'Manajemen Tagihan')
    @section('page-subtitle', 'Buat dan pantau tagihan pembayaran organisasi')

    {{-- X-DATA UTAMA --}}
    <div x-data="{
        showCreateModal: false,
        showEditModal: false,
        billToEdit: { id: null, name: '', due_date: '', amount: '' },
    
        filter: '{{ request('status', 'semua') }}',
        filterOpen: false,
        filterLabel: 'Semua Status',
    
        // ✅ ambil nilai search dari query string supaya setelah reload tetap keisi
        searchQuery: @js(request('search', '')),
    
        openEdit(bill) {
            this.billToEdit = bill;
            this.showEditModal = true;
        },
    
        setFilter(val, label) {
            this.filter = val;
            this.filterLabel = label;
            this.filterOpen = false;
    
            // ✅ kalau filter berubah, submit juga
            this.$nextTick(() => this.submitSearch());
        },
    
        submitSearch() {
            // submit form GET
            this.$refs.searchForm.requestSubmit();
        }
    }" class="space-y-6">


        {{-- Statistics Cards - Enhanced --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Total Tagihan --}}
            <div
                class="rounded-xl border border-white/20 bg-white/10 backdrop-blur-md p-6 shadow-lg hover:bg-white/15 transition-all group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-white/60">Total Tagihan</p>
                        <p class="text-3xl font-bold text-white mt-2">{{ $totalTagihan ?? 0 }}</p>
                        <p class="text-xs text-white/40 mt-1">Tagihan Aktif</p>
                    </div>
                    <div
                        class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 shadow-lg group-hover:scale-110 transition-transform">
                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Total Nilai --}}
            <div
                class="rounded-xl border border-white/20 bg-white/10 backdrop-blur-md p-6 shadow-lg hover:bg-white/15 transition-all group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-white/60">Total Nilai</p>
                        <p class="text-3xl font-bold text-white mt-2">Rp
                            {{ number_format($totalNilai ?? 0, 0, ',', '.') }}</p>
                        <p class="text-xs text-white/40 mt-1">Potensi Kas</p>
                    </div>
                    <div
                        class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-green-600 shadow-lg group-hover:scale-110 transition-transform">
                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Card 3: Sudah Dibayar --}}
            <div
                class="rounded-xl border border-white/20 bg-white/10 backdrop-blur-md p-6 shadow-lg hover:bg-white/15 transition-all group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-white/60">Sudah Dibayar</p>
                        <p class="text-3xl font-bold text-white mt-2">Rp
                            {{ number_format($totalSudahDibayar ?? 0, 0, ',', '.') }}</p>
                        <p class="text-xs text-green-300 mt-1">Terkumpul
                            {{ number_format((float) ($globalProgress ?? 0), 1) }}%</p>
                    </div>
                    <div
                        class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 shadow-lg group-hover:scale-110 transition-transform">
                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Card 4: Belum Dibayar --}}
            <div
                class="rounded-xl border border-white/20 bg-white/10 backdrop-blur-md p-6 shadow-lg hover:bg-white/15 transition-all group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-white/60">Belum Dibayar</p>
                        <p class="text-3xl font-bold text-white mt-2">Rp
                            {{ number_format($totalBelumDibayar ?? 0, 0, ',', '.') }}</p>
                        <p class="text-xs text-orange-300 mt-1">Tunggakan
                            {{ number_format(100 - ($globalProgress ?? 0), 1) }}%</p>
                    </div>
                    <div
                        class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-orange-500 to-yellow-600 shadow-lg group-hover:scale-110 transition-transform">
                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

        </div>

        {{-- Filter & Search Section - Enhanced --}}
        <div class="relative z-30 rounded-xl border border-white/20 bg-white/10 backdrop-blur-md p-6 shadow-lg">
            <div class="flex flex-col lg:flex-row gap-4">

                {{-- ✅ FORM GET untuk search --}}
                <form x-ref="searchForm" action="{{ url()->current() }}" method="GET" class="flex-1">
                    {{-- kalau kamu punya filter status, kirim juga biar gak hilang --}}
                    <input type="hidden" name="status" :value="filter">

                    <div class="relative">
                        <input type="text" name="search" x-model="searchQuery" placeholder="Cari nama tagihan..."
                            @input.debounce.400ms="submitSearch()"
                            class="w-full rounded-2xl border border-white/20 bg-white/5 px-4 py-3.5 pl-12 text-white placeholder-white/50 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-white/50" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>

                        {{-- ✅ tombol clear (opsional tapi enak) --}}
                        <button type="button" x-show="searchQuery.length" @click="searchQuery=''; submitSearch()"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-white/50 hover:text-white transition">
                            ✕
                        </button>
                    </div>
                </form>

                {{-- Actions --}}
                <div class="flex items-center gap-3 shrink-0">
                    <button @click="showCreateModal = true"
                        class="flex items-center justify-center gap-2 px-6 py-3.5 bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl text-white font-semibold text-sm shadow-lg hover:shadow-blue-500/50 hover:scale-105 transition-all whitespace-nowrap">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span class="hidden sm:inline">Buat Tagihan</span>
                        <span class="sm:hidden">Buat</span>
                    </button>
                </div>
            </div>
        </div>


        {{-- Bills List - Table & Card Hybrid --}}
        <div class="rounded-xl border border-white/20 bg-white/10 backdrop-blur-md shadow-lg overflow-hidden">
            <div class="border-b border-white/20 bg-white/5 px-6 py-4">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Daftar Tagihan
                </h2>
                <p class="text-sm text-white/60 mt-1">Kelola dan pantau semua tagihan organisasi</p>
            </div>

            {{-- Desktop Table View --}}
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-white/5 border-b border-white/10">
                        <tr>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-white/70 uppercase tracking-wider">
                                Tagihan</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-white/70 uppercase tracking-wider">
                                Periode</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-white/70 uppercase tracking-wider">
                                Nominal</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-white/70 uppercase tracking-wider">
                                Progress</th>
                            <th
                                class="px-6 py-4 text-right text-xs font-semibold text-white/70 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse($bills as $bill)
                            <tr class="hover:bg-white/5 transition-colors group">
                                {{-- Tagihan Info --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="p-2.5 bg-purple-500/20 rounded-xl group-hover:bg-purple-500/30 transition-colors">
                                            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-white">{{ $bill->name }}</p>
                                            <p class="text-xs text-white/50 mt-0.5">
                                                {{ $activeOrganization->users()->count() }} Anggota</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Periode --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <svg class="h-4 w-4 text-white/40" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <div>
                                            <p class="text-sm text-white">
                                                {{ \Carbon\Carbon::parse($bill->due_date)->format('M Y') }}</p>
                                            <p class="text-xs text-white/50">Jatuh tempo:
                                                {{ \Carbon\Carbon::parse($bill->due_date)->format('d M') }}</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Nominal --}}
                                <td class="px-6 py-4">
                                    <p class="text-sm font-semibold text-white">Rp
                                        {{ number_format($bill->amount, 0, ',', '.') }}</p>
                                    <p class="text-xs text-white/50">per anggota</p>
                                </td>



                                {{-- Progress --}}
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-2">
                                        <!-- Count & Percentage Row -->
                                        <div class="flex items-center justify-between gap-3">
                                            <span class="text-xs text-white/50 font-medium">
                                                {{ $bill->paid_unique_count }}/{{ $bill->target_members }}
                                            </span>
                                            <span class="text-xs text-white/70 font-semibold">
                                                {{ $bill->progress_percent }}%
                                            </span>
                                        </div>

                                        <!-- Progress Bar -->
                                        <div class="w-full max-w-[140px] bg-white/10 rounded-full h-2 overflow-hidden">
                                            <div class="bg-gradient-to-r from-emerald-500 to-green-400 h-full rounded-full transition-all duration-300"
                                                style="width: {{ $bill->progress_percent }}%">
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Edit --}}
                                        <button @click="openEdit({{ $bill }})" type="button"
                                            class="p-2 hover:bg-white/10 rounded-lg text-white/60 hover:text-yellow-400 transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>

                                        {{-- Hapus --}}
                                        <form action="{{ route('bills.destroy', $bill->id) }}" method="POST"
                                            onsubmit="return confirm('Hapus tagihan ini?');" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="p-2 hover:bg-white/10 rounded-lg text-white/60 hover:text-red-400 transition-all">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-16 px-6">
                                    <div class="text-center">
                                        <div class="flex justify-center mb-4">
                                            <div
                                                class="flex h-20 w-20 items-center justify-center rounded-full bg-white/5">
                                                <svg class="h-10 w-10 text-white/30" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                </svg>
                                            </div>
                                        </div>
                                        <h3 class="text-lg font-semibold text-white mb-2">Belum Ada Tagihan</h3>
                                        <p class="text-sm text-white/60 mb-4">Buat tagihan pertama untuk memulai</p>
                                        <button @click="showCreateModal = true"
                                            class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl text-white text-sm font-medium hover:shadow-lg transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                            Buat Tagihan
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Card View --}}
            <div class="lg:hidden divide-y divide-white/10">
                @forelse($bills as $bill)
                    <div class="p-6 hover:bg-white/5 transition-all" x-data="{ showDetails: false }">
                        <div class="flex items-start gap-4">
                            {{-- Icon --}}
                            <div class="p-3 bg-purple-500/20 rounded-xl flex-shrink-0">
                                <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-white">{{ $bill->name }}</h3>
                                        <p class="text-xs text-white/50 mt-0.5">
                                            {{ $activeOrganization->users()->count() }} Anggota</p>
                                    </div>
                                </div>

                                {{-- Info --}}
                                <div class="mt-3 space-y-2">
                                    <div class="flex items-center gap-2 text-sm">
                                        <svg class="h-4 w-4 text-white/40" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="text-white font-semibold">Rp
                                            {{ number_format($bill->amount, 0, ',', '.') }}</span>
                                        <span class="text-white/50">per anggota</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-sm text-white/70">
                                        <svg class="h-4 w-4 text-white/40" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span>{{ \Carbon\Carbon::parse($bill->due_date)->format('d M Y') }}</span>
                                    </div>
                                </div>

                                {{-- Progress --}}
                                {{-- Progress - Mobile --}}
                                <div class="mt-3 space-y-1.5">
                                    <div class="flex items-center justify-between gap-3">
                                        {{-- Count & Percentage --}}
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs text-white/50 font-medium">
                                                {{ $bill->paid_unique_count }}/{{ $bill->target_members }}
                                            </span>
                                        </div>

                                        <span class="text-xs text-white/70 font-semibold">
                                            {{ $bill->progress_percent }}%
                                        </span>
                                    </div>

                                    {{-- Progress Bar --}}
                                    <div class="w-full max-w-full bg-white/10 rounded-full h-2 overflow-hidden">
                                        <div class="bg-gradient-to-r from-emerald-500 to-green-400 h-full rounded-full transition-all duration-300"
                                            style="width: {{ $bill->progress_percent }}%">
                                        </div>
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="mt-4 flex gap-2">
                                    <button @click="showDetails = !showDetails"
                                        class="flex-1 flex items-center justify-center gap-2 rounded-lg border border-white/20 bg-white/5 px-4 py-2 text-sm font-medium text-white hover:bg-white/10 transition-all">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span x-text="showDetails ? 'Sembunyikan' : 'Detail'"></span>
                                    </button>

                                    <button @click="openEdit({{ $bill }})" type="button"
                                        class="flex items-center justify-center gap-2 rounded-lg border border-yellow-500/50 bg-yellow-500/10 px-4 py-2 text-sm font-medium text-yellow-300 hover:bg-yellow-500/20 transition-all">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit
                                    </button>

                                    <form action="{{ route('bills.destroy', $bill->id) }}" method="POST"
                                        onsubmit="return confirm('Hapus tagihan ini?');" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="flex items-center justify-center gap-2 rounded-lg border border-red-500/50 bg-red-500/10 px-4 py-2 text-sm font-medium text-red-300 hover:bg-red-500/20 transition-all">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>

                                {{-- Expandable Details --}}
                                <div x-show="showDetails" x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 transform scale-95"
                                    x-transition:enter-end="opacity-100 transform scale-100"
                                    class="mt-4 rounded-xl border border-white/20 bg-white/5 p-4">
                                    <div class="space-y-3 text-sm">
                                        <div>
                                            <p class="text-white/50 text-xs mb-1">Periode</p>
                                            <p class="text-white">
                                                {{ \Carbon\Carbon::parse($bill->due_date)->format('F Y') }}</p>
                                        </div>
                                        <div>
                                            <p class="text-white/50 text-xs mb-1">Total Potensi</p>
                                            <p class="text-white font-semibold">Rp
                                                {{ number_format($bill->amount * $activeOrganization->users()->count(), 0, ',', '.') }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-white/50 text-xs mb-1">Target Anggota</p>
                                            <p class="text-white">{{ $activeOrganization->users()->count() }} Anggota
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <div class="flex justify-center mb-4">
                            <div class="flex h-20 w-20 items-center justify-center rounded-full bg-white/5">
                                <svg class="h-10 w-10 text-white/30" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-lg font-semibold text-white mb-2">Belum Ada Tagihan</h3>
                        <p class="text-sm text-white/60 mb-4">Buat tagihan pertama untuk memulai</p>
                        <button @click="showCreateModal = true"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl text-white text-sm font-medium hover:shadow-lg transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Buat Tagihan
                        </button>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="border-t border-white/20 bg-white/5 px-6 py-4">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">

                    {{-- Info --}}
                    <p class="text-sm text-white/60">
                        Menampilkan
                        <span class="font-medium text-white">
                            {{ $bills->firstItem() ?? 0 }}–{{ $bills->lastItem() ?? 0 }}
                        </span>
                        dari
                        <span class="font-medium text-white">
                            {{ $bills->total() }}
                        </span>
                        tagihan
                    </p>

                    {{-- Controls --}}
                    @if ($bills->hasPages())
                        <nav class="flex items-center gap-2" aria-label="Pagination">

                            {{-- Prev --}}
                            @if ($bills->onFirstPage())
                                <span
                                    class="rounded-lg border border-white/20 bg-white/5 px-4 py-2 text-sm font-medium text-white/60 opacity-50 cursor-not-allowed">
                                    Sebelumnya
                                </span>
                            @else
                                <a href="{{ $bills->previousPageUrl() }}"
                                    class="rounded-lg border border-white/20 bg-white/5 px-4 py-2 text-sm font-medium text-white hover:bg-white/10 transition-all">
                                    Sebelumnya
                                </a>
                            @endif

                            {{-- Page Numbers --}}
                            <div class="hidden sm:flex items-center gap-2">
                                @foreach ($bills->links()->elements[0] ?? [] as $page => $url)
                                    @if ($page == $bills->currentPage())
                                        <span
                                            class="rounded-lg border border-blue-500/40 bg-blue-500/15 px-3 py-2 text-sm font-semibold text-white shadow-sm">
                                            {{ $page }}
                                        </span>
                                    @else
                                        <a href="{{ $url }}"
                                            class="rounded-lg border border-white/20 bg-white/5 px-3 py-2 text-sm font-medium text-white hover:bg-white/10 transition-all">
                                            {{ $page }}
                                        </a>
                                    @endif
                                @endforeach
                            </div>

                            {{-- Next --}}
                            @if ($bills->hasMorePages())
                                <a href="{{ $bills->nextPageUrl() }}"
                                    class="rounded-lg border border-white/20 bg-white/5 px-4 py-2 text-sm font-medium text-white hover:bg-white/10 transition-all">
                                    Selanjutnya
                                </a>
                            @else
                                <span
                                    class="rounded-lg border border-white/20 bg-white/5 px-4 py-2 text-sm font-medium text-white/60 opacity-50 cursor-not-allowed">
                                    Selanjutnya
                                </span>
                            @endif

                        </nav>
                    @endif
                </div>
            </div>

        </div>

        {{-- MODAL CREATE --}}
        <div x-show="showCreateModal" style="display: none;" class="relative z-50">
            <div x-show="showCreateModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/80 backdrop-blur-md"></div>

            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center">
                    <div x-show="showCreateModal" @click.outside="showCreateModal = false"
                        x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                        class="relative w-full max-w-lg transform overflow-hidden rounded-2xl border border-white/20 bg-slate-900/90 p-6 text-left shadow-2xl backdrop-blur-xl transition-all">

                        {{-- Glow Effect --}}
                        <div
                            class="absolute -top-20 -left-20 w-60 h-60 bg-blue-500/30 rounded-full blur-[80px] pointer-events-none">
                        </div>
                        <div
                            class="absolute -bottom-20 -right-20 w-60 h-60 bg-purple-500/30 rounded-full blur-[80px] pointer-events-none">
                        </div>

                        {{-- Modal Content --}}
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-xl font-bold text-white tracking-wide">Buat Tagihan Baru</h3>
                                <button @click="showCreateModal = false"
                                    class="rounded-full p-1 text-white/50 hover:bg-white/10 hover:text-white transition-all">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <form action="{{ route('bills.store') }}" method="POST" class="space-y-5">
                                @csrf

                                <div>
                                    <label
                                        class="block text-xs font-semibold text-blue-300 uppercase tracking-wider mb-2">Nama
                                        Tagihan</label>
                                    <div class="relative group">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-white/40 group-focus-within:text-blue-400 transition-colors"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                            </svg>
                                        </div>
                                        <input type="text" name="name" required
                                            placeholder="Contoh: Kas Bulan Januari"
                                            class="w-full pl-11 pr-4 py-3 rounded-xl bg-white/5 border border-white/10 text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <label
                                            class="block text-xs font-semibold text-purple-300 uppercase tracking-wider mb-2">Jatuh
                                            Tempo</label>
                                        <div class="relative group">
                                            <div
                                                class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                                <svg class="h-5 w-5 text-white/40 group-focus-within:text-purple-400 transition-colors"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                            <input type="date" name="due_date" required
                                                class="w-full pl-11 pr-4 py-3 rounded-xl bg-white/5 border border-white/10 text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/50 transition-all [color-scheme:dark]">
                                        </div>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-xs font-semibold text-green-300 uppercase tracking-wider mb-2">Nominal
                                            (Rp)</label>
                                        <div class="relative group">
                                            <div
                                                class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                                <span
                                                    class="text-white/40 font-bold group-focus-within:text-green-400 transition-colors">Rp</span>
                                            </div>
                                            <input type="number" name="amount" required placeholder="0"
                                                class="w-full pl-11 pr-4 py-3 rounded-xl bg-white/5 border border-white/10 text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-green-500/50 focus:border-green-500/50 transition-all font-mono [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                        </div>
                                    </div>
                                </div>

                                <div class="flex justify-end gap-3 pt-4 mt-6 border-t border-white/10">
                                    <button type="button" @click="showCreateModal = false"
                                        class="px-5 py-2.5 rounded-xl text-sm font-medium text-white/70 hover:text-white hover:bg-white/5 transition-all">Batal</button>
                                    <button type="submit"
                                        class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-purple-600 shadow-lg shadow-purple-500/30 hover:shadow-purple-500/50 hover:scale-[1.02] active:scale-[0.98] transition-all">Simpan
                                        Tagihan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL EDIT --}}
        <div x-show="showEditModal" style="display: none;" class="relative z-50">
            <div x-show="showEditModal" class="fixed inset-0 bg-slate-900/80 backdrop-blur-md" x-transition.opacity>
            </div>
            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center">
                    <div @click.outside="showEditModal = false"
                        class="relative w-full max-w-lg transform overflow-hidden rounded-2xl border border-white/20 bg-slate-900/90 p-6 text-left shadow-2xl backdrop-blur-xl transition-all">

                        <div
                            class="absolute -top-20 -left-20 w-60 h-60 bg-blue-500/30 rounded-full blur-[80px] pointer-events-none">
                        </div>
                        <div
                            class="absolute -bottom-20 -right-20 w-60 h-60 bg-purple-500/30 rounded-full blur-[80px] pointer-events-none">
                        </div>

                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-xl font-bold text-white tracking-wide">Edit Tagihan</h3>
                                <button @click="showEditModal = false"
                                    class="rounded-full p-1 text-white/50 hover:bg-white/10 hover:text-white transition-all">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <form :action="`{{ url('/bills') }}/${billToEdit.id}`" method="POST" class="space-y-5">
                                @csrf @method('PUT')

                                <div>
                                    <label
                                        class="block text-xs font-semibold text-blue-300 uppercase tracking-wider mb-2">Nama
                                        Tagihan</label>
                                    <div class="relative group">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-white/40 group-focus-within:text-blue-400 transition-colors"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </div>
                                        <input type="text" name="name" x-model="billToEdit.name" required
                                            class="w-full pl-11 pr-4 py-3 rounded-xl bg-white/5 border border-white/10 text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <label
                                            class="block text-xs font-semibold text-purple-300 uppercase tracking-wider mb-2">Jatuh
                                            Tempo</label>
                                        <div class="relative group">
                                            <div
                                                class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                                <svg class="h-5 w-5 text-white/40 group-focus-within:text-purple-400 transition-colors"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                            <input type="date" name="due_date" x-model="billToEdit.due_date"
                                                required
                                                class="w-full pl-11 pr-4 py-3 rounded-xl bg-white/5 border border-white/10 text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/50 transition-all [color-scheme:dark]">
                                        </div>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-xs font-semibold text-green-300 uppercase tracking-wider mb-2">Nominal</label>
                                        <div class="relative group">
                                            <div
                                                class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                                <span
                                                    class="text-white/40 font-bold group-focus-within:text-green-400 transition-colors">Rp</span>
                                            </div>
                                            <input type="number" name="amount" x-model="billToEdit.amount" required
                                                class="w-full pl-11 pr-4 py-3 rounded-xl bg-white/5 border border-white/10 text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-green-500/50 focus:border-green-500/50 transition-all font-mono [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                        </div>
                                    </div>
                                </div>

                                <div class="flex justify-end gap-3 pt-4 mt-6 border-t border-white/10">
                                    <button type="button" @click="showEditModal = false"
                                        class="px-5 py-2.5 rounded-xl text-sm font-medium text-white/70 hover:text-white hover:bg-white/5 transition-all">Batal</button>
                                    <button type="submit"
                                        class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-purple-600 shadow-lg shadow-purple-500/30 hover:shadow-purple-500/50 hover:scale-[1.02] active:scale-[0.98] transition-all">Simpan
                                        Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-layouts.app-layout>
