<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>KasKu - Sistem Manajemen Kas Organisasi</title>

    <!-- Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind + Alpine -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'poppins': ['Poppins', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <style>
        :root {
            --nav-h: 64px;
        }

        @media (min-width: 640px) {
            :root {
                --nav-h: 80px;
            }
        }

        body {
            font-family: 'Poppins', sans-serif;
        }

        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Animations */
        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-12px);
            }
        }

        @keyframes pulse-slow {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }

        @keyframes fade-in-up {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        .animate-pulse-slow {
            animation: pulse-slow 4s ease-in-out infinite;
        }

        .animate-fade-in-up {
            animation: fade-in-up 0.6s ease-out forwards;
        }

        /* Hero Section */
        .hero {
            padding-top: calc(var(--nav-h) + 2rem);
            padding-bottom: 2rem;
            min-height: calc(100vh - 4rem);
            min-height: calc(100dvh - 4rem);
            display: flex;
            align-items: center;
        }

        @media (min-width: 640px) {
            .hero {
                padding-top: calc(var(--nav-h) + 3rem);
                padding-bottom: 3rem;
            }
        }

        @media (min-width: 1024px) {
            .hero {
                padding-top: calc(var(--nav-h) + 4rem);
                padding-bottom: 4rem;
            }
        }

        /* Responsive title */
        .hero-title {
            font-size: clamp(1.75rem, 5vw, 3.5rem);
            line-height: 1.15;
        }

        /* Feature cards equal height */
        .feature-card {
            display: flex;
            flex-direction: column;
        }

        .feature-card p {
            flex-grow: 1;
        }

        /* Glass effect */
        .glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .glass-strong {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }

        /* Gradient text */
        .gradient-text {
            background: linear-gradient(135deg, #60a5fa 0%, #c084fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Button hover effect */
        .btn-primary {
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, #3b82f6 0%, #7c3aed 100%);
            z-index: -1;
            transition: opacity 0.3s ease;
        }

        .btn-primary::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, #2563eb 0%, #6d28d9 100%);
            z-index: -1;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .btn-primary:hover::after {
            opacity: 1;
        }

        /* Reduced motion */
        @media (prefers-reduced-motion: reduce) {
            .animate-float,
            .animate-pulse-slow,
            .animate-fade-in-up {
                animation: none !important;
            }
            
            html {
                scroll-behavior: auto;
            }
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #1e293b;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #3b82f6, #8b5cf6);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #2563eb, #7c3aed);
        }
    </style>
</head>

<body class="min-h-screen bg-slate-900 antialiased overflow-x-hidden font-poppins">

    <!-- GLOBAL BACKGROUND -->
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none" aria-hidden="true">
        <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-purple-900/40 to-slate-900"></div>

        <!-- Gradient orbs -->
        <div class="absolute top-1/4 left-1/4 -translate-x-1/2 -translate-y-1/2 w-64 h-64 sm:w-80 sm:h-80 lg:w-96 lg:h-96 bg-blue-500/20 rounded-full blur-3xl animate-pulse-slow"></div>
        <div class="absolute bottom-1/4 right-1/4 translate-x-1/2 translate-y-1/2 w-64 h-64 sm:w-80 sm:h-80 lg:w-96 lg:h-96 bg-purple-500/20 rounded-full blur-3xl animate-pulse-slow" style="animation-delay: 2s;"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-48 h-48 sm:w-64 sm:h-64 bg-indigo-500/10 rounded-full blur-3xl animate-pulse-slow" style="animation-delay: 1s;"></div>

        <!-- Grid pattern -->
        <div class="absolute inset-0 opacity-40 bg-[linear-gradient(to_right,#ffffff06_1px,transparent_1px),linear-gradient(to_bottom,#ffffff06_1px,transparent_1px)] bg-[size:3rem_3rem] sm:bg-[size:4rem_4rem]"></div>
    </div>

    <!-- NAV -->
    <nav class="fixed top-0 left-0 right-0 z-50 border-b border-white/10 bg-slate-900/70 backdrop-blur-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 sm:h-20">
                <!-- Logo -->
                <a href="#" class="flex items-center gap-2.5 sm:gap-3 group">
                    <div class="flex h-9 w-9 sm:h-11 sm:w-11 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 shadow-lg shadow-purple-500/25 transition-all duration-300 group-hover:scale-110 group-hover:shadow-purple-500/40">
                        <svg class="h-5 w-5 sm:h-6 sm:w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="text-lg sm:text-xl font-bold text-white tracking-tight">KasKu</span>
                </a>

                <!-- Actions -->
                <div class="flex items-center gap-1.5 sm:gap-3">
                    <a href="/login"
                        class="px-3 sm:px-5 py-2 sm:py-2.5 text-sm font-medium text-white/80 hover:text-white transition-colors rounded-lg hover:bg-white/5">
                        Masuk
                    </a>
                    <a href="/register"
                        class="btn-primary px-4 sm:px-6 py-2 sm:py-2.5 text-sm font-semibold text-white rounded-xl shadow-lg shadow-purple-500/25 hover:shadow-purple-500/40 transition-all duration-300 hover:scale-[1.02]">
                        Daftar Gratis
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- HERO -->
    <main class="hero relative px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-7xl mx-auto">
            <div class="grid lg:grid-cols-2 gap-8 sm:gap-10 lg:gap-12 xl:gap-16 items-center">

                <!-- LEFT: Text Content -->
                <div class="text-center lg:text-left space-y-5 sm:space-y-6 lg:space-y-8 animate-fade-in-up">
                    <!-- Badge -->
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 sm:px-4 sm:py-2 rounded-full glass border border-white/10 text-xs sm:text-sm text-white/80">
                        <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                        Platform Manajemen Keuangan #1
                    </div>

                    <!-- Title -->
                    <h1 class="hero-title font-bold text-white">
                        Platform Manajemen
                        <span class="gradient-text block sm:inline">
                            Keuangan & Kas
                        </span>
                        <span class="block">Multi-Organisasi</span>
                    </h1>

                    <!-- Description -->
                    <p class="text-sm sm:text-base lg:text-lg text-white/70 leading-relaxed max-w-xl mx-auto lg:mx-0">
                        Sistem berbasis web dengan arsitektur Multi-Tenancy untuk mendigitalisasi pengelolaan dana iuran
                        organisasi, komunitas, dan kelas akademik dengan transparansi penuh dan isolasi data yang aman.
                    </p>

                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center lg:justify-start pt-2">
                        <a href="/register"
                            class="btn-primary w-full sm:w-auto px-6 sm:px-8 py-3 sm:py-3.5 rounded-xl font-semibold text-white shadow-xl shadow-blue-500/30 hover:shadow-blue-500/50 transition-all duration-300 hover:scale-[1.02] text-center text-sm sm:text-base">
                            Mulai Gratis Sekarang
                        </a>

                        <a href="#features"
                            class="group w-full sm:w-auto px-6 sm:px-8 py-3 sm:py-3.5 border-2 border-white/20 rounded-xl font-semibold text-white hover:bg-white/10 transition-all duration-300 hover:border-white/40 text-center text-sm sm:text-base flex items-center justify-center gap-2">
                            Pelajari Lebih Lanjut
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 group-hover:translate-x-1 transition-transform duration-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </a>
                    </div>

                    <!-- Stats (Mobile & Desktop) -->
                    <div class="grid grid-cols-3 gap-4 sm:gap-6 pt-4 sm:pt-6 border-t border-white/10">
                        <div class="text-center lg:text-left">
                            <div class="text-xl sm:text-2xl lg:text-3xl font-bold text-white">500+</div>
                            <div class="text-xs sm:text-sm text-white/60">Organisasi</div>
                        </div>
                        <div class="text-center lg:text-left">
                            <div class="text-xl sm:text-2xl lg:text-3xl font-bold text-white">10K+</div>
                            <div class="text-xs sm:text-sm text-white/60">Pengguna</div>
                        </div>
                        <div class="text-center lg:text-left">
                            <div class="text-xl sm:text-2xl lg:text-3xl font-bold text-white">99.9%</div>
                            <div class="text-xs sm:text-sm text-white/60">Uptime</div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT: Feature Cards -->
                <div class="relative lg:pl-4" style="animation: fade-in-up 0.6s ease-out 0.2s forwards; opacity: 0;">
                    <!-- Desktop Cards (lg+) -->
                    <div class="hidden lg:block space-y-4 xl:space-y-5 animate-float">
                        <!-- Card 1 -->
                        <div class="glass-strong border border-white/15 rounded-2xl p-5 xl:p-6 shadow-2xl shadow-black/20 hover:bg-white/10 transition-all duration-300 hover:scale-[1.02] hover:border-white/25">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg shadow-purple-500/30">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-base xl:text-lg font-semibold text-white mb-1.5">Verifikasi Bertingkat</h3>
                                    <p class="text-white/65 text-sm leading-relaxed">
                                        Two-step verification dengan upload bukti pembayaran digital. Status pending hingga
                                        bendahara approve.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Card 2 -->
                        <div class="glass-strong border border-white/15 rounded-2xl p-5 xl:p-6 shadow-2xl shadow-black/20 hover:bg-white/10 transition-all duration-300 hover:scale-[1.02] hover:border-white/25 xl:ml-6">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg shadow-pink-500/30">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-base xl:text-lg font-semibold text-white mb-1.5">Kode Akses Unik</h3>
                                    <p class="text-white/65 text-sm leading-relaxed">
                                        Sistem join code untuk manajemen keanggotaan mandiri yang aman tanpa input data manual berulang.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Card 3 -->
                        <div class="glass-strong border border-white/15 rounded-2xl p-5 xl:p-6 shadow-2xl shadow-black/20 hover:bg-white/10 transition-all duration-300 hover:scale-[1.02] hover:border-white/25">
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-base xl:text-lg font-semibold text-white mb-1.5">Billing Fleksibel</h3>
                                    <p class="text-white/65 text-sm leading-relaxed">
                                        Tagihan seragam per anggota dengan fitur override untuk pengurus dan tabel pivot yang akurat.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile/Tablet Cards (< lg) -->
                    <div class="lg:hidden grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        <!-- Card 1 -->
                        <div class="glass border border-white/15 rounded-xl p-4 sm:p-5">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center shadow-lg shadow-purple-500/25">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm sm:text-base font-semibold text-white mb-1">Verifikasi Bertingkat</h3>
                                    <p class="text-white/65 text-xs sm:text-sm leading-relaxed">
                                        Pending hingga bendahara approve untuk menjaga integritas data.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Card 2 -->
                        <div class="glass border border-white/15 rounded-xl p-4 sm:p-5">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-lg bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center shadow-lg shadow-pink-500/25">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm sm:text-base font-semibold text-white mb-1">Kode Akses Unik</h3>
                                    <p class="text-white/65 text-xs sm:text-sm leading-relaxed">
                                        Join code aman, tanpa input manual berulang.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Card 3 -->
                        <div class="glass border border-white/15 rounded-xl p-4 sm:p-5 sm:col-span-2">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-lg bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-500/25">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm sm:text-base font-semibold text-white mb-1">Billing Fleksibel</h3>
                                    <p class="text-white/65 text-xs sm:text-sm leading-relaxed">
                                        Tagihan seragam/variatif dengan override berbasis pivot yang presisi.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Scroll Arrow -->
            <div class="hidden md:flex justify-center mt-8 lg:mt-12">
                <a href="#features" class="p-2 rounded-full border border-white/20 hover:border-white/40 hover:bg-white/10 transition-all duration-300 animate-bounce">
                    <svg class="w-5 h-5 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                    </svg>
                </a>
            </div>
        </div>
    </main>

    <!-- Divider -->
    <div class="relative h-16 sm:h-20 lg:h-24">
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-purple-900/20 to-transparent"></div>
    </div>

    <!-- FEATURES -->
    <section id="features" class="relative py-16 sm:py-20 lg:py-24 px-4 sm:px-6 lg:px-8 scroll-mt-20">
        <div class="max-w-7xl mx-auto">
            <!-- Section Header -->
            <div class="text-center mb-10 sm:mb-14 lg:mb-16">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 sm:px-4 sm:py-2 rounded-full glass border border-white/10 text-xs sm:text-sm text-white/80 mb-4 sm:mb-6">
                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Fitur Unggulan
                </div>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white mb-3 sm:mb-4">
                    Fitur Unggulan Sistem
                </h2>
                <p class="text-sm sm:text-base lg:text-lg text-white/70 max-w-2xl mx-auto leading-relaxed">
                    Solusi lengkap untuk transparansi dan akuntabilitas pengelolaan dana organisasi
                </p>
            </div>

            <!-- Features Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8">
                <!-- Feature 1 -->
                <div class="feature-card group glass border border-white/10 rounded-2xl p-5 sm:p-6 lg:p-7 hover:bg-white/10 hover:border-white/20 transition-all duration-300 hover:-translate-y-1">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center mb-4 sm:mb-5 group-hover:scale-110 transition-transform duration-300 shadow-lg shadow-purple-500/25">
                        <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <h3 class="text-base sm:text-lg lg:text-xl font-bold text-white mb-2 sm:mb-3">Multi-Tenancy Architecture</h3>
                    <p class="text-sm sm:text-base text-white/70 leading-relaxed">
                        Melayani banyak organisasi dalam satu platform dengan isolasi data yang aman dan privat untuk setiap entitas
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="feature-card group glass border border-white/10 rounded-2xl p-5 sm:p-6 lg:p-7 hover:bg-white/10 hover:border-white/20 transition-all duration-300 hover:-translate-y-1">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center mb-4 sm:mb-5 group-hover:scale-110 transition-transform duration-300 shadow-lg shadow-pink-500/25">
                        <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-base sm:text-lg lg:text-xl font-bold text-white mb-2 sm:mb-3">Two-Step Verification</h3>
                    <p class="text-sm sm:text-base text-white/70 leading-relaxed">
                        Verifikasi bertingkat dengan status pending hingga bendahara approve untuk menjaga integritas dan validitas data
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="feature-card group glass border border-white/10 rounded-2xl p-5 sm:p-6 lg:p-7 hover:bg-white/10 hover:border-white/20 transition-all duration-300 hover:-translate-y-1">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center mb-4 sm:mb-5 group-hover:scale-110 transition-transform duration-300 shadow-lg shadow-emerald-500/25">
                        <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </div>
                    <h3 class="text-base sm:text-lg lg:text-xl font-bold text-white mb-2 sm:mb-3">Unique Join Code</h3>
                    <p class="text-sm sm:text-base text-white/70 leading-relaxed">
                        Kode akses unik untuk manajemen keanggotaan mandiri yang aman tanpa input data manual berulang
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="feature-card group glass border border-white/10 rounded-2xl p-5 sm:p-6 lg:p-7 hover:bg-white/10 hover:border-white/20 transition-all duration-300 hover:-translate-y-1">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center mb-4 sm:mb-5 group-hover:scale-110 transition-transform duration-300 shadow-lg shadow-red-500/25">
                        <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-base sm:text-lg lg:text-xl font-bold text-white mb-2 sm:mb-3">Kalkulasi Tunggakan Otomatis</h3>
                    <p class="text-sm sm:text-base text-white/70 leading-relaxed">
                        Algoritma otomatis untuk menghitung defisit pembayaran anggota dengan laporan yang akurat dan terperinci
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="feature-card group glass border border-white/10 rounded-2xl p-5 sm:p-6 lg:p-7 hover:bg-white/10 hover:border-white/20 transition-all duration-300 hover:-translate-y-1">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-xl flex items-center justify-center mb-4 sm:mb-5 group-hover:scale-110 transition-transform duration-300 shadow-lg shadow-blue-500/25">
                        <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                        </svg>
                    </div>
                    <h3 class="text-base sm:text-lg lg:text-xl font-bold text-white mb-2 sm:mb-3">Flexible Billing System</h3>
                    <p class="text-sm sm:text-base text-white/70 leading-relaxed">
                        Tagihan seragam atau variatif per anggota dengan fitur override untuk pengurus melalui tabel pivot
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="feature-card group glass border border-white/10 rounded-2xl p-5 sm:p-6 lg:p-7 hover:bg-white/10 hover:border-white/20 transition-all duration-300 hover:-translate-y-1">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-pink-500 to-rose-600 rounded-xl flex items-center justify-center mb-4 sm:mb-5 group-hover:scale-110 transition-transform duration-300 shadow-lg shadow-rose-500/25">
                        <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-base sm:text-lg lg:text-xl font-bold text-white mb-2 sm:mb-3">Comprehensive Reporting</h3>
                    <p class="text-sm sm:text-base text-white/70 leading-relaxed">
                        Laporan keuangan lengkap dengan riwayat transaksi, status verifikasi, dan rekapitulasi untuk audit
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="relative py-16 sm:py-20 lg:py-24 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <div class="glass-strong border border-white/15 rounded-2xl sm:rounded-3xl p-6 sm:p-10 lg:p-12 text-center relative overflow-hidden">
                <!-- Background decoration -->
                <div class="absolute top-0 left-0 w-32 h-32 bg-blue-500/20 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 right-0 w-32 h-32 bg-purple-500/20 rounded-full blur-3xl"></div>
                
                <div class="relative z-10">
                    <h2 class="text-xl sm:text-2xl lg:text-3xl font-bold text-white mb-3 sm:mb-4">
                        Siap Mengelola Keuangan Organisasi Anda?
                    </h2>
                    <p class="text-sm sm:text-base text-white/70 mb-6 sm:mb-8 max-w-xl mx-auto">
                        Bergabunglah dengan ratusan organisasi yang telah mempercayakan pengelolaan kas mereka kepada KasKu
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center">
                        <a href="/register"
                            class="btn-primary px-6 sm:px-8 py-3 sm:py-3.5 rounded-xl font-semibold text-white shadow-xl shadow-blue-500/30 hover:shadow-blue-500/50 transition-all duration-300 hover:scale-[1.02] text-sm sm:text-base">
                            Daftar Gratis Sekarang
                        </a>
                        <a href="#"
                            class="px-6 sm:px-8 py-3 sm:py-3.5 border-2 border-white/20 rounded-xl font-semibold text-white hover:bg-white/10 transition-all duration-300 hover:border-white/40 text-sm sm:text-base">
                            Hubungi Kami
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="relative py-8 sm:py-10 px-4 sm:px-6 lg:px-8 border-t border-white/10">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <!-- Logo -->
                <a href="#" class="flex items-center gap-2.5 group">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-purple-600">
                        <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="text-base font-bold text-white">KasKu</span>
                </a>
                
                <!-- Copyright -->
                <p class="text-xs sm:text-sm text-white/50 text-center sm:text-left">
                    © 2024 KasKu. Hak cipta dilindungi undang-undang.
                </p>
                
                <!-- Links -->
                <div class="flex items-center gap-4 sm:gap-6">
                    <a href="#" class="text-xs sm:text-sm text-white/60 hover:text-white transition-colors">Privasi</a>
                    <a href="#" class="text-xs sm:text-sm text-white/60 hover:text-white transition-colors">Syarat</a>
                    <a href="#" class="text-xs sm:text-sm text-white/60 hover:text-white transition-colors">Bantuan</a>
                </div>
            </div>
        </div>
    </footer>

</body>

</html>