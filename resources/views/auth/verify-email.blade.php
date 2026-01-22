{{-- resources/views/auth/verify-email.blade.php --}}
<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KasKu - Verifikasi Email</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        @keyframes pulse-slow {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }

        .animate-pulse-slow {
            animation: pulse-slow 3s ease-in-out infinite;
        }

        /* Smooth scrolling untuk layar kecil */
        html {
            scroll-behavior: smooth;
        }

        /* Container dengan max-height dinamis */
        .verify-container {
            max-height: 100vh;
            max-height: 100dvh; /* Dynamic viewport height untuk mobile */
        }

        /* Responsive scaling untuk berbagai ukuran layar */
        @media (max-height: 900px) {
            .verify-card {
                transform: scale(0.98);
            }
        }

        @media (max-height: 800px) {
            .verify-card {
                transform: scale(0.95);
            }
        }

        @media (max-height: 700px) {
            .verify-card {
                transform: scale(0.90);
            }
            .hide-on-short {
                display: none;
            }
        }

        @media (max-height: 600px) {
            .verify-card {
                transform: scale(0.85);
            }
            .compact-on-short {
                padding: 1rem !important;
            }
            .compact-spacing {
                margin-bottom: 0.75rem !important;
            }
        }

        @media (max-height: 500px) {
            .verify-card {
                transform: scale(0.78);
            }
        }

        /* Landscape phone optimization */
        @media (max-height: 500px) and (orientation: landscape) {
            .verify-container {
                overflow-y: auto;
                padding: 1rem 0;
            }
            .verify-card {
                transform: scale(0.75);
                margin: auto;
            }
        }

        /* Small phone optimization */
        @media (max-width: 360px) {
            .verify-card {
                transform: scale(0.95);
            }
        }

        /* Tablet optimization */
        @media (min-width: 768px) and (min-height: 900px) {
            .verify-card {
                transform: scale(1.05);
            }
        }

        /* Large screen optimization */
        @media (min-width: 1024px) and (min-height: 1000px) {
            .verify-card {
                transform: scale(1.1);
            }
        }
    </style>
</head>

@php
    $displayEmail = session('pending_email') ?? ($email ?? null) ?: '-';
@endphp

<body class="h-full bg-slate-900 antialiased">

    {{-- GLOBAL BACKGROUND --}}
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900"></div>

        <div class="absolute top-1/4 left-1/4 w-72 h-72 sm:w-96 sm:h-96 bg-blue-500/30 rounded-full blur-3xl animate-pulse-slow"></div>
        <div class="absolute bottom-1/4 right-1/4 w-72 h-72 sm:w-96 sm:h-96 bg-purple-500/30 rounded-full blur-3xl animate-pulse-slow"
            style="animation-delay: 1.5s;"></div>

        <div class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff05_1px,transparent_1px),linear-gradient(to_bottom,#ffffff05_1px,transparent_1px)] bg-[size:4rem_4rem]">
        </div>
    </div>

    <main class="verify-container relative min-h-screen flex items-center justify-center px-3 sm:px-4 md:px-6 py-6 sm:py-8 md:py-10">
        <div class="w-full max-w-xl mx-auto">

            <div class="verify-card relative overflow-hidden rounded-2xl sm:rounded-3xl border border-white/15 bg-white/10 backdrop-blur-xl shadow-2xl transition-transform duration-300">
                <div class="absolute top-0 left-0 right-0 h-1 sm:h-1.5 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500">
                </div>

                <div class="compact-on-short p-6 sm:p-8 md:p-10">
                    {{-- Header --}}
                    <div class="text-center mb-5 sm:mb-6 md:mb-7 compact-spacing">
                        <div class="mx-auto mb-3 sm:mb-4 h-16 w-16 sm:h-20 sm:w-20 rounded-xl sm:rounded-2xl bg-gradient-to-br from-blue-500 to-purple-600 shadow-xl flex items-center justify-center">
                            <svg class="w-8 h-8 sm:w-10 sm:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>

                        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-white leading-tight">Verifikasi Email Anda</h1>
                        <p class="text-xs sm:text-sm text-white/70 mt-1">Langkah terakhir untuk mengaktifkan akun</p>

                        <p class="text-xs text-white/60 mt-2 break-all px-2">
                            Email:
                            <span class="text-white/80 font-semibold">{{ $displayEmail }}</span>
                        </p>
                    </div>

                    {{-- Info message --}}
                    <div class="mb-4 sm:mb-5 p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-blue-500/10 border border-blue-500/20 compact-spacing">
                        <div class="flex gap-2 sm:gap-3">
                            <div class="mt-0.5 flex-shrink-0">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-300" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <p class="text-xs sm:text-sm text-white/85 leading-relaxed">
                                Kami sudah mengirim tautan verifikasi ke
                                <span class="font-semibold text-white break-all">{{ $displayEmail }}</span>.
                                Silakan buka Gmail Anda lalu klik tautan tersebut untuk <b>mengaktifkan akun</b>.
                                Jika belum masuk, cek folder <span class="font-semibold">Spam</span> atau tab
                                <span class="font-semibold">Promosi</span>.
                            </p>
                        </div>
                    </div>

                    {{-- Success message --}}
                    @if (session('status') == 'verification-link-sent')
                        <div class="mb-4 sm:mb-5 p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-emerald-500/10 border border-emerald-400/20 compact-spacing" x-data
                            x-init="setTimeout(() => $el.style.display = 'none', 8000)">
                            <div class="flex gap-2 sm:gap-3">
                                <div class="mt-0.5 flex-shrink-0">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-emerald-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <p class="text-xs sm:text-sm font-semibold text-emerald-200 leading-relaxed">
                                    {{ __('Tautan verifikasi baru telah dikirim ke alamat email yang Anda berikan saat pendaftaran.') }}
                                </p>
                            </div>
                        </div>
                    @endif

                    {{-- Illustration --}}
                    <div class="hide-on-short py-4 sm:py-5 text-center compact-spacing">
                        <div class="mx-auto w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-white/5 border border-white/10 flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-10 h-10 sm:w-12 sm:h-12 text-purple-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <p class="text-xs text-white/60 font-medium mt-2 sm:mt-3">Periksa inbox atau folder spam Anda</p>
                    </div>

                    {{-- Actions --}}
                    <div class="space-y-2.5 sm:space-y-3 mt-2">

                        {{-- RESEND Button --}}
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <input type="hidden" name="email" value="{{ $displayEmail }}">
                            <button type="submit"
                                class="group w-full flex items-center justify-center gap-2 rounded-xl sm:rounded-2xl
                                       bg-gradient-to-r from-blue-600 to-purple-600
                                       px-4 sm:px-6 py-3 sm:py-3.5 text-xs sm:text-sm font-bold text-white
                                       shadow-lg hover:shadow-xl hover:shadow-purple-500/40
                                       transition-all duration-300 hover:scale-[1.01] active:scale-[0.99]
                                       disabled:opacity-50 disabled:cursor-not-allowed"
                                @disabled($displayEmail === '-')>
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 group-hover:rotate-180 transition-transform duration-500 flex-shrink-0"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                <span class="whitespace-nowrap">{{ __('Kirim Ulang Email Verifikasi') }}</span>
                            </button>
                        </form>

                        {{-- Back to Login --}}
                        <a href="{{ route('login') }}"
                            class="group w-full flex items-center justify-center gap-2 rounded-xl sm:rounded-2xl
                                  border border-white/20 bg-white/5
                                  px-4 sm:px-6 py-2.5 sm:py-3 text-xs sm:text-sm font-semibold text-white/90
                                  hover:bg-white/10 hover:border-white/30 hover:text-white
                                  transition-all duration-300">
                            <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 group-hover:-translate-x-1 transition-transform duration-300 flex-shrink-0"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                            {{ __('Kembali ke Login') }}
                        </a>

                        {{-- Change Email Link --}}
                        <a href="{{ route('register') }}"
                            class="block text-center text-xs text-white/60 hover:text-white/80 transition py-1">
                            Salah email? Daftar ulang
                        </a>
                    </div>

                    {{-- Footer --}}
                    <div class="mt-4 sm:mt-6 pt-4 sm:pt-5 border-t border-white/10">
                        <p class="text-xs text-center text-white/50 break-all px-2">
                            Email dikirim dari
                            <span class="text-white/70 font-semibold">{{ config('mail.from.address') }}</span>
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </main>
</body>

</html>