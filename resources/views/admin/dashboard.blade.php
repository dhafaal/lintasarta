@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="min-h-screen bg-white/90 backdrop-blur-lg">
        <div class="container mx-auto px-4 py-6 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-700 tracking-tight">
                            Admin Dashboard
                        </h1>
                        <p class="text-gray-500 text-base font-medium mt-1">
                            Welcome back! Here's what's happening today.
                        </p>
                    </div>
                    <div class="mt-4 sm:mt-0">
                        <div class="text-sm text-gray-500 font-medium">
                            Last updated: {{ now()->format('M d, Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="mb-8 relative">
                    <div class="bg-white/90 backdrop-blur-lg border border-sky-200 rounded-xl p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i data-lucide="check-circle" class="w-6 h-6 text-sky-600"></i>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sky-700 font-semibold">{{ session('success') }}</p>
                            </div>
                            <div class="ml-4 flex-shrink-0">
                                <button type="button" class="text-sky-500 hover:text-sky-700 p-1 rounded-full hover:bg-sky-100" onclick="this.closest('.mb-8').style.display='none';">
                                    <i data-lucide="x" class="w-5 h-5"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 sm:gridiejs-col-2 lg:grid-cols-3 gap-6 mb-8">
                <!-- Total Users Card -->
                <div class="group">
                    <div class="bg-white/90 backdrop-blur-lg border border-sky-200 rounded-xl overflow-hidden transition-background duration-200 hover:bg-sky-50 hover:border-sky-300">
                        <div class="p-6 sm:p-8">
                            <!-- Icon and Header -->
                            <div class="flex items-center justify-between mb-6">
                                <div class="p-3 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl">
                                    <i data-lucide="users" class="w-6 h-6 text-sky-600"></i>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-sky-500 uppercase tracking-wider">Total</div>
                                </div>
                            </div>
                            
                            <!-- Stats -->
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-700 mb-2">Users</h3>
                                <p class="text-4xl font-black text-sky-700 tracking-tight">{{ number_format($totalUsers) }}</p>
                            </div>
                            
                            <!-- Action Button -->
                            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center w-full px-4 py-3 text-sm font-semibold text-sky-700 bg-sky-50 border border-sky-200 rounded-xl hover:bg-sky-100 hover:text-sky-800 hover:scale-105 transition-all duration-200 group-hover:border-sky-300 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2">
                                <span>Manage Users</span>
                                <i data-lucide="chevron-right" class="w-4 h-4 ml-2 transform group-hover:scale-105 transition-transform duration-200"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Total Shifts Card -->
                <div class="group">
                    <div class="bg-white/90 backdrop-blur-lg border border-sky-200 rounded-xl overflow-hidden transition-background duration-200 hover:bg-sky-50 hover:border-sky-300">
                        <div class="p-6 sm:p-8">
                            <!-- Icon and Header -->
                            <div class="flex items-center justify-between mb-6">
                                <div class="p-3 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl">
                                    <i data-lucide="clock" class="w-6 h-6 text-sky-600"></i>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-sky-500 uppercase tracking-wider">Total</div>
                                </div>
                            </div>
                            
                            <!-- Stats -->
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-700 mb-2">Shifts</h3>
                                <p class="text-4xl font-black text-sky-700 tracking-tight">{{ number_format($totalShifts) }}</p>
                            </div>
                            
                            <!-- Action Button -->
                            <a href="{{ route('admin.shifts.index') }}" class="inline-flex items-center justify-center w-full px-4 py-3 text-sm font-semibold text-sky-700 bg-sky-50 border border-sky-200 rounded-xl hover:bg-sky-100 hover:text-sky-800 hover:scale-105 transition-all duration-200 group-hover:border-sky-300 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2">
                                <span>Manage Shifts</span>
                                <i data-lucide="chevron-right" class="w-4 h-4 ml-2 transform group-hover:scale-105 transition-transform duration-200"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Total Schedules Card -->
                <div class="group">
                    <div class="bg-white/90 backdrop-blur-lg border border-sky-200 rounded-xl overflow-hidden transition-background duration-200 hover:bg-sky-50 hover:border-sky-300">
                        <div class="p-6 sm:p-8">
                            <!-- Icon and Header -->
                            <div class="flex items-center justify-between mb-6">
                                <div class="p-3 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl">
                                    <i data-lucide="calendar" class="w-6 h-6 text-sky-600"></i>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-sky-500 uppercase tracking-wider">Total</div>
                                </div>
                            </div>
                            
                            <!-- Stats -->
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-700 mb-2">Schedules</h3>
                                <p class="text-4xl font-black text-sky-700 tracking-tight">{{ number_format($totalSchedules) }}</p>
                            </div>
                            
                            <!-- Action Button -->
                            <a href="{{ route('admin.schedules.index') }}" class="inline-flex items-center justify-center w-full px-4 py-3 text-sm font-semibold text-sky-700 bg-sky-50 border border-sky-200 rounded-xl hover:bg-sky-100 hover:text-sky-800 hover:scale-105 transition-all duration-200 group-hover:border-sky-300 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2">
                                <span>Manage Schedules</span>
                                <i data-lucide="chevron-right" class="w-4 h-4 ml-2 transform group-hover:scale-105 transition-transform duration-200"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Section -->
            <div class="bg-white/90 backdrop-blur-lg border border-sky-200 rounded-xl p-6 sm:p-8">
                <h2 class="text-2xl font-semibold text-gray-700 mb-6">Quick Actions</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <a href="{{ route('admin.users.create') }}" class="flex items-center p-4 text-sky-700 bg-sky-50 rounded-xl hover:bg-sky-100 hover:scale-105 transition-all duration-200 group focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2">
                        <i data-lucide="user-plus" class="w-6 h-6 mr-3 text-sky-600 group-hover:scale-105 transition-transform duration-200"></i>
                        <span class="font-semibold group-hover:text-sky-800">Add User</span>
                    </a>
                    <a href="{{ route('admin.shifts.create') }}" class="flex items-center p-4 text-sky-700 bg-sky-50 rounded-xl hover:bg-sky-100 hover:scale-105 transition-all duration-200 group focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2">
                        <i data-lucide="plus-circle" class="w-6 h-6 mr-3 text-sky-600 group-hover:scale-105 transition-transform duration-200"></i>
                        <span class="font-semibold group-hover:text-sky-800">Create Shift</span>
                    </a>
                    <a href="{{ route('admin.schedules.create') }}" class="flex items-center p-4 text-sky-700 bg-sky-50 rounded-xl hover:bg-sky-100 hover:scale-105 transition-all duration-200 group focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2">
                        <i data-lucide="calendar-plus" class="w-6 h-6 mr-3 text-sky-600 group-hover:scale-105 transition-transform duration-200"></i>
                        <span class="font-semibold group-hover:text-sky-800">New Schedule</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection