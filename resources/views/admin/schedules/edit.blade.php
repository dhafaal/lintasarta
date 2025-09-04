@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-white sm:p-6 lg:p-8">
    <div class="max-w-2xl mx-auto">
        <!-- Enhanced header to match users module styling -->
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-3">
                <div class="p-3 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl shadow-sm">
                    <svg class="w-7 h-7 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Edit Schedule</h1>
                    <p class="text-gray-600 mt-1">Perbarui jadwal kerja karyawan</p>
                </div>
            </div>
            <nav class="flex items-center space-x-2 text-sm text-gray-500">
                <a href="{{ route('admin.schedules.index') }}" class="hover:text-sky-600 transition-colors">Schedules</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span class="text-gray-900 font-medium">Edit Schedule</span>
            </nav>
        </div>

        <!-- Enhanced form card to match users module styling -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-sky-500 to-sky-600 px-8 py-6">
                <h2 class="text-xl font-semibold text-white">Update Informasi Schedule</h2>
                <p class="text-sky-100 mt-1">Perbarui data yang diperlukan</p>
            </div>
            
            <div class="p-8">
                <form action="{{ route('admin.schedules.update', $schedule) }}" method="POST" class="space-y-8" id="scheduleEditForm">
                    @csrf 
                    @method('PUT')
                    
                    <!-- User Selection -->
                    <div class="space-y-3">
                        <label for="user_id" class="block text-sm font-bold text-gray-800">
                            Pilih User
                            <span class="text-red-500 ml-1">*</span>
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-sky-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <select 
                                name="user_id" 
                                id="user_id" 
                                class="block w-full pl-12 pr-10 py-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-sky-100 focus:border-sky-500 transition-all duration-200 bg-gray-50 focus:bg-white text-gray-900 appearance-none cursor-pointer" 
                                required
                            >
                                <option value="">-- Pilih Karyawan --</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}" @if($schedule->user_id == $u->id) selected @endif>{{ $u->name }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-sky-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Shift Selection -->
                    <div class="space-y-3">
                        <label for="shift_id" class="block text-sm font-bold text-gray-800">
                            Pilih Shift
                            <span class="text-red-500 ml-1">*</span>
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-sky-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <select 
                                name="shift_id" 
                                id="shift_id" 
                                class="block w-full pl-12 pr-10 py-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-sky-100 focus:border-sky-500 transition-all duration-200 bg-gray-50 focus:bg-white text-gray-900 appearance-none cursor-pointer" 
                                required
                            >
                                <option value="">-- Pilih Shift --</option>
                                @foreach($shifts as $sh)
                                    <option value="{{ $sh->id }}" @if($schedule->shift_id == $sh->id) selected @endif>{{ $sh->name }} ({{ $sh->start_time }} - {{ $sh->end_time }})</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-sky-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Date Selection -->
                    <div class="space-y-3">
                        <label for="schedule_date" class="block text-sm font-bold text-gray-800">
                            Tanggal Schedule
                            <span class="text-red-500 ml-1">*</span>
                        </label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-sky-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <input 
                                type="date" 
                                name="schedule_date" 
                                id="schedule_date" 
                                value="{{ $schedule->schedule_date }}" 
                                class="block w-full pl-12 pr-4 py-4 border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-sky-100 focus:border-sky-500 transition-all duration-200 bg-gray-50 focus:bg-white text-gray-900" 
                                required
                            >
                        </div>
                    </div>

                    <!-- Enhanced action buttons to match users module -->
                    <div class="flex flex-col sm:flex-row gap-4 pt-8 border-t border-gray-200">
                        <button 
                            type="submit" 
                            class="flex-1 bg-gradient-to-r from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 text-white font-bold py-4 px-8 rounded-xl transition-all duration-200 transform hover:scale-[1.02] focus:outline-none focus:ring-4 focus:ring-sky-200 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed"
                            id="updateBtn"
                        >
                            <span class="flex items-center justify-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                </svg>
                                <span id="updateText">Update Schedule</span>
                                <svg class="w-5 h-5 animate-spin hidden" id="updateSpinner" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                        </button>
                        <a 
                            href="{{ route('admin.schedules.index') }}" 
                            class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-4 px-8 rounded-xl transition-all duration-200 text-center focus:outline-none focus:ring-4 focus:ring-gray-200 border-2 border-gray-200 hover:border-gray-300"
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
document.getElementById('scheduleEditForm').addEventListener('submit', function() {
    const updateBtn = document.getElementById('updateBtn');
    const updateText = document.getElementById('updateText');
    const updateSpinner = document.getElementById('updateSpinner');
    
    updateBtn.disabled = true;
    updateText.textContent = 'Mengupdate...';
    updateSpinner.classList.remove('hidden');
});
</script>
@endsection