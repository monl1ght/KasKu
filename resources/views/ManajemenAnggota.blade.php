<x-layouts.app-layout>
    @section('page-title', 'Manajemen Anggota')
    @section('page-subtitle', 'Kelola data siswa dan status keanggotaan')

    <div class="space-y-6">

        {{-- Statistics Cards - Enhanced dengan Icons & Gradients --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {{-- Total Anggota --}}
            <div
                class="rounded-xl border border-white/20 bg-white/10 backdrop-blur-md p-6 shadow-lg hover:bg-white/15 transition-all group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-white/60">Total Anggota</p>
                        <p class="text-3xl font-bold text-white mt-2">{{ $org->users()->count() }}</p>
                    </div>
                    <div
                        class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-cyan-600 shadow-lg group-hover:scale-110 transition-transform">
                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Anggota Aktif --}}
            <div
                class="rounded-xl border border-white/20 bg-white/10 backdrop-blur-md p-6 shadow-lg hover:bg-white/15 transition-all group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-white/60">Anggota Aktif</p>
                        <p class="text-3xl font-bold text-white mt-2">
                            {{ $org->users()->where('status', 'aktif')->count() }}</p>
                    </div>
                    <div
                        class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 shadow-lg group-hover:scale-110 transition-transform">
                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Tidak Aktif --}}
            <div
                class="rounded-xl border border-white/20 bg-white/10 backdrop-blur-md p-6 shadow-lg hover:bg-white/15 transition-all group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-white/60">Tidak Aktif</p>
                        <p class="text-3xl font-bold text-white mt-2">
                            {{ $org->users()->where('status', 'tidak_aktif')->count() }}</p>
                    </div>
                    <div
                        class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-yellow-500 to-orange-600 shadow-lg group-hover:scale-110 transition-transform">
                        <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>


        </div>

        {{-- Filter & Search Section - Enhanced --}}
        <div class="relative z-30 rounded-xl border border-white/20 bg-white/10 backdrop-blur-md p-6 shadow-lg">
            <div class="flex flex-col lg:flex-row gap-4">

                {{-- Search Box --}}
                <form method="GET" action="{{ route('members.index') }}" class="flex-1">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari nama, NIM, atau email anggota..."
                            class="w-full rounded-2xl border border-white/20 bg-white/5 px-4 py-3.5 pl-12 text-white placeholder-white/50 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all">
                        <svg class="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-white/50" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="hidden" name="status" value="{{ request('status') }}">
                </form>

                {{-- Filter & Export Actions --}}
                <div class="flex items-center gap-3 shrink-0">

                    {{-- Status Filter Dropdown - Enhanced --}}
                    <div x-data="{
                        open: false,
                        selected: '{{ request('status') }}',
                        label: '{{ request('status') ? ucfirst(str_replace('_', ' ', request('status'))) : 'Semua Status' }}'
                    }" class="relative w-full md:w-52">

                        <button @click="open = !open" @click.outside="open = false"
                            class="flex w-full items-center justify-between rounded-2xl border border-white/20 bg-[#0f172a] px-5 py-3.5 text-left text-sm font-medium text-white shadow-lg transition-all hover:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-purple-500/50">
                            <span x-text="label"></span>
                            <svg class="h-5 w-5 text-white/50 transition-transform duration-200"
                                :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="transform opacity-0 scale-95 translate-y-2"
                            x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="transform opacity-0 scale-95 translate-y-2"
                            class="absolute right-0 top-full mt-2 w-56 origin-top-right overflow-hidden rounded-2xl border border-white/20 bg-[#0f172a] p-1 shadow-[0_0_20px_rgba(0,0,0,0.5)] ring-1 ring-white/5 z-50"
                            style="display: none;">


                            @php
                                $statuses = [
                                    '' => ['label' => 'Semua Status', 'icon' => 'M4 6h16M4 10h16M4 14h16M4 18h16'],
                                    'aktif' => [
                                        'label' => 'Aktif',
                                        'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                                    ],
                                    'tidak_aktif' => [
                                        'label' => 'Tidak Aktif',
                                        'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                                    ],
                                ];
                            @endphp

                            <div class="space-y-1">
                                @foreach ($statuses as $value => $data)
                                    <a href="{{ route('members.index', array_merge(request()->except('status'), ['status' => $value])) }}"
                                        class="group flex w-full items-center gap-3 rounded-xl px-4 py-2.5 text-left text-sm transition-all hover:bg-white/10 hover:pl-5
                                        {{ request('status') === $value ? 'bg-purple-600/20 text-purple-300 font-bold' : 'text-white/80' }}">

                                        <svg class="h-4 w-4 {{ request('status') === $value ? 'text-purple-400' : 'text-white/40' }}"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="{{ $data['icon'] }}" />
                                        </svg>

                                        <span>{{ $data['label'] }}</span>

                                        @if (request('status') === $value)
                                            <svg class="ml-auto h-4 w-4 text-purple-400" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Export Button - Enhanced --}}
                    <a href="{{ route('members.export', request()->only(['search', 'status'])) }}"
                        class="flex items-center justify-center gap-2 px-6 py-3.5 bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl text-white font-semibold text-sm shadow-lg hover:shadow-blue-500/50 hover:scale-105 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span class="hidden sm:inline">Export Data</span>
                        <span class="sm:hidden">Export</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Members List - Table & Card Hybrid --}}
        <div class="rounded-xl border border-white/20 bg-white/10 backdrop-blur-md shadow-lg overflow-visible">
            <div class="border-b border-white/20 bg-white/5 px-6 py-4">
                <h2 class="text-lg font-semibold text-white">Daftar Anggota</h2>
                <p class="text-sm text-white/60 mt-1">Kelola dan pantau status keanggotaan organisasi</p>
            </div>

            {{-- Desktop Table View --}}
            <div class="hidden lg:block overflow-visible">
                <table class="w-full">
                    <thead class="bg-white/5 border-b border-white/10">
                        <tr>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-white/70 uppercase tracking-wider">
                                Anggota</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-white/70 uppercase tracking-wider">
                                Kontak</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-white/70 uppercase tracking-wider">
                                Status</th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-white/70 uppercase tracking-wider">
                                Bergabung</th>
                            <th
                                class="px-6 py-4 text-right text-xs font-semibold text-white/70 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @forelse($members as $member)
                            <tr class="hover:bg-white/5 transition-colors group cursor-pointer"
                                x-data="{ showActions: false }"
                                @click="window.location='{{ route('members.show', $member->id) }}'">

                                <td class="px-6 py-4">
                                    @php
                                        $initials = strtoupper(substr($member->name ?? 'U', 0, 2));
                                        $photoUrl = !empty($member->photo) ? asset('storage/' . $member->photo) : null;
                                    @endphp

                                    <a href="{{ route('members.show', $member->id) }}"
                                        class="flex items-center gap-3 group">
                                        @if ($photoUrl)
                                            <img src="{{ $photoUrl }}" alt="Foto {{ $member->name }}"
                                                class="w-12 h-12 rounded-full object-cover shadow-lg ring-2 ring-white/10 group-hover:ring-purple-400/40 transition" />
                                        @else
                                            <div
                                                class="w-12 h-12 rounded-full flex items-center justify-center bg-gradient-to-br from-indigo-500 to-purple-500 text-white font-bold text-base shadow-lg">
                                                {{ $initials }}
                                            </div>
                                        @endif

                                        <div>
                                            <div
                                                class="font-semibold text-white group-hover:text-purple-200 transition">
                                                {{ $member->name }}
                                            </div>
                                            @if (!empty($member->nim))
                                                <div class="text-xs text-white/50 mt-0.5">NIM: {{ $member->nim }}
                                                </div>
                                            @endif
                                        </div>
                                    </a>

                                </td>

                                <td class="px-6 py-4">
                                    <div class="text-sm text-white">{{ $member->email }}</div>
                                    @if (!empty($member->phone))
                                        <div class="text-xs text-white/50 mt-0.5">{{ $member->phone }}</div>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    @php
                                        $status = $member->status ?? 'aktif';

                                        // label mapping supaya 'diblokir' tampil sebagai 'Kick Anggota'
                                        $statusLabels = [
                                            'aktif' => 'Aktif',
                                            'tidak_aktif' => 'Tidak Aktif',
                                            'diblokir' => 'Kick Anggota',
                                        ];
                                        $displayStatus =
                                            $statusLabels[$status] ?? ucfirst(str_replace('_', ' ', $status));

                                        $badgeClass = match ($status) {
                                            'aktif' => 'bg-green-500/20 text-green-300 border-green-500/30',
                                            'diblokir' => 'bg-red-500/20 text-red-300 border-red-500/30',
                                            'tidak_aktif' => 'bg-yellow-500/20 text-yellow-300 border-yellow-500/30',
                                            default => 'bg-slate-700 text-white/70 border-white/10',
                                        };
                                        $dotClass = match ($status) {
                                            'aktif' => 'bg-green-400',
                                            'diblokir' => 'bg-red-400',
                                            'tidak_aktif' => 'bg-yellow-400',
                                            default => 'bg-white/50',
                                        };
                                    @endphp

                                    <span
                                        class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium border {{ $badgeClass }}">
                                        <span
                                            class="w-1.5 h-1.5 rounded-full mr-2 {{ $dotClass }} animate-pulse"></span>
                                        {{ $displayStatus }}
                                    </span>

                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <svg class="h-4 w-4 text-white/40" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <p class="text-sm text-white">
                                            {{ optional($member->pivot->created_at ?? $member->created_at)->format('d M Y') }}
                                        </p>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-right" @click.stop>

                                    <div class="relative flex items-center justify-end">
                                        @can('manageMembers', $org)
                                            <button @click.stop="showActions = !showActions"
                                                class="p-2 hover:bg-white/10 rounded-lg text-white/60 hover:text-white transition-all">

                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                    <path
                                                        d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z" />
                                                </svg>
                                            </button>

                                            <div x-show="showActions" @click.outside="showActions = false" @click.stop
                                                x-transition:enter="transition ease-out duration-200"
                                                x-transition:enter-start="transform opacity-0 scale-95"
                                                x-transition:enter-end="transform opacity-100 scale-100"
                                                x-transition:leave="transition ease-in duration-150"
                                                x-transition:leave-start="transform opacity-100 scale-100"
                                                x-transition:leave-end="transform opacity-0 scale-95"
                                                style="display:none;"
                                                class="absolute right-0 top-full mt-2 w-56 rounded-2xl border border-white/20 bg-[#0f172a]/95 backdrop-blur-xl shadow-2xl z-50 overflow-hidden">
                                                <div class="p-2">
                                                    <a href="{{ route('members.show', $member->id) }}"
                                                        class="flex items-center gap-3 px-4 py-3 text-sm text-white hover:bg-white/10 rounded-xl transition-all group">
                                                        <svg class="w-5 h-5 text-blue-400 group-hover:scale-110 transition-transform"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                        <span class="font-medium">Lihat Detail</span>
                                                    </a>

                                                    <div class="my-2 border-t border-white/10"></div>

                                                    @if ($member->status === 'tidak_aktif')
                                                        <form action="{{ route('members.activate', $member->id) }}"
                                                            method="POST" class="my-1">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit"
                                                                class="flex items-center gap-3 px-3 py-2 text-sm text-white hover:bg-green-500/10 rounded-xl w-full text-left transition-all"
                                                                onclick="return confirm('Yakin ingin mengaktifkan anggota ini?')">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24" aria-hidden="true">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2" d="M5 13l4 4L19 7" />
                                                                </svg>
                                                                <span class="font-medium">Aktifkan</span>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form action="{{ route('members.deactivate', $member->id) }}"
                                                            method="POST" class="my-1">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit"
                                                                class="flex items-center gap-3 px-3 py-2 text-sm text-white hover:bg-orange-500/10 rounded-xl w-full text-left transition-all"
                                                                onclick="return confirm('Yakin ingin menonaktifkan anggota ini?')">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24" aria-hidden="true">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                                <span class="font-medium">Nonaktifkan</span>
                                                            </button>
                                                        </form>
                                                    @endif

                                                    <form action="{{ route('members.kick', $member->id) }}"
                                                        method="POST" class="mt-1">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="flex items-center gap-3 px-4 py-3 text-sm text-red-400 hover:bg-red-500/10 rounded-xl w-full text-left transition-all group"
                                                            onclick="return confirm('Yakin ingin mengeluarkan anggota ini dari organisasi?')">
                                                            <svg class="w-5 h-5 group-hover:scale-110 transition-transform"
                                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                            </svg>
                                                            <span class="font-medium">Kick Anggota</span>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @else
                                            <a href="{{ route('profile.show', $member->id) }}"
                                                class="p-2 hover:bg-white/10 rounded-lg text-white/60 hover:text-purple-400 transition-all">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                            </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-16 px-6">
                                    <div class="text-center">
                                        <div class="flex justify-center mb-4">
                                            <div
                                                class="flex h-20 w-20 items-center justify-center rounded-full bg-white/5">
                                                <svg class="h-10 w-10 text-white/30" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 919.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                            </div>
                                        </div>
                                        <h3 class="text-lg font-semibold text-white mb-2">Tidak Ada Anggota</h3>
                                        <p class="text-sm text-white/60">Belum ada anggota yang sesuai dengan kriteria
                                            pencarian</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Card View --}}
            <div class="lg:hidden divide-y divide-white/10">
                @forelse($members as $member)
                    <div class="p-6 hover:bg-white/5 transition-all" x-data="{ showActions: false, showDetails: false }">
                        <div class="flex items-start gap-4">
                            {{-- Avatar --}}
                            <div
                                class="w-14 h-14 rounded-full flex items-center justify-center bg-gradient-to-br from-indigo-500 to-purple-500 text-white font-bold text-lg shadow-lg flex-shrink-0">
                                {{ strtoupper(substr($member->name, 0, 2)) }}
                            </div>

                            {{-- Member Info --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-white truncate">{{ $member->name }}</h3>
                                        @if (!empty($member->nim))
                                            <p class="text-xs text-white/50 mt-0.5">NIM: {{ $member->nim }}</p>
                                        @endif
                                    </div>

                                    {{-- Status Badge --}}
                                    @php
                                        $status = $member->status ?? 'aktif';

                                        // label mapping supaya 'diblokir' tampil sebagai 'Kick Anggota'
                                        $statusLabels = [
                                            'aktif' => 'Aktif',
                                            'tidak_aktif' => 'Tidak Aktif',
                                            'diblokir' => 'Kick Anggota',
                                        ];
                                        $displayStatus =
                                            $statusLabels[$status] ?? ucfirst(str_replace('_', ' ', $status));

                                        $badgeClass = match ($status) {
                                            'aktif' => 'bg-green-500/20 text-green-300 border-green-500/30',
                                            'diblokir' => 'bg-red-500/20 text-red-300 border-red-500/30',
                                            'tidak_aktif' => 'bg-yellow-500/20 text-yellow-300 border-yellow-500/30',
                                            default => 'bg-slate-700 text-white/70 border-white/10',
                                        };
                                        $dotClass = match ($status) {
                                            'aktif' => 'bg-green-400',
                                            'diblokir' => 'bg-red-400',
                                            'tidak_aktif' => 'bg-yellow-400',
                                            default => 'bg-white/50',
                                        };
                                    @endphp

                                    <span
                                        class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium border {{ $badgeClass }}">
                                        <span
                                            class="w-1.5 h-1.5 rounded-full mr-2 {{ $dotClass }} animate-pulse"></span>
                                        {{ $displayStatus }}
                                    </span>

                                </div>

                                {{-- Contact Info --}}
                                <div class="mt-3 space-y-1">
                                    <p class="text-sm text-white/70 truncate">{{ $member->email }}</p>
                                    @if (!empty($member->phone))
                                        <p class="text-xs text-white/50">{{ $member->phone }}</p>
                                    @endif
                                </div>

                                {{-- Quick Info --}}
                                <div class="mt-3 flex flex-wrap gap-3 text-xs text-white/50">
                                    <div class="flex items-center gap-1.5">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span>{{ optional($member->pivot->created_at ?? $member->created_at)->format('d M Y') }}</span>
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="mt-4 flex gap-2">
                                    <button @click="showDetails = !showDetails"
                                        class="flex-1 flex items-center justify-center gap-2 rounded-lg border border-white/20 bg-white/5 px-4 py-2 text-sm font-medium text-white hover:bg-white/10 transition-all">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span x-text="showDetails ? 'Sembunyikan' : 'Detail'"></span>
                                    </button>

                                    @can('manageMembers', $org)
                                        <button @click="showActions = !showActions"
                                            class="flex items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-blue-600 to-purple-600 px-4 py-2 text-sm font-medium text-white hover:shadow-lg hover:shadow-purple-500/50 transition-all">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                            </svg>
                                            Aksi
                                        </button>
                                    @endcan
                                </div>

                                {{-- Expandable Details --}}
                                <div x-show="showDetails" x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 transform scale-95"
                                    x-transition:enter-end="opacity-100 transform scale-100"
                                    class="mt-4 rounded-xl border border-white/20 bg-white/5 p-4">
                                    <div class="space-y-3 text-sm">
                                        <div>
                                            <p class="text-white/50 text-xs mb-1">Email</p>
                                            <p class="text-white">{{ $member->email }}</p>
                                        </div>
                                        @if (!empty($member->phone))
                                            <div>
                                                <p class="text-white/50 text-xs mb-1">Telepon</p>
                                                <p class="text-white">{{ $member->phone }}</p>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="text-white/50 text-xs mb-1">Bergabung Sejak</p>
                                            <p class="text-white">
                                                {{ optional($member->pivot->created_at ?? $member->created_at)->format('d F Y') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Action Menu --}}
                                <div x-show="showActions" x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 transform scale-95"
                                    x-transition:enter-end="opacity-100 transform scale-100"
                                    class="mt-4 rounded-xl border border-white/20 bg-[#0f172a] p-2">
                                    <a href="{{ route('members.show', $member->id) }}"
                                        class="flex items-center gap-3 px-4 py-3 text-sm text-white hover:bg-white/10 rounded-xl transition-all">
                                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <span class="font-medium">Lihat Detail</span>
                                    </a>

                                    <div class="my-2 border-t border-white/10"></div>

                                    {{-- Conditional Aktifkan / Nonaktifkan untuk Mobile --}}
                                    @if ($member->status === 'tidak_aktif')
                                        <form action="{{ route('members.activate', $member->id) }}" method="POST"
                                            class="mt-2">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="flex items-center gap-3 px-4 py-3 text-sm text-green-400 hover:bg-green-500/10 rounded-xl w-full text-left transition-all"
                                                onclick="return confirm('Yakin ingin mengaktifkan anggota ini?')">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                <span class="font-medium">Aktifkan</span>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('members.deactivate', $member->id) }}" method="POST"
                                            class="mt-2">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="flex items-center gap-3 px-4 py-3 text-sm text-orange-400 hover:bg-orange-500/10 rounded-xl w-full text-left transition-all"
                                                onclick="return confirm('Yakin ingin menonaktifkan anggota ini?')">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span class="font-medium">Nonaktifkan</span>
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('members.kick', $member->id) }}" method="POST"
                                        class="mt-1">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="flex items-center gap-3 px-4 py-3 text-sm text-red-400 hover:bg-red-500/10 rounded-xl w-full text-left transition-all"
                                            onclick="return confirm('Yakin ingin mengeluarkan anggota ini dari organisasi?')">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                            <span class="font-medium">Kick Anggota</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <div class="flex justify-center mb-4">
                            <div class="flex h-20 w-20 items-center justify-center rounded-full bg-white/5">
                                <svg class="h-10 w-10 text-white/30" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 919.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-lg font-semibold text-white mb-2">Tidak Ada Anggota</h3>
                        <p class="text-sm text-white/60">Belum ada anggota yang sesuai dengan kriteria pencarian</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="border-t border-white/20 bg-white/5 px-6 py-4">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-sm text-white/60">
                        Menampilkan <span
                            class="font-medium text-white">{{ $members->firstItem() ?? 0 }}-{{ $members->lastItem() ?? 0 }}</span>
                        dari <span class="font-medium text-white">{{ $members->total() }}</span> anggota
                    </p>
                    <div class="flex gap-2">
                        {{ $members->links() }}
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-layouts.app-layout>
