<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pilih Akses - KasKu</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        @keyframes pulse-slow {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .animate-pulse-slow {
            animation: pulse-slow 3s ease-in-out infinite;
        }

        ::-webkit-scrollbar {
            width: 0px;
            background: transparent;
        }
    </style>
</head>

<!-- NOTE: backend aman, cuma ubah kelas body + background layer -->

<body class="min-h-screen bg-slate-900 antialiased selection:bg-blue-500 selection:text-white overflow-x-hidden">

    {{-- GLOBAL Background (nyambung sampai bawah) --}}
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <!-- base gradient -->
        <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900"></div>

        <!-- blobs -->
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-blue-500/30 rounded-full blur-3xl animate-pulse-slow"></div>
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-purple-500/30 rounded-full blur-3xl animate-pulse-slow"
            style="animation-delay: 1.5s;"></div>

        <!-- grid -->
        <div
            class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff05_1px,transparent_1px),linear-gradient(to_bottom,#ffffff05_1px,transparent_1px)] bg-[size:4rem_4rem]">
        </div>
    </div>

    {{-- Navbar --}}
    <nav class="absolute top-0 left-0 right-0 z-50 p-6 flex justify-between items-center">
        <div class="flex items-center gap-3">
            <div
                class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold shadow-lg">
                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
            </div>
            <div class="hidden sm:block">
                <p class="text-white font-medium text-sm">Halo, {{ Auth::user()->name ?? 'User' }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="px-4 py-2 rounded-full bg-white/5 border border-white/10 text-white/70 hover:bg-white/10 hover:text-red-400 transition-all text-sm">
                Keluar
            </button>
        </form>
    </nav>

    <div class="relative min-h-screen flex flex-col items-center justify-center p-4 py-24 sm:p-8">

        {{-- SECTION 1: Pilih organisasi --}}
        @if (isset($organizations) && $organizations->count() > 0)
            <div class="w-full max-w-5xl mb-12">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold text-white">Organisasi Anda</h2>
                    <p class="text-white/60">Silakan pilih organisasi untuk masuk ke dashboard.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($organizations as $org)
                        <div
                            class="group bg-white/10 backdrop-blur-md border border-white/10 p-6 rounded-2xl hover:bg-white/20 transition-all cursor-pointer relative overflow-hidden flex flex-col">
                            <div class="absolute top-0 right-0 p-4 opacity-30">
                                <svg class="w-12 h-12 text-white/10 group-hover:text-white/20 transition-all"
                                    fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 2L2 7l10 5 10-5-10-5zm0 9l2.5-1.25L12 8.5l-2.5 1.25L12 11zm0 2.5l-5-2.5-5 2.5L12 22l10-8.5-5-2.5-5 2.5z" />
                                </svg>
                            </div>

                            <h3 class="text-xl font-bold text-white mb-1 truncate pr-8">{{ $org->name }}</h3>
                            <p class="text-sm text-white/60 mb-4 font-mono">Kode: {{ $org->code }}</p>

                            <div class="mt-auto flex items-center justify-between">
                                <span
                                    class="text-xs px-2 py-1 rounded bg-blue-500/20 text-blue-300 border border-blue-500/30 font-medium">
                                    {{ $org->pivot->role == 'admin' ? 'Bendahara' : 'Anggota' }}
                                </span>

                                <a href="{{ route('organization.access', $org->id) }}"
                                    class="px-4 py-2 bg-white text-slate-900 font-bold rounded-lg hover:scale-105 transition-transform text-sm shadow-lg hover:shadow-white/20">
                                    Masuk →
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="relative flex py-12 items-center">
                    <div class="flex-grow border-t border-white/10"></div>
                    <span class="flex-shrink-0 mx-4 text-white/30 text-sm uppercase tracking-widest">Atau buat
                        baru</span>
                    <div class="flex-grow border-t border-white/10"></div>
                </div>
            </div>
        @else
            <div class="text-center mb-12 space-y-2 max-w-2xl mx-auto">
                <h1 class="text-3xl sm:text-4xl font-bold text-white">Selamat Datang</h1>
                <p class="text-white/60">Bergabung dengan Organisasi sebagai anggota atau buat organisasi sendiri sebagai bendahara.</p>
            </div>
        @endif

        {{-- SECTION 2: MENU BUAT / GABUNG --}}
        <div class="grid md:grid-cols-2 gap-6 w-full max-w-5xl">

            {{-- KARTU 1: BUAT ORGANISASI BARU --}}
            <div class="group relative bg-white/5 hover:bg-white/10 backdrop-blur-xl border border-white/10 hover:border-blue-500/50 rounded-3xl p-8 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-blue-500/20 flex flex-col"
                x-data="{
                    createMode: false,
                    banks: [{ bank_name: '', number: '', owner_name: '' }],
                    ewallets: [{ type: '', number: '', owner_name: '' }],
                    addBank() { this.banks.push({ bank_name: '', number: '', owner_name: '' }) },
                    removeBank(i) { if (this.banks.length > 1) this.banks.splice(i, 1) },
                    addEwallet() { this.ewallets.push({ type: '', number: '', owner_name: '' }) },
                    removeEwallet(i) { if (this.ewallets.length > 1) this.ewallets.splice(i, 1) }
                }">
                <div
                    class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-purple-500/10 opacity-0 group-hover:opacity-100 transition-opacity rounded-3xl">
                </div>

                <div class="relative z-10">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center mb-6 shadow-lg transition-transform duration-300"
                        :class="createMode ? 'scale-100' : 'group-hover:scale-110'">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>

                    <h3 class="text-2xl font-bold text-white mb-2">Buat Kas Organisasi Baru</h3>
                </div>

                <div x-show="!createMode" class="relative z-10 flex-1 flex flex-col">
                    <p class="text-white/60 mb-6 leading-relaxed">
                        Cocok untuk <strong>Bendahara</strong>. Buat wadah baru untuk mengelola kas, atur anggota, dan
                        tentukan tagihan.
                    </p>
                    <div class="mt-auto">
                        <button type="button" @click="createMode = true"
                            class="block w-full py-4 text-center rounded-xl bg-gradient-to-r from-blue-500 to-purple-600 text-white font-semibold shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 transition-all hover:scale-[1.02] active:scale-[0.98]">
                            Buat Kas Sekarang
                        </button>
                    </div>
                </div>

                {{-- FORMULIR PEMBUATAN --}}
                <form x-show="createMode" x-transition:enter="transition ease-out duration-300 delay-100"
                    x-transition:enter-start="opacity-0 translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0" action="{{ route('organizations.store') }}"
                    method="POST" class="relative z-10 flex-1 flex flex-col" x-ref="createForm">
                    @csrf

                    {{-- Flash messages --}}
                    <div class="mb-4">
                        @if (session('success'))
                            <div class="mb-4 p-3 rounded-lg bg-green-600/20 text-green-200">{{ session('success') }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="mb-4 p-3 rounded-lg bg-red-600/20 text-red-200">{{ session('error') }}</div>
                        @endif
                    </div>

                    <div class="space-y-6">
                        {{-- Nama Organisasi --}}
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-white/80 uppercase tracking-wider">Nama
                                Organisasi</label>
                            <input type="text" name="name" placeholder="Contoh: Kas Kelas 12 RPL" required
                                class="w-full bg-white/5 border-2 border-white/10 rounded-2xl px-5 py-4 text-white placeholder-white/40 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition-all text-base">
                        </div>

                        {{-- BANKS Section dengan Custom Dropdown --}}
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <label class="block text-sm font-bold text-white/80 uppercase tracking-wider">
                                    Rekening / Bank
                                    <span class="text-xs text-white/40 font-normal normal-case ml-1">(opsional)</span>
                                </label>
                                <button type="button" @click="addBank()"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 hover:bg-white/15 border border-white/20 rounded-xl text-white text-sm font-semibold transition-all hover:scale-105">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    Tambah
                                </button>
                            </div>

                            <div class="space-y-3">
                                <template x-for="(bank, index) in banks" :key="index">
                                    <div
                                        class="p-4 bg-white/5 border border-white/10 rounded-2xl space-y-3 hover:bg-white/8 transition-all">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <!-- Custom Dropdown untuk Nama Bank -->
                                            <div x-data="{ open: false }">
                                                <label class="block text-xs font-medium text-white/60 mb-2">Nama
                                                    Bank</label>

                                                <!-- Hidden input untuk form submission -->
                                                <input type="hidden" x-bind:name="`banks[${index}][bank_name]`"
                                                    x-model="bank.bank_name">

                                                <!-- Custom Dropdown Button -->
                                                <div class="relative">
                                                    <button type="button" @click="open = !open"
                                                        @click.away="open = false"
                                                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all text-sm text-left flex items-center justify-between">
                                                        <span x-text="bank.bank_name || 'Pilih Bank'"
                                                            :class="{ 'text-white/30': !bank.bank_name }"></span>
                                                        <svg class="w-5 h-5 text-white/50 transition-transform"
                                                            :class="{ 'rotate-180': open }" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M19 9l-7 7-7-7" />
                                                        </svg>
                                                    </button>

                                                    <!-- Dropdown Menu -->
                                                    <div x-show="open"
                                                        x-transition:enter="transition ease-out duration-200"
                                                        x-transition:enter-start="opacity-0 translate-y-1"
                                                        x-transition:enter-end="opacity-100 translate-y-0"
                                                        x-transition:leave="transition ease-in duration-150"
                                                        x-transition:leave-start="opacity-100 translate-y-0"
                                                        x-transition:leave-end="opacity-0 translate-y-1"
                                                        class="absolute z-50 w-full mt-2 bg-gray-800/95 backdrop-blur-xl border border-white/20 rounded-xl shadow-2xl overflow-hidden"
                                                        style="display: none;">
                                                        <div class="py-1">
                                                            <button type="button"
                                                                @click="bank.bank_name = ''; open = false"
                                                                class="w-full px-4 py-3 text-left text-white/40 hover:bg-white/10 transition-colors text-sm">
                                                                Pilih Bank
                                                            </button>
                                                            <button type="button"
                                                                @click="bank.bank_name = 'BCA'; open = false"
                                                                class="w-full px-4 py-3 text-left text-white hover:bg-white/10 transition-colors text-sm font-medium">
                                                                <span class="inline-flex items-center gap-2">
                                                                    <span
                                                                        class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                                                    BCA
                                                                </span>
                                                            </button>
                                                            <button type="button"
                                                                @click="bank.bank_name = 'Mandiri'; open = false"
                                                                class="w-full px-4 py-3 text-left text-white hover:bg-white/10 transition-colors text-sm font-medium">
                                                                <span class="inline-flex items-center gap-2">
                                                                    <span
                                                                        class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                                                                    Bank Mandiri
                                                                </span>
                                                            </button>
                                                            <button type="button"
                                                                @click="bank.bank_name = 'BRI'; open = false"
                                                                class="w-full px-4 py-3 text-left text-white hover:bg-white/10 transition-colors text-sm font-medium">
                                                                <span class="inline-flex items-center gap-2">
                                                                    <span
                                                                        class="w-2 h-2 bg-blue-600 rounded-full"></span>
                                                                    BRI
                                                                </span>
                                                            </button>
                                                            <button type="button"
                                                                @click="bank.bank_name = 'BNI'; open = false"
                                                                class="w-full px-4 py-3 text-left text-white hover:bg-white/10 transition-colors text-sm font-medium">
                                                                <span class="inline-flex items-center gap-2">
                                                                    <span
                                                                        class="w-2 h-2 bg-orange-500 rounded-full"></span>
                                                                    BNI
                                                                </span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div>
                                                <label class="block text-xs font-medium text-white/60 mb-2">No.
                                                    Rekening</label>
                                                <input x-bind:name="`banks[${index}][number]`" x-model="bank.number"
                                                    placeholder="Contoh: 1234567890"
                                                    class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-white/30 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all text-sm font-mono">
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-medium text-white/60 mb-2">Atas
                                                Nama</label>
                                            <div class="flex gap-2">
                                                <input x-bind:name="`banks[${index}][owner_name]`"
                                                    x-model="bank.owner_name"
                                                    placeholder="Contoh: Bendahara Kelas 12 RPL"
                                                    class="flex-1 bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-white/30 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all text-sm">
                                                <button type="button" @click="removeBank(index)"
                                                    x-show="banks.length > 1"
                                                    class="px-5 py-3 rounded-xl bg-red-500/20 hover:bg-red-500/30 border border-red-500/30 text-red-300 font-semibold text-sm transition-all hover:scale-105 flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- E-WALLETS Section dengan Custom Dropdown --}}
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <label class="block text-sm font-bold text-white/80 uppercase tracking-wider">
                                    E-Wallet
                                    <span class="text-xs text-white/40 font-normal normal-case ml-1">(opsional)</span>
                                </label>
                                <button type="button" @click="addEwallet()"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 hover:bg-white/15 border border-white/20 rounded-xl text-white text-sm font-semibold transition-all hover:scale-105">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    Tambah
                                </button>
                            </div>

                            <div class="space-y-3">
                                <template x-for="(ew, i) in ewallets" :key="i">
                                    <div
                                        class="p-4 bg-white/5 border border-white/10 rounded-2xl space-y-3 hover:bg-white/8 transition-all">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <!-- Custom Dropdown untuk Tipe E-Wallet -->
                                            <div x-data="{ open: false }">
                                                <label class="block text-xs font-medium text-white/60 mb-2">Tipe
                                                    E-Wallet</label>

                                                <!-- Hidden input untuk form submission -->
                                                <input type="hidden" x-bind:name="`ewallets[${i}][type]`"
                                                    x-model="ew.type">

                                                <!-- Custom Dropdown Button -->
                                                <div class="relative">
                                                    <button type="button" @click="open = !open"
                                                        @click.away="open = false"
                                                        class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all text-sm text-left flex items-center justify-between">
                                                        <span
                                                            x-text="ew.type ? (ew.type === 'gopay' ? 'GoPay' : ew.type === 'ovo' ? 'OVO' : ew.type === 'dana' ? 'DANA' : ew.type === 'shopeepay' ? 'ShopeePay' : 'Pilih E-Wallet') : 'Pilih E-Wallet'"
                                                            :class="{ 'text-white/30': !ew.type }"></span>
                                                        <svg class="w-5 h-5 text-white/50 transition-transform"
                                                            :class="{ 'rotate-180': open }" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M19 9l-7 7-7-7" />
                                                        </svg>
                                                    </button>

                                                    <!-- Dropdown Menu -->
                                                    <div x-show="open"
                                                        x-transition:enter="transition ease-out duration-200"
                                                        x-transition:enter-start="opacity-0 translate-y-1"
                                                        x-transition:enter-end="opacity-100 translate-y-0"
                                                        x-transition:leave="transition ease-in duration-150"
                                                        x-transition:leave-start="opacity-100 translate-y-0"
                                                        x-transition:leave-end="opacity-0 translate-y-1"
                                                        class="absolute z-50 w-full mt-2 bg-gray-800/95 backdrop-blur-xl border border-white/20 rounded-xl shadow-2xl overflow-hidden"
                                                        style="display: none;">
                                                        <div class="py-1">
                                                            <button type="button" @click="ew.type = ''; open = false"
                                                                class="w-full px-4 py-3 text-left text-white/40 hover:bg-white/10 transition-colors text-sm">
                                                                Pilih E-Wallet
                                                            </button>
                                                            <button type="button"
                                                                @click="ew.type = 'gopay'; open = false"
                                                                class="w-full px-4 py-3 text-left text-white hover:bg-white/10 transition-colors text-sm font-medium">
                                                                <span class="inline-flex items-center gap-2">
                                                                    <span
                                                                        class="w-2 h-2 bg-green-500 rounded-full"></span>
                                                                    GoPay
                                                                </span>
                                                            </button>
                                                            <button type="button"
                                                                @click="ew.type = 'ovo'; open = false"
                                                                class="w-full px-4 py-3 text-left text-white hover:bg-white/10 transition-colors text-sm font-medium">
                                                                <span class="inline-flex items-center gap-2">
                                                                    <span
                                                                        class="w-2 h-2 bg-purple-500 rounded-full"></span>
                                                                    OVO
                                                                </span>
                                                            </button>
                                                            <button type="button"
                                                                @click="ew.type = 'dana'; open = false"
                                                                class="w-full px-4 py-3 text-left text-white hover:bg-white/10 transition-colors text-sm font-medium">
                                                                <span class="inline-flex items-center gap-2">
                                                                    <span
                                                                        class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                                                    DANA
                                                                </span>
                                                            </button>
                                                            <button type="button"
                                                                @click="ew.type = 'shopeepay'; open = false"
                                                                class="w-full px-4 py-3 text-left text-white hover:bg-white/10 transition-colors text-sm font-medium">
                                                                <span class="inline-flex items-center gap-2">
                                                                    <span
                                                                        class="w-2 h-2 bg-orange-500 rounded-full"></span>
                                                                    ShopeePay
                                                                </span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div>
                                                <label
                                                    class="block text-xs font-medium text-white/60 mb-2">Nomor/ID</label>
                                                <input x-bind:name="`ewallets[${i}][number]`" x-model="ew.number"
                                                    placeholder="Contoh: 081234567890"
                                                    class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-white/30 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all text-sm font-mono">
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-medium text-white/60 mb-2">Atas
                                                Nama</label>
                                            <div class="flex gap-2">
                                                <input x-bind:name="`ewallets[${i}][owner_name]`"
                                                    x-model="ew.owner_name"
                                                    placeholder="Contoh: Bendahara Kelas 12 RPL"
                                                    class="flex-1 bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-white/30 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all text-sm">
                                                <button type="button" @click="removeEwallet(i)"
                                                    x-show="ewallets.length > 1"
                                                    class="px-5 py-3 rounded-xl bg-red-500/20 hover:bg-red-500/30 border border-red-500/30 text-red-300 font-semibold text-sm transition-all hover:scale-105 flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex gap-4 mt-8 pt-6 border-t border-white/10">
                            <button type="button" @click="createMode = false"
                                class="flex-1 py-4 rounded-2xl border-2 border-white/20 text-white font-semibold hover:bg-white/5 hover:border-white/30 transition-all text-base">
                                Batal
                            </button>

                            <button type="button" @click="$refs.createForm.submit()"
                                class="flex-[2] py-4 rounded-2xl bg-gradient-to-r from-blue-500 to-purple-600 text-white font-bold shadow-2xl shadow-blue-500/40 hover:shadow-blue-500/60 hover:scale-105 transition-all text-base flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Simpan & Buat
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- KARTU 2: GABUNG ORGANISASI --}}
            <div class="group relative bg-white/5 hover:bg-white/10 backdrop-blur-xl border border-white/10 hover:border-emerald-500/50 rounded-3xl p-8 transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl hover:shadow-emerald-500/20 flex flex-col"
                x-data="{ code: '' }">
                <div
                    class="absolute inset-0 bg-gradient-to-br from-emerald-500/10 to-teal-500/10 opacity-0 group-hover:opacity-100 transition-opacity rounded-3xl">
                </div>

                <div class="relative z-10 flex-1">
                    <div
                        class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center mb-6 shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>

                    <h3 class="text-2xl font-bold text-white mb-2">Masuk Sebagai Anggota</h3>
                    <p class="text-white/60 mb-6 leading-relaxed">Punya <strong>Kode Unik</strong>? Masukkan di sini
                        untuk bergabung.</p>

                    @if (session('info'))
                        <div class="mb-4 p-4 rounded-xl bg-yellow-500/20 text-yellow-300">{{ session('info') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 p-4 rounded-xl bg-red-500/20 text-red-300">{{ session('error') }}</div>
                    @endif
                    @if (session('success'))
                        <div class="mb-4 p-4 rounded-xl bg-green-500/20 text-green-300">{{ session('success') }}</div>
                    @endif

                    <form action="{{ route('organization.join') }}" method="POST" class="space-y-4">
                        @csrf

                        <label class="text-xs font-bold text-white/70 uppercase tracking-wider ml-1">Kode
                            Organisasi</label>
                        <input type="text" name="code" x-model="code" placeholder="Contoh: KASKU-8829"
                            required
                            class="w-full bg-white/5 border border-white/10 rounded-xl px-5 py-4 text-white text-center text-lg uppercase font-mono">
                        <button type="submit" :disabled="!code"
                            class="w-full py-4 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 text-white font-semibold">
                            Gabung Sekarang
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

</body>

</html>
