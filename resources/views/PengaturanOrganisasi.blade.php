<x-layouts.app-layout>

    @section('page-title', 'Pengaturan Organisasi')
    @section('page-subtitle', 'Konfigurasi profil dan preferensi sistem')

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
                    <p class="text-sm font-bold text-blue-300 mb-1">Informasi Penting</p>
                    <p class="text-sm text-blue-200/80">Pastikan informasi rekening yang Anda masukkan sudah benar. Data
                        ini akan ditampilkan kepada anggota sebagai tujuan transfer pembayaran.</p>
                </div>
            </div>
        </div>

        {{-- SUCCESS MESSAGE --}}
        @if (session('success'))
            <div
                class="rounded-xl bg-gradient-to-br from-emerald-500/20 to-green-500/10 border border-emerald-500/30 p-5 backdrop-blur-md shadow-lg animate-pulse-once">
                <div class="flex items-start gap-3">
                    <svg class="h-5 w-5 text-emerald-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm font-medium text-emerald-300">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        {{-- ERROR SUMMARY --}}
        @if ($errors->any())
            <div
                class="rounded-xl bg-gradient-to-br from-red-500/20 to-pink-500/10 border border-red-500/30 p-5 backdrop-blur-md shadow-lg">
                <div class="flex items-start gap-3">
                    <svg class="h-5 w-5 text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm font-medium text-red-300">Terdapat error pada input. Periksa form di bawah.</p>
                </div>
            </div>
        @endif

        @php
            $org = $organization ?? null;
            $showEdit = (request()->has('edit') || $errors->any()) && !session('success');
        @endphp

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- LEFT COLUMN: Organization Profile --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- 1. INFORMASI ORGANISASI --}}
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
                                <p class="text-sm text-white/60 mt-0.5">Identitas dan detail organisasi Anda</p>
                            </div>
                        </div>
                    </div>

                    {{-- EDIT MODE --}}
                    @if ($showEdit)
                        <form method="POST" action="{{ route('organization.update') }}" enctype="multipart/form-data"
                            class="p-6 space-y-5">

                            @csrf
                            @method('PUT')

                            {{-- Nama Organisasi --}}
                            <div>
                                <label class="block text-xs font-semibold text-white/70 uppercase tracking-wider mb-2">
                                    Nama Organisasi <span class="text-red-400">*</span>
                                </label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-white/40 group-focus-within:text-blue-400 transition-colors"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                    <input name="name" type="text" value="{{ old('name', $org->name ?? '') }}"
                                        class="w-full pl-12 pr-4 py-3 rounded-xl border border-white/20 bg-white/5 text-white placeholder-white/40 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all">
                                </div>
                                @error('name')
                                    <p class="text-xs text-red-400 mt-2 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Singkatan --}}
                            <div>
                                <label
                                    class="block text-xs font-semibold text-white/70 uppercase tracking-wider mb-2">Singkatan/Alias</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-white/40 group-focus-within:text-purple-400 transition-colors"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                        </svg>
                                    </div>
                                    <input name="short_name" type="text"
                                        value="{{ old('short_name', $org->short_name ?? '') }}"
                                        class="w-full pl-12 pr-4 py-3 rounded-xl border border-white/20 bg-white/5 text-white placeholder-white/40 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50 transition-all">
                                </div>
                                @error('short_name')
                                    <p class="text-xs text-red-400 mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div>
                                <label class="block text-xs font-semibold text-white/70 uppercase tracking-wider mb-2">
                                    Email Organisasi <span class="text-red-400">*</span>
                                </label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-white/40 group-focus-within:text-blue-400 transition-colors"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <input name="email" type="email"
                                        value="{{ old('email', $org->email ?? '') }}"
                                        class="w-full pl-12 pr-4 py-3 rounded-xl border border-white/20 bg-white/5 text-white placeholder-white/40 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all">
                                </div>
                                @error('email')
                                    <p class="text-xs text-red-400 mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Telepon --}}
                            <div>
                                <label
                                    class="block text-xs font-semibold text-white/70 uppercase tracking-wider mb-2">Nomor
                                    Telepon</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-white/40 group-focus-within:text-green-400 transition-colors"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                    </div>
                                    <input name="phone" type="tel"
                                        value="{{ old('phone', $org->phone ?? '') }}"
                                        class="w-full pl-12 pr-4 py-3 rounded-xl border border-white/20 bg-white/5 text-white placeholder-white/40 focus:border-green-500 focus:outline-none focus:ring-2 focus:ring-green-500/50 transition-all">
                                </div>
                                @error('phone')
                                    <p class="text-xs text-red-400 mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Alamat --}}
                            <div>
                                <label
                                    class="block text-xs font-semibold text-white/70 uppercase tracking-wider mb-2">Alamat
                                    Lengkap</label>
                                <textarea name="address" rows="3"
                                    class="w-full px-4 py-3 rounded-xl border border-white/20 bg-white/5 text-white placeholder-white/40 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all resize-none">{{ old('address', $org->address ?? '') }}</textarea>
                                @error('address')
                                    <p class="text-xs text-red-400 mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Logo Upload --}}
                            <div>
                                <label
                                    class="block text-xs font-semibold text-white/70 uppercase tracking-wider mb-2">Logo
                                    (PNG/JPG, maks 2MB)</label>
                                <div class="flex items-start gap-4">
                                    @if (!empty($org->logo_path))
                                        <div class="flex-shrink-0">
                                            <img src="{{ asset('storage/' . $org->logo_path) }}" alt="logo"
                                                class="h-20 w-20 rounded-xl object-cover border-2 border-white/20">
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <input name="logo" type="file" accept="image/png,image/jpeg,image/jpg"
                                            class="w-full rounded-xl border border-white/20 bg-white/5 px-4 py-3 text-white file:mr-4 file:rounded-lg file:border-0 file:bg-gradient-to-r file:from-blue-500 file:to-purple-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:from-blue-600 hover:file:to-purple-700 transition-all">
                                        @error('logo')
                                            <p class="text-xs text-red-400 mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex gap-3 pt-4 mt-6 border-t border-white/10">
                                <a href="{{ url()->current() }}"
                                    class="flex-1 flex items-center justify-center gap-2 rounded-xl px-6 py-3 bg-white/5 border border-white/20 text-white font-semibold hover:bg-white/10 transition-all">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Batal
                                </a>
                                <button type="submit"
                                    class="flex-1 flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 px-6 py-3 text-white font-bold hover:from-blue-700 hover:to-purple-700 transition-all shadow-lg hover:shadow-purple-500/50 hover:scale-[1.02]">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    @else
                        {{-- READ-ONLY VIEW --}}
                        <div class="p-6 space-y-5">
                            <div
                                class="p-4 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 transition-all">
                                <h3 class="text-xs font-semibold text-white/50 uppercase tracking-wider mb-2">Nama
                                    Organisasi</h3>
                                <p class="text-lg font-bold text-white">{{ $org->name ?? '-' }}</p>
                            </div>

                            <div
                                class="p-4 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 transition-all">
                                <h3 class="text-xs font-semibold text-white/50 uppercase tracking-wider mb-2">Singkatan
                                    / Alias</h3>
                                <p class="text-base text-white">{{ $org->short_name ?? '-' }}</p>
                            </div>

                            <div
                                class="p-4 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 transition-all">
                                <h3 class="text-xs font-semibold text-white/50 uppercase tracking-wider mb-2">Email
                                </h3>
                                <p class="text-base text-white">{{ $org->email ?? '-' }}</p>
                            </div>

                            <div
                                class="p-4 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 transition-all">
                                <h3 class="text-xs font-semibold text-white/50 uppercase tracking-wider mb-2">Nomor
                                    Telepon</h3>
                                <p class="text-base text-white">{{ $org->phone ?? '-' }}</p>
                            </div>

                            <div
                                class="p-4 rounded-xl bg-white/5 border border-white/10 hover:bg-white/10 transition-all">
                                <h3 class="text-xs font-semibold text-white/50 uppercase tracking-wider mb-2">Alamat
                                </h3>
                                <p class="text-base text-white">{{ $org->address ?? '-' }}</p>
                            </div>

                            <div class="flex justify-end pt-4 border-t border-white/10">
                                <a href="{{ url()->current() . '?edit=1' }}"
                                    class="flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 px-8 py-3 text-sm font-bold text-white hover:from-blue-700 hover:to-purple-700 transition-all shadow-lg hover:shadow-purple-500/50 hover:scale-105">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Edit Informasi
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- 2. REKENING BANK --}}
                {{-- 2. REKENING BANK --}}
                <div
                    class="rounded-xl border border-white/20 bg-white/10 backdrop-blur-md shadow-lg overflow-hidden hover:border-green-500/30 transition-all group">
                    <div class="border-b border-white/20 bg-gradient-to-r from-white/5 to-white/10 px-6 py-5">
                        <div class="flex items-center justify-between">
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
                                    <p class="text-sm text-white/60 mt-0.5">Kelola rekening bank untuk menerima
                                        pembayaran</p>
                                </div>
                            </div>
                            <button x-data @click="$dispatch('open-bank-modal')"
                                class="flex items-center gap-2 rounded-xl bg-gradient-to-r from-green-500 to-emerald-600 px-4 py-2.5 text-sm font-bold text-white hover:from-green-600 hover:to-emerald-700 transition-all shadow-lg hover:shadow-green-500/50 hover:scale-105">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                <span class="hidden sm:inline">Tambah Rekening</span>
                            </button>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="space-y-4">
                            @php
                                $bankAccounts = $organization->bankAccounts ?? [];
                            @endphp

                            @forelse($bankAccounts as $account)
                                <div
                                    class="rounded-xl border border-white/20 bg-gradient-to-br from-white/5 to-white/10 p-5 hover:border-green-500/30 hover:bg-white/15 transition-all group/card">
                                    <div class="flex items-start gap-4">
                                        {{-- Icon --}}
                                        <div
                                            class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 shadow-lg flex-shrink-0 group-hover/card:scale-110 transition-transform">
                                            <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                            </svg>
                                        </div>

                                        {{-- Content --}}
                                        <div class="flex-1 min-w-0">
                                            {{-- Bank Name & Actions Row --}}
                                            <div class="flex items-start justify-between gap-4 mb-3">
                                                <div>
                                                    <h3 class="text-sm font-bold text-white/70 mb-1">
                                                        {{ $account->bank_name ?? ($account['bank_name'] ?? 'Bank') }}
                                                    </h3>
                                                    <p class="text-2xl font-bold text-white tracking-wider font-mono">
                                                        {{ $account->number ?? ($account['number'] ?? '-') }}
                                                    </p>
                                                </div>

                                                {{-- Badge & Delete Button --}}
                                                <div class="flex items-center gap-2">
                                                    <span
                                                        class="inline-flex items-center gap-1.5 rounded-lg bg-green-500/20 px-3 py-1.5 text-xs font-bold text-green-300 border border-green-500/30">
                                                        <span
                                                            class="h-2 w-2 rounded-full bg-green-400 animate-pulse"></span>
                                                        Aktif
                                                    </span>

                                                    <form method="POST"
                                                        action="{{ route('organization.bank.destroy', [$organization, $account]) }}"
                                                        onsubmit="return confirm('Yakin ingin menghapus rekening ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" @click.stop
                                                            class="p-2 rounded-lg bg-red-500/10 border border-red-500/20 text-red-300 hover:bg-red-500/20 hover:border-red-500/30 transition"
                                                            title="Hapus rekening">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>

                                            {{-- Owner Name --}}
                                            <p class="text-sm text-white/60">
                                                a.n. <span
                                                    class="font-semibold text-white">{{ $account->owner_name ?? ($account['owner_name'] ?? ($organization->name ?? '-')) }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="rounded-xl border-2 border-dashed border-white/10 p-12 text-center">
                                    <div class="relative inline-flex mb-4">
                                        <div class="absolute inset-0 bg-green-500/20 blur-2xl rounded-full"></div>
                                        <div
                                            class="relative w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg">
                                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <h3 class="text-base font-bold text-white mb-2">Belum Ada Rekening</h3>
                                    <p class="text-sm text-white/60 mb-4">Tambahkan rekening bank untuk menerima
                                        pembayaran</p>
                                    <button x-data @click="$dispatch('open-bank-modal')"
                                        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl font-semibold hover:from-green-600 hover:to-emerald-700 transition-all shadow-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                        Tambah Rekening Pertama
                                    </button>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- 3. E-WALLET --}}
                <div
                    class="rounded-xl border border-white/20 bg-white/10 backdrop-blur-md shadow-lg overflow-hidden hover:border-purple-500/30 transition-all group">
                    <div class="border-b border-white/20 bg-gradient-to-r from-white/5 to-white/10 px-6 py-5">
                        <div class="flex items-center justify-between">
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
                                    <p class="text-sm text-white/60 mt-0.5">Kelola akun e-wallet untuk pembayaran
                                        digital</p>
                                </div>
                            </div>
                            <button x-data @click="$dispatch('open-ewallet-modal')"
                                class="flex items-center gap-2 rounded-xl bg-gradient-to-r from-purple-500 to-pink-600 px-4 py-2.5 text-sm font-bold text-white hover:from-purple-600 hover:to-pink-700 transition-all shadow-lg hover:shadow-purple-500/50 hover:scale-105">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                <span class="hidden sm:inline">Tambah E-Wallet</span>
                            </button>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="space-y-4">
                            @php
                                $ewallets = $organization->ewallets ?? [];
                            @endphp

                            @forelse($ewallets as $ew)
                                <div
                                    class="rounded-xl border border-white/20 bg-gradient-to-br from-white/5 to-white/10 p-5 hover:border-purple-500/30 hover:bg-white/15 transition-all group/card">
                                    <div class="flex items-start gap-4">
                                        {{-- Icon --}}
                                        <div
                                            class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 shadow-lg flex-shrink-0 group-hover/card:scale-110 transition-transform">
                                            <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>

                                        {{-- Content --}}
                                        <div class="flex-1 min-w-0">
                                            {{-- Provider Name & Actions Row --}}
                                            <div class="flex items-start justify-between gap-4 mb-3">
                                                <div>
                                                    <h3 class="text-sm font-bold text-white/70 mb-1">
                                                        {{ strtoupper($ew->provider ?? ($ew['provider'] ?? 'E-WALLET')) }}
                                                    </h3>
                                                    <p class="text-2xl font-bold text-white tracking-wider font-mono">
                                                        {{ $ew->number ?? ($ew['number'] ?? '-') }}
                                                    </p>
                                                </div>

                                                {{-- Badge & Delete Button --}}
                                                <div class="flex items-center gap-2">
                                                    <span
                                                        class="inline-flex items-center gap-1.5 rounded-lg bg-green-500/20 px-3 py-1.5 text-xs font-bold text-green-300 border border-green-500/30">
                                                        <span
                                                            class="h-2 w-2 rounded-full bg-green-400 animate-pulse"></span>
                                                        Aktif
                                                    </span>

                                                    <form method="POST"
                                                        action="{{ route('organization.ewallet.destroy', [$organization, $ew]) }}"
                                                        onsubmit="return confirm('Yakin ingin menghapus e-wallet ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" @click.stop
                                                            class="p-2 rounded-lg bg-red-500/10 border border-red-500/20 text-red-300 hover:bg-red-500/20 hover:border-red-500/30 transition"
                                                            title="Hapus e-wallet">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>

                                            {{-- Owner Name --}}
                                            <p class="text-sm text-white/60">
                                                a.n. <span
                                                    class="font-semibold text-white">{{ $ew->owner_name ?? ($ew['owner_name'] ?? ($organization->name ?? '-')) }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="rounded-xl border-2 border-dashed border-white/10 p-12 text-center">
                                    <div class="relative inline-flex mb-4">
                                        <div class="absolute inset-0 bg-purple-500/20 blur-2xl rounded-full"></div>
                                        <div
                                            class="relative w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center shadow-lg">
                                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <h3 class="text-base font-bold text-white mb-2">Belum Ada E-Wallet</h3>
                                    <p class="text-sm text-white/60 mb-4">Tambahkan e-wallet untuk menerima pembayaran
                                        digital</p>
                                    <button x-data @click="$dispatch('open-ewallet-modal')"
                                        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-600 text-white rounded-xl font-semibold hover:from-purple-600 hover:to-pink-700 transition-all shadow-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                        Tambah E-Wallet Pertama
                                    </button>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div> {{-- END LEFT COLUMN --}}

            {{-- RIGHT COLUMN: Logo & Preferences --}}
            <div class="space-y-6">

                {{-- Logo Organisasi --}}
                {{-- Logo Organisasi --}}
                <div
                    class="rounded-xl border border-white/20 bg-white/10 backdrop-blur-md shadow-lg overflow-hidden hover:border-blue-500/30 transition-all">
                    <div class="border-b border-white/20 bg-gradient-to-r from-white/5 to-white/10 px-6 py-5">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-purple-600">
                                <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-white">Logo Organisasi</h2>
                                <p class="text-xs text-white/60 mt-0.5">Tanda pengenal visual</p>
                            </div>
                        </div>
                    </div>

                    {{-- FORM UPLOAD LOGO KHUSUS (agar ikon kamera berfungsi) --}}
                    <form x-data="{
                        preview: null,
                        pick() { this.$refs.file.click() },
                        onFile(e) {
                            const f = e.target.files?.[0];
                            if (!f) return;
                    
                            // validasi ringan di sisi client (server tetap wajib validasi)
                            const okType = ['image/png', 'image/jpeg', 'image/jpg'].includes(f.type);
                            if (!okType) {
                                alert('File harus PNG/JPG.');
                                e.target.value = '';
                                return;
                            }
                            if (f.size > 2 * 1024 * 1024) {
                                alert('Maksimal 2MB.');
                                e.target.value = '';
                                return;
                            }
                    
                            this.preview = URL.createObjectURL(f);
                    
                            // auto submit biar langsung ke-upload
                            this.$nextTick(() => this.$refs.form.submit());
                        }
                    }" x-ref="form" method="POST"
                        action="{{ route('organization.logo.update', $organization) }}" enctype="multipart/form-data"
                        class="p-8 flex flex-col items-center justify-center text-center">
                        @csrf
                        @method('PATCH')

                        <div class="relative group">
                            {{-- Glow effect --}}
                            <div
                                class="absolute -inset-4 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full opacity-20 blur-2xl group-hover:opacity-30 transition-opacity">
                            </div>

                            <div
                                class="relative h-36 w-36 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 p-1 shadow-2xl group-hover:scale-105 transition-transform">
                                <div
                                    class="h-full w-full rounded-full bg-slate-900 flex items-center justify-center overflow-hidden">
                                    {{-- Preview dulu, kalau belum ada preview pakai logo dari DB --}}
                                    <template x-if="preview">
                                        <img :src="preview" alt="logo preview"
                                            class="h-full w-full object-cover">
                                    </template>

                                    <template x-if="!preview">
                                        <div class="h-full w-full flex items-center justify-center overflow-hidden">
                                            @if (!empty($organization->logo_path))
                                                <img src="{{ asset('storage/' . $organization->logo_path) }}"
                                                    alt="logo" class="h-full w-full object-cover">
                                            @else
                                                <span class="text-4xl font-bold text-white">
                                                    {{ strtoupper(substr($organization->name ?? 'ORG', 0, 2)) }}
                                                </span>
                                            @endif
                                        </div>
                                    </template>
                                </div>
                            </div>

                            {{-- INPUT FILE HIDDEN --}}
                            <input x-ref="file" type="file" name="logo"
                                accept="image/png,image/jpeg,image/jpg" class="hidden" @change="onFile($event)">

                            {{-- TOMBOL KAMERA: trigger file picker --}}
                            <button type="button" @click="pick()"
                                class="absolute bottom-0 right-0 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 p-3 text-white shadow-xl hover:scale-110 transition-all group-hover:rotate-12"
                                title="Upload Logo">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </button>
                        </div>

                        <div class="mt-6 px-4 py-2 rounded-lg bg-white/5 border border-white/10">
                            <p class="text-xs text-white/60">Format: PNG, JPG (Maks. 2MB)</p>
                            <p class="text-xs text-white/50">Disarankan rasio 1:1</p>
                        </div>

                        @error('logo')
                            <p class="mt-3 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </form>
                </div>


                {{-- Danger Zone --}}
                <div
                    class="rounded-xl border border-red-500/30 bg-gradient-to-br from-red-500/10 to-pink-500/5 backdrop-blur-md shadow-lg overflow-hidden">
                    <div class="border-b border-red-500/20 bg-red-500/5 px-6 py-4">
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <h2 class="text-sm font-bold text-red-400 uppercase tracking-wide">Zona Bahaya</h2>
                        </div>
                    </div>

                    <div class="p-6">
                        <p class="text-sm text-white/70 mb-4 leading-relaxed">
                            Tindakan ini <strong class="text-red-400">tidak dapat dibatalkan</strong>. Menghapus
                            organisasi akan menghapus <strong class="text-white">semua data anggota dan
                                transaksi</strong> secara permanen.
                        </p>
                        <form method="POST" action="{{ route('organization.destroy') }}"
                            onsubmit="return confirm('Yakin ingin menghapus organisasi? Tindakan ini permanen.');">
                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                class="w-full rounded-xl border-2 border-red-500/50 bg-red-500/10 px-6 py-3 text-sm font-bold text-red-400 hover:bg-red-500 hover:text-white transition-all disabled:opacity-50 disabled:cursor-not-allowed group">
                                <span class="flex items-center justify-center gap-2">
                                    Hapus Organisasi
                                </span>
                            </button>
                        </form>



                    </div>
                </div>

            </div> {{-- END RIGHT COLUMN --}}

        </div>
    </div>
    {{-- MODAL: TAMBAH REKENING BANK --}}
    <div x-data="{ open: false }" x-on:open-bank-modal.window="open = true" x-on:keydown.escape.window="open = false"
        x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center px-4"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

        {{-- Backdrop with blur --}}
        <div class="absolute inset-0 bg-gradient-to-br from-black/70 via-black/60 to-black/70 backdrop-blur-sm"
            @click="open = false" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"></div>

        {{-- Modal Container --}}
        <div class="relative w-full max-w-lg" x-transition:enter="transition ease-out duration-300 delay-100"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95">

            <div
                class="relative rounded-3xl border-2 border-emerald-500/30 bg-gradient-to-br from-slate-900/95 via-slate-800/95 to-slate-900/95 backdrop-blur-2xl shadow-2xl overflow-hidden">

                {{-- Decorative gradient overlay --}}
                <div
                    class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-emerald-500/10 via-green-500/5 to-transparent rounded-full blur-3xl">
                </div>
                <div
                    class="absolute bottom-0 left-0 w-48 h-48 bg-gradient-to-tr from-blue-500/10 via-cyan-500/5 to-transparent rounded-full blur-3xl">
                </div>

                {{-- Header --}}
                <div
                    class="relative z-10 px-8 py-6 border-b border-white/10 bg-gradient-to-r from-emerald-500/10 via-green-500/5 to-transparent">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <div
                                    class="p-2.5 rounded-xl bg-gradient-to-br from-emerald-500/20 to-green-500/20 border border-emerald-500/30">
                                    <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                </div>
                                <h3 class="text-white font-bold text-xl">Tambah Rekening Bank</h3>
                            </div>
                            <p class="text-white/60 text-sm ml-14">Masukkan data rekening tujuan pembayaran</p>
                        </div>
                        <button type="button"
                            class="group p-2 rounded-xl hover:bg-white/10 transition-all duration-300 hover:rotate-90"
                            @click="open = false">
                            <svg class="w-5 h-5 text-white/70 group-hover:text-white transition-colors" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Form --}}
                <form method="POST" action="{{ route('organization.bank.store', $organization) }}"
                    class="relative z-10 p-8 space-y-6">
                    @csrf

                    {{-- Nama Bank dengan Custom Dropdown --}}
                    <div class="space-y-3 group" x-data="{
                        open: false,
                        selected: '{{ old('bank_name') }}',
                        banks: [
                            { value: 'BCA', label: 'BCA', color: 'bg-blue-500' },
                            { value: 'Mandiri', label: 'Bank Mandiri', color: 'bg-yellow-500' },
                            { value: 'BRI', label: 'BRI', color: 'bg-blue-600' },
                            { value: 'BNI', label: 'BNI', color: 'bg-orange-500' },
                            { value: 'CIMB Niaga', label: 'CIMB Niaga', color: 'bg-red-600' },
                            { value: 'Permata', label: 'Permata Bank', color: 'bg-green-600' },
                            { value: 'BTN', label: 'BTN', color: 'bg-blue-700' },
                            { value: 'Danamon', label: 'Danamon', color: 'bg-blue-400' }
                        ],
                        getLabel(value) {
                            const bank = this.banks.find(b => b.value === value);
                            return bank ? bank.label : 'Pilih Bank';
                        }
                    }">
                        <label
                            class="flex items-center gap-2 text-sm font-bold text-emerald-300 uppercase tracking-wider">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Nama Bank
                            <span class="text-xs text-red-400">*</span>
                        </label>

                        <div class="relative">
                            <!-- Hidden input untuk form submission -->
                            <input type="hidden" name="bank_name" :value="selected" required>

                            <!-- Custom Dropdown Button -->
                            <button type="button" @click="open = !open" @click.away="open = false"
                                class="w-full px-5 py-4 rounded-xl border-2 border-white/20 bg-white/5 text-white focus:outline-none focus:border-emerald-400 focus:ring-4 focus:ring-emerald-500/20 transition-all duration-300 hover:border-white/30 hover:bg-white/10 text-left flex items-center justify-between">
                                <span x-text="getLabel(selected)" :class="{ 'text-white/40': !selected }"></span>
                                <svg class="w-5 h-5 text-emerald-400 transition-transform duration-200"
                                    :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="open" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 translate-y-1"
                                class="absolute z-50 w-full mt-2 bg-slate-900/95 backdrop-blur-xl border border-white/20 rounded-xl shadow-2xl overflow-hidden max-h-64 overflow-y-auto"
                                style="display: none;">
                                <div class="py-1">
                                    <!-- Default Option -->
                                    <button type="button" @click="selected = ''; open = false"
                                        class="w-full px-5 py-3 text-left text-white/40 hover:bg-white/5 transition-colors text-sm">
                                        Pilih Bank
                                    </button>

                                    <!-- Bank Options -->
                                    <template x-for="bank in banks" :key="bank.value">
                                        <button type="button" @click="selected = bank.value; open = false"
                                            :class="{ 'bg-white/10': selected === bank.value }"
                                            class="w-full px-5 py-3 text-left text-white hover:bg-white/5 transition-colors text-sm font-medium flex items-center gap-3">
                                            <span :class="bank.color" class="w-2 h-2 rounded-full"></span>
                                            <span x-text="bank.label"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <!-- Gradient Overlay Effect -->
                            <div
                                class="absolute inset-0 rounded-xl bg-gradient-to-r from-emerald-500/0 via-green-500/0 to-emerald-500/0 group-hover:from-emerald-500/5 group-hover:via-green-500/5 group-hover:to-emerald-500/5 transition-all duration-500 pointer-events-none">
                            </div>
                        </div>

                        @error('bank_name')
                            <div
                                class="flex items-center gap-2 text-xs text-red-400 bg-red-500/10 border border-red-500/20 rounded-lg px-3 py-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Nomor Rekening --}}
                    <div class="space-y-3 group">
                        <label
                            class="flex items-center gap-2 text-sm font-bold text-blue-300 uppercase tracking-wider">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                            </svg>
                            Nomor Rekening
                            <span class="text-xs text-red-400">*</span>
                        </label>
                        <div class="relative">
                            <input name="number" value="{{ old('number') }}" placeholder="1234567890" required
                                class="w-full px-5 py-4 rounded-xl border-2 border-white/20 bg-white/5 text-white placeholder-white/40 focus:outline-none focus:border-blue-400 focus:ring-4 focus:ring-blue-500/20 transition-all duration-300 hover:border-white/30 hover:bg-white/10 font-mono">
                            <div
                                class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/0 via-cyan-500/0 to-blue-500/0 group-hover:from-blue-500/5 group-hover:via-cyan-500/5 group-hover:to-blue-500/5 transition-all duration-500 pointer-events-none">
                            </div>
                        </div>
                        @error('number')
                            <div
                                class="flex items-center gap-2 text-xs text-red-400 bg-red-500/10 border border-red-500/20 rounded-lg px-3 py-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Nama Pemilik --}}
                    <div class="space-y-3 group">
                        <label
                            class="flex items-center gap-2 text-sm font-bold text-purple-300 uppercase tracking-wider">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Nama Pemilik (a.n.)
                            <span class="text-xs text-red-400">*</span>
                        </label>
                        <div class="relative">
                            <input name="owner_name" value="{{ old('owner_name', $organization->name ?? '') }}"
                                placeholder="Bendahara Kelas" required
                                class="w-full px-5 py-4 rounded-xl border-2 border-white/20 bg-white/5 text-white placeholder-white/40 focus:outline-none focus:border-purple-400 focus:ring-4 focus:ring-purple-500/20 transition-all duration-300 hover:border-white/30 hover:bg-white/10">
                            <div
                                class="absolute inset-0 rounded-xl bg-gradient-to-r from-purple-500/0 via-pink-500/0 to-purple-500/0 group-hover:from-purple-500/5 group-hover:via-pink-500/5 group-hover:to-purple-500/5 transition-all duration-500 pointer-events-none">
                            </div>
                        </div>
                        @error('owner_name')
                            <div
                                class="flex items-center gap-2 text-xs text-red-400 bg-red-500/10 border border-red-500/20 rounded-lg px-3 py-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Action Buttons --}}
                    <div class="pt-6 flex gap-4">
                        <button type="button" @click="open=false"
                            class="group/cancel flex-1 relative py-4 rounded-xl border-2 border-white/30 text-white font-bold hover:bg-white/10 hover:border-white/40 transition-all duration-300 overflow-hidden">
                            <span
                                class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/5 to-white/0 translate-x-[-100%] group-hover/cancel:translate-x-[100%] transition-transform duration-700"></span>
                            <span class="relative flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Batal
                            </span>
                        </button>
                        <button type="submit"
                            class="group/submit flex-1 relative py-4 rounded-xl bg-gradient-to-r from-emerald-500 to-green-600 text-white font-bold hover:from-emerald-600 hover:to-green-700 shadow-xl shadow-emerald-500/30 hover:shadow-emerald-500/50 hover:scale-105 transition-all duration-300 overflow-hidden">
                            <span
                                class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/20 to-white/0 translate-x-[-100%] group-hover/submit:translate-x-[100%] transition-transform duration-500"></span>
                            <span class="relative flex items-center justify-center gap-2">
                                <svg class="w-5 h-5 group-hover/submit:scale-110 transition-transform" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Simpan Bank
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL: TAMBAH E-WALLET --}}
    <div x-data="{ open: false }" x-on:open-ewallet-modal.window="open = true"
        x-on:keydown.escape.window="open = false" x-show="open" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center px-4"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

        {{-- Backdrop with blur --}}
        <div class="absolute inset-0 bg-gradient-to-br from-black/70 via-black/60 to-black/70 backdrop-blur-sm"
            @click="open = false" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"></div>

        {{-- Modal Container --}}
        <div class="relative w-full max-w-lg" x-transition:enter="transition ease-out duration-300 delay-100"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95">

            <div
                class="relative rounded-3xl border-2 border-purple-500/30 bg-gradient-to-br from-slate-900/95 via-slate-800/95 to-slate-900/95 backdrop-blur-2xl shadow-2xl overflow-hidden">

                {{-- Decorative gradient overlay --}}
                <div
                    class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-purple-500/10 via-pink-500/5 to-transparent rounded-full blur-3xl">
                </div>
                <div
                    class="absolute bottom-0 left-0 w-48 h-48 bg-gradient-to-tr from-blue-500/10 via-purple-500/5 to-transparent rounded-full blur-3xl">
                </div>

                {{-- Header --}}
                <div
                    class="relative z-10 px-8 py-6 border-b border-white/10 bg-gradient-to-r from-purple-500/10 via-pink-500/5 to-transparent">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <div
                                    class="p-2.5 rounded-xl bg-gradient-to-br from-purple-500/20 to-pink-500/20 border border-purple-500/30">
                                    <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <h3 class="text-white font-bold text-xl">Tambah E-Wallet</h3>
                            </div>
                            <p class="text-white/60 text-sm ml-14">Masukkan akun dompet digital tujuan pembayaran</p>
                        </div>
                        <button type="button"
                            class="group p-2 rounded-xl hover:bg-white/10 transition-all duration-300 hover:rotate-90"
                            @click="open = false">
                            <svg class="w-5 h-5 text-white/70 group-hover:text-white transition-colors" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Form --}}
                <form method="POST" action="{{ route('organization.ewallet.store', $organization) }}"
                    class="relative z-10 p-8 space-y-6">
                    @csrf

                    {{-- Provider dengan Custom Dropdown - Clean Version --}}
                    <div class="space-y-3 group" x-data="{
                        open: false,
                        selected: '{{ old('provider') }}',
                        providers: [
                            { value: 'gopay', label: 'GoPay', color: 'bg-green-500' },
                            { value: 'ovo', label: 'OVO', color: 'bg-purple-500' },
                            { value: 'dana', label: 'DANA', color: 'bg-blue-500' },
                            { value: 'shopeepay', label: 'ShopeePay', color: 'bg-orange-500' },
                            { value: 'linkaja', label: 'LinkAja', color: 'bg-red-500' }
                        ],
                        getLabel(value) {
                            const provider = this.providers.find(p => p.value === value);
                            return provider ? provider.label : 'Pilih Provider';
                        }
                    }">
                        <label
                            class="flex items-center gap-2 text-sm font-bold text-purple-300 uppercase tracking-wider">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            Provider E-Wallet
                            <span class="text-xs text-red-400">*</span>
                        </label>

                        <div class="relative">
                            <!-- Hidden input untuk form submission -->
                            <input type="hidden" name="provider" :value="selected" required>

                            <!-- Custom Dropdown Button -->
                            <button type="button" @click="open = !open" @click.away="open = false"
                                class="w-full px-5 py-4 rounded-xl border-2 border-white/20 bg-white/5 text-white focus:outline-none focus:border-purple-400 focus:ring-4 focus:ring-purple-500/20 transition-all duration-300 hover:border-white/30 hover:bg-white/10 text-left flex items-center justify-between">
                                <span x-text="getLabel(selected)" :class="{ 'text-white/40': !selected }"></span>
                                <svg class="w-5 h-5 text-purple-400 transition-transform duration-200"
                                    :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="open" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 translate-y-1"
                                class="absolute z-50 w-full mt-2 bg-slate-900/95 backdrop-blur-xl border border-white/20 rounded-xl shadow-2xl overflow-hidden"
                                style="display: none;">
                                <div class="py-1">
                                    <!-- Default Option -->
                                    <button type="button" @click="selected = ''; open = false"
                                        class="w-full px-5 py-3 text-left text-white/40 hover:bg-white/5 transition-colors text-sm">
                                        Pilih Provider
                                    </button>

                                    <!-- Provider Options -->
                                    <template x-for="provider in providers" :key="provider.value">
                                        <button type="button" @click="selected = provider.value; open = false"
                                            :class="{ 'bg-white/10': selected === provider.value }"
                                            class="w-full px-5 py-3 text-left text-white hover:bg-white/5 transition-colors text-sm font-medium flex items-center gap-3">
                                            <span :class="provider.color" class="w-2 h-2 rounded-full"></span>
                                            <span x-text="provider.label"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <!-- Gradient Overlay Effect -->
                            <div
                                class="absolute inset-0 rounded-xl bg-gradient-to-r from-purple-500/0 via-pink-500/0 to-purple-500/0 group-hover:from-purple-500/5 group-hover:via-pink-500/5 group-hover:to-purple-500/5 transition-all duration-500 pointer-events-none">
                            </div>
                        </div>

                        @error('provider')
                            <div
                                class="flex items-center gap-2 text-xs text-red-400 bg-red-500/10 border border-red-500/20 rounded-lg px-3 py-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Nomor Akun --}}
                    <div class="space-y-3 group">
                        <label
                            class="flex items-center gap-2 text-sm font-bold text-blue-300 uppercase tracking-wider">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            Nomor Akun / No HP
                            <span class="text-xs text-red-400">*</span>
                        </label>
                        <div class="relative">
                            <input name="number" value="{{ old('number') }}" placeholder="081234567890" required
                                class="w-full px-5 py-4 rounded-xl border-2 border-white/20 bg-white/5 text-white placeholder-white/40 focus:outline-none focus:border-blue-400 focus:ring-4 focus:ring-blue-500/20 transition-all duration-300 hover:border-white/30 hover:bg-white/10 font-mono">
                            <div
                                class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-500/0 via-cyan-500/0 to-blue-500/0 group-hover:from-blue-500/5 group-hover:via-cyan-500/5 group-hover:to-blue-500/5 transition-all duration-500 pointer-events-none">
                            </div>
                        </div>
                        @error('number')
                            <div
                                class="flex items-center gap-2 text-xs text-red-400 bg-red-500/10 border border-red-500/20 rounded-lg px-3 py-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Nama Pemilik --}}
                    <div class="space-y-3 group">
                        <label
                            class="flex items-center gap-2 text-sm font-bold text-pink-300 uppercase tracking-wider">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Nama Pemilik (a.n.)
                            <span class="text-xs text-red-400">*</span>
                        </label>
                        <div class="relative">
                            <input name="owner_name" value="{{ old('owner_name', $organization->name ?? '') }}"
                                placeholder="Bendahara Kelas" required
                                class="w-full px-5 py-4 rounded-xl border-2 border-white/20 bg-white/5 text-white placeholder-white/40 focus:outline-none focus:border-pink-400 focus:ring-4 focus:ring-pink-500/20 transition-all duration-300 hover:border-white/30 hover:bg-white/10">
                            <div
                                class="absolute inset-0 rounded-xl bg-gradient-to-r from-pink-500/0 via-rose-500/0 to-pink-500/0 group-hover:from-pink-500/5 group-hover:via-rose-500/5 group-hover:to-pink-500/5 transition-all duration-500 pointer-events-none">
                            </div>
                        </div>
                        @error('owner_name')
                            <div
                                class="flex items-center gap-2 text-xs text-red-400 bg-red-500/10 border border-red-500/20 rounded-lg px-3 py-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Action Buttons --}}
                    <div class="pt-6 flex gap-4">
                        <button type="button" @click="open=false"
                            class="group/cancel flex-1 relative py-4 rounded-xl border-2 border-white/30 text-white font-bold hover:bg-white/10 hover:border-white/40 transition-all duration-300 overflow-hidden">
                            <span
                                class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/5 to-white/0 translate-x-[-100%] group-hover/cancel:translate-x-[100%] transition-transform duration-700"></span>
                            <span class="relative flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Batal
                            </span>
                        </button>
                        <button type="submit"
                            class="group/submit flex-1 relative py-4 rounded-xl bg-gradient-to-r from-purple-500 to-pink-600 text-white font-bold hover:from-purple-600 hover:to-pink-700 shadow-xl shadow-purple-500/30 hover:shadow-purple-500/50 hover:scale-105 transition-all duration-300 overflow-hidden">
                            <span
                                class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/20 to-white/0 translate-x-[-100%] group-hover/submit:translate-x-[100%] transition-transform duration-500"></span>
                            <span class="relative flex items-center justify-center gap-2">
                                <svg class="w-5 h-5 group-hover/submit:scale-110 transition-transform" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Simpan E-Wallet
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    {{-- Custom animations --}}
    <style>
        @keyframes pulse-once {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.8;
            }
        }

        .animate-pulse-once {
            animation: pulse-once 1s ease-in-out;
        }
    </style>

    {{-- Small JS: clear ?edit from URL on success --}}
    @if (session('success'))
        <script>
            (function() {
                try {
                    const url = new URL(window.location.href);
                    if (url.searchParams.has('edit')) {
                        url.searchParams.delete('edit');
                        window.history.replaceState({}, document.title, url.pathname + url.search);
                    }
                } catch (e) {
                    // ignore
                }
            })();
        </script>
    @endif

</x-layouts.app-layout>
