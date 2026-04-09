@extends('layouts.user')

@section('title', 'Permissions History')

@section('content')
    <div class="min-h-screen bg-white">
        <div class="px-3 py-6 sm:px-4 sm:py-8 md:px-6 lg:px-8">
            {{-- Header --}}
            <div class="mb-8 sm:mb-10">
                <div class="mb-3 flex flex-col gap-3 sm:flex-row sm:items-center sm:gap-4">
                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-purple-500">
                        <i data-lucide="file-text" class="h-5 w-5 text-white"></i>
                    </div>
                    <div class="min-w-0">
                        <h1 class="mb-1 text-2xl font-bold break-words text-gray-900 sm:text-3xl">
                            Permissions History
                        </h1>
                        <p class="text-sm text-gray-500">View all your permission and leave requests</p>
                    </div>
                </div>
            </div>

            {{-- Stats Cards --}}
            @php
                $totalPermissions = $permissions->total();
                $pendingCount = \App\Models\Permissions::where('user_id', Auth::id())
                    ->where('status', 'pending')
                    ->count();
                $approvedCount = \App\Models\Permissions::where('user_id', Auth::id())
                    ->where('status', 'approved')
                    ->count();
                $rejectedCount = \App\Models\Permissions::where('user_id', Auth::id())
                    ->where('status', 'rejected')
                    ->count();
            @endphp

            <div class="mb-6 grid grid-cols-2 gap-3 lg:grid-cols-4">
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-gray-500">
                            <i data-lucide="list" class="h-4 w-4 text-white"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-600">Total</p>
                            <p class="text-lg font-bold text-gray-900">{{ $totalPermissions }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border border-amber-200 bg-amber-50 p-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-amber-500">
                            <i data-lucide="clock" class="h-4 w-4 text-white"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-amber-700">Pending</p>
                            <p class="text-lg font-bold text-amber-900">{{ $pendingCount }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-emerald-500">
                            <i data-lucide="check-circle" class="h-4 w-4 text-white"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-emerald-700">Approved</p>
                            <p class="text-lg font-bold text-emerald-900">{{ $approvedCount }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border border-rose-200 bg-rose-50 p-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-rose-500">
                            <i data-lucide="x-circle" class="h-4 w-4 text-white"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-rose-700">Rejected</p>
                            <p class="text-lg font-bold text-rose-900">{{ $rejectedCount }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Permissions List --}}
            <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
                @if ($permissions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold tracking-wider text-gray-700 uppercase"
                                    >
                                        Date
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold tracking-wider text-gray-700 uppercase"
                                    >
                                        Type
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold tracking-wider text-gray-700 uppercase"
                                    >
                                        Shift
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold tracking-wider text-gray-700 uppercase"
                                    >
                                        Reason
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold tracking-wider text-gray-700 uppercase"
                                    >
                                        Status
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold tracking-wider text-gray-700 uppercase"
                                    >
                                        Catatan Admin
                                    </th>
                                    <th
                                        class="px-4 py-3 text-left text-xs font-semibold tracking-wider text-gray-700 uppercase"
                                    >
                                        Requested
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach ($permissions as $permission)
                                    <tr class="transition-colors hover:bg-gray-50">
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <i data-lucide="calendar" class="h-4 w-4 text-gray-400"></i>
                                                <span class="text-sm font-medium text-gray-900">
                                                    {{ \Carbon\Carbon::parse($permission->schedule->schedule_date)->format('d M Y') }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            @php
                                                $typeColors = [
                                                    'izin' => 'border-blue-200 bg-blue-100 text-blue-700',
                                                    'sakit' => 'border-orange-200 bg-orange-100 text-orange-700',
                                                    'cuti' => 'border-purple-200 bg-purple-100 text-purple-700',
                                                ];
                                                $typeIcons = [
                                                    'izin' => 'file-text',
                                                    'sakit' => 'heart-pulse',
                                                    'cuti' => 'calendar-x',
                                                ];
                                            @endphp

                                            <span
                                                class="{{ $typeColors[$permission->type] ?? 'bg-gray-100 text-gray-700 border-gray-200' }} inline-flex items-center gap-1.5 rounded-lg border px-2.5 py-1 text-xs font-medium"
                                            >
                                                <i
                                                    data-lucide="{{ $typeIcons[$permission->type] ?? 'file' }}"
                                                    class="h-3 w-3"
                                                ></i>
                                                {{ ucfirst($permission->type) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="text-sm text-gray-900">
                                                {{ $permission->schedule->shift->shift_name ?? '-' }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $permission->schedule->shift->start_time ?? '' }} -
                                                {{ $permission->schedule->shift->end_time ?? '' }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div
                                                class="max-w-xs truncate text-sm text-gray-700"
                                                title="{{ $permission->reason }}"
                                            >
                                                {{ $permission->reason }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            @php
                                                $statusColors = [
                                                    'pending' => 'border-amber-200 bg-amber-100 text-amber-700',
                                                    'approved' => 'border-emerald-200 bg-emerald-100 text-emerald-700',
                                                    'rejected' => 'border-rose-200 bg-rose-100 text-rose-700',
                                                ];
                                                $statusIcons = [
                                                    'pending' => 'clock',
                                                    'approved' => 'check-circle',
                                                    'rejected' => 'x-circle',
                                                ];
                                            @endphp

                                            <span
                                                class="{{ $statusColors[$permission->status] ?? 'bg-gray-100 text-gray-700 border-gray-200' }} inline-flex items-center gap-1.5 rounded-lg border px-2.5 py-1 text-xs font-medium"
                                            >
                                                <i
                                                    data-lucide="{{ $statusIcons[$permission->status] ?? 'circle' }}"
                                                    class="h-3 w-3"
                                                ></i>
                                                {{ ucfirst($permission->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="max-w-[150px] truncate text-xs text-gray-600 whitespace-normal" title="{{ $permission->admin_note }}">
                                                {{ $permission->admin_note ?? '-' }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="text-xs text-gray-500">
                                                {{ $permission->created_at->format('d M Y') }}
                                            </div>
                                            <div class="text-xs text-gray-400">
                                                {{ $permission->created_at->format('H:i') }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination Footer --}}
                    <div class="flex flex-col border-t border-gray-200 bg-white px-5 py-4 md:flex-row md:items-center md:justify-between">
                        <div class="flex items-center justify-center space-x-3 text-sm font-medium text-gray-600 md:justify-start">
                            <span>Tampilkan</span>
                            <select onchange="window.location.href = this.value" class="cursor-pointer rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 focus:border-purple-500 focus:ring-2 focus:ring-purple-500 focus:outline-none">
                                <option value="{{ request()->fullUrlWithQuery(['per_page' => 5]) }}" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                                <option value="{{ request()->fullUrlWithQuery(['per_page' => 10]) }}" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                <option value="{{ request()->fullUrlWithQuery(['per_page' => 25]) }}" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                <option value="{{ request()->fullUrlWithQuery(['per_page' => 50]) }}" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                <option value="{{ request()->fullUrlWithQuery(['per_page' => 100]) }}" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                            </select>
                            <span>data</span>
                        </div>
                        <div class="mt-4 flex flex-col items-center space-y-4 sm:flex-row sm:space-y-0 sm:space-x-6 md:mt-0">
                            <div class="text-sm font-medium text-gray-500">
                                Menampilkan {{ $permissions->count() > 0 ? $permissions->firstItem() : 0 }} - {{ $permissions->count() > 0 ? $permissions->lastItem() : 0 }} dari {{ $permissions->total() }} data
                            </div>
                            @if ($permissions->hasPages())
                                <div class="flex items-center space-x-2">
                                    {{ $permissions->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="p-12 text-center">
                        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100">
                            <i data-lucide="inbox" class="h-8 w-8 text-gray-400"></i>
                        </div>
                        <h3 class="mb-1 text-base font-semibold text-gray-900">No Permissions Yet</h3>
                        <p class="mb-4 text-sm text-gray-500">You haven't submitted any permission or leave requests</p>
                        <a
                            href="{{ route('user.attendances.index') }}"
                            class="inline-flex items-center gap-2 rounded-lg bg-purple-500 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-purple-600"
                        >
                            <i data-lucide="plus" class="h-4 w-4"></i>
                            <span>Request Permission</span>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Initialize Lucide icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    </script>
@endsection
