<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar - KasKu</title>
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

        /* Menghilangkan icon mata bawaan browser (Edge/IE) */
        input::-ms-reveal,
        input::-ms-clear {
            display: none;
        }
    </style>
</head>

<body
    class="h-full bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 antialiased selection:bg-blue-500 selection:text-white">

    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-blue-500/30 rounded-full blur-3xl animate-pulse-slow"></div>
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-purple-500/30 rounded-full blur-3xl animate-pulse-slow"
            style="animation-delay: 1.5s;"></div>
        <div
            class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff05_1px,transparent_1px),linear-gradient(to_bottom,#ffffff05_1px,transparent_1px)] bg-[size:4rem_4rem]">
        </div>
    </div>

    <div class="relative min-h-screen flex items-center justify-center p-4 py-8 sm:p-6 lg:p-8">
        <div class="w-full max-w-6xl grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">

            <div class="text-center lg:text-left space-y-6 lg:pl-8 order-last lg:order-first hidden lg:block">
                <a href="/" class="inline-flex items-center gap-3 group">
                    <div
                        class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 shadow-lg transition-transform group-hover:scale-110">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="text-3xl font-bold text-white tracking-tight">KasKu</span>
                </a>

                <div class="space-y-4">
                    <h2 class="text-4xl lg:text-5xl font-bold text-white leading-tight">
                        Mulai Perjalanan <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-400">Anda
                            Sekarang!</span>
                    </h2>
                    <p class="text-lg text-white/70 max-w-md mx-auto lg:mx-0 leading-relaxed">
                        Bergabunglah dengan ribuan organisasi lainnya. Kelola keuangan dengan transparan, aman, dan
                        efisien.
                    </p>
                </div>

                <div class="pt-4">
                    <p class="text-white/70">
                        Sudah punya akun?
                        <a href="{{ route('login') }}"
                            class="font-semibold text-blue-400 hover:text-blue-300 transition-colors inline-flex items-center gap-1 group-hover:gap-2">
                            Masuk sekarang
                            <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>
                    </p>
                </div>
            </div>

            <div class="w-full max-w-md mx-auto lg:mx-0 lg:ml-auto">
                <div
                    class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl shadow-2xl py-6 px-6 sm:px-10">

                    <h3 class="text-xl font-bold text-white mb-5 text-center lg:text-center">Buat Akun Baru</h3>

                    <form method="POST" action="{{ route('register') }}" class="space-y-3">
                        @csrf

                        <div class="space-y-1">
                            <label for="name" class="block text-sm font-medium text-white/80">
                                Nama Lengkap
                            </label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required
                                autofocus autocomplete="name"
                                class="block w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all hover:bg-white/10"
                                placeholder="Nama Lengkap">
                            @error('name')
                                <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label for="email" class="block text-sm font-medium text-white/80">
                                Email Gmail
                            </label>

                            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                autocomplete="username" inputmode="email" spellcheck="false" autocapitalize="off"
                                oninput="this.value = this.value.toLowerCase()"
                                pattern="^[^@\s]+@(gmail\.com|googlemail\.com)$"
                                title="Gunakan email Gmail yang aktif, contoh: nama@gmail.com"
                                class="block w-full rounded-xl border border-white/10 bg-white/5 px-4 py-3 text-white placeholder-white/40
               outline-none transition-all focus:border-blue-500/50 focus:ring-2 focus:ring-blue-500/30 hover:bg-white/10"
                                placeholder="nama@gmail.com">



                            @error('email')
                                <p class="text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>



                        <div class="space-y-1" x-data="{ show: false }">
                            <label for="password" class="block text-sm font-medium text-white/80">
                                Password
                            </label>
                            <div class="relative">
                                <input id="password" :type="show ? 'text' : 'password'" name="password" required
                                    autocomplete="new-password"
                                    class="block w-full px-4 py-2.5 pr-10 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all hover:bg-white/10"
                                    placeholder="Minimal 8 karakter">
                                <button type="button" @click="show = !show"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-white/70 hover:text-white transition-colors focus:outline-none">
                                    <svg x-show="show" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                    </svg>
                                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                        class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-1" x-data="{ show: false }">
                            <label for="password_confirmation" class="block text-sm font-medium text-white/80">
                                Konfirmasi Password
                            </label>
                            <div class="relative">
                                <input id="password_confirmation" :type="show ? 'text' : 'password'"
                                    name="password_confirmation" required autocomplete="new-password"
                                    class="block w-full px-4 py-2.5 pr-10 bg-white/5 border border-white/10 rounded-xl text-white placeholder-white/30 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all hover:bg-white/10"
                                    placeholder="Ulangi password">
                                <button type="button" @click="show = !show"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-white/70 hover:text-white transition-colors focus:outline-none">
                                    <svg x-show="show" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                        class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                    </svg>
                                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                        class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </button>
                            </div>
                            @error('password_confirmation')
                                <p class="text-xs text-red-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="pt-2">
                            <button type="submit"
                                class="w-full flex justify-center items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl font-bold text-white shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 transition-all hover:scale-[1.02] active:scale-[0.98] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-slate-900">
                                Daftar Sekarang
                            </button>
                        </div>
                    </form>

                    <div class="mt-6 mb-4 relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-white/10"></div>
                        </div>
                        <div class="relative flex justify-center text-xs">
                            <span class="px-4 bg-[#1e1b4b]/80 backdrop-blur-xl text-white/50 rounded-full">atau daftar
                                dengan</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1">
                        <a href="{{ route('auth.google.redirect') }}"
                            class="flex items-center justify-center px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white/80 hover:bg-white/10 hover:border-white/20 transition-all text-sm font-medium hover:scale-[1.02]">
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

                    <div class="mt-4 pt-4 border-t border-white/10 text-center">
                        <a href="/"
                            class="text-xs font-medium text-white/50 hover:text-white transition-colors inline-flex items-center gap-2 group">
                            <svg class="w-3 h-3 transition-transform group-hover:-translate-x-1" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Kembali ke beranda
                        </a>
                    </div>
                </div>
            </div>

            <div class="text-center space-y-4 lg:hidden mt-4">
                <p class="text-white/70 text-sm">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="font-semibold text-blue-400 hover:text-blue-300">
                        Masuk sekarang
                    </a>
                </p>
            </div>

        </div>
    </div>

</body>

</html>
