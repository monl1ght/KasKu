<x-layouts.app-layout>

    @section('page-title', 'Detail Tunggakan')
    @section('page-subtitle', 'Rincian tunggakan pembayaran anggota')

    <div class="space-y-6">

        {{-- Back Button --}}
        <div>
            <a href="{{ route('laporan.rekapitulasi') }}" 
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-white/20 bg-white/5 text-white hover:bg-white/10 transition-all group">
                <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                <span class="text-sm font-medium">Kembali</span>
            </a>
        </div>

        {{-- User Info Card --}}
        <div class="rounded-2xl border border-white/20 bg-gradient-to-br from-red-500/20 to-orange-500/20 backdrop-blur-md p-6 shadow-lg relative overflow-hidden">
            {{-- Glow Effect --}}
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-red-500/30 rounded-full blur-[80px] pointer-events-none"></div>
            <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-orange-500/30 rounded-full blur-[80px] pointer-events-none"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    {{-- Avatar --}}
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-red-500 to-orange-600 shadow-lg">
                        <span class="text-2xl font-bold text-white">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                    </div>
                    
                    {{-- User Info --}}
                    <div>
                        <h2 class="text-2xl font-bold text-white mb-1">{{ $user->name }}</h2>
                        <div class="flex items-center gap-3 text-sm text-white/70">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                ID: {{ $user->id }}
                            </span>
                            @if($user->email)
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    {{ $user->email }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Warning Badge --}}
                <div class="flex items-center gap-2 px-4 py-2 rounded-xl bg-red-500/20 border border-red-500/30">
                    <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span class="text-sm font-semibold text-red-300">Memiliki Tunggakan</span>
                </div>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Total Tunggakan --}}
            <div class="rounded-xl border border-white/20 bg-white/10 backdrop-blur-md p-6 shadow-lg hover:bg-white/15 transition-all group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-white/60 mb-1">Total Tunggakan</p>
                        <p class="text-3xl font-bold text-red-400">
                            Rp {{ number_format($totalTunggakan, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-red-500 to-pink-600 shadow-lg group-hover:scale-110 transition-transform">
                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Jumlah Tagihan --}}
            <div class="rounded-xl border border-white/20 bg-white/10 backdrop-blur-md p-6 shadow-lg hover:bg-white/15 transition-all group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-white/60 mb-1">Jumlah Tagihan</p>
                        <p class="text-3xl font-bold text-white">
                            {{ count($bills) }}
                        </p>
                        <p class="text-xs text-white/40 mt-1">Belum Dibayar</p>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-orange-500 to-yellow-600 shadow-lg group-hover:scale-110 transition-transform">
                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Rata-rata per Tagihan --}}
            <div class="rounded-xl border border-white/20 bg-white/10 backdrop-blur-md p-6 shadow-lg hover:bg-white/15 transition-all group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-white/60 mb-1">Rata-rata</p>
                        <p class="text-3xl font-bold text-white">
                            Rp {{ count($bills) > 0 ? number_format($totalTunggakan / count($bills), 0, ',', '.') : '0' }}
                        </p>
                        <p class="text-xs text-white/40 mt-1">Per Tagihan</p>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 shadow-lg group-hover:scale-110 transition-transform">
                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Bills Table --}}
        <div class="rounded-xl border border-white/20 bg-white/10 backdrop-blur-md shadow-lg overflow-hidden">
            
            {{-- Header --}}
            <div class="border-b border-white/20 bg-white/5 px-6 py-4">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Daftar Tunggakan
                </h2>
                <p class="text-sm text-white/60 mt-1">Tagihan yang belum diselesaikan</p>
            </div>

            {{-- Desktop Table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-white/5 border-b border-white/10">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-white/70 uppercase tracking-wider">Nama Tagihan</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-white/70 uppercase tracking-wider">Periode</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-white/70 uppercase tracking-wider">Nominal</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-white/70 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-white/70 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse($bills as $bill)
                            <tr class="hover:bg-white/5 transition-colors group">
                                {{-- Nama Tagihan --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 bg-red-500/20 rounded-lg group-hover:bg-red-500/30 transition-colors">
                                            <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-white">{{ $bill->nama }}</p>
                                            <p class="text-xs text-white/50">Menunggak</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Periode --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <svg class="h-4 w-4 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span class="text-sm text-white/70">{{ $bill->periode }}</span>
                                    </div>
                                </td>

                                {{-- Nominal --}}
                                <td class="px-6 py-4">
                                    <p class="text-sm font-bold text-red-400">Rp {{ number_format($bill->amount, 0, ',', '.') }}</p>
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-red-500/20 text-red-300 border border-red-500/30">
                                        <span class="w-1.5 h-1.5 rounded-full mr-2 bg-red-400 animate-pulse"></span>
                                        Belum Dibayar
                                    </span>
                                </td>

                                {{-- Aksi --}}
                                <td class="px-6 py-4 text-right">
                                    <button class="p-2 hover:bg-white/10 rounded-lg text-white/60 hover:text-blue-400 transition-all" title="Kirim Reminder">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-16 px-6">
                                    <div class="text-center">
                                        <div class="flex justify-center mb-4">
                                            <div class="flex h-20 w-20 items-center justify-center rounded-full bg-emerald-500/20">
                                                <svg class="h-10 w-10 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                        </div>
                                        <h3 class="text-lg font-semibold text-white mb-2">Tidak Ada Tunggakan! 🎉</h3>
                                        <p class="text-sm text-white/60">Semua tagihan telah diselesaikan</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="md:hidden divide-y divide-white/10">
                @forelse($bills as $bill)
                    <div class="p-6 hover:bg-white/5 transition-all">
                        <div class="flex items-start gap-4">
                            {{-- Icon --}}
                            <div class="p-3 bg-red-500/20 rounded-xl flex-shrink-0">
                                <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-3 mb-3">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-white">{{ $bill->nama }}</h3>
                                        <p class="text-xs text-white/50 mt-0.5">{{ $bill->periode }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-red-500/20 text-red-300 border border-red-500/30 flex-shrink-0">
                                        <span class="w-1.5 h-1.5 rounded-full mr-1.5 bg-red-400 animate-pulse"></span>
                                        Belum Lunas
                                    </span>
                                </div>

                                {{-- Amount --}}
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-sm text-white/60">Nominal</span>
                                    <span class="text-lg font-bold text-red-400">Rp {{ number_format($bill->amount, 0, ',', '.') }}</span>
                                </div>

                                {{-- Action --}}
                                <button class="w-full flex items-center justify-center gap-2 px-4 py-2 rounded-lg border border-white/20 bg-white/5 text-white hover:bg-white/10 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-sm font-medium">Kirim Reminder</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <div class="flex justify-center mb-4">
                            <div class="flex h-20 w-20 items-center justify-center rounded-full bg-emerald-500/20">
                                <svg class="h-10 w-10 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-lg font-semibold text-white mb-2">Tidak Ada Tunggakan! 🎉</h3>
                        <p class="text-sm text-white/60">Semua tagihan telah diselesaikan</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</x-layouts.app-layout>