@extends('layouts.app')

@section('title', 'Attendance Management')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-sky-700 mb-4">Attendance Management</h1>

    {{-- Flash message --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($users as $user)
            <div class="border rounded-lg p-4 shadow-sm bg-white">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="font-semibold text-sky-800">{{ $user->name }}</h2>

                    {{-- notif merah kalau ada izin pending --}}
                    @if($user->schedules->flatMap->attendances->where('status','izin_pending')->count() > 0)
                        <span class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded">Ada izin pending</span>
                    @endif
                </div>

                {{-- status ringkas --}}
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>✅ Hadir: {{ $user->schedules->flatMap->attendances->where('status','hadir')->count() }}</li>
                    <li>🟡 Izin: {{ $user->schedules->flatMap->attendances->whereIn('status',['izin','izin_pending','izin_approved'])->count() }}</li>
                    <li>❌ Alpha: {{ $user->schedules->flatMap->attendances->where('status','alpha')->count() }}</li>
                </ul>

                <div class="mt-3">
                    <a href="{{ route('admin.attendances.show', $user) }}"
                       class="px-3 py-1 text-xs bg-sky-600 text-white rounded hover:bg-sky-700">
                        Lihat Absensi
                    </a>
                </div>
            </div>
        @empty
            <p class="text-sky-500">Belum ada user yang punya jadwal.</p>
        @endforelse
    </div>
</div>
@endsection
