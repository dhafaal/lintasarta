@extends('layouts.admin')

@section('title', 'Tambah Lokasi')

@section('content')
    <div class="min-h-screen bg-white">
        {{-- Header Section --}}
        <div class="bg-white border-gray-200 px-6 py-4">
            <div class="mx-auto">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-map-pin text-sky-600">
                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Tambah Lokasi</h1>
                        <p class="text-sm text-gray-500">Tambahkan lokasi baru untuk absensi pengguna</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="mx-auto px-6 py-6">
            {{-- Form Card --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-sky-50 to-blue-50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-sky-100 to-sky-200 rounded-lg flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-file-text text-sky-600">
                                <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/>
                                <path d="M14 2v4a2 2 0 0 0 2 2h4"/>
                                <path d="M10 9H8"/>
                                <path d="M16 13H8"/>
                                <path d="M16 17H8"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-800">Informasi Lokasi</h2>
                            <p class="text-sm text-gray-500">Lengkapi semua field yang diperlukan untuk lokasi baru</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <form action="{{ route('admin.locations.store') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        {{-- Nama Lokasi --}}
                        <div class="space-y-2">
                            <label for="name" class="block text-sm font-semibold text-gray-700">
                                <div class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-tag text-sky-600">
                                        <path d="M12.586 2.586A2 2 0 0 0 11.172 2H4a2 2 0 0 0-2 2v7.172a2 2 0 0 0 .586 1.414l8.704 8.704a2.426 2.426 0 0 0 3.42 0l6.58-6.58a2.426 2.426 0 0 0 0-3.42z"/>
                                        <circle cx="7.5" cy="7.5" r=".5" fill="currentColor"/>
                                    </svg>
                                    <span>Nama Lokasi <span class="text-red-500">*</span></span>
                                </div>
                            </label>
                            <div class="relative">
                                <input type="text" name="name" id="name"
                                       class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 bg-white transition-all duration-200 @error('name') border-red-500 @enderror"
                                       value="{{ old('name') }}" required placeholder="Masukkan nama lokasi (contoh: Kantor Pusat)">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-map-pin text-gray-400">
                                        <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                                        <circle cx="12" cy="10" r="3"/>
                                    </svg>
                                </div>
                            </div>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600 flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-alert-circle">
                                        <circle cx="12" cy="12" r="10"/>
                                        <line x1="12" x2="12" y1="8" y2="12"/>
                                        <line x1="12" x2="12.01" y1="16" y2="16"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Tipe Lokasi & Status --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Tipe Lokasi --}}
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">
                                    <div class="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-building-2 text-sky-600">
                                            <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/>
                                            <path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/>
                                            <path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/>
                                            <path d="M10 6h4"/>
                                            <path d="M10 10h4"/>
                                            <path d="M10 14h4"/>
                                            <path d="M10 18h4"/>
                                        </svg>
                                        <span>Tipe Lokasi <span class="text-red-500">*</span></span>
                                    </div>
                                </label>
                                <div class="flex flex-col gap-2">
                                    <label class="flex items-center gap-3 px-4 py-3 border border-gray-300 rounded-lg cursor-pointer transition-all duration-200 hover:border-sky-400 hover:bg-sky-50 has-[:checked]:border-sky-500 has-[:checked]:bg-sky-50">
                                        <input type="radio" name="type" value="wfo" class="w-4 h-4 text-sky-600 focus:ring-sky-500" {{ old('type','wfo') === 'wfo' ? 'checked' : '' }}>
                                        <div class="flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="lucide lucide-building text-sky-600">
                                                <rect width="16" height="20" x="4" y="2" rx="2" ry="2"/>
                                                <path d="M9 22v-4h6v4"/>
                                                <path d="M8 6h.01"/>
                                                <path d="M16 6h.01"/>
                                                <path d="M12 6h.01"/>
                                                <path d="M12 10h.01"/>
                                                <path d="M12 14h.01"/>
                                                <path d="M16 10h.01"/>
                                                <path d="M16 14h.01"/>
                                                <path d="M8 10h.01"/>
                                                <path d="M8 14h.01"/>
                                            </svg>
                                            <span class="text-gray-700 font-medium">WFO (Work From Office)</span>
                                        </div>
                                    </label>
                                    <label class="flex items-center gap-3 px-4 py-3 border border-gray-300 rounded-lg cursor-pointer transition-all duration-200 hover:border-sky-400 hover:bg-sky-50 has-[:checked]:border-sky-500 has-[:checked]:bg-sky-50">
                                        <input type="radio" name="type" value="wfa" class="w-4 h-4 text-sky-600 focus:ring-sky-500" {{ old('type') === 'wfa' ? 'checked' : '' }}>
                                        <div class="flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="lucide lucide-globe text-sky-600">
                                                <circle cx="12" cy="12" r="10"/>
                                                <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/>
                                                <path d="M2 12h20"/>
                                            </svg>
                                            <span class="text-gray-700 font-medium">WFA (Work From Anywhere)</span>
                                        </div>
                                    </label>
                                </div>
                                @error('type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Status Lokasi --}}
                            <div class="space-y-2" x-data="{ isActive: {{ old('is_active', 1) ? 'true' : 'false' }} }">
                                <label class="block text-sm font-semibold text-gray-700">
                                    <div class="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-toggle-right text-sky-600">
                                            <rect width="20" height="12" x="2" y="6" rx="6" ry="6"/>
                                            <circle cx="16" cy="12" r="2"/>
                                        </svg>
                                        <span>Status Lokasi</span>
                                    </div>
                                </label>
                                <div class="p-4 rounded-lg border-2 transition-all duration-200"
                                     :class="isActive ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200'">
                                    <div class="inline-flex items-center cursor-pointer" @click="isActive = !isActive">
                                        <!-- Hidden input untuk memastikan nilai 0 terkirim saat nonaktif -->
                                        <input type="hidden" name="is_active" :value="isActive ? '1' : '0'">
                                        <div class="relative w-14 h-7 rounded-full transition-colors duration-300"
                                             :class="isActive ? 'bg-green-500' : 'bg-gray-300'">
                                            <div class="absolute top-0.5 left-0.5 w-6 h-6 bg-white rounded-full shadow-md transition-transform duration-300"
                                                 :class="isActive ? 'translate-x-7' : 'translate-x-0'"></div>
                                        </div>
                                        <span class="ml-3 text-sm font-semibold transition-colors duration-200"
                                              :class="isActive ? 'text-green-700' : 'text-gray-700'">
                                            <span x-show="isActive">✓ Lokasi Aktif</span>
                                            <span x-show="!isActive">✕ Lokasi Nonaktif</span>
                                        </span>
                                    </div>
                                    <p class="text-xs mt-2 transition-colors duration-200"
                                       :class="isActive ? 'text-green-600' : 'text-gray-500'">
                                        <span x-show="isActive">Lokasi dapat digunakan untuk absensi</span>
                                        <span x-show="!isActive">Lokasi tidak dapat digunakan untuk absensi</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Koordinat --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Latitude --}}
                            <div class="space-y-2">
                                <label for="latitude" class="block text-sm font-semibold text-gray-700">
                                    <div class="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-navigation text-sky-600">
                                            <polygon points="3 11 22 2 13 21 11 13 3 11"/>
                                        </svg>
                                        <span>Latitude <span class="text-red-500">*</span></span>
                                    </div>
                                </label>
                                <input type="number" step="any" name="latitude" id="latitude"
                                       class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 bg-white @error('latitude') border-red-500 @enderror"
                                       value="{{ old('latitude') }}" required placeholder="-6.200000">
                                @error('latitude')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Longitude --}}
                            <div class="space-y-2">
                                <label for="longitude" class="block text-sm font-semibold text-gray-700">
                                    <div class="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-compass text-sky-600">
                                            <circle cx="12" cy="12" r="10"/>
                                            <polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/>
                                        </svg>
                                        <span>Longitude <span class="text-red-500">*</span></span>
                                    </div>
                                </label>
                                <input type="number" step="any" name="longitude" id="longitude"
                                       class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 bg-white @error('longitude') border-red-500 @enderror"
                                       value="{{ old('longitude') }}" required placeholder="106.816666">
                                @error('longitude')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Radius --}}
                        <div class="space-y-2">
                            <label for="radius" class="block text-sm font-semibold text-gray-700">
                                <div class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="lucide lucide-target text-sky-600">
                                        <circle cx="12" cy="12" r="10"/>
                                        <circle cx="12" cy="12" r="6"/>
                                        <circle cx="12" cy="12" r="2"/>
                                    </svg>
                                    <span>Radius (meter) <span class="text-red-500">*</span></span>
                                </div>
                            </label>
                            <input type="number" name="radius" id="radius"
                                   class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 bg-white @error('radius') border-red-500 @enderror"
                                   value="{{ old('radius', 500) }}" required placeholder="500">
                            <p class="text-xs text-gray-500">Jarak maksimal dari titik lokasi untuk dapat melakukan absensi</p>
                            @error('radius')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t border-gray-200">
                            <button type="submit"
                                    class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-sky-500 hover:bg-sky-600 text-white font-semibold rounded-lg transition-colors duration-200 focus:outline-none focus:ring-4 focus:ring-sky-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="lucide lucide-save">
                                    <path d="M15.2 3a2 2 0 0 1 1.4.6l3.8 3.8a2 2 0 0 1 .6 1.4V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/>
                                    <path d="M17 21v-7a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7"/>
                                    <path d="M7 3v4a1 1 0 0 0 1 1h7"/>
                                </svg>
                                Simpan Lokasi
                            </button>
                            <a href="{{ route('admin.locations.index') }}"
                               class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-colors duration-200 focus:outline-none focus:ring-4 focus:ring-gray-200 border border-gray-300">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="lucide lucide-arrow-left">
                                    <path d="m12 19-7-7 7-7"/>
                                    <path d="M19 12H5"/>
                                </svg>
                                Kembali ke Daftar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection