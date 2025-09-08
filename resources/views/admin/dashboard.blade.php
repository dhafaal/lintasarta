@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="min-h-screen bg-white">
        <div class="container mx-auto px-4 py-8 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="mb-12">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-4xl font-bold text-sky-900 mb-2 tracking-tight">
                            Admin Dashboard
                        </h1>
                        <p class="text-sky-600 text-lg font-medium">
                            Welcome back! Here's what's happening today.
                        </p>
                    </div>
                    <div class="mt-4 sm:mt-0">
                        <div class="text-sm text-sky-500 font-medium">
                            Last updated: {{ now()->format('M d, Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="mb-8 relative">
                    <div class="bg-gradient-to-r from-sky-50 to-sky-100 border border-sky-200 rounded-2xl p-6 shadow-sm">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sky-800 font-semibold">{{ session('success') }}</p>
                            </div>
                            <div class="ml-4 flex-shrink-0">
                                <button type="button" class="text-sky-500 hover:text-sky-700 transition-colors duration-200 p-1 rounded-full hover:bg-sky-200" onclick="this.closest('.mb-8').style.display='none';">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
                <!-- Total Users Card -->
                <div class="group">
                    <div class="bg-white rounded-3xl border border-sky-100 shadow-sm hover:shadow-xl transition-all duration-500 transform hover:-translate-y-2 overflow-hidden">
                        <div class="p-8">
                            <!-- Icon and Header -->
                            <div class="flex items-center justify-between mb-6">
                                <div class="p-3 bg-gradient-to-br from-sky-100 to-sky-200 rounded-2xl">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-8 h-8 lucide lucide-users-icon lucide-users text-sky-600"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><path d="M16 3.128a4 4 0 0 1 0 7.744"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><circle cx="9" cy="7" r="4"/></svg>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-sky-500 uppercase tracking-wider">Total</div>
                                </div>
                            </div>
                            
                            <!-- Stats -->
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-sky-800 mb-2">Users</h3>
                                <p class="text-4xl font-black text-sky-900 tracking-tight">{{ number_format($totalUsers) }}</p>
                            </div>
                            
                            <!-- Action Button -->
                            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center w-full px-4 py-3 text-sm font-semibold text-sky-700 bg-sky-50 border border-sky-200 rounded-xl hover:bg-sky-100 hover:text-sky-800 transition-all duration-200 group-hover:border-sky-300">
                                <span>Manage Users</span>
                                <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Total Shifts Card -->
                <div class="group">
                    <div class="bg-white rounded-3xl border border-sky-100 shadow-sm hover:shadow-xl transition-all duration-500 transform hover:-translate-y-2 overflow-hidden">
                        <div class="p-8">
                            <!-- Icon and Header -->
                            <div class="flex items-center justify-between mb-6">
                                <div class="p-3 bg-gradient-to-br from-sky-100 to-sky-200 rounded-2xl">
                                    <svg class="w-8 h-8 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-sky-500 uppercase tracking-wider">Total</div>
                                </div>
                            </div>
                            
                            <!-- Stats -->
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-sky-800 mb-2">Shifts</h3>
                                <p class="text-4xl font-black text-sky-900 tracking-tight">{{ number_format($totalShifts) }}</p>
                            </div>
                            
                            <!-- Action Button -->
                            <a href="{{ route('admin.shifts.index') }}" class="inline-flex items-center justify-center w-full px-4 py-3 text-sm font-semibold text-sky-700 bg-sky-50 border border-sky-200 rounded-xl hover:bg-sky-100 hover:text-sky-800 transition-all duration-200 group-hover:border-sky-300">
                                <span>Manage Shifts</span>
                                <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Total Schedules Card -->
                <div class="group">
                    <div class="bg-white rounded-3xl border border-sky-100 shadow-sm hover:shadow-xl transition-all duration-500 transform hover:-translate-y-2 overflow-hidden">
                        <div class="p-8">
                            <!-- Icon and Header -->
                            <div class="flex items-center justify-between mb-6">
                                <div class="p-3 bg-gradient-to-br from-sky-100 to-sky-200 rounded-2xl">
                                    <svg class="w-8 h-8 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-sky-500 uppercase tracking-wider">Total</div>
                                </div>
                            </div>
                            
                            <!-- Stats -->
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-sky-800 mb-2">Schedules</h3>
                                <p class="text-4xl font-black text-sky-900 tracking-tight">{{ number_format($totalSchedules) }}</p>
                            </div>
                            
                            <!-- Action Button -->
                            <a href="{{ route('admin.schedules.index') }}" class="inline-flex items-center justify-center w-full px-4 py-3 text-sm font-semibold text-sky-700 bg-sky-50 border border-sky-200 rounded-xl hover:bg-sky-100 hover:text-sky-800 transition-all duration-200 group-hover:border-sky-300">
                                <span>Manage Schedules</span>
                                <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

            <!-- Quick Actions Section -->
            <div class="bg-white rounded-3xl border border-sky-100 shadow-sm p-8">
                <h2 class="text-2xl font-bold text-sky-900 mb-6">Quick Actions</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <a href="{{ route('admin.users.create') }}" class="flex items-center p-4 text-sky-700 bg-sky-50 rounded-2xl hover:bg-sky-100 transition-colors duration-200 group">
                        <svg class="w-6 h-6 mr-3 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span class="font-semibold group-hover:text-sky-800">Add User</span>
                    </a>
                    <a href="{{ route('admin.shifts.create') }}" class="flex items-center p-4 text-sky-700 bg-sky-50 rounded-2xl hover:bg-sky-100 transition-colors duration-200 group">
                        <svg class="w-6 h-6 mr-3 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span class="font-semibold group-hover:text-sky-800">Create Shift</span>
                    </a>
                    <a href="{{ route('admin.schedules.create') }}" class="flex items-center p-4 text-sky-700 bg-sky-50 rounded-2xl hover:bg-sky-100 transition-colors duration-200 group">
                        <svg class="w-6 h-6 mr-3 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span class="font-semibold group-hover:text-sky-800">New Schedule</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection