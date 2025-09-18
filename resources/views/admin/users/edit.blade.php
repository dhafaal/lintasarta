@extends('layouts.admin')

@section('title', 'Edit User')

@section('content')
    <div class="min-h-screen bg-white sm:p-6 lg:p-8">
        <div class="mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center gap-4 mb-3">
                    <div class="p-3 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl shadow-sm">
                        <svg class="w-7 h-7 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Edit User</h1>
                        <p class="text-gray-600 mt-1">Perbarui informasi pengguna {{ $user->name }}</p>
                    </div>
                </div>

                <!-- Breadcrumb -->
                <nav class="flex items-center space-x-2 text-sm text-gray-500">
                    <a href="{{ route('admin.users.index') }}" class="hover:text-sky-600 transition-colors">Users</a>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="text-gray-900 font-medium">Edit {{ $user->name }}</span>
                </nav>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-sky-500 to-sky-600 px-8 py-6">
                    <h2 class="text-xl font-semibold text-white">Update Informasi User</h2>
                    <p class="text-sky-100 mt-1">Perbarui data yang diperlukan</p>
                </div>

                <div class="p-8">
                    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-8"
                        id="userEditForm">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div class="space-y-3">
                            <label for="name" class="block text-sm font-bold text-gray-800">Nama Lengkap <span
                                    class="text-red-500 ml-1">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                                class="block w-full pl-4 pr-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-sky-100 focus:border-sky-500 transition-all duration-200 bg-gray-50 focus:bg-white text-gray-900 placeholder-gray-500"
                                placeholder="Masukkan nama lengkap pengguna" required autocomplete="name">
                        </div>

                        <!-- Email -->
                        <div class="space-y-3">
                            <label for="email" class="block text-sm font-bold text-gray-800">Alamat Email <span
                                    class="text-red-500 ml-1">*</span></label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                                class="block w-full pl-4 pr-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-sky-100 focus:border-sky-500 transition-all duration-200 bg-gray-50 focus:bg-white text-gray-900 placeholder-gray-500"
                                placeholder="user@example.com" required autocomplete="email">
                        </div>

                        <!-- Password -->
                        <div class="space-y-3">
                            <label for="password" class="block text-sm font-bold text-gray-800">
                                Password Baru
                                <span class="text-gray-500 text-xs font-normal">(Opsional)</span>
                            </label>
                            <input type="password" name="password" id="password"
                                class="block w-full pl-4 pr-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-sky-100 focus:border-sky-500 transition-all duration-200 bg-gray-50 focus:bg-white text-gray-900 placeholder-gray-500"
                                placeholder="Kosongkan jika tidak ingin mengubah password" minlength="8"
                                autocomplete="new-password">
                            <div
                                class="bg-amber-50 border border-amber-200 rounded-lg p-3 text-xs text-amber-800 flex items-center gap-2">
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Kosongkan field ini jika tidak ingin mengubah password. Jika diisi, password lama akan
                                diganti.
                            </div>
                        </div>

                        <!-- Role -->
                        <div class="space-y-3">
                            <label for="role" class="block text-sm font-bold text-gray-800">
                                Role Pengguna <span class="text-red-500 ml-1">*</span>
                            </label>
                            <select name="role" id="role"
                                class="block w-full pl-4 pr-10 py-4 border-2 border-gray-200 rounded-xl 
               focus:ring-4 focus:ring-sky-100 focus:border-sky-500 transition-all 
               duration-200 bg-gray-50 focus:bg-white text-gray-900 cursor-pointer"
                                required>
                                @php
                                    $currentRole = strtolower(old('role', $user->role));
                                @endphp

                                <option value="admin" {{ $currentRole === 'admin' ? 'selected' : '' }}>
                                    Admin - Akses penuh sistem
                                </option>
                                <option value="operator" {{ $currentRole === 'operator' ? 'selected' : '' }}>
                                    Operator - Kelola jadwal dan shift
                                </option>
                                <option value="user" {{ $currentRole === 'user' ? 'selected' : '' }}>
                                    User - Akses terbatas
                                </option>
                            </select>
                        </div>

                        <!-- Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4 pt-8 border-t border-gray-200">
                            <button type="submit"
                                class="flex-1 bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 text-white font-bold py-4 px-8 rounded-xl transition-all duration-200 transform hover:scale-[1.02] focus:outline-none focus:ring-4 focus:ring-sky-200 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
                                id="updateBtn">
                                <span class="flex items-center justify-center gap-3">
                                    Update User
                                    <svg class="w-5 h-5 animate-spin hidden" id="updateSpinner" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                </span>
                            </button>
                            <a href="{{ route('admin.users.index') }}"
                                class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-4 px-8 rounded-xl transition-all duration-200 text-center border-2 border-gray-200 hover:border-gray-300">
                                Kembali ke Daftar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('userEditForm').addEventListener('submit', function() {
            const btn = document.getElementById('updateBtn');
            const spinner = document.getElementById('updateSpinner');
            btn.disabled = true;
            spinner.classList.remove('hidden');
        });
    </script>
@endsection
