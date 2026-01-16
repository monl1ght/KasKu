<x-layouts.app-layout-anggota>
    {{-- Header Content (Judul Halaman) --}}
    @section('page-title', 'Informasi Organisasi')
    @section('page-subtitle', '-')

    @php
        // Sumber data harus sama dengan PengaturanOrganisasi:
        // $organization: object Organization
        // relasi: bankAccounts, ewallets
        $org = $organization ?? null;

        $bankAccounts = $org?->bankAccounts ?? [];
        $ewallets = $org?->ewallets ?? [];
    @endphp

    <div class="space-y-6">

        {{-- Alert Info --}}
        <div
            class="rounded-xl border border-blue-500/30 bg-gradient-to-br from-blue-500/10 to-cyan-500/5 backdrop-blur-md p-5 shadow-lg">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-10 h-10 bg-blue-500/20 rounded-xl flex items-center justify-center">
                    <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-bold text-blue-300 mb-1">Informasi Organisasi</p>
                    <p class="text-sm text-blue-200/80">
                        Halaman ini hanya menampilkan informasi organisasi dan tujuan pembayaran. Anda tidak dapat mengubah data di sini.
                    </p>
                </div>
            </div>
        </div>

        @if (!$org)
            <div class="rounded-xl border border-red-500/30 bg-red-500/10 p-5 backdrop-blur-md shadow-lg">
                <p class="text-sm text-red-200">
                    Data organisasi tidak ditemukan.
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- LEFT COLUMN - INFORMASI ORGANISASI + LOGO --}}
                <div class="lg:col-span-1 space-y-6">

                    {{-- INFORMASI ORGANISASI & LOGO (MERGED) --}}
                    <div
                        class="rounded-xl border border-white/20 bg-white/10 backdrop-blur-md shadow-lg overflow-hidden hover:border-purple-500/30 transition-all group">
                        <div class="border-b border-white/20 bg-gradient-to-r from-white/5 to-white/10 px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 shadow-lg group-hover:scale-110 transition-transform">
                                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-lg font-bold text-white">Informasi Organisasi</h2>
                                    <p class="text-sm text-white/60 mt-0.5">Identitas dan detail</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 space-y-6">
                            {{-- Logo Section --}}
                            <div class="flex flex-col items-center justify-center text-center pb-4 border-b border-white/10">
                                <div class="relative group">
                                    <div class="absolute -inset-4 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full opacity-20 blur-2xl"></div>

                                    <div class="relative h-32 w-32 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 p-1 shadow-2xl">
                                        <div class="h-full w-full rounded-full bg-slate-900 flex items-center justify-center overflow-hidden">
                                            @if (!empty($org->logo_path))
                                                <img src="{{ asset('storage/' . $org->logo_path) }}" alt="logo"
                                                    class="h-full w-full object-cover">
                                            @else
                                                <span class="text-3xl font-bold text-white">
                                                    {{ strtoupper(substr($org->name ?? 'ORG', 0, 2)) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Organization Details --}}
                            <div class="space-y-4">
                                <div class="p-4 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 transition-all">
                                    <h3 class="text-xs font-semibold text-white/50 uppercase tracking-wider mb-2">Nama Organisasi</h3>
                                    <p class="text-lg font-bold text-white">{{ $org->name ?? '-' }}</p>
                                </div>

                                <div class="p-4 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 transition-all">
                                    <h3 class="text-xs font-semibold text-white/50 uppercase tracking-wider mb-2">Singkatan / Alias</h3>
                                    <p class="text-base text-white">{{ $org->short_name ?? '-' }}</p>
                                </div>

                                <div class="p-4 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 transition-all">
                                    <h3 class="text-xs font-semibold text-white/50 uppercase tracking-wider mb-2">Email</h3>
                                    <p class="text-base text-white break-all">{{ $org->email ?? '-' }}</p>
                                </div>

                                <div class="p-4 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 transition-all">
                                    <h3 class="text-xs font-semibold text-white/50 uppercase tracking-wider mb-2">Nomor Telepon</h3>
                                    <p class="text-base text-white">{{ $org->phone ?? '-' }}</p>
                                </div>

                                <div class="p-4 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 transition-all">
                                    <h3 class="text-xs font-semibold text-white/50 uppercase tracking-wider mb-2">Alamat</h3>
                                    <p class="text-base text-white whitespace-pre-line">{{ $org->address ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- RIGHT COLUMN - REKENING BANK & E-WALLET --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- REKENING BANK (READ ONLY) --}}
                    <div
                        class="rounded-xl border border-white/20 bg-white/10 backdrop-blur-md shadow-lg overflow-hidden hover:border-green-500/30 transition-all group">
                        <div class="border-b border-white/20 bg-gradient-to-r from-white/5 to-white/10 px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 shadow-lg group-hover:scale-110 transition-transform">
                                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-lg font-bold text-white">Rekening Bank</h2>
                                    <p class="text-sm text-white/60 mt-0.5">Tujuan transfer pembayaran</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="space-y-4">
                                @forelse($bankAccounts as $account)
                                    <div
                                        class="rounded-xl border border-white/20 bg-gradient-to-br from-white/5 to-white/10 p-5 hover:border-green-500/30 hover:bg-white/15 transition-all">
                                        <div class="flex items-start gap-4">
                                            <div
                                                class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 shadow-lg flex-shrink-0">
                                                <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                                </svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-start justify-between gap-4 mb-3">
                                                    <div>
                                                        <h3 class="text-sm font-bold text-white/70 mb-1">
                                                            {{ $account->bank_name ?? ($account['bank_name'] ?? 'Bank') }}
                                                        </h3>
                                                        <p class="text-2xl font-bold text-white tracking-wider font-mono">
                                                            {{ $account->number ?? ($account['number'] ?? '-') }}
                                                        </p>
                                                    </div>
                                                    <span
                                                        class="inline-flex items-center gap-1.5 rounded-lg bg-green-500/20 px-3 py-1.5 text-xs font-bold text-green-300 border border-green-500/30">
                                                        <span class="h-2 w-2 rounded-full bg-green-400"></span>
                                                        Aktif
                                                    </span>
                                                </div>

                                                <p class="text-sm text-white/60">
                                                    a.n. <span class="font-semibold text-white">
                                                        {{ $account->owner_name ?? ($account['owner_name'] ?? ($org->name ?? '-')) }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="rounded-xl border-2 border-dashed border-white/10 p-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="h-16 w-16 rounded-full bg-white/5 flex items-center justify-center mb-4">
                                                <svg class="h-8 w-8 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                                </svg>
                                            </div>
                                            <h3 class="text-base font-bold text-white mb-2">Belum Ada Rekening</h3>
                                            <p class="text-sm text-white/60">Organisasi belum menambahkan rekening bank.</p>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- E-WALLET (READ ONLY) --}}
                    <div
                        class="rounded-xl border border-white/20 bg-white/10 backdrop-blur-md shadow-lg overflow-hidden hover:border-purple-500/30 transition-all group">
                        <div class="border-b border-white/20 bg-gradient-to-r from-white/5 to-white/10 px-6 py-5">
                            <div class="flex items-center gap-3">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 shadow-lg group-hover:scale-110 transition-transform">
                                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-lg font-bold text-white">Dompet Digital (E-Wallet)</h2>
                                    <p class="text-sm text-white/60 mt-0.5">Tujuan pembayaran digital</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="space-y-4">
                                @forelse($ewallets as $ew)
                                    <div
                                        class="rounded-xl border border-white/20 bg-gradient-to-br from-white/5 to-white/10 p-5 hover:border-purple-500/30 hover:bg-white/15 transition-all">
                                        <div class="flex items-start gap-4">
                                            <div
                                                class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 shadow-lg flex-shrink-0">
                                                <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-start justify-between gap-4 mb-3">
                                                    <div>
                                                        <h3 class="text-sm font-bold text-white/70 mb-1">
                                                            {{ strtoupper($ew->provider ?? ($ew['provider'] ?? 'E-WALLET')) }}
                                                        </h3>
                                                        <p class="text-2xl font-bold text-white tracking-wider font-mono">
                                                            {{ $ew->number ?? ($ew['number'] ?? '-') }}
                                                        </p>
                                                    </div>
                                                    <span
                                                        class="inline-flex items-center gap-1.5 rounded-lg bg-green-500/20 px-3 py-1.5 text-xs font-bold text-green-300 border border-green-500/30">
                                                        <span class="h-2 w-2 rounded-full bg-green-400"></span>
                                                        Aktif
                                                    </span>
                                                </div>

                                                <p class="text-sm text-white/60">
                                                    a.n. <span class="font-semibold text-white">
                                                        {{ $ew->owner_name ?? ($ew['owner_name'] ?? ($org->name ?? '-')) }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="rounded-xl border-2 border-dashed border-white/10 p-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="h-16 w-16 rounded-full bg-white/5 flex items-center justify-center mb-4">
                                                <svg class="h-8 w-8 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            <h3 class="text-base font-bold text-white mb-2">Belum Ada E-Wallet</h3>
                                            <p class="text-sm text-white/60">Organisasi belum menambahkan e-wallet.</p>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        @endif

    </div>
</x-layouts.app-layout-anggota>