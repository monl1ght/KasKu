{{-- resources/views/profil-saya.blade.php --}}

@php
    use Illuminate\Support\Str;

    // Ambil role aktif kalau ada, fallback ke properti user (kalau kamu punya).
    $roleName = Str::lower((string)($activeRole ?? (auth()->user()->role ?? '')));

    // Daftar role yang dianggap admin
    $isAdmin = in_array($roleName, ['admin', 'bendahara', 'treasurer']);

    // Pilih layout otomatis
    $layout = $isAdmin ? 'layouts.app-layout' : 'layouts.app-layout-anggota';
@endphp

<x-dynamic-component :component="$layout">
    @section('page-title', 'Profil Saya')
    @section('page-subtitle', 'Kelola informasi akun Anda')

    @php
        $userPhotoUrl = $user->photo ? asset('storage/' . $user->photo) : '';
        $userInitials = strtoupper(substr($user->name ?? 'U', 0, 2));
    @endphp

    <div class="space-y-6" x-data="{
        isEditing: false,
        photoUrl: '{{ $userPhotoUrl }}',
        photoName: '{{ $user->name ?? 'User' }}',
        fileName: '',

        enableEdit() {
            this.isEditing = true;
            $nextTick(() => { this.$refs.nameInput?.focus(); });
        },

        cancelEdit() {
            this.isEditing = false;
            this.fileName = '';
            this.photoUrl = '{{ $userPhotoUrl }}';

            const form = document.getElementById('profile-form');
            const photo = document.getElementById('photo-input');

            if (form) form.reset();
            if (photo) photo.value = '';
        },

        previewPhoto(event) {
            const file = event.target.files?.[0];
            if (!file) return;

            // Validate size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran file maksimal 2MB');
                event.target.value = '';
                return;
            }

            // Validate type
            if (!file.type.startsWith('image/')) {
                alert('File harus berupa gambar');
                event.target.value = '';
                return;
            }

            this.fileName = file.name;

            const reader = new FileReader();
            reader.onload = (e) => { this.photoUrl = e.target.result; };
            reader.readAsDataURL(file);
        }
    }">

        {{-- WRAPPER UTAMA --}}
        <div class="w-full max-w-none space-y-6">

            {{-- Back Button --}}
            <div>
                <a href="{{ url()->previous() }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white/5 backdrop-blur-md border border-white/10 text-white hover:bg-white/10 hover:border-white/20 transition-all group shadow-lg">
                    <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span class="text-sm font-medium">Kembali</span>
                </a>
            </div>

            {{-- Alert Success --}}
            @if (session('success'))
                <div class="rounded-2xl border border-emerald-500/30 bg-emerald-500/10 backdrop-blur-sm p-4 flex items-start gap-3">
                    <svg class="w-5 h-5 text-emerald-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-emerald-200 text-sm">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Alert Error --}}
            @if ($errors->any())
                <div class="rounded-2xl border border-red-500/30 bg-red-500/10 backdrop-blur-sm p-4"
                    x-init="isEditing = true">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="flex-1">
                            <div class="font-semibold text-red-200 mb-2">Terdapat beberapa kesalahan:</div>
                            <ul class="list-disc pl-5 space-y-1 text-sm text-red-300">
                                @foreach ($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Profile Header Card --}}
            <div class="rounded-2xl bg-gradient-to-r from-blue-600 to-purple-600 p-6 shadow-lg relative overflow-hidden group">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-white/20 rounded-full blur-2xl group-hover:scale-110 transition-transform duration-700"></div>

                <div class="flex flex-col items-center gap-6 relative z-10">
                    {{-- Avatar --}}
                    <div class="relative">
                        <div class="h-40 w-40 rounded-full bg-gradient-to-br from-white/30 to-white/10 p-1.5 shadow-2xl">
                            <div class="h-full w-full rounded-full bg-slate-900 flex items-center justify-center overflow-hidden border-4 border-white/20">
                                <template x-if="photoUrl">
                                    <img :src="photoUrl" :alt="photoName" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!photoUrl">
                                    <span class="text-5xl font-bold text-white">{{ $userInitials }}</span>
                                </template>
                            </div>
                        </div>

                        {{-- Upload Button (muncul saat edit) --}}
                        <button type="button" x-show="isEditing" x-transition @click="$refs.photoInput.click()"
                            aria-label="Ubah foto profil"
                            class="absolute bottom-0 right-0 p-3 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 text-white shadow-xl hover:scale-110 hover:rotate-6 transition-all duration-300">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>

                        {{-- Online status --}}
                        <div class="absolute top-2 right-2 h-4 w-4">
                            <span class="absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75 animate-ping"></span>
                            <span class="relative inline-flex rounded-full h-4 w-4 bg-green-500 border-2 border-white"></span>
                        </div>
                    </div>

                    {{-- User Info --}}
                    <div class="text-center">
                        <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">{{ $user->name }}</h1>

                        <p class="text-white/80 flex items-center gap-2 mb-3 justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            {{ $user->email }}
                        </p>

                        @if ($activeOrganization)
                            <div class="flex flex-wrap items-center gap-2 justify-center">
                                <span class="text-xs text-white/70 bg-white/10 px-3 py-1.5 rounded-full border border-white/20">
                                    <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    {{ $activeOrganization->name }}
                                </span>

                                <span class="text-xs font-semibold text-purple-300 bg-purple-500/20 px-3 py-1.5 rounded-full border border-purple-500/30">
                                    {{ $activeRole }}
                                </span>
                            </div>
                        @endif

                        <p class="text-xs text-white/50 mt-3" x-show="isEditing" x-transition>
                            Klik icon kamera untuk mengubah foto profil
                        </p>
                    </div>
                </div>
            </div>

            {{-- Main Content Card --}}
            <div class="rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm hover:bg-white/10 transition-colors">
                <div class="border-b border-white/10 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-blue-500/20 rounded-lg text-blue-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-white">Informasi Profil</h2>
                                <p class="text-sm text-white/60" x-text="isEditing ? 'Perbarui data akun Anda' : 'Data akun Anda'"></p>
                            </div>
                        </div>

                        <button type="button" x-show="!isEditing" @click="enableEdit()"
                            class="flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-blue-600 to-purple-600 text-white text-sm font-semibold hover:from-blue-500 hover:to-purple-500 transition-all duration-300 shadow-lg hover:shadow-xl">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit Profil
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <form method="POST" action="{{ route('profil.saya.update') }}" enctype="multipart/form-data"
                        id="profile-form" class="space-y-5">
                        @csrf
                        @method('PUT')

                        {{-- Hidden Photo Input --}}
                        <input type="file" id="photo-input" name="photo" accept="image/*" x-ref="photoInput"
                            class="hidden" @change="previewPhoto($event)" :disabled="!isEditing">

                        {{-- Photo Preview Indicator --}}
                        <div x-show="fileName && isEditing" x-transition
                            class="flex items-center gap-3 p-4 rounded-lg bg-blue-500/10 border border-blue-500/30">
                            <svg class="w-5 h-5 text-blue-400 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-blue-300">Foto baru dipilih</p>
                                <p class="text-xs text-blue-200/70" x-text="fileName"></p>
                            </div>
                            <button type="button"
                                @click="fileName=''; photoUrl='{{ $userPhotoUrl }}'; $refs.photoInput.value='';"
                                class="p-1 hover:bg-blue-500/20 rounded transition-colors">
                                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        @error('photo')
                            <p class="text-xs text-red-400">{{ $message }}</p>
                        @enderror

                        {{-- Nama --}}
                        <div>
                            <label class="block text-sm font-semibold text-white/90 mb-2">Nama Lengkap</label>

                            <div x-show="!isEditing"
                                class="w-full rounded-lg border border-white/10 bg-white/5 px-4 py-2.5 text-white">
                                {{ $user->name }}
                            </div>

                            <input x-show="isEditing" x-ref="nameInput" name="name"
                                value="{{ old('name', $user->name) }}" required maxlength="100"
                                class="w-full rounded-lg border border-white/20 bg-white/5 px-4 py-2.5 text-white placeholder-white/40 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition-all backdrop-blur-sm hover:bg-white/10"
                                placeholder="Masukkan nama lengkap Anda">

                            @error('name')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="block text-sm font-semibold text-white/90 mb-2">Email</label>

                            <div x-show="!isEditing"
                                class="w-full rounded-lg border border-white/10 bg-white/5 px-4 py-2.5 text-white">
                                {{ $user->email }}
                            </div>

                            <input x-show="isEditing" name="email" type="email"
                                value="{{ old('email', $user->email) }}" required maxlength="100"
                                class="w-full rounded-lg border border-white/20 bg-white/5 px-4 py-2.5 text-white placeholder-white/40 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition-all backdrop-blur-sm hover:bg-white/10"
                                placeholder="email@example.com">

                            @error('email')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Phone --}}
                        <div>
                            <label class="block text-sm font-semibold text-white/90 mb-2">
                                No. Handphone <span class="text-white/40 font-normal">(Opsional)</span>
                            </label>

                            <div x-show="!isEditing"
                                class="w-full rounded-lg border border-white/10 bg-white/5 px-4 py-2.5 text-white">
                                {{ $user->phone ?: '-' }}
                            </div>

                            <input x-show="isEditing" name="phone" value="{{ old('phone', $user->phone) }}" maxlength="20"
                                class="w-full rounded-lg border border-white/20 bg-white/5 px-4 py-2.5 text-white placeholder-white/40 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition-all backdrop-blur-sm hover:bg-white/10"
                                placeholder="08xxxxxxxxxx">

                            @error('phone')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Address --}}
                        <div>
                            <label class="block text-sm font-semibold text-white/90 mb-2">
                                Alamat Lengkap <span class="text-white/40 font-normal">(Opsional)</span>
                            </label>

                            <div x-show="!isEditing"
                                class="w-full rounded-lg border border-white/10 bg-white/5 px-4 py-2.5 text-white min-h-[100px] text-left leading-relaxed break-words">
                                {{ $user->address ? trim($user->address) : '-' }}
                            </div>

                            <textarea x-show="isEditing" name="address" rows="4" maxlength="500"
                                class="w-full rounded-lg border border-white/20 bg-white/5 px-4 py-2.5 text-white placeholder-white/40 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition-all backdrop-blur-sm hover:bg-white/10"
                                placeholder="Masukkan alamat lengkap Anda">{{ old('address', $user->address) }}</textarea>

                            @error('address')
                                <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Action Buttons --}}
                        <div x-show="isEditing" x-transition
                            class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 pt-4 border-t border-white/10">
                            <button type="submit"
                                class="flex items-center justify-center gap-2 px-6 py-2.5 rounded-lg bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold hover:from-blue-500 hover:to-purple-500 transition-all duration-300 shadow-lg hover:shadow-xl">
                                Simpan Perubahan
                            </button>

                            <button type="button" @click="cancelEdit()"
                                class="flex items-center justify-center gap-2 px-6 py-2.5 rounded-lg bg-white/5 border border-white/10 text-white/80 hover:bg-white/10 transition-colors">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-dynamic-component>
