<div id="schedules-logs" class="tab-content">
    <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-2">
            <i data-lucide="calendar" class="w-5 h-5 inline mr-2 text-emerald-600"></i>
            Schedules Management Logs
        </h3>
        <p class="text-sm text-gray-600">Activities related to schedule creation, updates, and deletions</p>
    </div>

    @if($logs && $logs->count() > 0)
        <div class="space-y-4">
            @foreach($logs as $log)
                <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-emerald-500">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $log->action == 'create' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $log->action == 'update' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $log->action == 'delete' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($log->action) }}
                                </span>
                                <span class="text-sm font-medium text-gray-900">{{ $log->target_user_name ?? 'Unknown User' }}</span>
                                @if($log->shift_name)
                                    <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">{{ $log->shift_name }}</span>
                                @endif
                            </div>

                            <p class="text-sm text-gray-700 mb-2">{{ $log->description }}</p>

                            @if($log->schedule_date)
                                <p class="text-xs text-gray-600 mb-2">
                                    <i data-lucide="calendar-days" class="w-3 h-3 inline mr-1"></i>
                                    {{ \Carbon\Carbon::parse($log->schedule_date)->format('d M Y') }}
                                </p>
                            @endif

                            <div class="flex items-center space-x-4 text-xs text-gray-500">
                                <span>
                                    <i data-lucide="user" class="w-3 h-3 inline mr-1"></i>
                                    {{ $log->user->name ?? 'System' }}
                                </span>
                                <span>
                                    <i data-lucide="clock" class="w-3 h-3 inline mr-1"></i>
                                    {{ $log->created_at->format('d M Y, H:i') }}
                                </span>
                                <span>
                                    <i data-lucide="globe" class="w-3 h-3 inline mr-1"></i>
                                    {{ $log->ip_address ?? 'Unknown IP' }}
                                </span>
                            </div>
                        </div>

                        <div class="ml-4">
                            <button onclick="toggleDetails('schedules-{{ $log->id }}')"
                                    class="text-emerald-600 hover:text-emerald-800 text-sm font-medium">
                                <i data-lucide="eye" class="w-4 h-4 inline mr-1"></i>
                                Details
                            </button>
                        </div>
                    </div>

                    <!-- Details (Hidden by default) -->
                    <div id="schedules-{{ $log->id }}" class="hidden mt-4 pt-4 border-t border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if($log->old_values)
                                <div>
                                    <h5 class="text-sm font-medium text-gray-700 mb-2">Old Values:</h5>
                                    <pre class="text-xs bg-red-50 p-2 rounded border overflow-x-auto">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            @endif

                            @if($log->new_values)
                                <div>
                                    <h5 class="text-sm font-medium text-gray-700 mb-2">New Values:</h5>
                                    <pre class="text-xs bg-green-50 p-2 rounded border overflow-x-auto">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            @endif
                        </div>

                        @if($log->user_agent)
                            <div class="mt-3">
                                <h5 class="text-sm font-medium text-gray-700 mb-1">User Agent:</h5>
                                <p class="text-xs text-gray-600 bg-gray-100 p-2 rounded">{{ $log->user_agent }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $logs->appends(request()->query())->fragment('schedules-logs')->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <i data-lucide="inbox" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Schedules Logs Found</h3>
            <p class="text-gray-500">No schedule management activities have been recorded yet.</p>
        </div>
    @endif
</div>
