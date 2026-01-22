<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk - KasKu</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Poppins', sans-serif; }

        @keyframes pulse-slow {
            0%, 100% { opacity: 1; }
            50% { opacity: .55; }
        }
        .animate-pulse-slow { animation: pulse-slow 3s ease-in-out infinite; }

        /* Sembunyikan ikon mata bawaan browser (Edge/IE) */
        input::-ms-reveal, input::-ms-clear { display: none; }

        /* Alpine helper */
        [x-cloak] { display: none !important; }

        /* ====== RESPONSIVE (berdasarkan tinggi layar laptop) ======
           Tujuan: tidak “sesak” di 1366x768 / 1600x800 / scaling 125%
        */
        @media (max-height: 820px) {
            .auth-shell { padding-top: 1.25rem !important; padding-bottom: 1.25rem !important; }
            .auth-grid { gap: 2rem !important; }
            .brand-title { font-size: clamp(2rem, 3.5vw, 2.75rem) !important; line-height: 1.05 !important; }
            .auth-card { padding-top: 1.5rem !important; padding-bottom: 1.5rem !important; }
            .auth-card .tight { margin-bottom: 1rem !important; }
            .auth-form { gap: 1rem !important; }
        }
    </style>
</head>

<body class="min-h-screen bg-slate-900 antialiased overflow-x-hidden">

    <!-- ✅ GLOBAL BACKGROUND (nyambung + halus) -->
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900"></div>

        <div class="absolute -top-28 -left-28 w-96 h-96 bg-blue-500/20 rounded-full blur-3xl animate-pulse-slow"></div>
        <div class="absolute top-1/3 -right-32 w-80 h-80 bg-purple-500/25 rounded-full blur-3xl animate-pulse-slow"
            style="animation-delay: -2s;"></div>
        <div class="absolute -bottom-32 left-1/4 w-72 h-72 bg-pink-500/15 rounded-full blur-3xl animate-pulse-slow"
            style="animation-delay: -4s;"></div>

        <div class="absolute inset-0 opacity-70
            bg-[linear-gradient(to_right,#ffffff05_1px,transparent_1px),linear-gradient(to_bottom,#ffffff05_1px,transparent_1px)]
            bg-[size:3.75rem_3.75rem] sm:bg-[size:4rem_4rem]"></div>
    </div>

    <!-- ✅ SHELL -->
    <div class="auth-shell relative min-h-screen flex items-center justify-center px-4 py-10 sm:py-12">
        <div class="w-full max-w-6xl mx-auto">
            <!-- Grid ratio dibuat lebih stabil agar rapi di berbagai laptop -->
            <div class="auth-grid grid grid-cols-1 lg:[grid-template-columns:1.05fr_0.95fr] gap-10 items-center">

                <!-- LEFT: BRANDING -->
                <div class="text-center lg:text-left space-y-6 lg:pl-6">
                    <a href="/" class="inline-flex items-center gap-3 group">
                        <div class="flex h-12 w-12 sm:h-14 sm:w-14 items-center justify-center rounded-2xl
                            bg-gradient-to-br from-blue-500 to-purple-600 shadow-lg transition-transform group-hover:scale-110">
                            <svg class="h-7 w-7 sm:h-8 sm:w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2v2a3 3 0 006 0v-2c0-1.105-1.343-2-3-2zm0 0V7a4 4 0 10-8 0v1m8 0a4 4 0 018 0v1m-2 12H6a2 2 0 01-2-2v-6a2 2 0 012-2h12a2 2 0 012 2v6a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <span class="text-2xl sm:text-3xl font-bold text-white tracking-tight">KasKu</span>
                    </a>

                    <div class="space-y-4">
                        <!-- Judul dibuat konsisten (tidak tergantung wrap) -->
                        <h2 class="brand-title text-3xl sm:text-4xl lg:text-5xl font-bold text-white tracking-tight leading-[1.08]">
                            <span class="block">Selamat Datang</span>
                            <span class="block">Kembali!</span>
                        </h2>

                        <p class="text-white/70 text-base leading-relaxed max-w-lg mx-auto lg:mx-0">
                            Kelola kas organisasi dengan lebih mudah, transparan, dan efisien.
                            Masuk untuk melanjutkan aktivitas Anda.
                        </p>
                    </div>

                    <div class="pt-1">
                        <p class="text-white/70">
                            Belum punya akun?
                            <a href="{{ route('register') }}"
                                class="font-semibold text-blue-400 hover:text-blue-300 transition-colors inline-flex items-center gap-1 group">
                                Daftar gratis sekarang
                                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </a>
                        </p>
                    </div>
                </div>

                <!-- RIGHT: FORM -->
                <div class="w-full max-w-md mx-auto lg:mx-0 lg:ml-auto">
                    <div class="auth-card bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl shadow-2xl px-6 sm:px-10 py-8">

                        <h3 class="text-xl font-bold text-white text-center tight mb-6">Masuk ke Akun</h3>

                        {{-- ✅ Status dari reset password / verifikasi / dll --}}
                        @php
                            $status = session('status');
                            $statusMessage = $status;

                            if ($status === trans('passwords.reset')) {
                                $statusMessage = 'Password berhasil direset. Silakan masuk dengan password baru.';
                            } elseif ($status === trans('passwords.sent')) {
                                $statusMessage = 'Link reset password sudah dikirim. Silakan cek email Anda.';
                            } elseif ($status === trans('passwords.throttled')) {
                                $statusMessage = 'Terlalu banyak permintaan. Coba lagi beberapa saat.';
                            }
                        @endphp

                        @if ($statusMessage)
                            <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/25 flex gap-3 items-start">
                                <svg class="w-5 h-5 text-emerald-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-sm text-emerald-200 leading-relaxed">{{ $statusMessage }}</p>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}" class="auth-form grid gap-5">
                            @csrf

                            <!-- Email -->
                            <div class="space-y-2">
                                <label for="email" class="block text-sm font-medium text-white/80">
                                    Email Gmail
                                </label>

                                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                                    autocomplete="username" inputmode="email" spellcheck="false" autocapitalize="off"
                                    oninput="this.value = this.value.toLowerCase()"
                                    pattern="^[^@\s]+@(gmail\.com|googlemail\.com)$"
                                    title="Masuk menggunakan email Gmail yang terdaftar, contoh: nama@gmail.com"
                                    class="block w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white placeholder-white/40 outline-none
                                        transition-all hover:bg-white/10
                                        focus:border-blue-500/50 focus:ring-2 focus:ring-blue-500/30"
                                    placeholder="nama@gmail.com">

                                @error('email')
                                    <p class="text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="space-y-2" x-data="{ show: false }">
                                <label for="password" class="block text-sm font-medium text-white/80">
                                    Password
                                </label>

                                <div class="relative">
                                    <input id="password" :type="show ? 'text' : 'password'" name="password" required
                                        autocomplete="current-password"
                                        class="block w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 pr-12 text-white placeholder-white/40 outline-none
                                            transition-all hover:bg-white/10
                                            focus:ring-2 focus:ring-blue-500/30 focus:border-blue-500/50"
                                        placeholder="••••••••">

                                    <button type="button" @click="show = !show" aria-label="Toggle password visibility"
                                        class="absolute inset-y-0 right-0 px-4 flex items-center text-white/40 hover:text-white/75 transition-colors focus:outline-none">
                                        <svg x-cloak x-show="show" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3.98 8.223A10.477 10.477 0 001.934 12c1.58 4.37 5.74 7.5 10.066 7.5 1.875 0 3.646-.505 5.216-1.39M6.228 6.228A10.45 10.45 0 0112 4.5c4.326 0 8.486 3.13 10.066 7.5a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.744 7.744L21 21m-3.378-3.378l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                        </svg>

                                        <svg x-cloak x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </button>
                                </div>

                                @error('password')
                                    <p class="text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Remember + Forgot -->
                            <div class="flex items-center justify-between pt-1">
                                <label for="remember_me" class="flex items-center cursor-pointer group select-none">
                                    <input id="remember_me" type="checkbox" name="remember"
                                        class="h-4 w-4 rounded border-white/20 bg-white/10 text-blue-500 focus:ring-blue-500/30">
                                    <span class="ml-2 text-sm text-white/70 group-hover:text-white/90 transition-colors">
                                        Ingat saya
                                    </span>
                                </label>

                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}"
                                        class="text-sm font-medium text-blue-400 hover:text-blue-300 transition-colors">
                                        Lupa password?
                                    </a>
                                @endif
                            </div>

                            <!-- Submit -->
                            <button type="submit"
                                class="w-full rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-3 font-semibold text-white
                                    shadow-lg hover:shadow-xl hover:from-blue-500 hover:to-purple-500 transition-all duration-200
                                    focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                                Masuk
                            </button>
                        </form>

                        <!-- Divider -->
                        <div class="my-6 flex items-center gap-3">
                            <div class="h-px flex-1 bg-white/10"></div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-4 py-1 rounded-full bg-slate-950/30 border border-white/10 text-white/55 backdrop-blur">
                                    atau lanjutkan dengan
                                </span>
                            </div>
                            <div class="h-px flex-1 bg-white/10"></div>
                        </div>

                        <!-- Google -->
                        <div class="grid grid-cols-1">
                            <a href="{{ route('auth.google.redirect') }}"
                                class="flex items-center justify-center px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white/80
                                    hover:bg-white/10 hover:border-white/20 transition-all text-sm font-medium hover:scale-[1.02]
                                    focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                                <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                                        fill="#4285F4" />
                                    <path
                                        d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                                        fill="#34A853" />
                                    <path
                                        d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                                        fill="#FBBC05" />
                                    <path
                                        d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                                        fill="#EA4335" />
                                </svg>
                                Google
                            </a>
                        </div>

                        <!-- Back -->
                        <div class="mt-6 text-center">
                            <a href="/"
                                class="inline-flex items-center justify-center gap-2 text-sm text-white/60 hover:text-white/90 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Kembali ke beranda
                            </a>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

</body>

</html>
