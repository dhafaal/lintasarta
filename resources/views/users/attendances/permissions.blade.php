@extends('layouts.user')

@section('title', 'Permissions History')

@section('content')
<div class="min-h-screen bg-white">
    <div class="px-3 sm:px-4 md:px-6 lg:px-8 py-6 sm:py-8">
        {{-- Header --}}
        <div class="mb-8 sm:mb-10">
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4 mb-3">
                <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i data-lucide="file-text" class="w-5 h-5 text-white"></i>
                </div>
                <div class="min-w-0">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1 break-words">Permissions History</h1>
                    <p class="text-sm text-gray-500">View all your permission and leave requests</p>
                </div>
            </div>
        </div>

        {{-- Stats Cards --}}
        @php
            $totalPermissions = $permissions->total();
            $pendingCount = \App\Models\Permissions::where('user_id', Auth::id())->where('status', 'pending')->count();
            $approvedCount = \App\Models\Permissions::where('user_id', Auth::id())->where('status', 'approved')->count();
            $rejectedCount = \App\Models\Permissions::where('user_id', Auth::id())->where('status', 'rejected')->count();
        @endphp

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-gray-500 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i data-lucide="list" class="w-4 h-4 text-white"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-600">Total</p>
                        <p class="text-lg font-bold text-gray-900">{{ $totalPermissions }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-amber-50 rounded-lg p-4 border border-amber-200">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-amber-500 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i data-lucide="clock" class="w-4 h-4 text-white"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-amber-700">Pending</p>
                        <p class="text-lg font-bold text-amber-900">{{ $pendingCount }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-emerald-50 rounded-lg p-4 border border-emerald-200">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i data-lucide="check-circle" class="w-4 h-4 text-white"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-emerald-700">Approved</p>
                        <p class="text-lg font-bold text-emerald-900">{{ $approvedCount }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-rose-50 rounded-lg p-4 border border-rose-200">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-rose-500 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i data-lucide="x-circle" class="w-4 h-4 text-white"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-rose-700">Rejected</p>
                        <p class="text-lg font-bold text-rose-900">{{ $rejectedCount }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Permissions List --}}
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            @if($permissions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Type</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Shift</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Reason</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Requested</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($permissions as $permission)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <i data-lucide="calendar" class="w-4 h-4 text-gray-400"></i>
                                            <span class="text-sm font-medium text-gray-900">
                                                {{ \Carbon\Carbon::parse($permission->schedule->schedule_date)->format('d M Y') }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        @php
                                            $typeColors = [
                                                'izin' => 'bg-blue-100 text-blue-700 border-blue-200',
                                                'sakit' => 'bg-orange-100 text-orange-700 border-orange-200',
                                                'cuti' => 'bg-purple-100 text-purple-700 border-purple-200',
                                            ];
                                            $typeIcons = [
                                                'izin' => 'file-text',
                                                'sakit' => 'heart-pulse',
                                                'cuti' => 'calendar-x',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium border {{ $typeColors[$permission->type] ?? 'bg-gray-100 text-gray-700 border-gray-200' }}">
                                            <i data-lucide="{{ $typeIcons[$permission->type] ?? 'file' }}" class="w-3 h-3"></i>
                                            {{ ucfirst($permission->type) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="text-sm text-gray-900">{{ $permission->schedule->shift->shift_name ?? '-' }}</div>
                                        <div class="text-xs text-gray-500">
                                            {{ $permission->schedule->shift->start_time ?? '' }} - {{ $permission->schedule->shift->end_time ?? '' }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="text-sm text-gray-700 max-w-xs truncate" title="{{ $permission->reason }}">
                                            {{ $permission->reason }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-amber-100 text-amber-700 border-amber-200',
                                                'approved' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                                'rejected' => 'bg-rose-100 text-rose-700 border-rose-200',
                                            ];
                                            $statusIcons = [
                                                'pending' => 'clock',
                                                'approved' => 'check-circle',
                                                'rejected' => 'x-circle',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium border {{ $statusColors[$permission->status] ?? 'bg-gray-100 text-gray-700 border-gray-200' }}">
                                            <i data-lucide="{{ $statusIcons[$permission->status] ?? 'circle' }}" class="w-3 h-3"></i>
                                            {{ ucfirst($permission->status) }}
                                        </span>
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

                {{-- Pagination --}}
                @if($permissions->hasPages())
                    <div class="px-4 py-4 border-t border-gray-200">
                        {{ $permissions->links() }}
                    </div>
                @endif
            @else
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="inbox" class="w-8 h-8 text-gray-400"></i>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900 mb-1">No Permissions Yet</h3>
                    <p class="text-sm text-gray-500 mb-4">You haven't submitted any permission or leave requests</p>
                    <a href="{{ route('user.attendances.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-purple-500 hover:bg-purple-600 text-white font-medium rounded-lg transition-colors text-sm">
                        <i data-lucide="plus" class="w-4 h-4"></i>
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
