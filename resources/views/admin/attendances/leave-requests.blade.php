@extends('layouts.admin')

@section('title', 'Leave Requests Management')

@section('content')
    <div class="min-h-screen bg-white p-3 sm:p-4 md:p-6 lg:p-8">
        <div class="space-y-4 sm:space-y-6 md:space-y-8">
            {{-- Enhanced Header Section --}}
            <div class="flex flex-col gap-4 sm:gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-sky-100 to-sky-200 shadow-sm sm:h-12 sm:w-12"
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
                            class="lucide lucide-calendar text-sky-700"
                        >
                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                            <line x1="16" x2="16" y1="2" y2="6" />
                            <line x1="8" x2="8" y1="2" y2="6" />
                            <line x1="3" x2="21" y1="10" y2="10" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold tracking-tight text-gray-900 sm:text-2xl md:text-3xl">
                            Leave Requests Management
                        </h1>
                        <p class="mt-1 text-xs text-gray-500 sm:text-sm md:text-base">
                            {{ now()->format('l, d F Y') }} - Review and manage employee leave requests
                        </p>
                    </div>
                </div>

                <a
                    href="{{ route('admin.attendances.index') }}"
                    class="inline-flex items-center rounded-xl border-2 border-sky-200 bg-white px-4 py-2 font-semibold text-sky-700 shadow-sm transition-all hover:bg-sky-50 sm:px-6 sm:py-3"
                >
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
                        class="lucide lucide-arrow-left mr-2"
                    >
                        <path d="m12 19-7-7 7-7" />
                        <path d="M19 12H5" />
                    </svg>
                    <span class="hidden sm:inline">Back to Attendances</span>
                    <span class="sm:hidden">Back</span>
                </a>
            </div>

            {{-- Success/Error Messages --}}
            @if (session('success'))
                <div class="rounded-xl border-2 border-green-200 bg-green-50 p-3 sm:p-4">
                    <div class="flex items-center">
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
                            class="lucide lucide-check-circle mr-3 text-green-600"
                        >
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                            <polyline points="22 4 12 14.01 9 11.01" />
                        </svg>
                        <p class="text-xs font-medium text-green-700 sm:text-sm">{{ session('success') }}</p>
                        <button
                            type="button"
                            class="ml-auto text-green-500 hover:text-green-700"
                            onclick="this.closest('div').parentElement.remove();"
                        >
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
                            >
                                <path d="M18 6 6 18" />
                                <path d="m6 6 12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-xl border-2 border-red-200 bg-red-50 p-3 sm:p-4">
                    <div class="flex items-center">
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
                            class="lucide lucide-alert-circle mr-3 text-red-600"
                        >
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" x2="12" y1="8" y2="12" />
                            <line x1="12" x2="12.01" y1="16" y2="16" />
                        </svg>
                        <p class="text-xs font-medium text-red-700 sm:text-sm">{{ session('error') }}</p>
                        <button
                            type="button"
                            class="ml-auto text-red-500 hover:text-red-700"
                            onclick="this.closest('div').parentElement.remove();"
                        >
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
                            >
                                <path d="M18 6 6 18" />
                                <path d="m6 6 12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            {{-- Stats Cards --}}
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-2 sm:gap-4 md:gap-6 lg:grid-cols-4">
                <div
                    class="rounded-2xl bg-gradient-to-br from-sky-500 to-sky-600 p-3 text-white shadow-xl sm:p-4 md:p-6"
                >
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium tracking-wide text-sky-100 uppercase sm:text-sm">
                                Total Requests
                            </p>
                            <p class="mt-1 text-xl font-bold sm:mt-2 sm:text-2xl md:text-3xl">
                                {{ $leaveRequests->total() }}
                            </p>
                            <p class="mt-1 text-xs text-sky-200">All Time</p>
                        </div>
                        <div
                            class="bg-opacity-30 flex h-10 w-10 items-center justify-center rounded-xl bg-sky-400 sm:h-12 sm:w-12 md:h-14 md:w-14"
                        >
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
                                class="text-white"
                            >
                                <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                                <line x1="16" x2="16" y1="2" y2="6" />
                                <line x1="8" x2="8" y1="2" y2="6" />
                                <line x1="3" x2="21" y1="10" y2="10" />
                            </svg>
                        </div>
                    </div>
                </div>

                <x-admin.stats-card
                    title="Pending"
                    :count="$leaveRequests->where('status', 'pending')->count()"
                    subtitle="Awaiting Review"
                    bgColor="bg-gradient-to-br from-amber-100 to-amber-200"
                    icon='<svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>'
                />

                <x-admin.stats-card
                    title="Approved"
                    :count="$leaveRequests->where('status', 'approved')->count()"
                    subtitle="Leave Granted"
                    bgColor="bg-gradient-to-br from-green-100 to-emerald-100"
                    icon='<svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>'
                />

                <x-admin.stats-card
                    title="Rejected"
                    :count="$leaveRequests->where('status', 'rejected')->count()"
                    subtitle="Leave Denied"
                    bgColor="bg-gradient-to-br from-red-100 to-rose-100"
                    icon='<svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="15" x2="9" y1="9" y2="15"/>
                        <line x1="9" x2="15" y1="9" y2="15"/>
                    </svg>'
                />
            </div>

            {{-- Enhanced Table Card --}}
            <div class="overflow-hidden rounded-2xl border-2 border-sky-100 bg-white shadow-xl">
                <div
                    class="border-b border-sky-100 bg-gradient-to-r from-sky-50 to-blue-50 px-3 py-3 sm:px-4 sm:py-4 md:px-6 md:py-6 lg:px-8"
                >
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between sm:gap-4">
                        <div>
                            <h2 class="text-base font-bold text-sky-900 sm:text-lg md:text-xl">Leave Requests</h2>
                            <p class="mt-1 text-xs text-sky-700 sm:text-sm md:text-base">
                                Manage and review employee leave requests
                            </p>
                        </div>

                        {{-- Filter Tabs --}}
                        <div class="flex flex-wrap gap-1 sm:gap-2">
                            <a
                                href="{{ route('admin.attendances.leave-requests') }}"
                                class="{{ ! request('status') ? 'bg-sky-600 text-white shadow-sm' : 'border-2 border-gray-200 bg-white text-gray-700 hover:bg-gray-50' }} rounded-lg px-2 py-1 text-xs font-semibold transition-all sm:px-3 sm:py-1.5 sm:text-sm md:px-4 md:py-2"
                            >
                                All Requests
                            </a>
                            <a
                                href="{{ route('admin.attendances.leave-requests', ['status' => 'pending']) }}"
                                class="{{ request('status') === 'pending' ? 'bg-amber-600 text-white shadow-sm' : 'border-2 border-gray-200 bg-white text-gray-700 hover:bg-gray-50' }} rounded-lg px-2 py-1 text-xs font-semibold transition-all sm:px-3 sm:py-1.5 sm:text-sm md:px-4 md:py-2"
                            >
                                Pending
                            </a>
                            <a
                                href="{{ route('admin.attendances.leave-requests', ['status' => 'approved']) }}"
                                class="{{ request('status') === 'approved' ? 'bg-green-600 text-white shadow-sm' : 'border-2 border-gray-200 bg-white text-gray-700 hover:bg-gray-50' }} rounded-lg px-2 py-1 text-xs font-semibold transition-all sm:px-3 sm:py-1.5 sm:text-sm md:px-4 md:py-2"
                            >
                                Approved
                            </a>
                            <a
                                href="{{ route('admin.attendances.leave-requests', ['status' => 'rejected']) }}"
                                class="{{ request('status') === 'rejected' ? 'bg-red-600 text-white shadow-sm' : 'border-2 border-gray-200 bg-white text-gray-700 hover:bg-gray-50' }} rounded-lg px-2 py-1 text-xs font-semibold transition-all sm:px-3 sm:py-1.5 sm:text-sm md:px-4 md:py-2"
                            >
                                Rejected
                            </a>
                        </div>

                        {{-- Mobile View Toggle --}}
                        <div class="flex items-center gap-2 sm:hidden">
                            <span class="text-xs text-gray-600">View:</span>
                            <button
                                onclick="toggleMobileView()"
                                class="rounded-lg bg-sky-100 px-2 py-1 text-xs font-medium text-sky-700"
                            >
                                <span id="viewToggleText">Table</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Desktop Table View --}}
                <div id="desktopView" class="overflow-x-auto">
                    <table class="w-full min-w-[500px] sm:min-w-[600px]">
                        <thead class="border-b-2 border-gray-200 bg-gray-50">
                            <tr>
                                <th
                                    class="px-3 py-2 text-left text-xs font-bold tracking-wider text-gray-700 uppercase sm:px-4 sm:py-3 md:px-6 md:py-4 lg:px-8"
                                >
                                    <div class="flex items-center">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="12"
                                            height="12"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="lucide lucide-user mr-1 text-sky-600 sm:mr-2"
                                        >
                                            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                                            <circle cx="12" cy="7" r="4" />
                                        </svg>
                                        <span class="hidden sm:inline">Employee</span>
                                        <span class="sm:hidden">Emp</span>
                                    </div>
                                </th>
                                <th
                                    class="px-3 py-2 text-left text-xs font-bold tracking-wider text-gray-700 uppercase sm:px-4 sm:py-3 md:px-6 md:py-4 lg:px-8"
                                >
                                    <div class="flex items-center">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="12"
                                            height="12"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="lucide lucide-calendar mr-1 text-sky-600 sm:mr-2"
                                        >
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                            <line x1="16" x2="16" y1="2" y2="6" />
                                            <line x1="8" x2="8" y1="2" y2="6" />
                                            <line x1="3" x2="21" y1="10" y2="10" />
                                        </svg>
                                        Days
                                    </div>
                                </th>
                                <th
                                    class="hidden px-3 py-2 text-left text-xs font-bold tracking-wider text-gray-700 uppercase sm:table-cell sm:px-4 sm:py-3 md:px-6 md:py-4 lg:px-8"
                                >
                                    <div class="flex items-center">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="12"
                                            height="12"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="lucide lucide-calendar-range mr-1 text-sky-600 sm:mr-2"
                                        >
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
                                <th
                                    class="hidden px-3 py-2 text-left text-xs font-bold tracking-wider text-gray-700 uppercase sm:px-4 sm:py-3 md:table-cell md:px-6 md:py-4 lg:px-8"
                                >
                                    <div class="flex items-center">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="12"
                                            height="12"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="lucide lucide-message-circle mr-1 text-sky-600 sm:mr-2"
                                        >
                                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                                        </svg>
                                        Reason
                                    </div>
                                </th>
                                <th
                                    class="px-3 py-2 text-left text-xs font-bold tracking-wider text-gray-700 uppercase sm:px-4 sm:py-3 md:px-6 md:py-4 lg:px-8"
                                >
                                    <div class="flex items-center">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="12"
                                            height="12"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="lucide lucide-flag mr-1 text-sky-600 sm:mr-2"
                                        >
                                            <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z" />
                                            <line x1="4" x2="4" y1="22" y2="15" />
                                        </svg>
                                        Status
                                    </div>
                                </th>
                                <th
                                    class="hidden px-3 py-2 text-left text-xs font-bold tracking-wider text-gray-700 uppercase sm:table-cell sm:px-4 sm:py-3 md:px-6 md:py-4 lg:px-8"
                                >
                                    <div class="flex items-center">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="12"
                                            height="12"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="lucide lucide-clock mr-1 text-sky-600 sm:mr-2"
                                        >
                                            <circle cx="12" cy="12" r="10" />
                                            <polyline points="12 6 12 12 16 14" />
                                        </svg>
                                        Submitted
                                    </div>
                                </th>
                                <th
                                    class="px-3 py-2 text-center text-left text-xs font-bold tracking-wider text-gray-700 uppercase sm:px-4 sm:py-3 md:px-6 md:py-4 lg:px-8"
                                >
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($leaveRequests as $request)
                                <tr class="transition-colors hover:bg-gray-50">
                                    <td class="px-3 py-3 whitespace-nowrap sm:px-4 sm:py-4 md:px-6 lg:px-8">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 flex-shrink-0 sm:h-10 sm:w-10">
                                                <div
                                                    class="mr-4 flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-sky-100 to-sky-200 transition-colors group-hover:from-sky-200 group-hover:to-sky-300"
                                                >
                                                    <span class="text-xs font-bold text-sky-600 sm:text-sm">
                                                        {{ strtoupper(substr($request->user->name, 0, 1)) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-3 sm:ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $request->user->name }}
                                                </div>
                                                <div class="hidden text-xs text-gray-500 sm:block">
                                                    {{ $request->user->email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap sm:px-4 sm:py-4 md:px-6 lg:px-8">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $request->schedules_count }} days
                                        </div>
                                    </td>
                                    <td
                                        class="hidden px-3 py-3 whitespace-nowrap sm:table-cell sm:px-4 sm:py-4 md:px-6 lg:px-8"
                                    >
                                        <div class="text-sm text-gray-900">{{ $request->date_range }}</div>
                                    </td>
                                    <td class="hidden px-3 py-3 sm:px-4 sm:py-4 md:table-cell md:px-6 lg:px-8">
                                        <div
                                            class="max-w-xs truncate text-sm text-gray-900"
                                            title="{{ $request->reason }}"
                                        >
                                            {{ $request->reason }}
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 whitespace-nowrap sm:px-4 sm:py-4 md:px-6 lg:px-8">
                                        @if ($request->status === 'pending')
                                            <span
                                                class="inline-flex items-center rounded-full bg-amber-100 px-2 py-1 text-xs font-semibold text-amber-800"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="8"
                                                    height="8"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    class="lucide lucide-clock mr-1"
                                                >
                                                    <circle cx="12" cy="12" r="10" />
                                                    <polyline points="12 6 12 12 16 14" />
                                                </svg>
                                                Pending
                                            </span>
                                        @elseif ($request->status === 'approved')
                                            <span
                                                class="inline-flex items-center rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-800"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="8"
                                                    height="8"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    class="lucide lucide-check-circle mr-1"
                                                >
                                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                                    <polyline points="22 4 12 14.01 9 11.01" />
                                                </svg>
                                                Approved
                                            </span>
                                        @elseif ($request->status === 'rejected')
                                            <span
                                                class="inline-flex items-center rounded-full bg-red-100 px-2 py-1 text-xs font-semibold text-red-800"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    width="8"
                                                    height="8"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    class="lucide lucide-x-circle mr-1"
                                                >
                                                    <circle cx="12" cy="12" r="10" />
                                                    <path d="m15 9-6 6" />
                                                    <path d="m9 9 6 6" />
                                                </svg>
                                                Rejected
                                            </span>
                                        @endif
                                    </td>
                                    <td
                                        class="hidden px-3 py-3 text-sm whitespace-nowrap text-gray-500 sm:table-cell sm:px-4 sm:py-4 md:px-6 lg:px-8"
                                    >
                                        {{ $request->created_at->format('M d, Y') }}
                                    </td>
                                    <td
                                        class="px-3 py-3 text-center text-sm font-medium whitespace-nowrap sm:px-4 sm:py-4 md:px-6 lg:px-8"
                                    >
                                        <div class="flex items-center justify-center space-x-1 sm:space-x-2">
                                            <button
                                                type="button"
                                                onclick="viewLeaveRequest({{ $request->id }})"
                                                class="inline-flex items-center rounded-lg bg-sky-100 px-4 py-2 text-sm font-semibold text-sky-700 transition-all duration-200 hover:bg-sky-200"
                                                title="View Details"
                                            >
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
                                                    class="lucide lucide-eye"
                                                >
                                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                                                    <circle cx="12" cy="12" r="3" />
                                                </svg>
                                            </button>
                                            @if ($request->status === 'pending')
                                                <button
                                                    type="button"
                                                    onclick="processLeaveRequest({{ $request->id }}, 'approve')"
                                                    class="inline-flex items-center rounded-lg bg-green-100 px-4 py-2 text-sm font-semibold text-green-700 transition-all duration-200 hover:bg-green-200"
                                                    title="Approve"
                                                >
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
                                                        class="lucide lucide-check"
                                                    >
                                                        <path d="M20 6 9 17l-5-5" />
                                                    </svg>
                                                </button>
                                                <button
                                                    type="button"
                                                    onclick="processLeaveRequest({{ $request->id }}, 'reject')"
                                                    class="inline-flex items-center rounded-lg bg-red-100 px-4 py-2 text-sm font-semibold text-red-700 transition-all duration-200 hover:bg-red-200"
                                                    title="Reject"
                                                >
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
                                                        class="lucide lucide-x"
                                                    >
                                                        <path d="M18 6 6 18" />
                                                        <path d="m6 6 12 12" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="32"
                                                height="32"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                class="lucide lucide-inbox mb-2 text-gray-400"
                                            >
                                                <polyline points="22 12 16 12 14 15 10 15 8 12 2 12" />
                                                <path
                                                    d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"
                                                />
                                            </svg>
                                            <p class="text-sm">No leave requests found</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Card View (Hidden by default) --}}
                <div id="mobileView" class="hidden space-y-3 p-3 sm:space-y-4 sm:p-4">
                    @forelse ($leaveRequests as $request)
                        <div
                            class="rounded-lg border border-gray-200 bg-white p-3 shadow-sm transition-shadow hover:shadow-md sm:p-4"
                        >
                            {{-- Employee Header --}}
                            <div class="mb-2 flex items-center justify-between sm:mb-3">
                                <div class="flex items-center gap-2 sm:gap-3">
                                    <div
                                        class="from-100-400 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-md bg-gradient-to-br to-sky-200 sm:h-10 sm:w-10"
                                    >
                                        <span class="text-xs font-bold text-sky-600 sm:text-sm">
                                            {{ strtoupper(substr($request->user->name, 0, 1)) }}
                                        </span>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="text-xs font-bold text-gray-900 sm:text-sm">
                                            {{ $request->user->name }}
                                        </div>
                                        <div class="hidden text-xs text-gray-500 sm:block">
                                            {{ $request->user->email }}
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    @if ($request->status === 'pending')
                                        <span
                                            class="inline-flex items-center rounded-full bg-amber-100 px-2 py-1 text-xs font-semibold text-amber-800"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="8"
                                                height="8"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                class="lucide lucide-clock mr-1"
                                            >
                                                <circle cx="12" cy="12" r="10" />
                                                <polyline points="12 6 12 12 16 14" />
                                            </svg>
                                            Pending
                                        </span>
                                    @elseif ($request->status === 'approved')
                                        <span
                                            class="inline-flex items-center rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-800"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="8"
                                                height="8"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                class="lucide lucide-check-circle mr-1"
                                            >
                                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                                <polyline points="22 4 12 14.01 9 11.01" />
                                            </svg>
                                            Approved
                                        </span>
                                    @elseif ($request->status === 'rejected')
                                        <span
                                            class="inline-flex items-center rounded-full bg-red-100 px-2 py-1 text-xs font-semibold text-red-800"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                width="8"
                                                height="8"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                class="lucide lucide-x-circle mr-1"
                                            >
                                                <circle cx="12" cy="12" r="10" />
                                                <path d="m15 9-6 6" />
                                                <path d="m9 9 6 6" />
                                            </svg>
                                            Rejected
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Leave Details --}}
                            <div class="mb-2 grid grid-cols-2 gap-2 sm:mb-3 sm:gap-3">
                                <div class="rounded-lg bg-gray-50 p-2 sm:p-3">
                                    <div class="mb-1 text-xs font-medium text-gray-500">Duration</div>
                                    <div class="flex items-center gap-1 sm:gap-2">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="10"
                                            height="10"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="lucide lucide-calendar text-sky-600"
                                        >
                                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                            <line x1="16" x2="16" y1="2" y2="6" />
                                            <line x1="8" x2="8" y1="2" y2="6" />
                                            <line x1="3" x2="21" y1="10" y2="10" />
                                        </svg>
                                        <span class="text-xs font-semibold text-gray-900 sm:text-sm">
                                            {{ $request->schedules_count }} days
                                        </span>
                                    </div>
                                </div>
                                <div class="rounded-lg bg-gray-50 p-2 sm:p-3">
                                    <div class="mb-1 text-xs font-medium text-gray-500">Submitted</div>
                                    <div class="flex items-center gap-1 sm:gap-2">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="10"
                                            height="10"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="lucide lucide-clock text-sky-600"
                                        >
                                            <circle cx="12" cy="12" r="10" />
                                            <polyline points="12 6 12 12 16 14" />
                                        </svg>
                                        <span class="text-xs font-semibold text-gray-900 sm:text-sm">
                                            {{ $request->created_at->format('d M') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Date Range --}}
                            <div class="mb-2 sm:mb-3">
                                <div class="mb-1 text-xs font-medium text-gray-500">Date Range</div>
                                <div
                                    class="rounded-lg bg-gray-50 px-2 py-1 text-xs font-medium text-gray-900 sm:px-3 sm:py-2 sm:text-sm"
                                >
                                    {{ $request->date_range }}
                                </div>
                            </div>

                            {{-- Reason --}}
                            <div class="mb-3 sm:mb-4">
                                <div class="mb-1 text-xs font-medium text-gray-500">Reason</div>
                                <div
                                    class="rounded-lg bg-gray-50 px-2 py-1 text-xs text-gray-900 sm:px-3 sm:py-2 sm:text-sm"
                                >
                                    {{ $request->reason }}
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex flex-wrap gap-1 sm:gap-2">
                                <button
                                    type="button"
                                    onclick="viewLeaveRequest({{ $request->id }})"
                                    class="inline-flex min-h-[32px] flex-1 items-center justify-center rounded-lg bg-sky-100 px-2 py-1 text-xs font-semibold text-sky-700 transition-all duration-200 hover:bg-sky-200 sm:min-h-[36px] sm:px-3 sm:py-2"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="10"
                                        height="10"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="lucide lucide-eye mr-1"
                                    >
                                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                                        <circle cx="12" cy="12" r="3" />
                                    </svg>
                                    <span class="hidden sm:inline">View</span>
                                </button>
                                @if ($request->status === 'pending')
                                    <button
                                        type="button"
                                        onclick="processLeaveRequest({{ $request->id }}, 'approve')"
                                        class="inline-flex min-h-[32px] flex-1 items-center justify-center rounded-lg bg-green-100 px-2 py-1 text-xs font-semibold text-green-700 transition-all duration-200 hover:bg-green-200 sm:min-h-[36px] sm:px-3 sm:py-2"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="10"
                                            height="10"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="lucide lucide-check mr-1"
                                        >
                                            <path d="M20 6 9 17l-5-5" />
                                        </svg>
                                        <span class="hidden sm:inline">Approve</span>
                                    </button>
                                    <button
                                        type="button"
                                        onclick="processLeaveRequest({{ $request->id }}, 'reject')"
                                        class="inline-flex min-h-[32px] flex-1 items-center justify-center rounded-lg bg-red-100 px-2 py-1 text-xs font-semibold text-red-700 transition-all duration-200 hover:bg-red-200 sm:min-h-[36px] sm:px-3 sm:py-2"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="10"
                                            height="10"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="lucide lucide-x mr-1"
                                        >
                                            <path d="M18 6 6 18" />
                                            <path d="m6 6 12 12" />
                                        </svg>
                                        <span class="hidden sm:inline">Reject</span>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="32"
                                    height="32"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="lucide lucide-inbox mb-2 text-gray-400"
                                >
                                    <polyline points="22 12 16 12 14 15 10 15 8 12 2 12" />
                                    <path
                                        d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"
                                    />
                                </svg>
                                <p class="text-sm">No leave requests found</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Leave Detail Modal --}}
    <div id="leave-detail-modal" class="fixed inset-0 z-50 hidden bg-black/50 p-4" style="display: none">
        <div class="mx-auto my-8 max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-2xl bg-white shadow-2xl">
            <div class="p-6">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Leave Request Details</h3>
                    <button type="button" onclick="closeLeaveDetailModal()" class="text-gray-400 hover:text-gray-600">
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
                            class="lucide lucide-x"
                        >
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                    </button>
                </div>
                <div id="modal-content">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleMobileView() {
            const desktopView = document.getElementById('desktopView');
            const mobileView = document.getElementById('mobileView');
            const toggleText = document.getElementById('viewToggleText');

            if (desktopView.classList.contains('hidden')) {
                desktopView.classList.remove('hidden');
                mobileView.classList.add('hidden');
                toggleText.textContent = 'Table';
            } else {
                desktopView.classList.add('hidden');
                mobileView.classList.remove('hidden');
                toggleText.textContent = 'Cards';
            }
        }

        // Auto-show mobile view on small screens
        function checkScreenSize() {
            const mobileViewToggle = document.querySelector('button[onclick="toggleMobileView()"]');
            if (window.innerWidth < 768) {
                // Show mobile view by default on small screens
                const desktopView = document.getElementById('desktopView');
                const mobileView = document.getElementById('mobileView');
                const toggleText = document.getElementById('viewToggleText');

                if (desktopView && mobileView && !desktopView.classList.contains('hidden')) {
                    desktopView.classList.add('hidden');
                    mobileView.classList.remove('hidden');
                    toggleText.textContent = 'Cards';
                }
            } else {
                // Show desktop view on larger screens
                const desktopView = document.getElementById('desktopView');
                const mobileView = document.getElementById('mobileView');
                const toggleText = document.getElementById('viewToggleText');

                if (desktopView && mobileView && !mobileView.classList.contains('hidden')) {
                    mobileView.classList.add('hidden');
                    desktopView.classList.remove('hidden');
                    toggleText.textContent = 'Table';
                }
            }
        }

        // Check on load and resize
        window.addEventListener('load', checkScreenSize);
        window.addEventListener('resize', checkScreenSize);

        async function viewLeaveRequest(requestId) {
            const modal = document.getElementById('leave-detail-modal');
            const content = document.getElementById('modal-content');

            try {
                content.innerHTML = `
                <div class="text-center py-8">
                    <div class="inline-flex items-center space-x-2 text-gray-500">
                        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-sky-600"></div>
                        <span class="text-sm">Loading leave request details...</span>
                    </div>
                </div>
            `;
                modal.classList.remove('hidden');
                modal.style.display = 'flex';
                modal.style.alignItems = 'center';
                modal.style.justifyContent = 'center';

                const response = await fetch(`/admin/attendances/leave-requests/${requestId}`, {
                    headers: {
                        Accept: 'application/json',
                    },
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
                        <p class="text-sm">Failed to load leave request details</p>
                    </div>
                </div>
            `;
            }
        }

        function closeLeaveDetailModal() {
            const modal = document.getElementById('leave-detail-modal');
            modal.classList.add('hidden');
            modal.style.display = 'none';
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
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({
                        action: action,
                    }),
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

        document.getElementById('leave-detail-modal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeLeaveDetailModal();
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeLeaveDetailModal();
            }
        });
    </script>
@endsection
