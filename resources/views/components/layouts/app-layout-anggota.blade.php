<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- TAMBAHAN CSS KHUSUS UNTUK ANIMASI HALUS --}}
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        

        /* Animasi garis bawah melebar dari 0 ke 100% */
        @keyframes expandWidth {
            from {
                width: 0;
                opacity: 0;
            }

            to {
                width: 100%;
                opacity: 1;
            }
        }

        /* Class helper untuk memanggil animasi */
        .animate-expand {
            animation: expandWidth 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
    </style>

    @stack('styles')
</head>

<body class="min-h-screen bg-fixed bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 antialiased"
    x-data="{ sidebarOpen: false }">
    {{-- [BARU] LOGIKA PHP LANGSUNG DI VIEW --}}
    @php
        // 1. Cek apakah ada ID organisasi di sesi login
        $orgIdLayout = session('active_organization_id');

        // 2. Jika ada, cari datanya di database. Jika tidak, kosong (null)
        $activeOrgLayout = $orgIdLayout ? \App\Models\Organization::find($orgIdLayout) : null;

        // 3. Tentukan Nama Organisasi (Jika ada tampilkan nama, jika tidak tampilkan default)
        $displayName = $activeOrgLayout ? $activeOrgLayout->name : 'Pilih Organisasi';

        // 4. Tentukan Role User (Bendahara/Anggota)
        $displayRole = 'Anggota'; // Default
        if ($activeOrgLayout && Auth::check()) {
            // Cek tabel penghubung (pivot) untuk lihat role user ini
            $membership = $activeOrgLayout->users()->where('user_id', Auth::id())->first();
            if ($membership && $membership->pivot->role == 'admin') {
                $displayRole = 'Bendahara';
            }
        }
    @endphp

    <div class="flex h-full">

        {{-- Mobile Overlay --}}
        <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="sidebarOpen = false"
            class="fixed inset-0 z-40 bg-slate-900/80 backdrop-blur-sm lg:hidden" style="display: none;"></div>

        {{-- SIDEBAR --}}
        <aside
            class="fixed inset-y-0 left-0 z-50 w-72 transform border-r border-white/20 bg-white/10 shadow-2xl backdrop-blur-md transition-transform duration-300 ease-in-out lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

            {{-- Sidebar Header --}}
            <div class="flex h-20 items-center justify-between px-6">
                <a href="{{ route('member.dashboard') }}" class="flex items-center gap-3">
                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 shadow-lg">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-white">KasKu</span>
                </a>
                <button @click="sidebarOpen = false" class="rounded-lg p-2 text-white/80 hover:bg-white/10 lg:hidden">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Sidebar Navigation --}}
            <nav class="space-y-2 px-4 py-6">

                {{-- 1. Dashboard --}}
                <a href="{{ route('member.dashboard') }}"
                    class="group relative flex items-center gap-3 overflow-hidden rounded-xl px-4 py-3 text-white transition-all duration-300
        after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 after:bg-gradient-to-r after:from-blue-500 after:to-purple-600 after:opacity-0 after:transition-all after:duration-300
        hover:bg-white/20 hover:after:w-full hover:after:opacity-100
        {{ request()->is('member/dashboard') ? 'bg-white/20 hover:bg-white/30 after:w-full after:opacity-100 animate-expand' : '' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="text-sm font-medium">Dashboard</span>
                </a>

                {{-- 2. Pembayaran Kas --}}
                <a href="{{ route('member.PembayaranKas') }}"
                    class="group relative flex items-center gap-3 overflow-hidden rounded-xl px-4 py-3 text-white transition-all duration-300
        after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 after:bg-gradient-to-r after:from-blue-500 after:to-purple-600 after:opacity-0 after:transition-all after:duration-300
        hover:bg-white/20 hover:after:w-full hover:after:opacity-100
        {{ request()->is('member/PembayaranKas*') ? 'bg-white/20 hover:bg-white/30 after:w-full after:opacity-100 animate-expand' : '' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span class="flex-1 text-sm font-medium whitespace-nowrap">Pembayaran Kas</span>
                </a>

                {{-- 3. Riwayat Transaksi Saya --}}
                <a href="{{ route('member.RiwayatTransaksi') }}"
                    class="group relative flex items-center gap-3 overflow-hidden rounded-xl px-4 py-3 text-white transition-all duration-300
        after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 after:bg-gradient-to-r after:from-blue-500 after:to-purple-600 after:opacity-0 after:transition-all after:duration-300
        hover:bg-white/20 hover:after:w-full hover:after:opacity-100
        {{ request()->is('member/RiwayatTransaksi*') ? 'bg-white/20 hover:bg-white/30 after:w-full after:opacity-100 animate-expand' : '' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="flex-1 text-sm font-medium whitespace-nowrap">Riwayat Transaksi</span>
                </a>

                {{-- Informasi Organisasi --}}
                <a href="{{ route('member.PengaturanProfil') }}"
                    class="group relative flex items-center gap-3 overflow-hidden rounded-xl px-4 py-3 text-white transition-all duration-300
    after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 after:bg-gradient-to-r after:from-blue-500 after:to-purple-600 after:opacity-0 after:transition-all after:duration-300
    hover:bg-white/20 hover:after:w-full hover:after:opacity-100
    {{ request()->routeIs('member.PengaturanProfil') ? 'bg-white/20 hover:bg-white/30 after:w-full after:opacity-100 animate-expand' : '' }}">

                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 21h18M5 21V7a2 2 0 012-2h10a2 2 0 012 2v14M9 21V12h6v9M9 9h.01M15 9h.01" />
                    </svg>

                    <span class="flex-1 text-sm font-medium whitespace-nowrap">
                        Informasi Organisasi
                    </span>
                </a>

                {{-- Log Aktivitas Pembayaran Admin --}}
                <a href="{{ route('member.admin_payment_log') }}"
                    class="group relative flex items-center gap-3 rounded-xl px-4 py-3 text-white transition-all duration-300
          hover:bg-white/20
          after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 after:bg-gradient-to-r after:from-blue-500 after:to-purple-600
          after:opacity-0 after:transition-all after:duration-300
          hover:after:w-full hover:after:opacity-100
          {{ request()->routeIs('member.admin_payment_log') ? 'bg-white/20 hover:bg-white/30 after:w-full after:opacity-100 animate-expand' : '' }}">

                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5h6m-6 0a2 2 0 00-2 2v12a2 2 0 002 2h6a2 2 0 002-2V7a2 2 0 00-2-2m-6 0a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>

                    <span class="flex-1 min-w-0 text-sm font-medium leading-tight break-words">
                        Log Aktivitas Bendahara
                    </span>
                </a>


            </nav>

            {{-- User Profile (Mobile) --}}
            <div class="absolute bottom-0 left-0 right-0 border-t border-white/20 p-4 lg:hidden">
                <div class="flex w-full items-center gap-3 rounded-xl p-3">
                    <div class="h-10 w-10 overflow-hidden rounded-full bg-gradient-to-br from-blue-500 to-purple-600">
                        <div class="flex h-full w-full items-center justify-center text-sm font-semibold text-white">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                        </div>
                    </div>
                    <div class="flex-1 overflow-hidden text-left">
                        <p class="truncate text-sm font-medium text-white">{{ auth()->user()->name ?? 'User' }}</p>
                    </div>
                </div>
            </div>
        </aside>

        {{-- MAIN CONTENT AREA --}}
        <div class="flex flex-1 flex-col lg:pl-72">

            {{-- HEADER: FIX Z-INDEX HERE --}}
            <header class="relative z-30 border-b border-white/20 bg-white/10 backdrop-blur-md">
                <div class="flex h-20 items-center gap-4 px-4 sm:px-6 lg:px-8">

                    {{-- Mobile Menu Button --}}
                    <button @click="sidebarOpen = true" class="rounded-lg p-2 text-white hover:bg-white/10 lg:hidden">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    {{-- Page Title --}}
                    <div class="flex-1">
                        <h1 class="text-2xl font-bold text-white">
                            @yield('page-title', 'Dashboard')
                        </h1>
                        <p class="hidden text-sm text-white/60 sm:block">
                            @yield('page-subtitle', 'Selamat datang di KasKu')
                        </p>
                    </div>

                    {{-- Header Right Actions (POSISI YANG BENAR DI SINI) --}}
                    <div class="flex items-center gap-4">

                        {{-- 1. BAGIAN NAMA ORGANISASI (HANYA SATU INI SAJA) --}}
                        <div class="hidden text-right md:block">
                            <p class="text-sm font-bold text-white">
                                {{ $displayName }}
                            </p>
                            <span
                                class="inline-flex items-center rounded-md bg-purple-400/10 px-2 py-1 text-xs font-medium text-purple-400 ring-1 ring-inset ring-purple-400/20">
                                {{ $displayRole }}
                            </span>
                        </div>

                        {{-- 2. PEMBATAS VERTIKAL --}}
                        <div class="hidden h-8 w-px bg-white/10 md:block"></div>

                        {{-- 3. PROFILE DROPDOWN --}}
                        <div class="relative" x-data="{ profileOpen: false }">
                            <button @click="profileOpen = !profileOpen" @click.outside="profileOpen = false"
                                class="flex items-center gap-2 rounded-full border border-white/10 bg-white/5 p-1 pr-3 text-white transition-all hover:bg-white/10 focus:ring-2 focus:ring-purple-500/50">

                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-purple-600 shadow-md">
                                    <span class="text-xs font-bold text-white">
                                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                                    </span>
                                </div>

                                <span class="hidden text-sm font-medium sm:block">
                                    {{ explode(' ', auth()->user()->name ?? 'User')[0] }}
                                </span>

                                <svg class="h-4 w-4 text-white/70 transition-transform duration-200"
                                    :class="profileOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            {{-- DROPDOWN MENU ISI --}}
                            <div x-show="profileOpen" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="transform opacity-0 scale-95 translate-y-2"
                                x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
                                x-transition:leave-end="transform opacity-0 scale-95 translate-y-2"
                                class="absolute right-0 mt-3 w-56 origin-top-right overflow-hidden rounded-2xl border border-white/20 bg-[#0f172a] shadow-[0_0_20px_rgba(0,0,0,0.5)] focus:outline-none z-50 ring-1 ring-white/5"
                                style="display: none;">

                                <div class="border-b border-white/10 px-4 py-3 mb-1 bg-white/5">
                                    <p class="text-xs font-medium text-white/50">Masuk sebagai</p>
                                    <p class="truncate text-sm font-bold text-white tracking-wide">
                                        {{ auth()->user()->name ?? 'User' }}</p>
                                </div>

                                <div class="space-y-1">
                                    <a href="{{ route('profil.saya.edit') }}"
                                        class="group flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm text-white/80 transition-all duration-200 hover:bg-white/10 hover:text-white hover:pl-5">
                                        <svg class="h-4 w-4 text-white/50 transition-colors group-hover:text-blue-400"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                            </path>
                                        </svg>
                                        Profil Saya
                                    </a>
                                </div>

                                <div class="mt-1 border-t border-white/10 pt-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="group flex w-full items-center gap-3 rounded-xl px-4 py-2.5 text-left text-sm text-red-400 transition-all duration-200 hover:bg-red-500/10 hover:text-red-300 hover:pl-5">
                                            <svg class="h-4 w-4 text-red-400/50 transition-colors group-hover:text-red-400"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                                </path>
                                            </svg>
                                            Keluar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </header>

            {{-- Main Content --}}
            <main class="flex-1 p-6 z-0"> {{-- z-0 added for clarity, though default is auto --}}

                {{-- Flash Messages --}}
                @if (session('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                        class="mb-6 overflow-hidden rounded-xl border border-white/20 bg-white/10 p-4 shadow-lg backdrop-blur-md">
                        <div class="flex items-start gap-3">
                            <svg class="h-5 w-5 flex-shrink-0 text-green-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="flex-1 text-sm font-medium text-white">{{ session('success') }}</p>
                            <button @click="show = false">
                                <svg class="h-4 w-4 text-white/80" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                        class="mb-6 overflow-hidden rounded-xl border border-white/20 bg-white/10 p-4 shadow-lg backdrop-blur-md">
                        <div class="flex items-start gap-3">
                            <svg class="h-5 w-5 flex-shrink-0 text-red-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="flex-1 text-sm font-medium text-white">{{ session('error') }}</p>
                            <button @click="show = false">
                                <svg class="h-4 w-4 text-white/80" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif

                {{ $slot ?? '' }}
                @yield('content')

            </main>

        </div>
    </div>

    @stack('scripts')
</body>

</html>
