<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lupa Password - KasKu</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-15px);
            }
        }

        @keyframes glow {

            0%,
            100% {
                box-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
            }

            50% {
                box-shadow: 0 0 30px rgba(147, 51, 234, 0.4);
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes shimmer {
            0% {
                background-position: -1000px 0;
            }

            100% {
                background-position: 1000px 0;
            }
        }

        .animate-float {
            animation: float 5s ease-in-out infinite;
        }

        .animate-glow {
            animation: glow 3s ease-in-out infinite;
        }

        .slide-in {
            animation: slideIn 0.6s ease-out;
        }

        .shimmer {
            background: linear-gradient(90deg,
                    transparent 0%,
                    rgba(255, 255, 255, 0.1) 50%,
                    transparent 100%);
            background-size: 1000px 100%;
            animation: shimmer 3s infinite;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .input-glow:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2), 0 0 20px rgba(59, 130, 246, 0.3);
        }

        .gradient-text {
            background: linear-gradient(135deg, #60a5fa, #a855f7, #ec4899);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>

<body class="h-full overflow-hidden bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900">
    <!-- Background Effects -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-20 -left-20 w-96 h-96 bg-blue-500/20 rounded-full blur-3xl animate-float"></div>
        <div class="absolute top-1/3 -right-32 w-80 h-80 bg-purple-500/25 rounded-full blur-3xl animate-float"
            style="animation-delay: -2s;"></div>
        <div class="absolute -bottom-32 left-1/4 w-72 h-72 bg-pink-500/15 rounded-full blur-3xl animate-float"
            style="animation-delay: -4s;"></div>
        <div class="absolute inset-0 opacity-30 shimmer"></div>
    </div>

    <div class="relative min-h-full flex items-center justify-center px-4 py-10">
        <div class="w-full max-w-md">
            <!-- Main Card -->
            <div class="glass-card rounded-3xl shadow-2xl overflow-hidden animate-glow slide-in">
                <!-- Header -->
                <div class="px-8 pt-10 pb-8 text-center border-b border-white/10">
                    <div
                        class="mx-auto h-16 w-16 rounded-2xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center mb-6 shadow-lg shadow-blue-500/30">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold gradient-text mb-2">
                        Lupa Password?
                    </h1>
                    <p class="text-white/60 text-sm leading-relaxed">
                        Jangan khawatir! Masukkan email Anda dan kami akan mengirimkan link untuk reset password.
                    </p>
                </div>

                <!-- Content -->
                <div class="px-8 py-8">
                    <!-- Status Messages -->
                    @if (session('status'))
                        <div
                            class="mb-6 rounded-2xl border border-emerald-500/40 bg-gradient-to-br from-emerald-500/20 to-emerald-600/10 px-5 py-4 backdrop-blur-sm slide-in">
                            <div class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-emerald-400 mt-0.5 flex-shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-sm text-emerald-200 leading-relaxed">{{ session('status') }}</p>
                            </div>
                        </div>
                    @endif

                    @error('email')
                        <div
                            class="mb-6 rounded-2xl border border-rose-500/40 bg-gradient-to-br from-rose-500/20 to-rose-600/10 px-5 py-4 backdrop-blur-sm slide-in">
                            <div class="flex items-start gap-3">
                                <svg class="h-5 w-5 text-rose-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-sm text-rose-200 leading-relaxed">{{ $message }}</p>
                            </div>
                        </div>
                    @enderror

                    <!-- Form -->
                    <form id="forgotPasswordForm" method="POST" action="{{ route('password.email') }}"
                        class="space-y-6">
                        @csrf

                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-white/90">
                                Alamat Email
                            </label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-white/40 group-focus-within:text-blue-400 transition-colors"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-18 8h18a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v7a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                    placeholder="nama@email.com"
                                    class="w-full rounded-xl border border-white/10 bg-white/5 pl-12 pr-4 py-3.5 text-white placeholder-white/40 focus:border-blue-500/60 focus:bg-white/10 input-glow hover:bg-white/8">
                            </div>
                        </div>

                        <button type="submit"
                            class="group relative w-full rounded-2xl bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 px-6 py-4 text-sm font-bold text-white shadow-xl shadow-purple-500/30 transition-all duration-300 hover:shadow-2xl hover:shadow-purple-500/40 hover:scale-[1.02] active:scale-[0.98] overflow-hidden">
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 opacity-0 group-hover:opacity-20 transition-opacity duration-300">
                            </div>
                            <span class="relative z-10 flex items-center justify-center gap-2">
                                <svg class="h-5 w-5 group-hover:rotate-12 transition-transform" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                Kirim Link Reset Password
                            </span>
                        </button>
                    </form>

                    <!-- Back to Login -->
                    <div class="mt-8 text-center">
                        <a href="{{ route('login') }}"
                            class="group inline-flex items-center gap-2 text-sm font-semibold text-white/80 hover:text-white transition-colors">
                            <svg class="h-4 w-4 group-hover:-translate-x-1 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Kembali ke Login
                        </a>
                    </div>
                </div>

                <!-- Footer -->
                <div class="border-t border-white/10 bg-white/5 px-8 py-5 text-center">
                    <div class="flex items-center justify-center gap-2">
                        <div
                            class="h-8 w-8 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                            <span class="text-white font-bold text-xs">K</span>
                        </div>
                        <div class="text-left">
                            <p class="text-xs font-semibold text-white/90">KasKu</p>
                            <p class="text-xs text-white/50">Sistem Manajemen Kas Organisasi</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Info -->
            <div class="mt-6 text-center text-xs text-white/40">
                <p>Pastikan Anda memasukkan email yang terdaftar di sistem</p>
            </div>
        </div>
    </div>

    <script>
        // Loading UI saat submit (tanpa mencegah form submit ke Laravel)
        document.getElementById('forgotPasswordForm')?.addEventListener('submit', function () {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (!submitBtn) return;

            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <span class="relative z-10 flex items-center justify-center gap-2">
                    <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    Mengirim...
                </span>
            `;
        });

        // Input animation
        const emailInput = document.getElementById('email');
        if (emailInput) {
            emailInput.addEventListener('focus', function () {
                this.parentElement?.classList.add('scale-[1.01]');
            });
            emailInput.addEventListener('blur', function () {
                this.parentElement?.classList.remove('scale-[1.01]');
            });
        }
    </script>
</body>

</html>
