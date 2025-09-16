@extends('layouts.admin')

@section('title', 'History Jadwal - ' . $user->name)

@section('content')
<div class="mx-auto p-6 bg-white rounded-xl shadow">
    <h2 class="text-2xl font-bold mb-4">Riwayat Jadwal - {{ $user->name }}</h2>
    <p class="text-gray-600 mb-6">Menampilkan semua jadwal yang sudah lewat.</p>

    <div class="overflow-x-auto">
        <table class="w-full border border-gray-200 rounded-lg overflow-hidden">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Shift</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jam</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($histories as $schedule)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ \Carbon\Carbon::parse($schedule->schedule_date)->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $schedule->shift->name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ \Carbon\Carbon::parse($schedule->shift->start_time)->format('H:i') }} -
                            {{ \Carbon\Carbon::parse($schedule->shift->end_time)->format('H:i') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                            Belum ada riwayat jadwal.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $histories->links() }}
    </div>

    <div class="mt-6">
        <a href="{{ route('admin.users.index') }}"
           class="px-4 py-2 bg-sky-500 text-white rounded-lg hover:bg-sky-600">
           Kembali
        </a>
    </div>
</div>
@endsection

tolong buat ui views di atas, menjadi satu tema dengan refresnsi yang saya kasih, terutama tablenya
Refrensi:

@extends('layouts.admin')

@section('title', 'Daftar Users')

@section('content')
    <div class="min-h-screen bg-white sm:p-6 lg:p-8">
        <div class="mx-auto space-y-8">
            <!-- Enhanced Header Section -->
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl flex items-center justify-center shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users-icon lucide-users text-sky-700"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><path d="M16 3.128a4 4 0 0 1 0 7.744"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><circle cx="9" cy="7" r="4"/></svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-700 tracking-tight">Manajemen Users</h1>
                        <p class="text-gray-500 mt-1">Kelola semua pengguna dalam sistem</p>
                    </div>
                </div>

                <a href="{{ route('admin.users.create') }}"
                    class="inline-flex items-center px-6 py-3 bg-sky-600 hover:to-sky-700 text-white font-bold rounded-xl transition-all transform hover:scale-105 focus:outline-none focus:ring-4 focus:ring-sky-200 shadow-sm hover:shadow-md">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Tambah User
                </a>
            </div>

            <!-- Enhanced Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-gradient-to-br from-sky-500 to-sky-600 rounded-2xl p-6 text-white shadow-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sky-100 text-sm font-medium uppercase tracking-wide">Total Users</p>
                            <p class="text-3xl font-bold mt-2">{{ $users->count() }}</p>
                            <p class="text-sky-200 text-xs mt-1">Pengguna aktif</p>
                        </div>
                        <div class="w-14 h-14 bg-sky-400 bg-opacity-30 rounded-xl flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users-icon lucide-users text-white"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><path d="M16 3.128a4 4 0 0 1 0 7.744"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><circle cx="9" cy="7" r="4"/></svg>
                        </div>
                    </div>
                </div>

                <x-role-card 
                    title="Admin" 
                    :count="$countAdmin" 
                    subtitle="Akses penuh"
                    bgColor="bg-gradient-to-br from-red-100 to-red-200"
                    icon='<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7 text-red-600 lucide lucide-shield-user-icon lucide-shield-user"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="M6.376 18.91a6 6 0 0 1 11.249.003"/><circle cx="12" cy="11" r="4"/></svg>'
                />

                <x-role-card 
                    title="Operator" 
                    :count="$countOperator" 
                    subtitle="Kelola jadwal"
                    bgColor="bg-gradient-to-br from-blue-100 to-sky-100"
                    icon='<svg class="w-7 h-7 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>'
                />

                <x-role-card
                    title="User" 
                    :count="$countUser" 
                    subtitle="Akses terbatas"
                    bgColor="bg-gradient-to-br from-green-100 to-emerald-100"
                    icon='<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7 text-green-600 lucide lucide-circle-user-round-icon lucide-circle-user-round"><path d="M18 20a6 6 0 0 0-12 0"/><circle cx="12" cy="10" r="4"/><circle cx="12" cy="12" r="10"/></svg>'
                />
            </div>

            <!-- Enhanced Table Card -->
            <div class="bg-white rounded-2xl border-2 border-sky-100 overflow-hidden shadow-xl">
                <div class="px-8 py-6 border-b border-sky-100 bg-gradient-to-r from-sky-50 to-blue-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-sky-900">Daftar Users</h2>
                            <p class="text-sky-700 mt-1">Semua pengguna yang terdaftar dalam sistem</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="relative">
                                <input type="text" placeholder="Cari user..."
                                    class="pl-10 pr-4 bg-white py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm">
                                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b-2 border-gray-200">
                            <tr>
                                <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    User
                                </th>
                                <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Role
                                </th>
                                <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Bergabung
                                </th>
                                <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($users as $user)
                                <tr class="hover:bg-sky-50 transition-colors duration-200 group">
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="w-10 h-10 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl flex items-center justify-center mr-4 group-hover:from-sky-200 group-hover:to-sky-300 transition-colors">
                                                <span
                                                    class="text-sky-600 font-bold text-sm">{{ substr($user->name, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <div class="text-base font-semibold text-gray-700">{{ $user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                        @if ($user->role == 'Admin') bg-red-100 text-red-800
                                        @elseif($user->role == 'Operator') bg-sky-100 text-sky-800
                                        @else bg-green-100 text-green-800 @endif">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div class="text-base font-semibold text-gray-700">{{ $user->created_at->format('d F Y') }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->updated_at->format('d F Y') }}</div>
                                    </td>
                                    <td class="px-8 py-6 whitespace-nowrap text-left">
                                        <div class="flex items-center justify-start space-x-3">
                                            <a href="{{ route('admin.users.edit', $user->id) }}"
                                                class="inline-flex items-center px-4 py-2 bg-sky-100 hover:bg-sky-200 text-sky-700 font-semibold text-sm rounded-lg transition-all duration-200 hover:scale-105">
                                                Edit
                                            </a>

                                            <a href="{{ route('admin.users.history', $user->id) }}"
                                                class="inline-flex items-center px-4 py-2 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 font-semibold text-sm rounded-lg transition-all duration-200 hover:scale-105">
                                                History
                                            </a>

                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    onclick="return confirm('Yakin ingin menghapus user {{ $user->name }}? Tindakan ini tidak dapat dibatalkan.')"
                                                    class="inline-flex items-center px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 font-semibold text-sm rounded-lg transition-all duration-200 hover:scale-105">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-8 py-16 text-center">
                                        <div class="flex flex-col items-center">
                                            <div
                                                class="w-20 h-20 bg-gradient-to-br from-sky-100 to-sky-200 rounded-full flex items-center justify-center mb-6">
                                                <svg class="w-10 h-10 text-sky-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z">
                                                    </path>
                                                </svg>
                                            </div>
                                            <h3 class="text-xl font-bold text-gray-900 mb-2">Belum ada user</h3>
                                            <p class="text-gray-600 mb-6 max-w-sm">Mulai dengan membuat user pertama untuk
                                                mengakses sistem</p>
                                            <a href="{{ route('admin.users.create') }}"
                                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 text-white font-bold rounded-xl transition-all duration-200 transform hover:scale-105 shadow-lg">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                                Tambah User Pertama
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
