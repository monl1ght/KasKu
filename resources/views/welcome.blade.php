<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KasKu - Sistem Manajemen Kas Organisasi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
                transform: translateY(-20px);
            }
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

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        .animate-pulse-slow {
            animation: pulse-slow 3s ease-in-out infinite;
        }
    </style>
</head>

<!-- NOTE: body nggak lagi pakai gradient, gradient dipindah ke GLOBAL background -->

<body class="min-h-screen bg-slate-900 antialiased overflow-x-hidden">

    <nav class="fixed top-0 left-0 right-0 z-50 border-b border-white/10 bg-slate-900/50 backdrop-blur-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <a href="#" class="flex items-center gap-3 group">
                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 shadow-lg transition-transform group-hover:scale-110">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-white">KasKu</span>
                </a>

                <div class="flex items-center gap-3">
                    <a href="/login"
                        class="px-6 py-2.5 text-sm font-medium text-white hover:text-blue-400 transition-colors">
                        Masuk
                    </a>
                    <a href="/register"
                        class="px-6 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl hover:shadow-lg hover:shadow-blue-500/50 transition-all hover:scale-105">
                        Daftar Gratis
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- ✅ GLOBAL BACKGROUND (nyambung semua section) -->
    <div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
        <!-- Base gradient -->
        <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900"></div>

        <!-- Glow blobs -->
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-blue-500/30 rounded-full blur-3xl animate-pulse-slow"></div>
        <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-purple-500/30 rounded-full blur-3xl animate-pulse-slow"
            style="animation-delay: 1.5s;"></div>

        <!-- Grid overlay -->
        <div
            class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff05_1px,transparent_1px),linear-gradient(to_bottom,#ffffff05_1px,transparent_1px)] bg-[size:4rem_4rem]">
        </div>
    </div>

    <main class="relative min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8 pt-20">

        <div class="relative max-w-7xl mx-auto grid lg:grid-cols-2 gap-12 items-center">

            <div class="text-center lg:text-left space-y-8">
                <h1 class="text-5xl sm:text-6xl lg:text-6xl font-bold text-white leading-tight">
                    Platform Manajemen
                    <span class="bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">
                        Keuangan & Kas
                    </span>
                    Multi-Organisasi
                </h1>

                <p class="text-lg sm:text-xl text-white/70 max-w-2xl">
                    Sistem berbasis web dengan arsitektur Multi-Tenancy untuk mendigitalisasi pengelolaan dana iuran
                    organisasi, komunitas, dan kelas akademik dengan transparansi penuh dan isolasi data yang aman.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="/register"
                        class="group relative px-8 py-4 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl font-semibold text-white shadow-2xl shadow-blue-500/50 hover:shadow-blue-500/70 transition-all hover:scale-105">
                        <span class="relative z-10">Mulai Gratis Sekarang</span>
                        <div
                            class="absolute inset-0 rounded-xl bg-gradient-to-r from-blue-600 to-purple-700 opacity-0 group-hover:opacity-100 transition-opacity">
                        </div>
                    </a>

                    <a href="#features"
                        class="group px-8 py-4 border-2 border-white/20 rounded-xl font-semibold text-white hover:bg-white/10 transition-all hover:border-white/40 backdrop-blur-sm">
                        Pelajari Lebih Lanjut
                        <svg class="inline-block ml-2 w-5 h-5 group-hover:translate-x-1 transition-transform"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>

                <div class="grid grid-cols-3 gap-6 pt-8">
                    <!-- isi statistik kalau perlu -->
                </div>
            </div>

            <div class="relative hidden lg:block">
                <div class="space-y-6 animate-float">

                    <div
                        class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6 shadow-2xl hover:bg-white/15 transition-all">
                        <div class="flex items-start gap-4">
                            <div
                                class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-white mb-2">Verifikasi Bertingkat</h3>
                                <p class="text-white/70 text-sm">
                                    Two-step verification dengan upload bukti pembayaran digital. Status pending hingga
                                    bendahara approve untuk menjaga integritas data
                                </p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6 shadow-2xl hover:bg-white/15 transition-all ml-8">
                        <div class="flex items-start gap-4">
                            <div
                                class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-white mb-2">Kode Akses Unik</h3>
                                <p class="text-white/70 text-sm">
                                    Sistem join code untuk manajemen keanggotaan mandiri yang aman. Anggota dapat
                                    bergabung tanpa input data manual berulang
                                </p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl p-6 shadow-2xl hover:bg-white/15 transition-all">
                        <div class="flex items-start gap-4">
                            <div
                                class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-white mb-2">Billing Fleksibel</h3>
                                <p class="text-white/70 text-sm">
                                    Tagihan seragam per anggota. Fitur override untuk pengurus inti dengan
                                    tabel pivot yang akurat
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
            <svg class="w-6 h-6 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
            </svg>
        </div>
    </main>

    <!-- ✅ Divider halus biar transisi makin mulus (opsional, tapi cakep) -->
    <div class="relative h-16 -mt-8">
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-purple-900/30 to-transparent"></div>
    </div>

    <section id="features" class="relative py-24 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-white mb-4">Fitur Unggulan Sistem</h2>
                <p class="text-xl text-white/70 max-w-2xl mx-auto">
                    Solusi lengkap untuk transparansi dan akuntabilitas pengelolaan dana organisasi
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div
                    class="group bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-8 hover:bg-white/10 hover:border-white/20 transition-all hover:scale-105">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Multi-Tenancy Architecture</h3>
                    <p class="text-white/70">Melayani banyak organisasi dalam satu platform dengan isolasi data yang
                        aman dan privat untuk setiap entitas</p>
                </div>

                <div
                    class="group bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-8 hover:bg-white/10 hover:border-white/20 transition-all hover:scale-105">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Two-Step Verification</h3>
                    <p class="text-white/70">Verifikasi bertingkat dengan status pending hingga bendahara approve untuk
                        menjaga integritas dan validitas data keuangan</p>
                </div>

                <div
                    class="group bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-8 hover:bg-white/10 hover:border-white/20 transition-all hover:scale-105">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Unique Join Code</h3>
                    <p class="text-white/70">Kode akses unik untuk manajemen keanggotaan mandiri yang aman tanpa input
                        data manual berulang</p>
                </div>

                <div
                    class="group bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-8 hover:bg-white/10 hover:border-white/20 transition-all hover:scale-105">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Kalkulasi Tunggakan Otomatis</h3>
                    <p class="text-white/70">Algoritma otomatis untuk menghitung defisit pembayaran anggota dengan
                        laporan yang akurat dan terperinci</p>
                </div>

                <div
                    class="group bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-8 hover:bg-white/10 hover:border-white/20 transition-all hover:scale-105">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Flexible Billing System</h3>
                    <p class="text-white/70">Tagihan seragam atau variatif per anggota dengan fitur override untuk
                        pengurus melalui tabel pivot</p>
                </div>

                <div
                    class="group bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-8 hover:bg-white/10 hover:border-white/20 transition-all hover:scale-105">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-pink-500 to-rose-600 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Comprehensive Reporting</h3>
                    <p class="text-white/70">Laporan keuangan lengkap dengan riwayat transaksi, status verifikasi, dan
                        rekapitulasi untuk audit & akuntabilitas</p>
                </div>
            </div>
        </div>
    </section>

</body>

</html>
