{{-- resources/views/auth/verify-email.blade.php --}}
<!DOCTYPE html>
<html lang="id" class="h-full overflow-hidden">

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

        html,
        body {
            height: 100%;
            overflow: hidden;
        }

        @media (max-height: 820px) {
            .verify-card {
                transform: scale(0.93);
                transform-origin: top center;
            }
        }

        @media (max-height: 740px) {
            .verify-card {
                transform: scale(0.88);
                transform-origin: top center;
            }

            .hide-on-short {
                display: none;
            }
        }
    </style>
</head>

@php
    $displayEmail = session('pending_email') ?? ($email ?? null) ?: '-';
@endphp

<body class="h-full overflow-hidden bg-slate-900 antialiased">

    @php
        // ✅ Karena user belum login (akun belum dibuat), email harus diambil dari:
        // - $email (dari controller) ATAU
        // - session('pending_email') (diset setelah register) ATAU
        // - fallback kalau memang masih login (opsional)
        $displayEmail = $email ?? (session('pending_email') ?? auth()->user()?->email) ?: '-';
    @endphp

    {{-- ✅ GLOBAL BACKGROUND --}}
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900"></div>

        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-blue-500/30 rounded-full blur-3xl animate-pulse-slow"></div>
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-purple-500/30 rounded-full blur-3xl animate-pulse-slow"
            style="animation-delay: 1.5s;"></div>

        <div
            class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff05_1px,transparent_1px),linear-gradient(to_bottom,#ffffff05_1px,transparent_1px)] bg-[size:4rem_4rem]">
        </div>
    </div>

    <main class="relative h-full overflow-hidden flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-xl mx-auto">

            <div
                class="verify-card relative overflow-hidden rounded-3xl border border-white/15 bg-white/10 backdrop-blur-xl shadow-2xl">
                <div
                    class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500">
                </div>

                <div class="p-8 sm:p-10">
                    {{-- Header --}}
                    <div class="text-center mb-7">
                        <div
                            class="mx-auto mb-4 h-20 w-20 rounded-2xl bg-gradient-to-br from-blue-500 to-purple-600 shadow-xl flex items-center justify-center">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>

                        <h1 class="text-2xl sm:text-3xl font-bold text-white">Verifikasi Email Anda</h1>
                        <p class="text-sm text-white/70 mt-1">Langkah terakhir untuk mengaktifkan akun</p>

                        <p class="text-xs text-white/60 mt-2">
                            Email:
                            <span class="text-white/80 font-semibold">{{ $displayEmail }}</span>
                        </p>
                    </div>

                    {{-- Info message --}}
                    <div class="mb-5 p-4 rounded-2xl bg-blue-500/10 border border-blue-500/20">
                        <div class="flex gap-3">
                            <div class="mt-0.5">
                                <svg class="w-5 h-5 text-blue-300" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <p class="text-sm text-white/85 leading-relaxed">
                                Kami sudah mengirim tautan verifikasi ke
                                <span class="font-semibold text-white">{{ $displayEmail }}</span>.
                                Silakan buka Gmail Anda lalu klik tautan tersebut untuk <b>mengaktifkan akun</b>.
                                Jika belum masuk, cek folder <span class="font-semibold">Spam</span> atau tab
                                <span class="font-semibold">Promosi</span>.
                            </p>
                        </div>
                    </div>

                    {{-- Success message --}}
                    @if (session('status') == 'verification-link-sent')
                        <div class="mb-5 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-400/20" x-data
                            x-init="setTimeout(() => $el.style.display = 'none', 8000)">
                            <div class="flex gap-3">
                                <div class="mt-0.5">
                                    <svg class="w-5 h-5 text-emerald-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <p class="text-sm font-semibold text-emerald-200 leading-relaxed">
                                    {{ __('Tautan verifikasi baru telah dikirim ke alamat email yang Anda berikan saat pendaftaran.') }}
                                </p>
                            </div>
                        </div>
                    @endif

                    {{-- Illustration --}}
                    <div class="hide-on-short py-5 text-center">
                        <div
                            class="mx-auto w-24 h-24 rounded-full bg-white/5 border border-white/10 flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-12 h-12 text-purple-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <p class="text-xs text-white/60 font-medium mt-3">Periksa inbox atau folder spam Anda</p>
                    </div>

                    {{-- Actions --}}
                    <div class="space-y-3 mt-2">

                        {{-- ✅ RESEND: kirim email lewat hidden input (karena user belum login) --}}
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <input type="hidden" name="email" value="{{ $displayEmail }}">
                            <button type="submit"
                                class="group w-full flex items-center justify-center gap-2 rounded-2xl
                                       bg-gradient-to-r from-blue-600 to-purple-600
                                       px-6 py-3.5 text-sm font-bold text-white
                                       shadow-lg hover:shadow-xl hover:shadow-purple-500/40
                                       transition-all duration-300 hover:scale-[1.01] active:scale-[0.99]"
                                @disabled($displayEmail === '-')>
                                <svg class="w-5 h-5 group-hover:rotate-180 transition-transform duration-500"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                {{ __('Kirim Ulang Email Verifikasi') }}
                            </button>
                        </form>

                        {{-- ✅ Karena ini guest flow, tombol kedua sebaiknya "Kembali ke Login" --}}
                        <a href="{{ route('login') }}"
                            class="group w-full flex items-center justify-center gap-2 rounded-2xl
                                  border border-white/20 bg-white/5
                                  px-6 py-3 text-sm font-semibold text-white/90
                                  hover:bg-white/10 hover:border-white/30 hover:text-white
                                  transition-all duration-300">
                            <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform duration-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                            {{ __('Kembali ke Login') }}
                        </a>

                        {{-- opsional: kalau mau ganti email --}}
                        <a href="{{ route('register') }}"
                            class="block text-center text-xs text-white/60 hover:text-white/80 transition">
                            Salah email? Daftar ulang
                        </a>
                    </div>

                    {{-- Footer --}}
                    <div class="mt-6 pt-5 border-t border-white/10">
                        <p class="text-xs text-center text-white/50">
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
