@extends('layouts.admin')

@section('title', 'Tambah Lokasi')

@section('content')
    <div class="min-h-screen bg-white sm:p-6 lg:p-8">
        <div class="mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center gap-4 mb-3">
                    <div class="p-3 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-map-pin text-sky-700">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-700 tracking-tight">Tambah Lokasi</h1>
                        <p class="text-gray-500 mt-1">Tambahkan lokasi baru untuk absensi pengguna</p>
                    </div>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <!-- Form Header -->
                <div class="bg-gradient-to-r from-sky-500 to-sky-600 px-8 py-6">
                    <h2 class="text-xl font-semibold text-white">Informasi Lokasi</h2>
                    <p class="text-sky-100 mt-1">Lengkapi semua field yang diperlukan untuk lokasi baru</p>
                </div>

                <div class="p-8">
                    <form action="{{ route('admin.locations.store') }}" method="POST" class="space-y-6">
                        @csrf
                        <div class="space-y-3">
                            <label for="name" class="block text-sm font-bold text-gray-800">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    <span>Nama Lokasi <span class="text-red-500">*</span></span>
                                </div>
                            </label>
                            <div class="relative group">
                                <input type="text" name="name" id="name"
                                       class="block w-full py-4 px-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-sky-100 focus:border-sky-500 bg-gray-50 focus:bg-white transition-all duration-200 @error('name') border-red-500 @enderror"
                                       value="{{ old('name') }}" required placeholder="Masukkan nama lokasi (contoh: Kantor Pusat)">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400 group-focus-within:text-sky-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        <div class="space-y-2">
                            <label for="latitude" class="block text-sm font-bold text-gray-800">
                                Latitude <span class="text-red-500">*</span>
                            </label>
                            <input type="number" step="any" name="latitude" id="latitude"
                                   class="block w-full py-4 px-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-sky-100 focus:border-sky-500 bg-gray-50 focus:bg-white @error('latitude') border-red-500 @enderror"
                                   value="{{ old('latitude') }}" required>
                            @error('latitude')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="space-y-2">
                            <label for="longitude" class="block text-sm font-bold text-gray-800">
                                Longitude <span class="text-red-500">*</span>
                            </label>
                            <input type="number" step="any" name="longitude" id="longitude"
                                   class="block w-full py-4 px-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-sky-100 focus:border-sky-500 bg-gray-50 focus:bg-white @error('longitude') border-red-500 @enderror"
                                   value="{{ old('longitude') }}" required>
                            @error('longitude')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="space-y-2">
                            <label for="radius" class="block text-sm font-bold text-gray-800">
                                Radius (meter) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="radius" id="radius"
                                   class="block w-full py-4 px-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-sky-100 focus:border-sky-500 bg-gray-50 focus:bg-white @error('radius') border-red-500 @enderror"
                                   value="{{ old('radius', 500) }}" required>
                            @error('radius')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                            <button type="submit"
                                    class="flex-1 bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 text-white font-bold py-4 px-8 rounded-xl transition-all duration-200 transform hover:scale-[1.02] focus:outline-none focus:ring-4 focus:ring-sky-200 shadow-lg hover:shadow-xl">
                                <span class="flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 4v10m6-10v10m-6-4h6"></path>
                                    </svg>
                                    Simpan Lokasi
                                </span>
                            </button>
                            <a href="{{ route('admin.locations.index') }}"
                               class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-4 px-8 rounded-xl transition-all duration-200 text-center focus:outline-none focus:ring-4 focus:ring-gray-200 border-2 border-gray-200 hover:border-gray-300">
                                <span class="flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                    </svg>
                                    Kembali ke Daftar
                                </span>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection