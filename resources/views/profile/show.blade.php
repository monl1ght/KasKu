<x-layouts.app-layout>
    @section('page-title', 'Profil ' . $user->name)

    <div>
        <h1>{{ $user->name }}</h1>
        <p>{{ $user->email }}</p>
        <p>NIM: {{ $user->nim ?? '-' }}</p>
        <p>Phone: {{ $user->phone ?? '-' }}</p>
    </div>
</x-layouts.app-layout>
