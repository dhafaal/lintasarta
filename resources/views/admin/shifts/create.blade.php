@extends('layouts.app')

@section('title', 'Tambah Shift')

@section('content')
<div class="min-h-screen bg-white sm:p-6 lg:p-8">
    <div class="mx-auto">
        <!-- Enhanced header to match users module styling -->
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-3">
                <div class="p-3 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl shadow-sm">
                    <svg class="w-7 h-7 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Tambah Shift Baru</h1>
                    <p class="text-gray-600 mt-1">Buat shift kerja baru untuk sistem penjadwalan</p>
                </div>
            </div>
            <nav class="flex items-center space-x-2 text-sm text-gray-500">
                <a href="{{ route('admin.shifts.index') }}" class="hover:text-sky-600 transition-colors">Shifts</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span class="text-gray-900 font-medium">Tambah Shift</span>
            </nav>
        </div>

        <!-- Enhanced form card to match users module styling -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-sky-500 to-sky-600 px-8 py-6">
                <h2 class="text-xl font-semibold text-white">Informasi Shift</h2>
                <p class="text-sky-100 mt-1">Lengkapi semua field yang diperlukan</p>
            </div>
            
            <div class="p-8">
                <form action="{{ route('admin.shifts.store') }}" method="POST" class="space-y-8" id="shiftForm">
                    @csrf
                    
                    <!-- Enhanced form fields with consistent styling -->
                    <div class="space-y-3">
                        <label for="name" class="block text-sm font-bold text-gray-800">
                            Nama Shift
                            <span class="text-red-500 ml-1">*</span>
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-sky-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <select 
                                name="name" 
                                id="name" 
                                class="block w-full pl-12 pr-10 py-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-sky-100 focus:border-sky-500 transition-all duration-200 bg-gray-50 focus:bg-white text-gray-900 appearance-none cursor-pointer" 
                                required
                            >
                                <option value="" disabled selected>Pilih nama shift</option>
                                <option value="Pagi">Pagi</option>
                                <option value="Siang">Siang</option>
                                <option value="Malam">Malam</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-sky-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Pilih periode waktu kerja untuk shift ini
                        </p>
                    </div>

                    <div class="space-y-3">
                        <label for="start_time" class="block text-sm font-bold text-gray-800">
                            Jam Mulai
                            <span class="text-red-500 ml-1">*</span>
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-sky-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <input 
                                type="time" 
                                name="start_time" 
                                id="start_time" 
                                class="block w-full pl-12 pr-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-sky-100 focus:border-sky-500 transition-all duration-200 bg-gray-50 focus:bg-white text-gray-900" 
                                required
                            >
                        </div>
                        <p class="text-xs text-gray-500 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Waktu mulai shift dalam format 24 jam
                        </p>
                    </div>

                    <div class="space-y-3">
                        <label for="end_time" class="block text-sm font-bold text-gray-800">
                            Jam Selesai
                            <span class="text-red-500 ml-1">*</span>
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-sky-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <input 
                                type="time" 
                                name="end_time" 
                                id="end_time" 
                                class="block w-full pl-12 pr-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-sky-100 focus:border-sky-500 transition-all duration-200 bg-gray-50 focus:bg-white text-gray-900" 
                                required
                            >
                        </div>
                        <p class="text-xs text-gray-500 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Waktu selesai shift dalam format 24 jam
                        </p>
                    </div>

                    <!-- Enhanced action buttons to match users module -->
                    <div class="flex flex-col sm:flex-row gap-4 pt-8 border-t border-gray-200">
                        <button 
                            type="submit" 
                            class="bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 text-white font-bold py-4 px-8 rounded-xl transition-all duration-200 transform hover:scale-[1.02] focus:outline-none focus:ring-4 focus:ring-sky-200 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
                            id="submitBtn"
                        >
                            <span class="flex items-center justify-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span id="submitText">Simpan Shift</span>
                                <svg class="w-5 h-5 animate-spin hidden" id="loadingSpinner" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                        </button>
                        <a 
                            href="{{ route('admin.shifts.index') }}" 
                            class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-4 px-8 rounded-xl transition-all duration-200 text-center focus:outline-none focus:ring-4 focus:ring-gray-200 border-2 border-gray-200 hover:border-gray-300"
                        >
                            <span class="flex items-center justify-center gap-3">
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

<script>
document.getElementById('shiftForm').addEventListener('submit', function() {
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    const loadingSpinner = document.getElementById('loadingSpinner');
    
    submitBtn.disabled = true;
    submitText.textContent = 'Menyimpan...';
    loadingSpinner.classList.remove('hidden');
});
</script>
@endsection
