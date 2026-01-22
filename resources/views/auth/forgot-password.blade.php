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
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-15px);
            }
        }

        @keyframes glow {
            0%, 100% {
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

        /* Responsive container */
        .forgot-container {
            max-height: 100vh;
            max-height: 100dvh;
        }

        /* Responsive card scaling */
        @media (max-height: 900px) {
            .forgot-card {
                transform: scale(0.98);
            }
        }

        @media (max-height: 800px) {
            .forgot-card {
                transform: scale(0.95);
            }
        }

        @media (max-height: 700px) {
            .forgot-card {
                transform: scale(0.92);
            }
            .compact-padding {
                padding: 1.5rem !important;
            }
        }

        @media (max-height: 600px) {
            .forgot-card {
                transform: scale(0.88);
            }
            .compact-padding {
                padding: 1.25rem !important;
            }
            .reduce-spacing {
                margin-bottom: 1rem !important;
            }
        }

        @media (max-height: 500px) {
            .forgot-card {
                transform: scale(0.82);
            }
        }

        /* Landscape phone optimization */
        @media (max-height: 500px) and (orientation: landscape) {
            .forgot-container {
                overflow-y: auto;
                padding: 0.75rem 0;
            }
            .forgot-card {
                transform: scale(0.78);
                margin: auto;
            }
        }

        /* Small phone optimization */
        @media (max-width: 360px) {
            .forgot-card {
                transform: scale(0.96);
            }
        }

        /* Tablet & Desktop optimization */
        @media (min-width: 768px) and (min-height: 900px) {
            .forgot-card {
                transform: scale(1.05);
            }
        }

        @media (min-width: 1024px) and (min-height: 1000px) {
            .forgot-card {
                transform: scale(1.1);
            }
        }

        /* Smooth transitions */
        .forgot-card {
            transition: transform 0.3s ease;
        }
    </style>
</head>

<body class="h-full bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900">
    <!-- Background Effects -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-20 -left-20 w-64 h-64 sm:w-96 sm:h-96 bg-blue-500/20 rounded-full blur-3xl animate-float"></div>
        <div class="absolute top-1/3 -right-20 sm:-right-32 w-56 h-56 sm:w-80 sm:h-80 bg-purple-500/25 rounded-full blur-3xl animate-float"
            style="animation-delay: -2s;"></div>
        <div class="absolute -bottom-20 sm:-bottom-32 left-1/4 w-48 h-48 sm:w-72 sm:h-72 bg-pink-500/15 rounded-full blur-3xl animate-float"
            style="animation-delay: -4s;"></div>
        <div class="absolute inset-0 opacity-30 shimmer"></div>
    </div>

    <div class="forgot-container relative min-h-screen flex items-center justify-center px-3 sm:px-4 md:px-6 py-6 sm:py-8 md:py-10">
        <div class="w-full max-w-md">
            <!-- Main Card -->
            <div class="forgot-card glass-card rounded-2xl sm:rounded-3xl shadow-2xl overflow-hidden animate-glow slide-in">
                <!-- Header -->
                <div class="compact-padding px-6 sm:px-8 pt-8 sm:pt-10 pb-6 sm:pb-8 text-center border-b border-white/10 reduce-spacing">
                    <div
                        class="mx-auto h-14 w-14 sm:h-16 sm:w-16 rounded-xl sm:rounded-2xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center mb-4 sm:mb-6 shadow-lg shadow-blue-500/30">
                        <svg class="h-7 w-7 sm:h-8 sm:w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold gradient-text mb-2">
                        Lupa Password?
                    </h1>
                    <p class="text-white/60 text-xs sm:text-sm leading-relaxed px-2">
                        Jangan khawatir! Masukkan email Anda dan kami akan mengirimkan link untuk reset password.
                    </p>
                </div>

                <!-- Content -->
                <div class="compact-padding px-6 sm:px-8 py-6 sm:py-8">
                    <!-- Status Messages -->
                    @if (session('status'))
                        <div
                            class="mb-5 sm:mb-6 rounded-xl sm:rounded-2xl border border-emerald-500/40 bg-gradient-to-br from-emerald-500/20 to-emerald-600/10 px-4 sm:px-5 py-3 sm:py-4 backdrop-blur-sm slide-in reduce-spacing">
                            <div class="flex items-start gap-2 sm:gap-3">
                                <svg class="h-4 w-4 sm:h-5 sm:w-5 text-emerald-400 mt-0.5 flex-shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-xs sm:text-sm text-emerald-200 leading-relaxed">{{ session('status') }}</p>
                            </div>
                        </div>
                    @endif

                    @error('email')
                        <div
                            class="mb-5 sm:mb-6 rounded-xl sm:rounded-2xl border border-rose-500/40 bg-gradient-to-br from-rose-500/20 to-rose-600/10 px-4 sm:px-5 py-3 sm:py-4 backdrop-blur-sm slide-in reduce-spacing">
                            <div class="flex items-start gap-2 sm:gap-3">
                                <svg class="h-4 w-4 sm:h-5 sm:w-5 text-rose-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-xs sm:text-sm text-rose-200 leading-relaxed">{{ $message }}</p>
                            </div>
                        </div>
                    @enderror

                    <!-- Form -->
                    <form id="forgotPasswordForm" method="POST" action="{{ route('password.email') }}"
                        class="space-y-5 sm:space-y-6 reduce-spacing">
                        @csrf

                        <div class="space-y-2">
                            <label class="block text-xs sm:text-sm font-semibold text-white/90">
                                Alamat Email
                            </label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 sm:h-5 sm:w-5 text-white/40 group-focus-within:text-blue-400 transition-colors"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-18 8h18a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v7a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                    placeholder="nama@email.com"
                                    class="w-full rounded-xl border border-white/10 bg-white/5 pl-10 sm:pl-12 pr-3 sm:pr-4 py-3 sm:py-3.5 text-sm sm:text-base text-white placeholder-white/40 focus:border-blue-500/60 focus:bg-white/10 input-glow hover:bg-white/8 transition-all duration-300">
                            </div>
                        </div>

                        <button type="submit"
                            class="group relative w-full rounded-xl sm:rounded-2xl bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 px-4 sm:px-6 py-3.5 sm:py-4 text-xs sm:text-sm font-bold text-white shadow-xl shadow-purple-500/30 transition-all duration-300 hover:shadow-2xl hover:shadow-purple-500/40 hover:scale-[1.02] active:scale-[0.98] overflow-hidden">
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 opacity-0 group-hover:opacity-20 transition-opacity duration-300">
                            </div>
                            <span class="relative z-10 flex items-center justify-center gap-2">
                                <svg class="h-4 w-4 sm:h-5 sm:w-5 group-hover:rotate-12 transition-transform flex-shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                </svg>
                                <span class="whitespace-nowrap">Kirim Link Reset Password</span>
                            </span>
                        </button>
                    </form>

                    <!-- Back to Login -->
                    <div class="mt-6 sm:mt-8 text-center">
                        <a href="{{ route('login') }}"
                            class="group inline-flex items-center gap-2 text-xs sm:text-sm font-semibold text-white/80 hover:text-white transition-colors">
                            <svg class="h-3.5 w-3.5 sm:h-4 sm:w-4 group-hover:-translate-x-1 transition-transform flex-shrink-0" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Kembali ke Login
                        </a>
                    </div>
                </div>

                <!-- Footer -->
                <div class="border-t border-white/10 bg-white/5 px-6 sm:px-8 py-4 sm:py-5 text-center">
                    <div class="flex items-center justify-center gap-2">
                        <div
                            class="h-7 w-7 sm:h-8 sm:w-8 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center flex-shrink-0">
                            <span class="text-white font-bold text-xs">K</span>
                        </div>
                        <div class="text-left">
                            <p class="text-xs font-semibold text-white/90 leading-tight">KasKu</p>
                            <p class="text-xs text-white/50 leading-tight">Sistem Manajemen Kas Organisasi</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Info -->
            <div class="mt-4 sm:mt-6 text-center text-xs text-white/40 px-4">
                <p>Pastikan Anda memasukkan email yang terdaftar di sistem</p>
            </div>
        </div>
    </div>

    <script>
        // Loading UI saat submit
        document.getElementById('forgotPasswordForm')?.addEventListener('submit', function () {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (!submitBtn) return;

            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <span class="relative z-10 flex items-center justify-center gap-2">
                    <svg class="animate-spin h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    <span class="whitespace-nowrap">Mengirim...</span>
                </span>
            `;
        });

        // Input animation
        const emailInput = document.getElementById('email');
        if (emailInput) {
            let parent = emailInput.parentElement;
            
            emailInput.addEventListener('focus', function () {
                if (parent) {
                    parent.classList.add('scale-[1.01]');
                }
            });
            
            emailInput.addEventListener('blur', function () {
                if (parent) {
                    parent.classList.remove('scale-[1.01]');
                }
            });
        }
    </script>
</body>

</html>