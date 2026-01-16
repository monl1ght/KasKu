<x-layouts.app-layout>
    @section('page-title', 'Detail Anggota')
    @section('page-subtitle', 'Informasi lengkap anggota organisasi')

    @php
        $photoUrl = !empty($member->photo) ? asset('storage/' . $member->photo) : null;
        $initials = strtoupper(substr($member->name ?? 'U', 0, 2));

        $status = $member->status ?? 'aktif';
        $statusLabels = [
            'aktif' => 'Aktif',
            'tidak_aktif' => 'Tidak Aktif',
            'diblokir' => 'Kick Anggota',
        ];
        $displayStatus = $statusLabels[$status] ?? ucfirst(str_replace('_', ' ', $status));

        $badgeClass = match ($status) {
            'aktif' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
            'diblokir' => 'bg-red-500/20 text-red-300 border-red-500/30',
            'tidak_aktif' => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
            default => 'bg-slate-700 text-white/70 border-white/10',
        };
        $dotClass = match ($status) {
            'aktif' => 'bg-emerald-400',
            'diblokir' => 'bg-red-400',
            'tidak_aktif' => 'bg-amber-400',
            default => 'bg-white/50',
        };

        $joinedAt = optional($member->pivot->created_at ?? $member->created_at);
        $role = $member->pivot->role ?? null;
    @endphp

    {{-- Back Button --}}
    <div class="mb-6">
        <a href="{{ route('members.index', request()->only(['search','status'])) }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-white/5 backdrop-blur-md border border-white/10 text-white hover:bg-white/10 hover:border-white/20 transition-all group shadow-lg">
            <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span class="text-sm font-medium">Kembali</span>
        </a>
    </div>

    <div class="space-y-6">

        {{-- Alert Messages --}}
        @if(session('success'))
            <div class="rounded-2xl border border-emerald-500/30 bg-emerald-500/10 backdrop-blur-sm p-4 flex items-start gap-3 animate-in fade-in slide-in-from-top-2 duration-300">
                <svg class="w-5 h-5 text-emerald-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-emerald-200 text-sm">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-2xl border border-red-500/30 bg-red-500/10 backdrop-blur-sm p-4 flex items-start gap-3 animate-in fade-in slide-in-from-top-2 duration-300">
                <svg class="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-red-200 text-sm">{{ session('error') }}</span>
            </div>
        @endif

        {{-- Profile Header Card --}}
        <div class="rounded-2xl bg-gradient-to-r from-blue-600 to-purple-600 p-8 shadow-2xl relative overflow-hidden group">
            {{-- Decorative Elements --}}
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-40 h-40 bg-white/20 rounded-full blur-3xl group-hover:scale-110 transition-transform duration-700"></div>
            <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
            
            <div class="relative z-10">
                <div class="flex flex-col md:flex-row items-center md:items-start gap-6">
                    {{-- Avatar with Status Ring --}}
                    <div class="relative shrink-0">
                        <div class="h-32 w-32 rounded-2xl bg-gradient-to-br from-white/30 to-white/10 p-1.5 shadow-2xl">
                            @if ($photoUrl)
                                <img src="{{ $photoUrl }}" alt="Foto {{ $member->name }}"
                                     class="w-full h-full rounded-xl object-cover border-4 border-white/20" />
                            @else
                                <div class="w-full h-full rounded-xl flex items-center justify-center bg-gradient-to-br from-slate-800 to-slate-900 border-4 border-white/20">
                                    <span class="text-4xl font-bold text-white">{{ $initials }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Status Indicator --}}
                        <div class="absolute -bottom-2 -right-2 {{ $badgeClass }} px-3 py-1 rounded-full text-xs font-semibold shadow-lg backdrop-blur-sm">
                            <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $dotClass }} inline-block animate-pulse"></span>
                            {{ $displayStatus }}
                        </div>
                    </div>

                    {{-- Member Info --}}
                    <div class="flex-1 min-w-0 text-center md:text-left">
                        <h1 class="text-3xl font-bold text-white mb-2 truncate">{{ $member->name }}</h1>
                        
                        <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 mb-4">
                            <div class="flex items-center gap-2 text-white/80">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span class="text-sm">{{ $member->email }}</span>
                            </div>
                        </div>

                        {{-- Meta Info Badges --}}
                        <div class="flex flex-wrap gap-2 justify-center md:justify-start">
                            @if ($role)
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-white/10 text-white border border-white/20 backdrop-blur-sm">
                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                    </svg>
                                    {{ ucfirst($role) }}
                                </span>
                            @endif

                            @if ($joinedAt)
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-white/10 text-white/80 border border-white/20 backdrop-blur-sm">
                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Bergabung {{ $joinedAt->format('d M Y') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Quick Actions (Admin) --}}
                    @can('manageMembers', $org)
                        <div class="flex flex-wrap gap-2 justify-center md:justify-end">
                            @if (($member->status ?? 'aktif') === 'tidak_aktif')
                                <form action="{{ route('members.activate', $member->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-emerald-600/30 text-emerald-200 border border-emerald-500/50 hover:bg-emerald-600/40 transition-all shadow-lg backdrop-blur-sm"
                                        onclick="return confirm('Yakin ingin mengaktifkan anggota ini?')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Aktifkan
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('members.deactivate', $member->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-amber-600/30 text-amber-200 border border-amber-500/50 hover:bg-amber-600/40 transition-all shadow-lg backdrop-blur-sm"
                                        onclick="return confirm('Yakin ingin menonaktifkan anggota ini?')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                        </svg>
                                        Nonaktifkan
                                    </button>
                                </form>
                            @endif

                            <form action="{{ route('members.kick', $member->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-red-600/30 text-red-200 border border-red-500/50 hover:bg-red-600/40 transition-all shadow-lg backdrop-blur-sm"
                                    onclick="return confirm('Yakin ingin mengeluarkan anggota ini dari organisasi?')">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6" />
                                    </svg>
                                    Kick
                                </button>
                            </form>
                        </div>
                    @endcan
                </div>
            </div>
        </div>

        {{-- Information Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            {{-- Contact Information --}}
            <div class="rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm hover:bg-white/10 transition-colors shadow-lg">
                <div class="border-b border-white/10 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-blue-500/20 rounded-lg text-blue-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white">Informasi Kontak</h3>
                            <p class="text-sm text-white/60">Detail kontak anggota</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-5">
                    {{-- Email --}}
                    <div class="flex items-start gap-3 group">
                        <div class="p-2 bg-blue-500/10 rounded-lg text-blue-400 group-hover:bg-blue-500/20 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-white/50 uppercase tracking-wider mb-1">Email</p>
                            <p class="text-white break-all">{{ $member->email }}</p>
                        </div>
                    </div>

                    {{-- Phone --}}
                    @if (!empty($member->phone))
                        <div class="flex items-start gap-3 group">
                            <div class="p-2 bg-emerald-500/10 rounded-lg text-emerald-400 group-hover:bg-emerald-500/20 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-white/50 uppercase tracking-wider mb-1">Telepon</p>
                                <p class="text-white">{{ $member->phone }}</p>
                            </div>
                        </div>
                    @else
                        <div class="flex items-start gap-3 opacity-50">
                            <div class="p-2 bg-white/5 rounded-lg text-white/40">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-white/50 uppercase tracking-wider mb-1">Telepon</p>
                                <p class="text-white/40 italic">Tidak ada data</p>
                            </div>
                        </div>
                    @endif

                    {{-- Address --}}
                    @if (!empty($member->address))
                        <div class="flex items-start gap-3 group">
                            <div class="p-2 bg-purple-500/10 rounded-lg text-purple-400 group-hover:bg-purple-500/20 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-white/50 uppercase tracking-wider mb-1">Alamat</p>
                                <p class="text-white whitespace-pre-line">{{ $member->address }}</p>
                            </div>
                        </div>
                    @else
                        <div class="flex items-start gap-3 opacity-50">
                            <div class="p-2 bg-white/5 rounded-lg text-white/40">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-white/50 uppercase tracking-wider mb-1">Alamat</p>
                                <p class="text-white/40 italic">Tidak ada data</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Account Information --}}
            <div class="rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm hover:bg-white/10 transition-colors shadow-lg">
                <div class="border-b border-white/10 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-purple-500/20 rounded-lg text-purple-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white">Informasi Akun</h3>
                            <p class="text-sm text-white/60">Detail akun dan aktivitas</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-5">
                    {{-- NIM --}}
                    @if (!empty($member->nim))
                        <div class="flex items-start gap-3 group">
                            <div class="p-2 bg-indigo-500/10 rounded-lg text-indigo-400 group-hover:bg-indigo-500/20 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-white/50 uppercase tracking-wider mb-1">NIM</p>
                                <p class="text-white font-mono">{{ $member->nim }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Registered Date --}}
                    <div class="flex items-start gap-3 group">
                        <div class="p-2 bg-emerald-500/10 rounded-lg text-emerald-400 group-hover:bg-emerald-500/20 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-white/50 uppercase tracking-wider mb-1">Terdaftar Sejak</p>
                            <p class="text-white">{{ optional($member->created_at)->format('d F Y') ?? 'N/A' }}</p>
                        </div>
                    </div>

                    {{-- Last Activity --}}
                    <div class="flex items-start gap-3 group">
                        <div class="p-2 bg-blue-500/10 rounded-lg text-blue-400 group-hover:bg-blue-500/20 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-white/50 uppercase tracking-wider mb-1">Aktivitas Terakhir</p>
                            <p class="text-white">
                                {{ optional($member->last_activity)->diffForHumans() ?? 'Tidak ada aktivitas' }}
                            </p>
                        </div>
                    </div>

                    {{-- Account Status --}}
                    <div class="flex items-start gap-3 group">
                        <div class="p-2 {{ $status === 'aktif' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-red-500/10 text-red-400' }} rounded-lg group-hover:opacity-80 transition-opacity">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-semibold text-white/50 uppercase tracking-wider mb-1">Status Akun</p>
                            <span class="inline-flex items-center {{ $badgeClass }} px-2.5 py-1 rounded-lg text-sm font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $dotClass }} animate-pulse"></span>
                                {{ $displayStatus }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-layouts.app-layout>