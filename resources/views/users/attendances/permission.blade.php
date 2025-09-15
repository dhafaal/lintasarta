    @extends('layouts.user')

    @section('title', 'Request Permission')

    @section('content')
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow border border-sky-200 p-6">
            <h2 class="text-xl font-bold text-sky-800 mb-4">Form Pengajuan Izin</h2>

            {{-- Pesan sukses --}}
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 border border-green-300 rounded">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Pesan error --}}
            @if($errors->any())
                <div class="mb-4 p-4 bg-red-100 text-red-800 border border-red-300 rounded">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('user.attendances.permission.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="date" class="block text-sm font-medium text-sky-700">Tanggal Izin</label>
                    <input type="date" name="date" id="date"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-sky-500 focus:border-sky-500"
                        value="{{ old('date') }}" required>
                </div>

                <div>
                    <label for="reason" class="block text-sm font-medium text-sky-700">Alasan</label>
                    <textarea name="reason" id="reason" rows="4"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-sky-500 focus:border-sky-500"
                            required>{{ old('reason') }}</textarea>
                </div>

                <div class="flex justify-end">
                    <a href="{{ route('user.attendances.index') }}"
                    class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded shadow mr-2">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded shadow">
                        Ajukan Izin
                    </button>
                </div>
            </form>
        </div>

        {{-- Riwayat Izin --}}
        <div class="bg-white rounded-lg shadow border border-sky-200 p-6 mt-6">
            <h2 class="text-lg font-semibold text-sky-800 mb-3">Riwayat Izin</h2>
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-sky-100 text-sky-800 text-sm">
                        <th class="border border-sky-200 px-4 py-2 text-left">Tanggal</th>
                        <th class="border border-sky-200 px-4 py-2 text-left">Alasan</th>
                        <th class="border border-sky-200 px-4 py-2 text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($permissions as $permission)
                        <tr class="text-sm">
                            <td class="border border-sky-200 px-4 py-2">{{ $permission->schedule->schedule_date }}</td>
                            <td class="border border-sky-200 px-4 py-2">{{ $permission->reason }}</td>
                            <td class="border border-sky-200 px-4 py-2">
                                <span class="px-2 py-1 rounded text-xs
                                    @if($permission->status === 'pending') bg-yellow-100 text-yellow-700
                                    @elseif($permission->status === 'approved') bg-green-100 text-green-700
                                    @else bg-red-100 text-red-700 @endif">
                                    {{ ucfirst($permission->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="border border-sky-200 px-4 py-3 text-center text-gray-500">
                                Belum ada pengajuan izin.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endsection
