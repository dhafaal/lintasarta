@extends('layouts.admin')

@section('title', 'Edit Lokasi')

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
                        <h1 class="text-3xl font-bold text-gray-700 tracking-tight">Edit Lokasi</h1>
                        <p class="text-gray-500 mt-1">Perbarui informasi lokasi untuk absensi pengguna</p>
                    </div>
                </div>
            </div>

            <!-- Form Card -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                <!-- Form Header -->
                <div class="bg-gradient-to-r from-sky-500 to-sky-600 px-8 py-6">
                    <h2 class="text-xl font-semibold text-white">Informasi Lokasi</h2>
                    <p class="text-sky-100 mt-1">Perbarui field yang diperlukan untuk lokasi ini</p>
                </div>

                <div class="p-8">
                    <form action="{{ route('admin.locations.update', $location) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')
                        <div class="space-y-2">
                            <label for="name" class="block text-sm font-bold text-gray-800">
                                Nama Lokasi <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name"
                                   class="block w-full py-4 px-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-sky-100 focus:border-sky-500 bg-gray-50 focus:bg-white @error('name') border-red-500 @enderror"
                                   value="{{ old('name', $location->name) }}" required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="space-y-2">
                            <label for="latitude" class="block text-sm font-bold text-gray-800">
                                Latitude <span class="text-red-500">*</span>
                            </label>
                            <input type="number" step="any" name="latitude" id="latitude"
                                   class="block w-full py-4 px-3 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-sky-100 focus:border-sky-500 bg-gray-50 focus:bg-white @error('latitude') border-red-500 @enderror"
                                   value="{{ old('latitude', $location->latitude) }}" required>
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
                                   value="{{ old('longitude', $location->longitude) }}" required>
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
                                   value="{{ old('radius', $location->radius) }}" required>
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
                                    Update Lokasi
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