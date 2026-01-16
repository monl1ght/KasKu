<x-layouts.app-layout>

    @section('page-title', 'Verifikasi Pembayaran')
    @section('page-subtitle', 'Kelola validasi bukti pembayaran anggota')

    @php
        // helper URL foto user
        $photoUrlOf = function ($user) {
            $path = data_get($user, 'photo');
            if (!$path) {
                return null;
            }

            // kalau sudah URL / data-uri
            if (preg_match('/^(https?:\/\/|data:)/i', $path)) {
                return $path;
            }

            $path = ltrim($path, '/');

            // normalisasi kalau kepakai prefix yang bikin dobel
            $path = preg_replace('#^public/#', '', $path);
            $path = preg_replace('#^storage/#', '', $path);

            return \Illuminate\Support\Facades\Storage::disk('public')->url($path);
        };

        // helper inisial
        $initialsOf = function ($name) {
            $name = trim(preg_replace('/\s*\(.*?\)\s*/', '', (string) $name));
            $parts = preg_split('/\s+/', $name);
            $a = strtoupper(substr($parts[0] ?? 'U', 0, 1));
            $b = strtoupper(substr($parts[1] ?? '', 0, 1));
            return $a . $b;
        };

        // detect extension gambar
        $isImagePath = function ($path) {
            return (bool) preg_match('/\.(jpe?g|png|gif|webp)$/i', (string) $path);
        };
    @endphp

    <div class="space-y-6" x-data="{
        openProof: false,
        proofUrl: null,
        proofIsImage: true,
        proofTitle: 'Bukti Pembayaran',
    
        showProof(url, isImage, title) {
            if (!url) return;
            this.proofUrl = url;
            this.proofIsImage = isImage;
            this.proofTitle = title || 'Bukti Pembayaran';
            this.openProof = true;
        }
    }">

        {{-- MODAL PREVIEW BUKTI --}}
        <template x-teleport="body">
            <div x-show="openProof" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" x-cloak
                class="fixed inset-0 z-[999999] flex items-center justify-center p-4" style="display:none;"
                @keydown.escape.window="openProof=false">

                {{-- Backdrop dengan blur --}}
                <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm" @click="openProof=false"></div>

                {{-- Modal Content --}}
                <div x-show="openProof" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                    class="relative w-full max-w-6xl mx-auto bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl overflow-hidden shadow-2xl border border-white/20">

                    {{-- Header --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-white/10 bg-white/5">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="p-2 bg-blue-500/20 rounded-lg">
                                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-base font-bold text-white truncate" x-text="proofTitle"></h3>
                                <p class="text-xs text-white/50">Preview bukti pembayaran</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <a :href="proofUrl" target="_blank" x-show="proofUrl"
                                class="flex items-center gap-2 px-4 py-2 text-xs font-medium text-blue-300 hover:text-blue-200 bg-blue-500/10 hover:bg-blue-500/20 rounded-lg border border-blue-500/20 transition-all"
                                x-cloak>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                                Buka Tab Baru
                            </a>

                            <button type="button" @click="openProof=false"
                                class="p-2 text-white/60 hover:text-white hover:bg-white/10 rounded-lg transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="p-6 overflow-auto bg-slate-950/50" style="max-height: calc(90vh - 80px);">
                        <template x-if="proofIsImage">
                            <div class="w-full flex items-center justify-center">
                                <img :src="proofUrl"
                                    class="max-w-full max-h-[80vh] object-contain rounded-xl border-2 border-white/20 shadow-2xl"
                                    alt="Bukti Pembayaran">
                            </div>
                        </template>

                        <template x-if="!proofIsImage">
                            <iframe :src="proofUrl"
                                class="w-full h-[80vh] bg-white rounded-xl border-2 border-white/20 shadow-2xl"
                                frameborder="0"></iframe>
                        </template>
                    </div>
                </div>
            </div>
        </template>
        {{-- END MODAL --}}

        {{-- Statistik Cards dengan animasi hover lebih menarik --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Pending Card --}}
            <div
                class="group relative rounded-2xl border border-white/10 bg-white/5 backdrop-blur-sm p-6 shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden">
                {{-- Animated background gradient --}}
                <div
                    class="absolute inset-0 bg-gradient-to-br from-yellow-500/10 to-orange-600/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                </div>

                <div class="relative z-10 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-yellow-400/80 uppercase tracking-wider mb-1">Menunggu
                            Verifikasi</p>
                        <p class="text-4xl font-bold text-white mt-2 mb-1">{{ $pendingCount }}</p>
                        <p class="text-xs text-white/40">Perlu ditindaklanjuti</p>
                    </div>
                    <div
                        class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-yellow-500 to-orange-600 shadow-lg group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                {{-- Progress indicator --}}
                <div class="relative mt-4 h-1.5 bg-white/10 rounded-full overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-yellow-500 to-orange-600 rounded-full"
                        style="width: {{ $pendingCount > 0 ? '100' : '0' }}%"></div>
                </div>
            </div>

            {{-- Approved Card --}}
            <div
                class="group relative rounded-2xl border border-white/10 bg-white/5 backdrop-blur-sm p-6 shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden">
                <div
                    class="absolute inset-0 bg-gradient-to-br from-green-500/10 to-emerald-600/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                </div>

                <div class="relative z-10 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-green-400/80 uppercase tracking-wider mb-1">Disetujui Hari
                            Ini</p>
                        <p class="text-4xl font-bold text-white mt-2 mb-1">{{ $approvedTodayCount }}</p>
                        <p class="text-xs text-white/40">Transaksi terverifikasi</p>
                    </div>
                    <div
                        class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-green-500 to-emerald-600 shadow-lg group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <div class="relative mt-4 h-1.5 bg-white/10 rounded-full overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full"
                        style="width: {{ $approvedTodayCount > 0 ? '100' : '0' }}%"></div>
                </div>
            </div>

            {{-- Rejected Card --}}
            <div
                class="group relative rounded-2xl border border-white/10 bg-white/5 backdrop-blur-sm p-6 shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden">
                <div
                    class="absolute inset-0 bg-gradient-to-br from-red-500/10 to-pink-600/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                </div>

                <div class="relative z-10 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-red-400/80 uppercase tracking-wider mb-1">Ditolak Hari Ini
                        </p>
                        <p class="text-4xl font-bold text-white mt-2 mb-1">{{ $rejectedTodayCount }}</p>
                        <p class="text-xs text-white/40">Perlu perbaikan</p>
                    </div>
                    <div
                        class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-red-500 to-pink-600 shadow-lg group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <div class="relative mt-4 h-1.5 bg-white/10 rounded-full overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-red-500 to-pink-600 rounded-full"
                        style="width: {{ $rejectedTodayCount > 0 ? '100' : '0' }}%"></div>
                </div>
            </div>
        </div>

        {{-- Main List Container --}}
        <div class="rounded-2xl border border-white/10 bg-white/5 backdrop-blur-md shadow-2xl overflow-hidden">
            {{-- Header --}}
            <div class="border-b border-white/10 bg-gradient-to-r from-white/10 to-white/5 px-6 py-5">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-purple-500/20 rounded-lg">
                        <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white">Antrean Verifikasi Pembayaran</h2>
                        <p class="text-sm text-white/60 mt-0.5">Daftar transaksi yang memerlukan pemeriksaan dan
                            validasi</p>
                    </div>
                </div>
            </div>

            {{-- List Items --}}
            <div class="divide-y divide-white/10">
                @forelse($pembayaran as $p)
                    @php

                        $billName = $p->bill?->name ?? ($p->type ?? ($p->description ?? '—')); // fallback kalau bill null
                        $billPeriod = $p->bill?->period ?? null;

                        $u = $p->user;

                        $displayName = trim(
                            preg_replace('/\s*\(.*?\)\s*/', '', (string) data_get($u, 'name', 'Anggota')),
                        );
                        $initials = strtoupper(substr($displayName ?: 'U', 0, 2));

                        $photoUrl = !empty($u?->photo) ? asset('storage/' . ltrim($u->photo, '/')) : null;

                        $receiptPath = $p->receipt_path;
                        $receiptUrl = $receiptPath ? route('verifikasi.pembayaran.receipt', $p) : null;

                        $receiptIsImage = $receiptPath ? $isImagePath($receiptPath) : true;

                        $statusText = $p->status ?? 'pending';
                        $badge = [
                            'pending' => [
                                'bg' => 'bg-yellow-500/20',
                                'text' => 'text-yellow-300',
                                'border' => 'border-yellow-500/30',
                                'label' => 'Pending',
                                'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                            ],
                            'confirmed' => [
                                'bg' => 'bg-green-500/20',
                                'text' => 'text-green-300',
                                'border' => 'border-green-500/30',
                                'label' => 'Verified',
                                'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                            ],
                            'rejected' => [
                                'bg' => 'bg-red-500/20',
                                'text' => 'text-red-300',
                                'border' => 'border-red-500/30',
                                'label' => 'Rejected',
                                'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
                            ],
                        ][$statusText] ?? [
                            'bg' => 'bg-slate-500/20',
                            'text' => 'text-slate-200',
                            'border' => 'border-slate-500/30',
                            'label' => ucfirst($statusText),
                            'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                        ];
                    @endphp

                    <div class="group p-6 hover:bg-white/5 transition-all duration-300">
                        <div class="flex flex-col lg:flex-row gap-6">
                            {{-- Left Section: User Info --}}
                            <div class="flex items-start gap-4 lg:w-80 flex-shrink-0">
                                {{-- Avatar dengan ring effect --}}
                                <div class="relative">
                                    <div
                                        class="h-14 w-14 rounded-2xl overflow-hidden border-2 border-white/20 flex-shrink-0 group-hover:border-purple-500/50 transition-all duration-300">
                                        @if ($photoUrl)
                                            <img src="{{ $photoUrl }}" alt="{{ $displayName }}"
                                                class="h-full w-full object-cover">
                                        @else
                                            <div
                                                class="h-full w-full flex items-center justify-center bg-gradient-to-br from-blue-500 to-purple-600">
                                                <span
                                                    class="text-lg font-bold text-white">{{ $initials ?: 'U' }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    {{-- Online indicator --}}
                                    <div
                                        class="absolute -bottom-1 -right-1 h-4 w-4 bg-green-500 border-2 border-slate-900 rounded-full">
                                    </div>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <h3
                                        class="text-base font-bold text-white truncate group-hover:text-purple-300 transition-colors">
                                        {{ $displayName }}
                                    </h3>
                                    <p class="text-xs text-white/50 flex items-center gap-1 mt-0.5">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                        </svg>
                                        ID: {{ data_get($u, 'id', '—') }}
                                    </p>

                                    <div class="mt-3 flex flex-wrap gap-2">
                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-semibold {{ $badge['bg'] }} {{ $badge['text'] }} border {{ $badge['border'] }}">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="{{ $badge['icon'] }}" />
                                            </svg>
                                            {{ $badge['label'] }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Right Section: Payment Details & Actions --}}
                            <div class="flex-1 space-y-4">
                                {{-- Info Grid --}}
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    {{-- Nama Tagihan --}}
                                    <div class="p-3 bg-white/5 rounded-xl border border-white/10">
                                        <p class="text-xs text-white/50 mb-1">Nama Tagihan</p>
                                        <p class="text-sm font-semibold text-white truncate"
                                            title="{{ $billName }}">
                                            {{ $billName }}
                                        </p>

                                        @if ($billPeriod)
                                            <p class="text-xs text-white/40 mt-0.5">{{ $billPeriod }}</p>
                                        @endif
                                    </div>

                                    {{-- Nominal --}}
                                    <div class="p-3 bg-white/5 rounded-xl border border-white/10">
                                        <p class="text-xs text-white/50 mb-1">Nominal</p>
                                        <p class="text-sm font-semibold text-emerald-300">
                                            Rp {{ number_format($p->amount ?? 0, 0, ',', '.') }}
                                        </p>
                                    </div>

                                    {{-- Tanggal Upload --}}
                                    <div class="p-3 bg-white/5 rounded-xl border border-white/10">
                                        <p class="text-xs text-white/50 mb-1">Tanggal Upload</p>
                                        <p class="text-sm text-white">
                                            {{ optional($p->created_at)->format('d M Y, H:i') }}
                                        </p>
                                    </div>
                                </div>


                                {{-- Action Buttons --}}
                                <div class="flex flex-wrap gap-3 pt-2">
                                    <button type="button"
                                        @click="showProof({{ json_encode($receiptUrl) }}, {{ $receiptIsImage ? 'true' : 'false' }}, {{ json_encode('Bukti - ' . $displayName) }})"
                                        class="group/btn flex items-center gap-2 rounded-xl border border-white/20 bg-white/5 px-5 py-2.5 text-sm font-semibold text-white hover:bg-white/10 hover:border-purple-500/50 hover:text-purple-300 transition-all duration-300 {{ $receiptUrl ? '' : 'opacity-40 cursor-not-allowed' }}"
                                        {{ $receiptUrl ? '' : 'disabled' }}>
                                        <svg class="h-4 w-4 group-hover/btn:scale-110 transition-transform"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Lihat Bukti
                                    </button>

                                    <form action="{{ route('verifikasi.pembayaran.approve', $p) }}" method="POST"
                                        class="inline">
                                        @csrf
                                        <button type="submit"
                                            class="group/btn flex items-center gap-2 rounded-xl bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 px-5 py-2.5 text-sm font-bold text-white shadow-lg hover:shadow-green-500/50 transition-all duration-300 hover:scale-105">
                                            <svg class="h-4 w-4 group-hover/btn:rotate-12 transition-transform"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                            Setujui
                                        </button>
                                    </form>

                                    <form action="{{ route('verifikasi.pembayaran.reject', $p) }}" method="POST"
                                        class="inline"
                                        onsubmit="return confirm('Yakin ingin menolak pembayaran ini?')">
                                        @csrf
                                        <input type="hidden" name="reason" value="Ditolak oleh bendahara">
                                        <button type="submit"
                                            class="group/btn flex items-center gap-2 rounded-xl border-2 border-red-500/50 bg-red-500/10 px-5 py-2.5 text-sm font-bold text-red-300 hover:bg-red-500/20 hover:border-red-500 transition-all duration-300 hover:scale-105">
                                            <svg class="h-4 w-4 group-hover/btn:rotate-90 transition-transform"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Tolak
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-16 text-center">
                        <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-white/5 mb-6">
                            <svg class="w-10 h-10 text-white/40" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Tidak Ada Transaksi</h3>
                        <p class="text-sm text-white/60 max-w-md mx-auto">
                            Belum ada pembayaran yang perlu diverifikasi saat ini. Semua transaksi telah diproses.
                        </p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if ($pembayaran->hasPages())
                <div class="px-6 py-4 bg-white/5 border-t border-white/10">
                    {{ $pembayaran->links() }}
                </div>
            @endif
        </div>

    </div>

</x-layouts.app-layout>
