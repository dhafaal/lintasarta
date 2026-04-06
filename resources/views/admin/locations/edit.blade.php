@extends('layouts.admin')

@section('title', 'Edit Lokasi')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <style>
        #map {
            height: 400px;
            width: 100%;
            border-radius: 0.75rem;
            border: 2px solid #e0f2fe;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }
        .leaflet-control-geocoder {
            border-radius: 8px !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
            border: none !important;
        }
    </style>
@endpush

@section('content')
    <div class="min-h-screen bg-white">
        {{-- Header Section --}}
        <div class="border-gray-200 bg-white px-6 py-4">
            <div class="mx-auto">
                <div class="flex items-center space-x-3">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-sky-100 to-sky-200"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="lucide lucide-map-pin text-sky-600"
                        >
                            <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                            <circle cx="12" cy="10" r="3" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Edit Lokasi</h1>
                        <p class="text-sm text-gray-500">Perbarui informasi lokasi untuk absensi pengguna</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="mx-auto px-6 py-6">
            {{-- Form Card --}}
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 bg-gradient-to-r from-sky-50 to-blue-50 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-sky-100 to-sky-200"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="20"
                                height="20"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                class="lucide lucide-edit text-sky-600"
                            >
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4Z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-800">Edit Informasi Lokasi</h2>
                            <p class="text-sm text-gray-500">Perbarui field yang diperlukan untuk lokasi ini</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <form action="{{ route('admin.locations.update', $location) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')
                        {{-- Nama Lokasi --}}
                        <div class="space-y-2">
                            <label for="name" class="block text-sm font-semibold text-gray-700">
                                <div class="flex items-center gap-2">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="16"
                                        height="16"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="lucide lucide-tag text-sky-600"
                                    >
                                        <path
                                            d="M12.586 2.586A2 2 0 0 0 11.172 2H4a2 2 0 0 0-2 2v7.172a2 2 0 0 0 .586 1.414l8.704 8.704a2.426 2.426 0 0 0 3.42 0l6.58-6.58a2.426 2.426 0 0 0 0-3.42z"
                                        />
                                        <circle cx="7.5" cy="7.5" r=".5" fill="currentColor" />
                                    </svg>
                                    <span>
                                        Nama Lokasi
                                        <span class="text-red-500">*</span>
                                    </span>
                                </div>
                            </label>
                            <div class="relative">
                                <input
                                    type="text"
                                    name="name"
                                    id="name"
                                    class="@error('name') @enderror block w-full rounded-lg border border-gray-300 border-red-500 bg-white px-4 py-2.5 transition-all duration-200 focus:border-sky-500 focus:ring-2 focus:ring-sky-500"
                                    value="{{ old('name', $location->name) }}"
                                    required
                                    placeholder="Masukkan nama lokasi (contoh: Kantor Pusat)"
                                />
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="18"
                                        height="18"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="lucide lucide-map-pin text-gray-400"
                                    >
                                        <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                                        <circle cx="12" cy="10" r="3" />
                                    </svg>
                                </div>
                            </div>
                            @error('name')
                                <p class="mt-1 flex items-center gap-1 text-sm text-red-600">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="14"
                                        height="14"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="lucide lucide-alert-circle"
                                    >
                                        <circle cx="12" cy="12" r="10" />
                                        <line x1="12" x2="12" y1="8" y2="12" />
                                        <line x1="12" x2="12.01" y1="16" y2="16" />
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        {{-- Tipe Lokasi & Status --}}
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            {{-- Tipe Lokasi --}}
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">
                                    <div class="flex items-center gap-2">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="16"
                                            height="16"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="lucide lucide-building-2 text-sky-600"
                                        >
                                            <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z" />
                                            <path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2" />
                                            <path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2" />
                                            <path d="M10 6h4" />
                                            <path d="M10 10h4" />
                                            <path d="M10 14h4" />
                                            <path d="M10 18h4" />
                                        </svg>
                                        <span>
                                            Tipe Lokasi
                                            <span class="text-red-500">*</span>
                                        </span>
                                    </div>
                                </label>
                                <div class="flex flex-col gap-2">
                                    <label
                                        class="flex cursor-pointer items-center gap-3 rounded-lg border border-gray-300 px-4 py-3 transition-all duration-200 hover:border-sky-400 hover:bg-sky-50 has-[:checked]:border-sky-500 has-[:checked]:bg-sky-50"
                                    >
                                        <input
                                            type="radio"
                                            name="type"
                                            value="wfo"
                                            class="h-4 w-4 text-sky-600 focus:ring-sky-500"
                                            {{ old('type', $location->type) === 'wfo' ? 'checked' : '' }}
                                        />
                                        <div class="flex items-center gap-2">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="18"
                                                height="18"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                class="lucide lucide-building text-sky-600"
                                            >
                                                <rect width="16" height="20" x="4" y="2" rx="2" ry="2" />
                                                <path d="M9 22v-4h6v4" />
                                                <path d="M8 6h.01" />
                                                <path d="M16 6h.01" />
                                                <path d="M12 6h.01" />
                                                <path d="M12 10h.01" />
                                                <path d="M12 14h.01" />
                                                <path d="M16 10h.01" />
                                                <path d="M16 14h.01" />
                                                <path d="M8 10h.01" />
                                                <path d="M8 14h.01" />
                                            </svg>
                                            <span class="font-medium text-gray-700">WFO (Work From Office)</span>
                                        </div>
                                    </label>
                                    <label
                                        class="flex cursor-pointer items-center gap-3 rounded-lg border border-gray-300 px-4 py-3 transition-all duration-200 hover:border-sky-400 hover:bg-sky-50 has-[:checked]:border-sky-500 has-[:checked]:bg-sky-50"
                                    >
                                        <input
                                            type="radio"
                                            name="type"
                                            value="wfa"
                                            class="h-4 w-4 text-sky-600 focus:ring-sky-500"
                                            {{ old('type', $location->type) === 'wfa' ? 'checked' : '' }}
                                        />
                                        <div class="flex items-center gap-2">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="18"
                                                height="18"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                class="lucide lucide-globe text-sky-600"
                                            >
                                                <circle cx="12" cy="12" r="10" />
                                                <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20" />
                                                <path d="M2 12h20" />
                                            </svg>
                                            <span class="font-medium text-gray-700">WFA (Work From Anywhere)</span>
                                        </div>
                                    </label>
                                </div>
                                @error('type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Status Lokasi --}}
                            <div
                                class="space-y-2"
                                x-data="{
                                    isActive: {{ old('is_active', $location->is_active) ? 'true' : 'false' }},
                                }"
                            >
                                <label class="block text-sm font-semibold text-gray-700">
                                    <div class="flex items-center gap-2">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="16"
                                            height="16"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="lucide lucide-toggle-right text-sky-600"
                                        >
                                            <rect width="20" height="12" x="2" y="6" rx="6" ry="6" />
                                            <circle cx="16" cy="12" r="2" />
                                        </svg>
                                        <span>Status Lokasi</span>
                                    </div>
                                </label>
                                <div
                                    class="rounded-lg border-2 p-4 transition-all duration-200"
                                    :class="isActive ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200'"
                                >
                                    <div class="inline-flex cursor-pointer items-center" @click="isActive = !isActive">
                                        <!-- Hidden input untuk memastikan nilai 0 terkirim saat nonaktif -->
                                        <input type="hidden" name="is_active" :value="isActive ? '1' : '0'" />
                                        <div
                                            class="relative h-7 w-14 rounded-full transition-colors duration-300"
                                            :class="isActive ? 'bg-green-500' : 'bg-gray-300'"
                                        >
                                            <div
                                                class="absolute top-0.5 left-0.5 h-6 w-6 rounded-full bg-white shadow-md transition-transform duration-300"
                                                :class="isActive ? 'translate-x-7' : 'translate-x-0'"
                                            ></div>
                                        </div>
                                        <span
                                            class="ml-3 text-sm font-semibold transition-colors duration-200"
                                            :class="isActive ? 'text-green-700' : 'text-gray-700'"
                                        >
                                            <span x-show="isActive">✓ Lokasi Aktif</span>
                                            <span x-show="!isActive">✕ Lokasi Nonaktif</span>
                                        </span>
                                    </div>
                                    <p
                                        class="mt-2 text-xs transition-colors duration-200"
                                        :class="isActive ? 'text-green-600' : 'text-gray-500'"
                                    >
                                        <span x-show="isActive">Lokasi dapat digunakan untuk absensi</span>
                                        <span x-show="!isActive">Lokasi tidak dapat digunakan untuk absensi</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        {{-- Koordinat --}}
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            {{-- Latitude --}}
                            <div class="space-y-2">
                                <label for="latitude" class="block text-sm font-semibold text-gray-700">
                                    <div class="flex items-center gap-2">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="16"
                                            height="16"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="lucide lucide-navigation text-sky-600"
                                        >
                                            <polygon points="3 11 22 2 13 21 11 13 3 11" />
                                        </svg>
                                        <span>
                                            Latitude
                                            <span class="text-red-500">*</span>
                                        </span>
                                    </div>
                                </label>
                                <input
                                    type="number"
                                    step="any"
                                    name="latitude"
                                    id="latitude"
                                    class="@error('latitude') @enderror block w-full rounded-lg border border-gray-300 border-red-500 bg-white px-4 py-2.5 focus:border-sky-500 focus:ring-2 focus:ring-sky-500"
                                    value="{{ old('latitude', $location->latitude) }}"
                                    required
                                    placeholder="-6.200000"
                                />
                                @error('latitude')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Longitude --}}
                            <div class="space-y-2">
                                <label for="longitude" class="block text-sm font-semibold text-gray-700">
                                    <div class="flex items-center gap-2">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="16"
                                            height="16"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="lucide lucide-compass text-sky-600"
                                        >
                                            <circle cx="12" cy="12" r="10" />
                                            <polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76" />
                                        </svg>
                                        <span>
                                            Longitude
                                            <span class="text-red-500">*</span>
                                        </span>
                                    </div>
                                </label>
                                <input
                                    type="number"
                                    step="any"
                                    name="longitude"
                                    id="longitude"
                                    class="@error('longitude') @enderror block w-full rounded-lg border border-gray-300 border-red-500 bg-white px-4 py-2.5 focus:border-sky-500 focus:ring-2 focus:ring-sky-500"
                                    value="{{ old('longitude', $location->longitude) }}"
                                    required
                                    placeholder="106.816666"
                                />
                                @error('longitude')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Radius --}}
                        <div class="space-y-4">
                            <div class="space-y-2">
                                <label for="radius" class="block text-sm font-semibold text-gray-700">
                                    <div class="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-target text-sky-600">
                                            <circle cx="12" cy="12" r="10" />
                                            <circle cx="12" cy="12" r="6" />
                                            <circle cx="12" cy="12" r="2" />
                                        </svg>
                                        <span>Radius (meter) <span class="text-red-500">*</span></span>
                                    </div>
                                </label>
                                <input type="number" name="radius" id="radius" class="@error('radius') border-red-500 @enderror block w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 focus:border-sky-500 focus:ring-2 focus:ring-sky-500" value="{{ old('radius', $location->radius) }}" required placeholder="500" />
                                <p class="text-xs text-gray-500">Jarak maksimal dari titik lokasi untuk dapat melakukan absensi</p>
                            </div>

                            {{-- Leaflet Map --}}
                            <div class="space-y-3">
                                <label class="block text-sm font-semibold text-gray-700">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map text-sky-600">
                                                <polygon points="3 6 9 3 15 6 21 3 21 18 15 21 9 18 3 21" />
                                                <line x1="9" x2="9" y1="3" y2="18" />
                                                <line x1="15" x2="15" y1="6" y2="21" />
                                            </svg>
                                            <span>Pilih Lokasi di Peta</span>
                                        </div>
                                        <button type="button" onclick="getCurrentLocation()" class="text-xs font-medium text-sky-600 hover:text-sky-700 underline flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-locate-fixed">
                                                <line x1="2" x2="5" y1="12" y2="12" />
                                                <line x1="19" x2="22" y1="12" y2="12" />
                                                <line x1="12" x2="12" y1="2" y2="5" />
                                                <line x1="12" x2="12" y1="19" y2="22" />
                                                <circle cx="12" cy="12" r="7" />
                                                <circle cx="12" cy="12" r="3" />
                                            </svg>
                                            Gunakan Lokasi Saat Ini
                                        </button>
                                    </div>
                                </label>
                                <div id="map"></div>
                                <p class="text-xs text-gray-500 italic flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-info">
                                        <circle cx="12" cy="12" r="10" />
                                        <path d="M12 16v-4" />
                                        <path d="M12 8h.01" />
                                    </svg>
                                    Klik pada peta atau geser penanda untuk menentukan lokasi. Gunakan kotak pencarian di dalam peta untuk mencari alamat.
                                </p>
                            </div>
                        </div>
                        {{-- Action Buttons --}}
                        <div class="flex flex-col gap-3 border-t border-gray-200 pt-6 sm:flex-row">
                            <button
                                type="submit"
                                class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg bg-sky-500 px-6 py-2.5 font-semibold text-white transition-colors duration-200 hover:bg-sky-600 focus:ring-4 focus:ring-sky-200 focus:outline-none"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="18"
                                    height="18"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="lucide lucide-save"
                                >
                                    <path
                                        d="M15.2 3a2 2 0 0 1 1.4.6l3.8 3.8a2 2 0 0 1 .6 1.4V19a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"
                                    />
                                    <path d="M17 21v-7a1 1 0 0 0-1-1H8a1 1 0 0 0-1 1v7" />
                                    <path d="M7 3v4a1 1 0 0 0 1 1h7" />
                                </svg>
                                Update Lokasi
                            </button>
                            <a
                                href="{{ route('admin.locations.index') }}"
                                class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg border border-gray-300 bg-gray-100 px-6 py-2.5 font-semibold text-gray-700 transition-colors duration-200 hover:bg-gray-200 focus:ring-4 focus:ring-gray-200 focus:outline-none"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="18"
                                    height="18"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="lucide lucide-arrow-left"
                                >
                                    <path d="m12 19-7-7 7-7" />
                                    <path d="M19 12H5" />
                                </svg>
                                Kembali ke Daftar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    <script>
        let map, marker, circle;
        const defaultLat = -6.2088;
        const defaultLng = 106.8456;
        const defaultZoom = 13;

        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        const radInput = document.getElementById('radius');

        function initMap() {
            const initialLat = parseFloat(latInput.value) || defaultLat;
            const initialLng = parseFloat(lngInput.value) || defaultLng;
            const initialZoom = latInput.value ? 16 : defaultZoom;

            map = L.map('map').setView([initialLat, initialLng], initialZoom);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Add search control
            const geocoder = L.Control.geocoder({
                defaultMarkGeocode: false,
                placeholder: "Cari alamat...",
                errorMessage: "Alamat tidak ditemukan."
            })
            .on('markgeocode', function(e) {
                const center = e.geocode.center;
                updateLocation(center.lat, center.lng, true);
            })
            .addTo(map);

            // Initialize marker
            marker = L.marker([initialLat, initialLng], {
                draggable: true
            }).addTo(map);

            // Initialize radius circle
            const radius = parseInt(radInput.value) || 0;
            circle = L.circle([initialLat, initialLng], {
                radius: radius,
                color: '#0ea5e9',
                fillColor: '#0ea5e9',
                fillOpacity: 0.2
            }).addTo(map);

            // Map click event
            map.on('click', function(e) {
                updateLocation(e.latlng.lat, e.latlng.lng);
            });

            // Marker drag event
            marker.on('dragend', function(e) {
                const pos = marker.getLatLng();
                updateLocation(pos.lat, pos.lng);
            });

            // Input change events
            latInput.addEventListener('input', () => syncInputsToMap());
            lngInput.addEventListener('input', () => syncInputsToMap());
            radInput.addEventListener('input', () => {
                const r = parseInt(radInput.value) || 0;
                circle.setRadius(r);
            });
        }

        function updateLocation(lat, lng, moveMap = false) {
            latInput.value = parseFloat(lat).toFixed(6);
            lngInput.value = parseFloat(lng).toFixed(6);
            
            marker.setLatLng([lat, lng]);
            circle.setLatLng([lat, lng]);

            if (moveMap) {
                map.setView([lat, lng], 16);
            }
        }

        function syncInputsToMap() {
            const lat = parseFloat(latInput.value);
            const lng = parseFloat(lngInput.value);

            if (!isNaN(lat) && !isNaN(lng)) {
                marker.setLatLng([lat, lng]);
                circle.setLatLng([lat, lng]);
                map.panTo([lat, lng]);
            }
        }

        function getCurrentLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        updateLocation(position.coords.latitude, position.coords.longitude, true);
                    },
                    (error) => {
                        alert("Gagal mendapatkan lokasi: " + error.message);
                    }
                );
            } else {
                alert("Geolocation tidak didukung oleh browser ini.");
            }
        }

        document.addEventListener('DOMContentLoaded', initMap);

        // Fix Leaflet rendering issues when modal/container size changes
        window.addEventListener('resize', () => {
            if (map) {
                map.invalidateSize();
            }
        });
    </script>
@endpush
@endsection
