<x-layouts.app-layout>

<h1 class="text-2xl font-bold mb-6 text-white">
    Permintaan Bergabung Anggota
</h1>

@if(session('success'))
    <div class="mb-4 p-3 bg-green-500/20 text-green-300 rounded">
        {{ session('success') }}
    </div>
@endif

@if($requests->isEmpty())
    <p class="text-white/60">Belum ada permintaan.</p>
@endif

@foreach($requests as $req)
    <div class="flex justify-between items-center p-4 mb-3 bg-white/5 rounded-xl">
        <div>
            <p class="text-white font-semibold">{{ $req->user->name }}</p>
            <p class="text-white/50 text-sm">{{ $req->user->email }}</p>
        </div>

        <div class="flex gap-2">
            <form method="POST" action="{{ route('member.requests.approve', $req->id) }}">
                @csrf
                <button class="px-4 py-2 bg-green-600 text-white rounded">
                    Setujui
                </button>
            </form>

            <form method="POST" action="{{ route('member.requests.reject', $req->id) }}">
                @csrf
                <button class="px-4 py-2 bg-red-600 text-white rounded">
                    Tolak
                </button>
            </form>
        </div>
    </div>
@endforeach

</x-layouts.app-layout>
