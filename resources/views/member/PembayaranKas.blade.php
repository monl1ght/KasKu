<x-layouts.app-layout-anggota>
    @section('page-title', 'Pembayaran Kas')
    @section('page-subtitle', 'Lakukan penyetoran dana dengan mudah dan aman')

    @if (!isset($org))
        <div class="rounded-xl border border-yellow-500/30 bg-yellow-500/10 p-6 backdrop-blur-md">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 text-yellow-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <h3 class="text-lg font-bold text-yellow-400 mb-1">Organisasi Tidak Ditemukan</h3>
                    <p class="text-yellow-300/80">Silakan hubungi administrator untuk informasi lebih lanjut.</p>
                </div>
            </div>
        </div>
    @else
        <div x-data="{
            step: 1,
            selectedCharge: null,
            amount: 0,
            previewImage: null,
            fileName: '',
            fileSize: '',
            isValidFile: false,
            notes: '',
            selectCharge(bill) {
                this.selectedCharge = bill;
                this.amount = bill.amount;
                this.step = 2;
            },
            handleFileUpload(event) {
                const file = event.target.files[0];
                if (!file) {
                    this.resetFile();
                    return;
                }
        
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                if (!validTypes.includes(file.type)) {
                    alert('Format file tidak valid! Gunakan JPG, PNG, atau WEBP');
                    this.resetFile();
                    return;
                }
        
                if (file.size > 5 * 1024 * 1024) {
                    alert('Ukuran file terlalu besar! Maksimal 5MB');
                    this.resetFile();
                    return;
                }
        
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.previewImage = e.target.result;
                };
                reader.readAsDataURL(file);
        
                this.fileName = file.name;
                this.fileSize = (file.size / 1024 / 1024).toFixed(2) + ' MB';
                this.isValidFile = true;
                this.step = 3;
            },
            resetFile() {
                this.previewImage = null;
                this.fileName = '';
                this.fileSize = '';
                this.isValidFile = false;
                const input = document.getElementById('payment_proof');
                if (input) input.value = '';
                this.step = 2;
            },
            resetForm() {
                this.step = 1;
                this.selectedCharge = null;
                this.amount = 0;
                this.notes = '';
                this.resetFile();
            },
            validateForm() {
                if (!this.selectedCharge) {
                    alert('Pilih jenis tagihan terlebih dahulu!');
                    return false;
                }
                if (!this.isValidFile) {
                    alert('Unggah bukti pembayaran yang valid!');
                    return false;
                }
                return true;
            }
        }" class="space-y-6">

            {{-- SECTION 1: STATS & INFO --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Total Tagihan --}}
                <div
                    class="rounded-xl border border-white/20 bg-white/10 backdrop-blur-md p-6 shadow-lg hover:bg-white/15 transition-all group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-white/60">Total Tagihan Anda</p>
                            <p class="text-3xl font-bold text-white mt-2">Rp
                                {{ number_format($totalTagihan ?? 0, 0, ',', '.') }}</p>
                            <p class="text-xs text-white/40 mt-1">{{ count($bills) }} Tagihan Aktif</p>
                        </div>
                        <div
                            class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 shadow-lg group-hover:scale-110 transition-transform">
                            <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Sudah Dibayar --}}
                <div
                    class="rounded-xl border border-white/20 bg-white/10 backdrop-blur-md p-6 shadow-lg hover:bg-white/15 transition-all group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-white/60">Sudah Dibayar</p>
                            <p class="text-3xl font-bold text-white mt-2">Rp
                                {{ number_format($totalDibayar ?? 0, 0, ',', '.') }}</p>
                            @php
                                $total = $totalTagihan ?? 0;
                                $progress = $total > 0 ? round((($totalDibayar ?? 0) / $total) * 100) : 0;
                            @endphp
                            <p class="text-xs text-white/40 mt-1">Progress {{ $progress }}%</p>
                        </div>
                        <div
                            class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-green-600 shadow-lg group-hover:scale-110 transition-transform">
                            <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Sisa Tunggakan --}}
                <div
                    class="rounded-xl border border-white/20 bg-white/10 backdrop-blur-md p-6 shadow-lg hover:bg-white/15 transition-all group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-white/60">Sisa Tunggakan</p>
                            <p class="text-3xl font-bold text-white mt-2">Rp
                                {{ number_format($sisa ?? 0, 0, ',', '.') }}</p>
                            @php
                                $unpaid = count($bills) - 0; // Sesuaikan dengan jumlah yang sudah dibayar
                            @endphp
                            <p class="text-xs text-white/40 mt-1">{{ $unpaid }} Tagihan Belum Lunas</p>
                        </div>
                        <div
                            class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-orange-500 to-red-600 shadow-lg group-hover:scale-110 transition-transform">
                            <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION 2: PROGRESS STEPPER --}}
            <div class="rounded-xl border border-white/20 bg-white/10 backdrop-blur-md p-6 shadow-lg">
                <div class="flex items-center justify-between max-w-3xl mx-auto">
                    {{-- Step 1 --}}
                    <div class="flex flex-col items-center flex-1">
                        <div class="flex items-center justify-center w-12 h-12 rounded-full transition-all duration-300"
                            :class="step >= 1 ? 'bg-gradient-to-br from-blue-500 to-purple-600 shadow-lg shadow-blue-500/50' :
                                'bg-white/5 border-2 border-white/20'">
                            <template x-if="step > 1">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </template>
                            <template x-if="step === 1">
                                <span class="text-white font-bold">1</span>
                            </template>
                        </div>
                        <p class="text-xs font-medium mt-2 transition-colors"
                            :class="step >= 1 ? 'text-white' : 'text-white/50'">Pilih Tagihan</p>
                    </div>

                    {{-- Connector --}}
                    <div class="flex-1 h-0.5 mx-2 transition-colors duration-300"
                        :class="step >= 2 ? 'bg-gradient-to-r from-blue-500 to-purple-600' : 'bg-white/20'"></div>

                    {{-- Step 2 --}}
                    <div class="flex flex-col items-center flex-1">
                        <div class="flex items-center justify-center w-12 h-12 rounded-full transition-all duration-300"
                            :class="step >= 2 ? 'bg-gradient-to-br from-blue-500 to-purple-600 shadow-lg shadow-blue-500/50' :
                                'bg-white/5 border-2 border-white/20'">
                            <template x-if="step > 2">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </template>
                            <template x-if="step <= 2">
                                <span class="text-white font-bold">2</span>
                            </template>
                        </div>
                        <p class="text-xs font-medium mt-2 transition-colors"
                            :class="step >= 2 ? 'text-white' : 'text-white/50'">Upload Bukti</p>
                    </div>

                    {{-- Connector --}}
                    <div class="flex-1 h-0.5 mx-2 transition-colors duration-300"
                        :class="step >= 3 ? 'bg-gradient-to-r from-blue-500 to-purple-600' : 'bg-white/20'"></div>

                    {{-- Step 3 --}}
                    <div class="flex flex-col items-center flex-1">
                        <div class="flex items-center justify-center w-12 h-12 rounded-full transition-all duration-300"
                            :class="step >= 3 ? 'bg-gradient-to-br from-blue-500 to-purple-600 shadow-lg shadow-blue-500/50' :
                                'bg-white/5 border-2 border-white/20'">
                            <span class="text-white font-bold">3</span>
                        </div>
                        <p class="text-xs font-medium mt-2 transition-colors"
                            :class="step >= 3 ? 'text-white' : 'text-white/50'">Konfirmasi</p>
                    </div>
                </div>
            </div>

            {{-- SECTION 3: MAIN CONTENT --}}
            <div class="grid lg:grid-cols-3 gap-6 items-stretch">
                {{-- LEFT: FORM AREA --}}
                <div class="lg:col-span-2 h-full flex flex-col">
                    <form class="h-full flex flex-col" action="{{ route('pembayaran-kas.store') }}" method="POST"
                        enctype="multipart/form-data" @submit.prevent="if(validateForm()) { $el.submit(); }">
                        @csrf

                        <input type="hidden" name="bill_id" :value="selectedCharge?.id">
                        <input type="hidden" name="amount" :value="amount">

                        {{-- STEP 1: Pilih Tagihan --}}
                        <div x-show="step === 1" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100"
                            class="space-y-4 h-full flex flex-col">

                            <div
                                class="rounded-xl border border-white/20 bg-white/10 backdrop-blur-md p-6 shadow-lg h-full flex flex-col">
                                <div class="flex items-center gap-3 mb-6">
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-purple-600">
                                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-white">Pilih Tagihan</h3>
                                        <p class="text-sm text-white/60">Pilih tagihan yang ingin Anda bayar</p>
                                    </div>
                                </div>

                                <div class="grid gap-4 flex-1 overflow-y-auto pr-1 items-start content-start">
                                    @forelse ($bills as $bill)
                                        <button type="button"
                                            @click="selectCharge({
                                            id: {{ $bill->id }},
                                            name: '{{ $bill->name }}',
                                            amount: {{ $bill->amount }},
                                            due_date: '{{ $bill->due_date->format('Y-m-d') }}',
                                            description: '{{ $bill->description ?? '' }}',
                                            category: '{{ $bill->category ?? 'Wajib' }}'
                                        })"
                                            class="group relative overflow-hidden rounded-xl border border-white/20 bg-white/5 p-5 text-left transition-all hover:bg-white/10 hover:border-blue-500/50 hover:shadow-lg hover:shadow-blue-500/20">

                                            {{-- Gradient hover effect --}}
                                            <div
                                                class="absolute inset-0 bg-gradient-to-r from-blue-500/0 via-purple-500/0 to-blue-500/0 group-hover:from-blue-500/10 group-hover:via-purple-500/10 group-hover:to-blue-500/10 transition-all duration-500">
                                            </div>

                                            <div class="relative flex items-center justify-between gap-4">
                                                <div class="flex items-start gap-4 flex-1">
                                                    {{-- Icon --}}
                                                    <div
                                                        class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-blue-500/20 text-blue-400 transition-all">
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                                        </svg>
                                                    </div>

                                                    {{-- Info --}}
                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex items-center gap-2 mb-1">
                                                            <h4 class="font-semibold text-white">{{ $bill->name }}
                                                            </h4>
                                                            <span
                                                                class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-500/20 text-blue-300">
                                                                {{ $bill->category ?? 'Wajib' }}
                                                            </span>
                                                        </div>
                                                        @if ($bill->description)
                                                            <p class="text-sm text-white/60 mb-2">
                                                                {{ $bill->description }}</p>
                                                        @endif
                                                        <div class="flex items-center gap-4 text-xs text-white/50">
                                                            <span class="flex items-center gap-1">
                                                                <svg class="w-3 h-3" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round" stroke-width="2"
                                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                                </svg>
                                                                <span>{{ $bill->due_date->format('d M Y') }}</span>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Amount & Arrow --}}
                                                <div class="flex flex-col items-end gap-2">
                                                    <p class="text-xl font-bold text-white">Rp
                                                        {{ number_format($bill->amount, 0, ',', '.') }}</p>
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-xs text-white/50">per anggota</span>
                                                        <svg class="w-5 h-5 text-white/40 group-hover:text-blue-400 group-hover:translate-x-1 transition-all"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M9 5l7 7-7 7" />
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                        </button>
                                    @empty
                                        <div class="text-center py-12">
                                            <div
                                                class="w-16 h-16 bg-yellow-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                                                <svg class="w-8 h-8 text-yellow-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                                </svg>
                                            </div>
                                            <p class="text-white/60">Tidak ada tagihan yang tersedia saat ini</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        {{-- STEP 2: Upload Bukti --}}
                        <div x-show="step === 2" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100" class="space-y-4">

                            {{-- Selected Charge Info --}}
                            <div class="rounded-xl border border-emerald-500/30 bg-emerald-500/10 backdrop-blur-md p-5 shadow-lg"
                                x-show="selectedCharge">
                                <div class="flex items-center justify-between gap-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/30">
                                            <svg class="h-5 w-5 text-emerald-300" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-emerald-300"
                                                x-text="selectedCharge?.name"></p>
                                            <p class="text-xs text-emerald-400/70"
                                                x-text="selectedCharge?.description"></p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xl font-bold text-white"
                                            x-text="'Rp ' + amount.toLocaleString('id-ID')"></p>
                                        <button type="button" @click="resetForm()"
                                            class="text-xs text-emerald-400 hover:text-emerald-300 underline">Ganti</button>
                                    </div>
                                </div>
                            </div>

                            {{-- Upload Area --}}
                            <div class="rounded-xl border border-white/20 bg-white/10 backdrop-blur-md p-6 shadow-lg">
                                <div class="flex items-center gap-3 mb-6">
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-purple-600">
                                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-white">Upload Bukti Transfer</h3>
                                        <p class="text-sm text-white/60">Unggah foto/screenshot bukti pembayaran Anda
                                        </p>
                                    </div>
                                </div>

                                <div @click="$refs.fileInput.click()"
                                    class="border-2 border-dashed rounded-2xl p-12 text-center cursor-pointer transition-all duration-300"
                                    :class="isValidFile ? 'border-emerald-500/50 bg-emerald-500/5' :
                                        'border-white/20 hover:border-blue-500/50 hover:bg-white/5 hover:scale-[1.02]'">

                                    <template x-if="!previewImage">
                                        <div>
                                            <div class="relative inline-flex mb-6">
                                                <div class="absolute inset-0 bg-blue-500/20 blur-2xl rounded-full">
                                                </div>
                                                <div
                                                    class="relative w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                                                    <svg class="w-10 h-10 text-white" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <p class="text-lg font-semibold text-white mb-2">Klik atau Drag & Drop</p>
                                            <p class="text-sm text-white/60 mb-4">untuk mengunggah bukti transfer</p>
                                            <div
                                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white/10 border border-white/20">
                                                <svg class="w-4 h-4 text-white/50" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span class="text-xs text-white/50">JPG, PNG, WEBP • Max 5MB</span>
                                            </div>
                                        </div>
                                    </template>

                                    <template x-if="previewImage">
                                        <div>
                                            <div class="relative inline-block mb-4">
                                                <img :src="previewImage" alt="Preview"
                                                    class="max-h-80 rounded-xl shadow-2xl">
                                                <div
                                                    class="absolute -top-2 -right-2 p-2 bg-emerald-500 rounded-full shadow-lg">
                                                    <svg class="w-5 h-5 text-white" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="flex items-center justify-center gap-2 text-emerald-400 mb-2">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span class="font-semibold" x-text="fileName"></span>
                                            </div>
                                            <p class="text-sm text-white/50" x-text="fileSize"></p>
                                        </div>
                                    </template>
                                </div>

                                <input type="file" id="payment_proof" name="receipt"
                                    accept="image/jpeg,image/jpg,image/png,image/webp"
                                    @change="handleFileUpload($event)" x-ref="fileInput" required class="hidden">

                                {{-- Actions --}}
                                <div class="flex gap-3 mt-6" x-show="isValidFile">
                                    <button type="button" @click="resetFile()"
                                        class="flex-1 px-6 py-3 bg-white/5 text-white border border-white/20 rounded-xl font-semibold hover:bg-white/10 transition-all flex items-center justify-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Hapus File
                                    </button>
                                    <button type="button" @click="$refs.fileInput.click()"
                                        class="flex-1 px-6 py-3 bg-white/5 text-white border border-white/20 rounded-xl font-semibold hover:bg-white/10 transition-all flex items-center justify-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        Ganti File
                                    </button>
                                </div>

                                {{-- Warning --}}
                                <div class="mt-6 p-4 bg-orange-500/10 border border-orange-500/30 rounded-xl">
                                    <div class="flex items-start gap-3">
                                        <svg class="w-5 h-5 text-orange-400 flex-shrink-0 mt-0.5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        <div>
                                            <p class="text-sm font-semibold text-orange-400">Tips Upload Bukti</p>
                                            <ul class="text-xs text-orange-300/80 mt-2 space-y-1">
                                                <li>• Pastikan nominal transfer sesuai dengan tagihan</li>
                                                <li>• Screenshot harus jelas dan tidak blur</li>
                                                <li>• Tampilkan tanggal, waktu, dan nama penerima</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Notes --}}
                            <div class="rounded-xl border border-white/20 bg-white/10 backdrop-blur-md p-6 shadow-lg">
                                <label class="block text-sm font-semibold text-white/70 mb-3">
                                    Catatan Tambahan <span class="text-xs text-white/40 font-normal">(opsional)</span>
                                </label>
                                <textarea name="notes" x-model="notes" rows="3" placeholder="Contoh: Transfer dari rekening atas nama..."
                                    class="w-full bg-white/5 border border-white/20 text-white rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 focus:outline-none transition-all resize-none placeholder-white/30"></textarea>
                            </div>
                        </div>

                        {{-- STEP 3: Review & Submit --}}
                        <div x-show="step === 3" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 transform scale-95"
                            x-transition:enter-end="opacity-100 transform scale-100" class="space-y-4">

                            <div class="rounded-xl border border-white/20 bg-white/10 backdrop-blur-md p-6 shadow-lg">
                                <div class="flex items-center gap-3 mb-6">
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-purple-600">
                                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-white">Review Pembayaran</h3>
                                        <p class="text-sm text-white/60">Periksa kembali data sebelum mengirim</p>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    {{-- Charge Info --}}
                                    <div class="p-5 bg-white/5 border border-white/10 rounded-xl">
                                        <p class="text-xs font-semibold text-white/50 uppercase tracking-wider mb-3">
                                            Informasi Tagihan</p>
                                        <div class="space-y-3">
                                            <div class="flex justify-between">
                                                <span class="text-sm text-white/60">Nama Tagihan</span>
                                                <span class="text-sm font-semibold text-white"
                                                    x-text="selectedCharge?.name"></span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-sm text-white/60">Kategori</span>
                                                <span class="text-sm font-semibold text-white"
                                                    x-text="selectedCharge?.category"></span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-sm text-white/60">Jatuh Tempo</span>
                                                <span class="text-sm font-semibold text-white"
                                                    x-text="selectedCharge ? new Date(selectedCharge.due_date).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'}) : ''"></span>
                                            </div>
                                            <div class="h-px bg-white/10"></div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm font-semibold text-white/70">Total
                                                    Pembayaran</span>
                                                <span class="text-2xl font-bold text-white"
                                                    x-text="'Rp ' + amount.toLocaleString('id-ID')"></span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- File Info --}}
                                    <div class="p-5 bg-white/5 border border-white/10 rounded-xl">
                                        <p class="text-xs font-semibold text-white/50 uppercase tracking-wider mb-3">
                                            Bukti Transfer</p>
                                        <div class="flex items-center gap-4">
                                            <img :src="previewImage" alt="Preview"
                                                class="w-24 h-24 object-cover rounded-lg">
                                            <div class="flex-1">
                                                <p class="text-sm font-semibold text-white mb-1" x-text="fileName">
                                                </p>
                                                <p class="text-xs text-white/50" x-text="fileSize"></p>
                                                <button type="button" @click="step = 2"
                                                    class="text-xs text-blue-400 hover:text-blue-300 underline mt-2">
                                                    Edit Bukti
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Notes Display --}}
                                    <div x-show="notes.trim()"
                                        class="p-5 bg-white/5 border border-white/10 rounded-xl">
                                        <p class="text-xs font-semibold text-white/50 uppercase tracking-wider mb-2">
                                            Catatan</p>
                                        <p class="text-sm text-white/80" x-text="notes"></p>
                                    </div>

                                    {{-- Success Message --}}
                                    <div class="p-4 bg-emerald-500/10 border border-emerald-500/30 rounded-xl">
                                        <div class="flex items-start gap-3">
                                            <svg class="w-5 h-5 text-emerald-400 flex-shrink-0 mt-0.5" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <div>
                                                <p class="text-sm font-semibold text-emerald-400">Siap Dikirim!</p>
                                                <p class="text-xs text-emerald-300/80 mt-1">
                                                    Data pembayaran Anda akan dikirim ke bendahara untuk diverifikasi.
                                                    Proses verifikasi biasanya memakan waktu maksimal 1x24 jam.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="flex gap-4">
                                <button type="button" @click="step = 2"
                                    class="px-6 py-3 bg-white/5 text-white border border-white/20 rounded-xl font-semibold hover:bg-white/10 transition-all">
                                    Kembali
                                </button>
                                <button type="submit"
                                    class="flex-1 px-6 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl font-bold text-lg hover:from-blue-700 hover:to-purple-700 transition-all shadow-lg hover:shadow-2xl hover:shadow-purple-500/50 hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center gap-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    Kirim Pembayaran
                                </button>
                            </div>
                        </div>

                    </form>
                </div>

                {{-- RIGHT: INFO SIDEBAR --}}
                <div class="space-y-6 sticky top-6">
                    {{-- Info Rekening --}}
                    <div
                        class="rounded-xl border border-emerald-500/30 bg-gradient-to-br from-emerald-500/20 to-teal-500/20 backdrop-blur-md p-6 shadow-lg">

                        <div class="flex items-center gap-3 mb-6">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-500/30">
                                <svg class="h-5 w-5 text-emerald-300" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-white">Rekening Tujuan</h3>
                        </div>

                        {{-- ========================= REKENING BANK (WAJIB) ========================= --}}
                        <p class="text-xs font-semibold text-white/50 uppercase tracking-wider mb-3">Rekening Bank</p>

                        @forelse ($banks as $bank)
                            @php
                                $bankName = $bank->bank_name ?? ($bank->name ?? '-');
                                $bankNumber = $bank->number ?? ($bank->account_number ?? '-');
                                $bankOwner = $bank->owner_name ?? ($bank->account_name ?? ($org->name ?? '-'));
                            @endphp

                            <div class="p-4 bg-white/5 border border-white/10 rounded-xl mb-4">
                                <div class="flex items-center gap-2 mb-3">
                                    <div class="w-8 h-8 bg-white/10 rounded flex items-center justify-center">
                                        <span class="text-xs font-bold text-white">
                                            {{ strtoupper(substr($bankName ?: 'B', 0, 1)) }}
                                        </span>
                                    </div>
                                    <p class="text-white font-semibold">{{ $bankName }}</p>
                                </div>

                                <div class="relative group">
                                    <div
                                        class="flex items-center justify-between bg-slate-900/50 rounded-xl px-4 py-3.5 border border-white/20">
                                        <p class="text-white font-bold text-xl tracking-wider font-mono">
                                            {{ $bankNumber }}</p>

                                        <button type="button"
                                            @click="navigator.clipboard.writeText(@js($bankNumber));
                                    $el.querySelector('svg').classList.add('scale-110');
                                    setTimeout(() => $el.querySelector('svg').classList.remove('scale-110'), 200)"
                                            class="text-emerald-400 hover:text-emerald-300 transition-all">
                                            <svg class="w-5 h-5 transition-transform" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                            </svg>
                                        </button>
                                    </div>

                                    <div
                                        class="absolute -inset-0.5 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-xl opacity-0 group-hover:opacity-20 blur transition-all duration-300">
                                    </div>
                                </div>

                                <p class="text-white/70 text-sm mt-3">a.n {{ $bankOwner }}</p>
                            </div>
                        @empty
                            <div class="p-4 bg-yellow-500/10 border border-yellow-500/30 rounded-xl">
                                <p class="text-sm text-yellow-300">Rekening bank belum diatur oleh admin.</p>
                            </div>
                        @endforelse

                        {{-- ========================= E-WALLET (WAJIB) ========================= --}}
                        <p class="text-xs font-semibold text-white/50 uppercase tracking-wider mt-6 mb-3">E-Wallet</p>

                        @forelse ($ewallets as $ewallet)
                            @php
                                $ewalletName = $ewallet->provider ?? ($ewallet->name ?? '-');
                                $ewalletNumber =
                                    $ewallet->number ?? ($ewallet->phone_number ?? ($ewallet->account_number ?? '-'));
                                $ewalletOwner = $ewallet->owner_name ?? ($ewallet->account_name ?? ($org->name ?? '-'));
                            @endphp

                            <div class="p-4 bg-white/5 border border-white/10 rounded-xl mb-4">
                                <div class="flex items-center justify-between gap-4">
                                    <div>
                                        <p class="text-white font-semibold">{{ $ewalletName }}</p>
                                        <p class="text-white font-bold text-lg tracking-wider font-mono">
                                            {{ $ewalletNumber }}</p>
                                        <p class="text-white/60 text-xs mt-1">a.n {{ $ewalletOwner }}</p>
                                    </div>

                                    <button type="button"
                                        @click="navigator.clipboard.writeText(@js($ewalletNumber));
                                $el.querySelector('svg').classList.add('scale-110');
                                setTimeout(() => $el.querySelector('svg').classList.remove('scale-110'), 200)"
                                        class="text-emerald-400 hover:text-emerald-300 transition-all">
                                        <svg class="w-5 h-5 transition-transform" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 bg-yellow-500/10 border border-yellow-500/30 rounded-xl">
                                <p class="text-sm text-yellow-300">E-wallet belum diatur oleh admin.</p>
                            </div>
                        @endforelse

                        <div class="mt-6 p-4 bg-blue-500/10 border border-blue-500/30 rounded-xl">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-blue-400 flex-shrink-0 mt-0.5" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-xs text-blue-300">Transfer ke rekening di atas sebelum mengunggah bukti
                                    pembayaran</p>
                            </div>
                        </div>
                    </div>

                    {{-- Panduan Cepat --}}
                    <div class="rounded-xl border border-white/20 bg-white/10 backdrop-blur-md p-6 shadow-lg">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-500/20">
                                <svg class="h-4 w-4 text-purple-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547M12 8a4 4 0 00-4 4c0 1.657 1.01 3.064 2.441 3.691L10 18h4l-.441-2.309A4.001 4.001 0 0012 8z" />
                                </svg>
                            </div>
                            <h3 class="text-sm font-bold text-white">Panduan Cepat</h3>
                        </div>

                        <ol class="space-y-3 text-sm text-white/70">
                            <li class="flex gap-2"><span class="font-bold text-purple-400">1.</span> Pilih tagihan
                                yang ingin Anda bayar</li>
                            <li class="flex gap-2"><span class="font-bold text-purple-400">2.</span> Transfer sesuai
                                nominal ke rekening tujuan</li>
                            <li class="flex gap-2"><span class="font-bold text-purple-400">3.</span> Upload bukti
                                transfer yang jelas</li>
                            <li class="flex gap-2"><span class="font-bold text-purple-400">4.</span> Konfirmasi dan
                                kirim pembayaran</li>
                        </ol>

                        <div class="mt-4 p-3 rounded-lg bg-emerald-500/10 border border-emerald-500/30">
                            <p class="text-xs text-emerald-300">
                                Pembayaran akan diverifikasi oleh bendahara maksimal <b>1x24 jam</b>.
                            </p>
                        </div>
                    </div>
                </div>

            </div> {{-- END GRID --}}
        </div> {{-- END x-data --}}
    @endif
</x-layouts.app-layout-anggota>
