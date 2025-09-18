@extends('layouts.admin')

@section('title', 'Edit Shift')

@section('content')
<div class="min-h-screen bg-white dark:bg-gray-900 sm:p-6 lg:p-8 transition-colors duration-300">
    <div class="mx-auto">
        <!-- Enhanced header -->
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-3">
                <div class="p-3 bg-gradient-to-br from-sky-100 to-sky-200 dark:from-sky-900 dark:to-sky-800 rounded-xl shadow-sm">
                    <svg class="w-7 h-7 text-sky-600 dark:text-sky-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 tracking-tight">Edit Shift</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">Perbarui informasi shift kerja</p>
                </div>
            </div>
        </div>

        <!-- Enhanced form card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="bg-gradient-to-r from-sky-500 to-sky-600 dark:from-sky-600 dark:to-sky-700 px-8 py-6">
                <h2 class="text-xl font-semibold text-white">Update Informasi Shift</h2>
                <p class="text-sky-100 dark:text-sky-200 mt-1">Perbarui data yang diperlukan</p>
            </div>
            
            <div class="p-8">
                <form action="{{ route('admin.shifts.update', $shift->id) }}" method="POST" class="space-y-8" id="shiftEditForm">
                    @csrf
                    @method('PUT')
                    
                    <!-- Nama Shift -->
                    <div class="space-y-3">
                        <label for="shift_name" class="block text-sm font-bold text-gray-800 dark:text-gray-200">
                            Nama Shift
                            <span class="text-red-500 ml-1">*</span>
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-sky-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                name="shift_name" 
                                id="shift_name" 
                                value="{{ $shift->shift_name }}"
                                class="block w-full pl-12 pr-4 py-4 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-4 focus:ring-sky-100 dark:focus:ring-sky-900 focus:border-sky-500 dark:focus:border-sky-400 transition-all duration-200 bg-gray-50 dark:bg-gray-700 focus:bg-white dark:focus:bg-gray-600 text-gray-900 dark:text-gray-100" 
                                placeholder="Contoh: Shift A, Shift Security"
                                required
                            >
                        </div>
                    </div>

                    <!-- Kategori Waktu -->
                    <div class="space-y-3">
                        <label for="category" class="block text-sm font-bold text-gray-800 dark:text-gray-200">
                            Kategori Waktu
                            <span class="text-red-500 ml-1">*</span>
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-sky-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <select 
                                name="category" 
                                id="category" 
                                class="block w-full pl-12 pr-10 py-4 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-4 focus:ring-sky-100 dark:focus:ring-sky-900 focus:border-sky-500 dark:focus:border-sky-400 transition-all duration-200 bg-gray-50 dark:bg-gray-700 focus:bg-white dark:focus:bg-gray-600 text-gray-900 dark:text-gray-100 appearance-none cursor-pointer" 
                                required
                            >
                                <option value="Pagi" {{ $shift->category == 'Pagi' ? 'selected' : '' }}>Pagi</option>
                                <option value="Siang" {{ $shift->category == 'Siang' ? 'selected' : '' }}>Siang</option>
                                <option value="Malam" {{ $shift->category == 'Malam' ? 'selected' : '' }}>Malam</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 dark:text-gray-500 group-focus-within:text-sky-500 dark:group-focus-within:text-sky-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Jam Mulai -->
                    <div class="space-y-3">
                        <label for="start_time" class="block text-sm font-bold text-gray-800 dark:text-gray-200">
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
                                value="{{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}"
                                class="block w-full pl-12 pr-4 py-4 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-4 focus:ring-sky-100 dark:focus:ring-sky-900 focus:border-sky-500 dark:focus:border-sky-400 transition-all duration-200 bg-gray-50 dark:bg-gray-700 focus:bg-white dark:focus:bg-gray-600 text-gray-900 dark:text-gray-100" 
                                required
                            >
                        </div>
                    </div>

                    <!-- Jam Selesai -->
                    <div class="space-y-3">
                        <label for="end_time" class="block text-sm font-bold text-gray-800 dark:text-gray-200">
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
                                value="{{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}"
                                class="block w-full pl-12 pr-4 py-4 border-2 border-gray-200 dark:border-gray-600 rounded-xl focus:ring-4 focus:ring-sky-100 dark:focus:ring-sky-900 focus:border-sky-500 dark:focus:border-sky-400 transition-all duration-200 bg-gray-50 dark:bg-gray-700 focus:bg-white dark:focus:bg-gray-600 text-gray-900 dark:text-gray-100" 
                                required
                            >
                        </div>
                    </div>

                    <!-- Action buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 pt-8 border-t border-gray-200 dark:border-gray-700">
                        <button 
                            type="submit" 
                            class="flex-1 bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 text-white font-bold py-4 px-8 rounded-xl transition-all duration-200 transform hover:scale-[1.02] focus:outline-none focus:ring-4 focus:ring-sky-200 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
                            id="updateBtn"
                        >
                            <span class="flex items-center justify-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                </svg>
                                <span id="updateText">Update Shift</span>
                                <svg class="w-5 h-5 animate-spin hidden" id="updateSpinner" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                        </button>
                        <a 
                            href="{{ route('admin.shifts.index') }}" 
                            class="flex-1 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-bold py-4 px-8 rounded-xl transition-all duration-200 text-center focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-600 border-2 border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500"
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
document.getElementById('shiftEditForm').addEventListener('submit', function() {
    const updateBtn = document.getElementById('updateBtn');
    const updateText = document.getElementById('updateText');
    const updateSpinner = document.getElementById('updateSpinner');
    
    updateBtn.disabled = true;
    updateText.textContent = 'Mengupdate...';
    updateSpinner.classList.remove('hidden');
});
</script>
@endsection
