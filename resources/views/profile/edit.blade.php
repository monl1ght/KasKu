<x-layouts.app-layout>
    @section('page-title', 'Profil Saya')
    @section('page-subtitle', 'Kelola informasi akun, keamanan, dan preferensi Anda')

    <div class="space-y-6">

        {{-- Header Card --}}
        <div class="rounded-2xl bg-gradient-to-r from-purple-600 to-indigo-600 p-6 shadow-lg relative overflow-hidden">
            <div class="absolute top-0 right-0 -mt-6 -mr-6 w-40 h-40 bg-white/20 rounded-full blur-2xl"></div>

            <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-white/15 border border-white/20 flex items-center justify-center text-white font-bold text-xl">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div>
                        <div class="text-white text-xl font-bold">{{ auth()->user()->name }}</div>
                        <div class="text-white/80 text-sm">{{ auth()->user()->email }}</div>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="px-3 py-1.5 rounded-xl bg-black/20 border border-white/15 text-white/90 text-xs">
                        Terakhir update: {{ optional(auth()->user()->updated_at)->diffForHumans() }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Alerts --}}
        @if (session('status') === 'profile-updated')
            <div class="rounded-2xl border border-emerald-500/30 bg-emerald-500/10 p-4 text-emerald-200">
                Profil berhasil diperbarui.
            </div>
        @elseif (session('status') === 'password-updated')
            <div class="rounded-2xl border border-emerald-500/30 bg-emerald-500/10 p-4 text-emerald-200">
                Password berhasil diperbarui.
            </div>
        @elseif (session('status') === 'verification-link-sent')
            <div class="rounded-2xl border border-blue-500/30 bg-blue-500/10 p-4 text-blue-200">
                Link verifikasi email sudah dikirim. Cek inbox/spam Anda.
            </div>
        @endif

        {{-- Grid --}}
        <div class="grid lg:grid-cols-3 gap-6">

            {{-- LEFT: Profile Update --}}
            <div class="lg:col-span-2 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm p-6">
                <div class="flex items-start justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-white font-bold text-lg">Informasi Akun</h3>
                        <p class="text-white/50 text-sm">Ubah nama dan email Anda.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
                    @csrf
                    @method('patch')

                    <div>
                        <label class="text-sm text-white/70">Nama</label>
                        <input
                            name="name"
                            type="text"
                            value="{{ old('name', auth()->user()->name) }}"
                            class="mt-2 w-full rounded-xl bg-slate-900/60 border border-white/10 text-white px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Nama lengkap"
                            required
                            autocomplete="name"
                        />
                        @error('name')
                            <p class="mt-2 text-xs text-red-300">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="text-sm text-white/70">Email</label>
                        <input
                            name="email"
                            type="email"
                            value="{{ old('email', auth()->user()->email) }}"
                            class="mt-2 w-full rounded-xl bg-slate-900/60 border border-white/10 text-white px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="email@domain.com"
                            required
                            autocomplete="username"
                        />
                        @error('email')
                            <p class="mt-2 text-xs text-red-300">{{ $message }}</p>
                        @enderror

                        {{-- Email verification (Breeze standard) --}}
                        @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! auth()->user()->hasVerifiedEmail())
                            <div class="mt-3 rounded-xl border border-yellow-500/20 bg-yellow-500/10 p-3">
                                <p class="text-yellow-200 text-xs">
                                    Email Anda belum terverifikasi.
                                </p>

                                <button
                                    form="send-verification"
                                    type="submit"
                                    class="mt-2 inline-flex items-center px-3 py-2 rounded-lg bg-yellow-500/20 text-yellow-100 text-xs hover:bg-yellow-500/30 transition"
                                >
                                    Kirim ulang link verifikasi
                                </button>
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <button
                            type="submit"
                            class="px-4 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-500 transition"
                        >
                            Simpan Perubahan
                        </button>

                        <a href="{{ url()->previous() }}"
                           class="px-4 py-2.5 rounded-xl bg-white/5 border border-white/10 text-white/80 text-sm hover:bg-white/10 transition">
                            Kembali
                        </a>
                    </div>
                </form>

                {{-- Hidden form for resend verification --}}
                <form id="send-verification" method="POST" action="{{ route('verification.send') }}">
                    @csrf
                </form>
            </div>

            {{-- RIGHT: Security --}}
            <div class="space-y-6">

                {{-- Password Update --}}
                <div class="rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm p-6">
                    <h3 class="text-white font-bold text-lg">Keamanan</h3>
                    <p class="text-white/50 text-sm mt-1">Ubah password akun Anda.</p>

                    <form method="POST" action="{{ route('password.update') }}" class="mt-5 space-y-4">
                        @csrf
                        @method('put')

                        <div>
                            <label class="text-sm text-white/70">Password saat ini</label>
                            <input
                                name="current_password"
                                type="password"
                                class="mt-2 w-full rounded-xl bg-slate-900/60 border border-white/10 text-white px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                autocomplete="current-password"
                                required
                            />
                            @error('current_password')
                                <p class="mt-2 text-xs text-red-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm text-white/70">Password baru</label>
                            <input
                                name="password"
                                type="password"
                                class="mt-2 w-full rounded-xl bg-slate-900/60 border border-white/10 text-white px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                autocomplete="new-password"
                                required
                            />
                            @error('password')
                                <p class="mt-2 text-xs text-red-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-sm text-white/70">Konfirmasi password baru</label>
                            <input
                                name="password_confirmation"
                                type="password"
                                class="mt-2 w-full rounded-xl bg-slate-900/60 border border-white/10 text-white px-4 py-3 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                autocomplete="new-password"
                                required
                            />
                        </div>

                        <button
                            type="submit"
                            class="w-full mt-2 px-4 py-2.5 rounded-xl bg-white/10 border border-white/10 text-white text-sm font-semibold hover:bg-white/15 transition"
                        >
                            Update Password
                        </button>
                    </form>
                </div>

                {{-- Delete Account --}}
                <div class="rounded-2xl bg-red-500/10 border border-red-500/20 backdrop-blur-sm p-6">
                    <h3 class="text-red-200 font-bold text-lg">Zona Berbahaya</h3>
                    <p class="text-red-200/70 text-sm mt-1">
                        Hapus akun akan menghapus akses Anda dan tidak bisa dibatalkan.
                    </p>

                    <form method="POST" action="{{ route('profile.destroy') }}" class="mt-5 space-y-4">
                        @csrf
                        @method('delete')

                        <div>
                            <label class="text-sm text-red-100/80">Masukkan password untuk konfirmasi</label>
                            <input
                                name="password"
                                type="password"
                                class="mt-2 w-full rounded-xl bg-slate-900/60 border border-red-500/20 text-white px-4 py-3 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                autocomplete="current-password"
                                required
                            />
                            @error('password')
                                <p class="mt-2 text-xs text-red-200">{{ $message }}</p>
                            @enderror
                        </div>

                        <button
                            type="submit"
                            class="w-full px-4 py-2.5 rounded-xl bg-red-600 text-white text-sm font-semibold hover:bg-red-500 transition"
                            onclick="return confirm('Yakin mau hapus akun? Tindakan ini tidak bisa dibatalkan.')"
                        >
                            Hapus Akun
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-layouts.app-layout>
