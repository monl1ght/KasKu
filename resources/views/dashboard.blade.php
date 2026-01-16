<x-layouts.app-layout>
    {{-- Header Content (Judul Halaman) --}}
    @section('page-title', 'Dashboard')
    @section('page-subtitle', 'Pusat kendali keuangan organisasi Anda')

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @endpush

    <div class="space-y-6">

        {{-- SECTION 1: JOIN CODE & WELCOME --}}
        <div class="grid lg:grid-cols-3 gap-6">

            {{-- Welcome Card --}}
            <div
                class="lg:col-span-2 rounded-2xl bg-gradient-to-r from-blue-600 to-purple-600 p-6 shadow-lg relative overflow-hidden group">
                <div
                    class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white/20 rounded-full blur-2xl group-hover:scale-110 transition-transform duration-700">
                </div>
                <div class="relative z-10">
                    <h2 class="text-2xl font-bold text-white mb-2">Halo, {{ Auth::user()->name ?? 'Bendahara' }}! 👋</h2>
                    <p class="text-white/80 max-w-xl">
                        Ada {{ $pendingTodayCount ?? 0 }} transaksi baru yang perlu diverifikasi hari ini.
                        @if (($pendingCount ?? 0) > ($pendingTodayCount ?? 0))
                            <span class="text-white/70">({{ $pendingCount }} total pending)</span>
                        @endif
                    </p>
                    <div class="mt-6 flex gap-3">
                        <a href="{{ route('verifikasi.pembayaran') }}"
                            class="px-4 py-2 bg-white text-blue-600 rounded-lg font-semibold text-sm hover:bg-blue-50 transition-colors">
                            Verifikasi Pembayaran
                        </a>
                        <a href="{{ route('laporan.rekapitulasi') }}"
                            class="px-4 py-2 bg-black/20 text-white border border-white/20 rounded-lg font-semibold text-sm hover:bg-black/30 transition-colors">
                            Lihat Laporan
                        </a>
                    </div>
                </div>
            </div>

            {{-- Join Code Card (Copy Fix: Clipboard API + fallback) --}}
            <div data-code="{{ $activeOrganization->code ?? '' }}" x-data="{
                code: $el.dataset.code || '',
                copied: false,
                error: false,
            
                async copyToClipboard() {
                    this.error = false;
                    if (!this.code) return;
            
                    try {
                        // 1) Clipboard API (butuh HTTPS / secure context)
                        if (navigator.clipboard && window.isSecureContext) {
                            await navigator.clipboard.writeText(this.code);
                        } else {
                            // 2) Fallback untuk HTTP / not-secure
                            const el = this.$refs.fallback;
                            el.value = this.code;
            
                            // pastikan bisa di-select
                            el.removeAttribute('readonly');
                            el.focus();
                            el.select();
                            el.setSelectionRange(0, el.value.length);
            
                            const ok = document.execCommand('copy');
                            el.setAttribute('readonly', 'readonly');
                            el.blur();
            
                            if (!ok) throw new Error('execCommand copy failed');
                        }
            
                        this.copied = true;
                        setTimeout(() => this.copied = false, 2000);
                    } catch (e) {
                        console.error(e);
                        this.error = true;
            
                        // fallback terakhir: auto select kode biar user bisa Ctrl+C
                        const el = this.$refs.fallback;
                        el.value = this.code;
                        el.removeAttribute('readonly');
                        el.focus();
                        el.select();
                        el.setSelectionRange(0, el.value.length);
                        el.setAttribute('readonly', 'readonly');
                    }
                }
            }"
                class="rounded-2xl bg-slate-800 border border-white/10 p-6 flex flex-col justify-center relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/10 to-transparent"></div>

                {{-- input hidden untuk fallback copy (HARUS ADA DI DOM, jangan display:none) --}}
                <input x-ref="fallback" type="text" readonly
                    class="fixed -left-[9999px] top-0 opacity-0 pointer-events-none" value="">

                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-emerald-400 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                            Kode Organisasi
                        </span>
                    </div>

                    <h3 class="text-3xl font-mono font-bold text-white tracking-wider mb-4" x-text="code || '-'"></h3>

                    <button type="button" @click="copyToClipboard()"
                        class="w-full py-2.5 rounded-xl border border-dashed border-white/20 text-sm font-medium transition-all duration-200 flex items-center justify-center gap-2"
                        :class="copied
                            ?
                            'bg-emerald-500/10 text-emerald-400 border-emerald-500' :
                            (error ?
                                'bg-red-500/10 text-red-300 border-red-500/40 hover:bg-red-500/15' :
                                'text-white/60 hover:bg-white/5 hover:border-emerald-500 hover:text-emerald-400')">
                        <template x-if="copied">
                            <span class="flex items-center gap-2">✓ Berhasil Disalin</span>
                        </template>

                        <template x-if="!copied && !error">
                            <span class="flex items-center gap-2">Salin Kode</span>
                        </template>

                        <template x-if="error">
                            <span class="flex items-center gap-2">Gagal salin — tekan Ctrl+C</span>
                        </template>
                    </button>

                    <p class="text-xs text-white/40 mt-3 text-center">
                        Bagikan kode ini kepada anggota baru
                    </p>

                    {{-- Kalau mau, tampilkan hint saat error --}}
                    <p x-show="error" x-cloak class="text-[11px] text-red-300/80 mt-2 text-center">
                        Kode sudah terseleksi otomatis. Tekan <span class="font-semibold">Ctrl+C</span> untuk menyalin.
                    </p>
                </div>
            </div>


        </div>

        {{-- SECTION 2: MACRO STATISTICS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- Saldo Kas --}}
            <div
                class="rounded-2xl bg-white/5 border border-white/10 p-5 backdrop-blur-sm hover:bg-white/10 transition-colors">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-sm font-medium text-white/50">Total Saldo Kas</p>
                        <h3 class="text-2xl font-bold text-white mt-1">
                            Rp {{ number_format((float) ($saldoKas ?? 0), 0, ',', '.') }}
                        </h3>
                        <p class="text-[11px] text-white/40 mt-1">
                            Periode: {{ $from ?? '-' }} s/d {{ $to ?? '-' }}
                        </p>
                    </div>
                    <div class="p-2 bg-green-500/20 rounded-lg text-green-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-center text-xs">
                    @php
                        $growth = (float) ($saldoGrowthPercent ?? 0);
                        $growthClass = $growth >= 0 ? 'text-green-400' : 'text-red-400';
                    @endphp
                    <span class="{{ $growthClass }} flex items-center font-medium">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                        {{ $growth >= 0 ? '+' : '' }}{{ $growth }}%
                    </span>
                    <span class="text-white/40 ml-2">dibanding periode sebelumnya</span>
                </div>
            </div>

            {{-- Total Tunggakan --}}
            <div
                class="rounded-2xl bg-white/5 border border-white/10 p-5 backdrop-blur-sm hover:bg-white/10 transition-colors">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-sm font-medium text-white/50">Total Tunggakan</p>
                        <h3 class="text-2xl font-bold text-white mt-1">
                            Rp {{ number_format((float) ($totalTunggakan ?? 0), 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="p-2 bg-red-500/20 rounded-lg text-red-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-center text-xs">
                    <span class="text-red-400 flex items-center font-medium">
                        {{ (int) ($tunggakanMembersCount ?? 0) }} Anggota
                    </span>
                    <span class="text-white/40 ml-2">belum melunasi tagihan</span>
                </div>
            </div>

            {{-- Anggota Aktif --}}
            <div
                class="rounded-2xl bg-white/5 border border-white/10 p-5 backdrop-blur-sm hover:bg-white/10 transition-colors">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <p class="text-sm font-medium text-white/50">Anggota Aktif</p>
                        <h3 class="text-2xl font-bold text-white mt-1">
                            {{ (int) ($anggotaAktifCount ?? 0) }} Orang
                        </h3>
                    </div>
                    <div class="p-2 bg-blue-500/20 rounded-lg text-blue-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-center text-xs">
                    <span class="text-green-400 flex items-center font-medium">
                        +{{ (int) ($anggotaBaruMingguIniCount ?? 0) }} Baru
                    </span>
                    <span class="text-white/40 ml-2">bergabung minggu ini</span>
                </div>
            </div>

        </div>

        {{-- SECTION 3: GRAPH & RECENT ACTIVITY --}}
        <div class="grid lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 rounded-2xl bg-white/5 border border-white/10 p-6 backdrop-blur-sm"
                x-data="{
                    range: '{{ $range ?? '6m' }}',
                    from: '{{ $from ?? '' }}',
                    to: '{{ $to ?? '' }}'
                }">
                <div class="flex flex-col gap-3 mb-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-white">Tren Pemasukan</h3>

                        {{-- FILTER FORM (GET) --}}
                        <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2">
                            <select name="range" x-model="range"
                                class="bg-slate-900 border border-white/10 text-white text-xs rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="6m">6 Bulan Terakhir</option>
                                <option value="ytd">Tahun Ini</option>
                                <option value="30d">30 Hari Terakhir</option>
                                <option value="custom">Custom</option>
                            </select>

                            <template x-if="range === 'custom'">
                                <div class="flex items-center gap-2">
                                    <input type="date" name="from" x-model="from"
                                        class="bg-slate-900 border border-white/10 text-white text-xs rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500" />
                                    <span class="text-white/40 text-xs">s/d</span>
                                    <input type="date" name="to" x-model="to"
                                        class="bg-slate-900 border border-white/10 text-white text-xs rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500" />
                                </div>
                            </template>

                            {{-- kalau bukan custom, tetap kirim from/to yang sekarang supaya controller konsisten --}}
                            <input type="hidden" name="from" :value="range === 'custom' ? from : ''">
                            <input type="hidden" name="to" :value="range === 'custom' ? to : ''">

                            <button type="submit"
                                class="px-3 py-2 text-xs font-semibold rounded-lg bg-white/10 text-white hover:bg-white/15 border border-white/10 transition">
                                Terapkan
                            </button>
                        </form>
                    </div>

                    <p class="text-xs text-white/40">
                        Menampilkan pemasukan <span class="text-white/70 font-medium">{{ $from ?? '-' }}</span> s/d
                        <span class="text-white/70 font-medium">{{ $to ?? '-' }}</span>
                    </p>
                </div>

                <div class="relative h-72 w-full">
                    <canvas id="incomeChart"></canvas>
                </div>
            </div>

            <div class="rounded-2xl bg-white/5 border border-white/10 p-6 backdrop-blur-sm">
                <h3 class="text-lg font-bold text-white mb-4">Aktivitas Terbaru</h3>

                <div class="space-y-4">
                    @forelse(($recentActivities ?? []) as $a)
                        @php
                            $type = $a['type'] ?? 'payment_pending';

                            $iconWrap = match ($type) {
                                'payment_confirmed' => 'bg-green-500/20 text-green-400',
                                'payment_rejected' => 'bg-red-500/20 text-red-400',
                                'join_pending' => 'bg-blue-500/20 text-blue-400',
                                default => 'bg-orange-500/20 text-orange-400',
                            };

                            $iconPath = match ($type) {
                                'payment_confirmed' => 'M5 13l4 4L19 7',
                                'payment_rejected' => 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                                'join_pending'
                                    => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z',
                                default => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                            };
                        @endphp

                        <div class="flex gap-3">
                            <div
                                class="flex-shrink-0 w-8 h-8 rounded-full {{ $iconWrap }} flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="{{ $iconPath }}" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-white font-medium">{{ $a['title'] ?? 'Aktivitas' }}</p>
                                <p class="text-xs text-white/50">{{ $a['subtitle'] ?? '-' }}</p>
                                <p class="text-[10px] text-white/30 mt-1">{{ $a['time_human'] ?? '' }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-white/50">Belum ada aktivitas.</p>
                    @endforelse
                </div>


            </div>

        </div>

    </div>

    {{-- SCRIPT CHART.JS INITIALIZATION --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const canvas = document.getElementById('incomeChart');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');

            let gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(59, 130, 246, 0.5)');
            gradient.addColorStop(1, 'rgba(147, 51, 234, 0.05)');

            const labels = @json($incomeChartLabels ?? []);
            const dataPoints = @json($incomeChartData ?? []);

            // optional: destroy jika ada re-render (biar aman)
            if (window.__incomeChart) {
                window.__incomeChart.destroy();
            }

            window.__incomeChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pemasukan (Rp)',
                        data: dataPoints,
                        borderColor: '#8b5cf6',
                        backgroundColor: gradient,
                        borderWidth: 2,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#8b5cf6',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(255, 255, 255, 0.05)'
                            },
                            ticks: {
                                color: 'rgba(255, 255, 255, 0.5)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: 'rgba(255, 255, 255, 0.5)'
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-layouts.app-layout>
