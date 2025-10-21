@extends('layouts.admin')

@section('title', 'Leave Requests Management')

@section('content')
    <div class="min-h-screen bg-white sm:p-6 lg:p-8">
        <div class="mx-auto space-y-8">
            {{-- Enhanced Header Section --}}
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex items-center space-x-4">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl flex items-center justify-center shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-calendar-x text-sky-700">
                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                            <line x1="16" x2="16" y1="2" y2="6" />
                            <line x1="8" x2="8" y1="2" y2="6" />
                            <line x1="3" x2="21" y1="10" y2="10" />
                            <path d="m14 14-4 4" />
                            <path d="m10 14 4 4" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-700 tracking-tight">Leave Requests Management</h1>
                        <p class="text-gray-500 mt-1">{{ now()->format('l, d F Y') }} - Review and manage employee leave
                            requests</p>
                    </div>
                </div>

                <div class="text-right">
                    <div
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-sky-50 to-blue-50 border-2 border-sky-200 rounded-xl">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="lucide lucide-inbox text-sky-600 mr-2">
                            <polyline points="22 12 16 12 14 15 10 15 8 12 2 12" />
                            <path
                                d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z" />
                        </svg>
                        <div>
                            <div class="text-sm font-semibold text-gray-700">Total Requests</div>
                            <div class="text-2xl font-bold text-sky-600">{{ $leaveRequests->total() }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Success/Error Messages --}}
            @if (session('success'))
                <div class="bg-green-50 border-2 border-green-200 rounded-xl p-4">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="lucide lucide-check-circle text-green-600 mr-3">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                            <polyline points="22 4 12 14.01 9 11.01" />
                        </svg>
                        <p class="text-green-700 font-medium">{{ session('success') }}</p>
                        <button type="button" class="ml-auto text-green-500 hover:text-green-700"
                            onclick="this.closest('div').parentElement.remove();">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M18 6 6 18" />
                                <path d="m6 6 12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-50 border-2 border-red-200 rounded-xl p-4">
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="lucide lucide-alert-circle text-red-600 mr-3">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" x2="12" y1="8" y2="12" />
                            <line x1="12" x2="12.01" y1="16" y2="16" />
                        </svg>
                        <p class="text-red-700 font-medium">{{ session('error') }}</p>
                        <button type="button" class="ml-auto text-red-500 hover:text-red-700"
                            onclick="this.closest('div').parentElement.remove();">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M18 6 6 18" />
                                <path d="m6 6 12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-gradient-to-br from-sky-500 to-sky-600 rounded-2xl p-6 text-white shadow-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sky-100 text-sm font-medium uppercase tracking-wide">Total Requests</p>
                            <p class="text-3xl font-bold mt-2">{{ $leaveRequests->total() }}</p>
                            <p class="text-sky-200 text-xs mt-1">All Time</p>
                        </div>
                        <div class="w-14 h-14 bg-sky-400 bg-opacity-30 rounded-xl flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="text-white">
                                <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                                <line x1="16" x2="16" y1="2" y2="6" />
                                <line x1="8" x2="8" y1="2" y2="6" />
                                <line x1="3" x2="21" y1="10" y2="10" />
                            </svg>
                        </div>
                    </div>
                </div>
                <x-stats-card title="Pending" :count="$leaveRequests->where('status', 'pending')->count()" subtitle="Awaiting Review"
                    bgColor="bg-gradient-to-br from-amber-100 to-amber-200"
                    icon='<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7 text-amber-600 lucide lucide-clock"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>' />
                <x-stats-card title="Approved" :count="$leaveRequests->where('status', 'approved')->count()" subtitle="Leave Granted"
                    bgColor="bg-gradient-to-br from-green-100 to-green-200"
                    icon='<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7 text-green-600 lucide lucide-check-circle-2"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="m9 12 2 2 4-4"/></svg>' />
                <x-stats-card title="Rejected" :count="$leaveRequests->where('status', 'rejected')->count()" subtitle="Leave Denied"
                    bgColor="bg-gradient-to-br from-red-100 to-red-200"
                    icon='<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7 text-red-600 lucide lucide-x-circle"><circle cx="12" cy="12" r="10"/><line x1="15" x2="9" y1="9" y2="15"/><line x1="9" x2="15" y1="9" y2="15"/></svg>' />
            </div>

            {{-- Enhanced Table Card --}}
            <div class="bg-white rounded-2xl border-2 border-sky-100 overflow-hidden shadow-xl">
                <div class="px-8 py-6 border-b border-sky-100 bg-gradient-to-r from-sky-50 to-blue-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-sky-900">Leave Requests</h2>
                            <p class="text-sky-700 mt-1">Manage and review employee leave requests</p>
                        </div>

                        {{-- Filter Tabs --}}
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('admin.attendances.leave-requests') }}"
                                class="px-4 py-2 rounded-lg text-sm font-semibold transition-all {{ !request('status') ? 'bg-sky-600 text-white shadow-sm' : 'bg-white text-gray-700 hover:bg-gray-50 border-2 border-gray-200' }}">
                                All Requests
                            </a>
                            <a href="{{ route('admin.attendances.leave-requests', ['status' => 'pending']) }}"
                                class="px-4 py-2 rounded-lg text-sm font-semibold transition-all {{ request('status') === 'pending' ? 'bg-amber-600 text-white shadow-sm' : 'bg-white text-gray-700 hover:bg-gray-50 border-2 border-gray-200' }}">
                                Pending
                            </a>
                            <a href="{{ route('admin.attendances.leave-requests', ['status' => 'approved']) }}"
                                class="px-4 py-2 rounded-lg text-sm font-semibold transition-all {{ request('status') === 'approved' ? 'bg-green-600 text-white shadow-sm' : 'bg-white text-gray-700 hover:bg-gray-50 border-2 border-gray-200' }}">
                                Approved
                            </a>
                            <a href="{{ route('admin.attendances.leave-requests', ['status' => 'rejected']) }}"
                                class="px-4 py-2 rounded-lg text-sm font-semibold transition-all {{ request('status') === 'rejected' ? 'bg-red-600 text-white shadow-sm' : 'bg-white text-gray-700 hover:bg-gray-50 border-2 border-gray-200' }}">
                                Rejected
                            </a>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b-2 border-gray-200">
                            <tr>
                                <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-user text-sky-600 mr-2">
                                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                                            <circle cx="12" cy="7" r="4" />
                                        </svg>
                                        Employee
                                    </div>
                                </th>
                                <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-calendar text-sky-600 mr-2">
                                            <rect x="3" y="4" width="18" height="18" rx="2"
                                                ry="2" />
                                            <line x1="16" x2="16" y1="2" y2="6" />
                                            <line x1="8" x2="8" y1="2" y2="6" />
                                            <line x1="3" x2="21" y1="10" y2="10" />
                                        </svg>
                                        Total Schedules
                                    </div>
                                </th>
                                <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-calendar-range text-sky-600 mr-2">
                                            <rect width="18" height="18" x="3" y="4" rx="2" />
                                            <path d="M16 2v4" />
                                            <path d="M3 10h18" />
                                            <path d="M8 2v4" />
                                            <path d="M17 14h-6" />
                                            <path d="M13 18H7" />
                                            <path d="M7 14h.01" />
                                            <path d="M17 18h.01" />
                                        </svg>
                                        Date Range
                                    </div>
                                </th>
                                <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-message-circle text-sky-600 mr-2">
                                            <path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z" />
                                        </svg>
                                        Reason
                                    </div>
                                </th>
                                <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-activity text-sky-600 mr-2">
                                            <path d="M22 12h-4l-3 9L9 3l-3 9H2" />
                                        </svg>
                                        Status
                                    </div>
                                </th>
                                <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-clock text-sky-600 mr-2">
                                            <circle cx="12" cy="12" r="10" />
                                            <polyline points="12 6 12 12 16 14" />
                                        </svg>
                                        Submitted
                                    </div>
                                </th>
                                <th class="px-8 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($leaveRequests as $request)
                                <tr class="hover:bg-sky-50 transition-colors duration-200 group">
                                    {{-- Employee --}}
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="w-10 h-10 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl flex items-center justify-center mr-4 group-hover:from-sky-200 group-hover:to-sky-300 transition-colors">
                                                <span class="text-sky-600 font-bold text-sm">
                                                    {{ strtoupper(substr($request->user->name, 0, 2)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <div class="text-base font-semibold text-gray-700">
                                                    {{ $request->user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $request->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Total Schedules --}}
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="w-8 h-8 bg-gradient-to-br from-sky-100 to-sky-200 rounded-lg flex items-center justify-center mr-3">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="lucide lucide-calendar text-sky-600">
                                                    <rect x="3" y="4" width="18" height="18" rx="2"
                                                        ry="2" />
                                                    <line x1="16" x2="16" y1="2" y2="6" />
                                                    <line x1="8" x2="8" y1="2" y2="6" />
                                                    <line x1="3" x2="21" y1="10" y2="10" />
                                                </svg>
                                            </div>
                                            <div>
                                                <span
                                                    class="text-base font-semibold text-gray-900">{{ $request->schedules_count }}</span>
                                                <span class="text-sm text-gray-500 ml-1">days</span>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Date Range --}}
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $request->date_range }}</div>
                                    </td>

                                    {{-- Reason --}}
                                    <td class="px-8 py-6">
                                        <div class="text-sm text-gray-900 max-w-xs truncate"
                                            title="{{ $request->reason }}">
                                            {{ $request->reason }}
                                        </div>
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        @if ($request->status === 'pending')
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-amber-100 text-amber-800">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="lucide lucide-clock mr-1">
                                                    <circle cx="12" cy="12" r="10" />
                                                    <polyline points="12 6 12 12 16 14" />
                                                </svg>
                                                Pending
                                            </span>
                                        @elseif($request->status === 'approved')
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="lucide lucide-check-circle mr-1">
                                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                                    <polyline points="22 4 12 14.01 9 11.01" />
                                                </svg>
                                                Approved
                                            </span>
                                        @elseif($request->status === 'rejected')
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="lucide lucide-x-circle mr-1">
                                                    <circle cx="12" cy="12" r="10" />
                                                    <path d="m15 9-6 6" />
                                                    <path d="m9 9 6 6" />
                                                </svg>
                                                Rejected
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Submitted --}}
                                    <td class="px-8 py-6 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $request->created_at->format('d M Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ $request->created_at->format('H:i') }}</div>
                                    </td>

                                    {{-- Actions --}}
                                    <td class="px-8 py-6 whitespace-nowrap text-left">
                                        <div class="flex items-center space-x-2">
                                            <button type="button" onclick="viewLeaveRequest({{ $request->id }})"
                                                class="inline-flex items-center px-3 py-1.5 bg-sky-100 hover:bg-sky-200 text-sky-700 text-xs font-semibold rounded-lg transition-all duration-200  ">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="lucide lucide-eye mr-1">
                                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                                                    <circle cx="12" cy="12" r="3" />
                                                </svg>
                                                View
                                            </button>

                                            @if ($request->status === 'pending')
                                                <button type="button"
                                                    onclick="processLeaveRequest({{ $request->id }}, 'approve')"
                                                    class="inline-flex items-center px-3 py-1.5 bg-green-100 hover:bg-green-200 text-green-700 text-xs font-semibold rounded-lg transition-all duration-200  ">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="lucide lucide-check mr-1">
                                                        <polyline points="20 6 9 17 4 12" />
                                                    </svg>
                                                    Approve
                                                </button>

                                                <button type="button"
                                                    onclick="processLeaveRequest({{ $request->id }}, 'reject')"
                                                    class="inline-flex items-center px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 text-xs font-semibold rounded-lg transition-all duration-200  ">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="lucide lucide-x mr-1">
                                                        <path d="M18 6 6 18" />
                                                        <path d="m6 6 12 12" />
                                                    </svg>
                                                    Reject
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-8 py-16 text-center">
                                        <div class="flex flex-col items-center">
                                            <div
                                                class="w-20 h-20 bg-gradient-to-br from-sky-100 to-sky-200 rounded-full flex items-center justify-center mb-6">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="lucide lucide-calendar-x text-sky-400">
                                                    <rect width="18" height="18" x="3" y="4" rx="2"
                                                        ry="2" />
                                                    <line x1="16" x2="16" y1="2" y2="6" />
                                                    <line x1="8" x2="8" y1="2" y2="6" />
                                                    <line x1="3" x2="21" y1="10" y2="10" />
                                                    <path d="m14 14-4 4" />
                                                    <path d="m10 14 4 4" />
                                                </svg>
                                            </div>
                                            <h3 class="text-xl font-bold text-gray-900 mb-2">No leave requests found</h3>
                                            <p class="text-gray-600 mb-6 max-w-sm">There are no leave requests to display
                                                at this time.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            @if ($leaveRequests->hasPages())
                <div class="mt-6">
                    {{ $leaveRequests->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Leave Request Detail Modal --}}
    <div id="leave-detail-modal"
        class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div
            class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl relative transform transition-all border border-gray-100 max-h-[90vh] overflow-y-auto">
            {{-- Modal Header --}}
            <div
                class="flex items-center justify-between p-6 border-b border-gray-100 bg-gradient-to-r from-sky-50 to-blue-50 rounded-t-2xl sticky top-0 z-10">
                <div class="flex items-center space-x-4">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-sky-500 to-sky-600 rounded-xl flex items-center justify-center shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="lucide lucide-calendar-x text-white">
                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                            <line x1="16" x2="16" y1="2" y2="6" />
                            <line x1="8" x2="8" y1="2" y2="6" />
                            <line x1="3" x2="21" y1="10" y2="10" />
                            <path d="m14 14-4 4" />
                            <path d="m10 14 4 4" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">Leave Request Details</h2>
                        <p class="text-sm text-sky-600">Review and manage leave request</p>
                    </div>
                </div>
                <button type="button" onclick="closeLeaveDetailModal()"
                    class="w-10 h-10 flex items-center justify-center rounded-xl text-gray-400 hover:text-gray-600 hover:bg-white/50 transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-x">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                </button>
            </div>

            {{-- Modal Content --}}
            <div id="modal-content" class="p-6">
                {{-- Content will be loaded here --}}
            </div>
        </div>
    </div>

    <script>
        async function viewLeaveRequest(requestId) {
            const modal = document.getElementById('leave-detail-modal');
            const content = document.getElementById('modal-content');

            try {
                content.innerHTML = `
                <div class="text-center py-8">
                    <div class="inline-flex items-center space-x-2 text-gray-500">
                        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-sky-600"></div>
                        <span>Loading leave request details...</span>
                    </div>
                </div>
            `;
                modal.classList.remove('hidden');

                const response = await fetch(`/admin/attendances/leave-requests/${requestId}`, {
                    headers: {
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('Failed to load leave request details');
                }

                const html = await response.text();
                content.innerHTML = html;

            } catch (error) {
                console.error('Error loading leave request:', error);
                content.innerHTML = `
                <div class="text-center py-8">
                    <div class="text-red-500">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-alert-circle mx-auto mb-2">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" x2="12" y1="8" y2="12"/>
                            <line x1="12" x2="12.01" y1="16" y2="16"/>
                        </svg>
                        <p>Failed to load leave request details</p>
                    </div>
                </div>
            `;
            }
        }

        function closeLeaveDetailModal() {
            document.getElementById('leave-detail-modal').classList.add('hidden');
        }

        async function processLeaveRequest(requestId, action) {
            if (!confirm(`Are you sure you want to ${action} this leave request?`)) {
                return;
            }

            try {
                const response = await fetch(`/admin/attendances/leave-requests/${requestId}/process-simple`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        action: action
                    })
                });

                if (response.ok) {
                    const result = await response.json();
                    alert(result.message);
                    location.reload();
                } else {
                    const error = await response.json();
                    alert(error.message || 'Failed to process leave request');
                }
            } catch (error) {
                console.error('Error processing leave request:', error);
                alert('An error occurred while processing the request');
            }
        }

        document.getElementById('leave-detail-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeLeaveDetailModal();
            }
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeLeaveDetailModal();
            }
        });
    </script>
@endsection
