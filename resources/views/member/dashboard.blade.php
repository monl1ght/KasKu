<x-layouts.app-layout-anggota>
    {{-- Header Content (Judul Halaman) --}}
    @section('page-title', 'Dashboard Anggota')
    @section('page-subtitle', 'Ringkasan kewajiban finansial pribadi Anda')

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @endpush

    <div class="space-y-6">

        {{-- SECTION 1: WELCOME CARD & QUICK STATUS --}}
        <div class="grid lg:grid-cols-3 gap-6">

            {{-- Welcome Card --}}
            <div
                class="lg:col-span-2 rounded-2xl bg-gradient-to-r from-blue-600 to-purple-600 p-6 shadow-lg relative overflow-hidden group">
                <div
                    class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white/20 rounded-full blur-2xl group-hover:scale-110 transition-transform duration-700">
                </div>
                <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>

                <div class="relative z-10">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h2 class="text-2xl font-bold text-white mb-1">Halo, {{ Auth::user()->name ?? 'Anggota' }}!
                                👋</h2>
                            <p class="text-white/80 text-sm">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                            </p>
                        </div>

                        {{-- SERVER-SIDE SAFE CALCULATION --}}
                        @php
                            // ensure numeric defaults (these variables coming from controller)
                            $total = (int) ($total ?? 0);
                            $paid = (int) ($paid ?? 0);
                            $remaining = (int) ($remaining ?? 0);
                            $percent = (int) ($percent ?? 0);

                            $status = $remaining <= 0 ? 'lunas' : ($percent >= 50 ? 'baik' : 'perlu-perhatian');
                        @endphp

                        <div
                            class="px-3 py-1.5 rounded-full text-xs font-semibold {{ $status === 'lunas' ? 'bg-white text-emerald-600' : ($status === 'baik' ? 'bg-white/20 text-white border border-white/30' : 'bg-red-500/20 text-white border border-red-300/30') }}">
                            @if ($status === 'lunas')
                                ✓ Lunas
                            @elseif($status === 'baik')
                                Status Baik
                            @else
                                ⚠ Perlu Perhatian
                            @endif
                        </div>
                    </div>

                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20 mb-4">
                        <div class="flex items-center gap-4">
                            <div class="flex-1">
                                <p class="text-xs text-white/70 mb-1">Progress Pembayaran</p>
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 bg-white/20 rounded-full h-2.5 overflow-hidden">
                                        <div class="h-2.5 rounded-full bg-white transition-all duration-500"
                                            style="width: {{ $percent }}%;">
                                        </div>
                                    </div>
                                    <span class="text-white font-bold text-sm min-w-[45px]">{{ $percent }}%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <a href="{{ route('member.PembayaranKas') }}"
                            class="flex-1 px-4 py-2.5 bg-white text-emerald-600 rounded-lg font-semibold text-sm hover:bg-emerald-50 transition-colors shadow-lg flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            Bayar Sekarang
                        </a>
                    </div>
                </div>
            </div>

            {{-- Payment Status Quick View --}}
            <div
                class="rounded-2xl bg-slate-800 border border-white/10 p-6 flex flex-col justify-between relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-transparent"></div>

                <div class="relative z-10">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="p-2 bg-blue-500/20 rounded-lg text-blue-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-white/70">Status Tagihan</span>
                    </div>

                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-white/50">Total Tagihan</span>
                            <span class="text-sm font-bold text-white">Rp
                                {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-white/50">Sudah Dibayar</span>
                            <span class="text-sm font-bold text-emerald-400">Rp
                                {{ number_format($paid, 0, ',', '.') }}</span>
                        </div>
                        <div class="h-px bg-white/10"></div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-white/50">Sisa Tunggakan</span>
                            <span class="text-lg font-bold {{ $remaining > 0 ? 'text-red-400' : 'text-emerald-400' }}">
                                Rp {{ number_format($remaining, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    @if ($remaining > 0)
                        <div class="p-3 bg-red-500/10 border border-red-500/20 rounded-lg">
                            <p class="text-xs text-red-400 flex items-start gap-2">
                                <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <span>Segera lunasi untuk menjaga status keaktifan Anda</span>
                            </p>
                        </div>
                    @else
                        <div class="p-3 bg-emerald-500/10 border border-emerald-500/20 rounded-lg">
                            <p class="text-xs text-emerald-400 flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Semua tagihan telah lunas!
                            </p>
                        </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- SECTION 2: DETAILED STATISTICS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <div
                class="rounded-2xl bg-white/5 border border-white/10 p-5 backdrop-blur-sm hover:bg-white/10 transition-all group">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-sm font-medium text-white/50">Total Tagihan</p>
                        <h3 class="text-2xl font-bold text-white mt-1">Rp
                            {{ number_format($total, 0, ',', '.') }}</h3>
                    </div>
                    <div class="p-2 bg-blue-500/20 rounded-lg text-blue-400 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
                <p class="text-xs text-white/40">Seluruh tagihan yang dibebankan</p>
            </div>

            <div
                class="rounded-2xl bg-white/5 border border-white/10 p-5 backdrop-blur-sm hover:bg-white/10 transition-all group">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-sm font-medium text-white/50">Sudah Dibayar</p>
                        <h3 class="text-2xl font-bold text-white mt-1">Rp
                            {{ number_format($paid, 0, ',', '.') }}</h3>
                    </div>
                    <div
                        class="p-2 bg-emerald-500/20 rounded-lg text-emerald-400 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-center text-xs">
                    <span class="text-emerald-400 flex items-center font-medium">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7" />
                        </svg>
                        {{ $percent }}% Complete
                    </span>
                </div>
            </div>

            <div
                class="rounded-2xl bg-white/5 border border-white/10 p-5 backdrop-blur-sm hover:bg-white/10 transition-all group">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-sm font-medium text-white/50">Sisa Tunggakan</p>
                        <h3
                            class="text-2xl font-bold {{ $remaining > 0 ? 'text-red-400' : 'text-emerald-400' }} mt-1">
                            Rp {{ number_format($remaining, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div
                        class="p-2 {{ $remaining > 0 ? 'bg-red-500/20 text-red-400' : 'bg-emerald-500/20 text-emerald-400' }} rounded-lg group-hover:scale-110 transition-transform">
                        @if ($remaining > 0)
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @else
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        @endif
                    </div>
                </div>
                <p class="text-xs text-white/40">
                    @if ($remaining > 0)
                        Segera lunasi untuk status aktif
                    @else
                        Semua kewajiban telah terpenuhi
                    @endif
                </p>
            </div>

        </div>

        {{-- SECTION 3: CHART & ACTIVITY --}}
        <div class="grid lg:grid-cols-3 gap-6">

            {{-- Donut Chart with Center Text --}}
            <div class="lg:col-span-2 rounded-2xl bg-white/5 border border-white/10 p-6 backdrop-blur-sm">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-white">Visualisasi Pembayaran</h3>
                    <div class="text-xs text-white/40">
                        Terakhir diperbarui: {{ now()->translatedFormat('j M Y, H:i') }}
                    </div>
                </div>

                <div class="flex flex-col md:flex-row gap-8 items-center">
                    {{-- Chart --}}
                    <div class="relative w-64 h-64 flex-shrink-0">
                        <canvas id="memberProgressChart"></canvas>
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <div class="text-center">
                                <div class="text-4xl font-bold text-white">{{ $percent }}%</div>
                                <div class="text-xs text-white/50 mt-1">Terbayar</div>
                            </div>
                        </div>
                    </div>

                    {{-- Legend & Details --}}
                    <div class="flex-1 w-full">
                        <div class="space-y-4">
                            <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-3 h-3 bg-emerald-500 rounded-full"></div>
                                    <span class="text-sm font-medium text-white/70">Sudah Dibayar</span>
                                </div>
                                <div class="text-2xl font-bold text-emerald-400">
                                    Rp {{ number_format($paid, 0, ',', '.') }}
                                </div>
                                <div class="text-xs text-white/40 mt-1">
                                    {{ $percent }}% dari total tagihan
                                </div>
                            </div>

                            <div class="p-4 bg-white/5 border border-white/10 rounded-xl">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-3 h-3 bg-white/20 rounded-full"></div>
                                    <span class="text-sm font-medium text-white/70">Sisa Tunggakan</span>
                                </div>
                                <div class="text-2xl font-bold text-white">
                                    Rp {{ number_format($remaining, 0, ',', '.') }}
                                </div>
                                <div class="text-xs text-white/40 mt-1">
                                    {{ 100 - $percent }}% belum terbayar
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 p-4 bg-blue-500/10 border border-blue-500/20 rounded-xl">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-blue-400">Informasi</p>
                                    <p class="text-xs text-white/60 mt-1">
                                        Data pembayaran diperbarui secara real-time setelah verifikasi bendahara.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RECENT ACTIVITY Enhanced --}}
            <div class="rounded-2xl bg-white/5 border border-white/10 p-6 backdrop-blur-sm">
                <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Aktivitas Terakhir
                </h3>

                <div class="space-y-4 max-h-80 overflow-y-auto pr-2 custom-scrollbar">
                    @forelse($recentActivities ?? [] as $act)
                        <div class="flex gap-3 group hover:bg-white/5 p-2 rounded-lg transition-colors -ml-2">
                            @php
                                $iconColor = match ($act['type'] ?? 'default') {
                                    'payment' => 'bg-emerald-500/20 text-emerald-400',
                                    'pending' => 'bg-orange-500/20 text-orange-400',
                                    'rejected' => 'bg-red-500/20 text-red-400',
                                    'info' => 'bg-blue-500/20 text-blue-400',
                                    default => 'bg-slate-700 text-white/70',
                                };
                            @endphp
                            <div
                                class="flex-shrink-0 w-8 h-8 rounded-full {{ $iconColor }} flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-white font-medium truncate">{{ $act['title'] ?? 'Aktivitas' }}
                                </p>
                                <p class="text-xs text-white/50 truncate">{{ $act['subtitle'] ?? '' }}</p>
                                <p class="text-[10px] text-white/30 mt-1">
                                    <time datetime="{{ $act['time_iso'] ?? now()->toIsoString() }}">
                                        {{ $act['time_human'] ?? 'baru saja' }}
                                    </time>
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <div
                                class="w-12 h-12 bg-white/5 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-white/30" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                            </div>
                            <p class="text-sm text-white/50">Belum ada aktivitas</p>
                            <p class="text-xs text-white/30 mt-1">Aktivitas Anda akan muncul di sini</p>
                        </div>
                    @endforelse
                </div>

                <a href="{{ route('member.RiwayatTransaksi') }}"
                    class="w-full mt-6 inline-block text-center py-2.5 text-xs text-white/50 hover:text-white border border-white/10 rounded-lg hover:bg-white/5 transition-colors font-medium">
                    Lihat Semua Aktivitas →
                </a>
            </div>

        </div>

    </div>

    {{-- Prepare JS-safe values (avoid @json($var ?? 0) directly) --}}
    @php
        $jsTotal = $total ?? 0;
        $jsPaid = $paid ?? 0;
        $jsRemaining = $remaining ?? 0;
    @endphp

    {{-- SCRIPTS --}}
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const total = Number(@json($jsTotal));
                const paid = Number(@json($jsPaid));
                // remaining computed server-side and clamped to >=0
                const remainingServer = Number(@json($jsRemaining));

                // For chart: ensure paid slice never exceeds total (visual clamp)
                const paidForChart = total > 0 ? Math.min(paid, total) : paid;
                const remainingForChart = Math.max(0, total - paidForChart);

                const ctx = document.getElementById('memberProgressChart').getContext('2d');

                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Dibayar', 'Sisa'],
                        datasets: [{
                            data: [paidForChart, remainingForChart],
                            borderWidth: 0,
                            backgroundColor: [
                                'rgba(16, 185, 129, 0.9)', // emerald-500
                                'rgba(255, 255, 255, 0.08)' // white/8
                            ],
                            hoverBackgroundColor: [
                                'rgba(16, 185, 129, 1)',
                                'rgba(255, 255, 255, 0.12)'
                            ],
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        cutout: '75%',
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                borderColor: 'rgba(255, 255, 255, 0.1)',
                                borderWidth: 1,
                                callbacks: {
                                    label: function(context) {
                                        const v = context.parsed;
                                        return context.label + ': Rp ' + (v || 0).toLocaleString('id-ID');
                                    }
                                }
                            }
                        }
                    }
                });
            });
        </script>

        <style>
            /* Custom Scrollbar */
            .custom-scrollbar::-webkit-scrollbar {
                width: 6px;
            }

            .custom-scrollbar::-webkit-scrollbar-track {
                background: rgba(255, 255, 255, 0.05);
                border-radius: 10px;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: rgba(255, 255, 255, 0.2);
                border-radius: 10px;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: rgba(255, 255, 255, 0.3);
            }
        </style>
    @endpush

</x-layouts.app-layout-anggota>
