<div id="schedules-logs" class="tab-content">
    <div class="mb-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="calendar" class="w-4 h-4 text-emerald-600"></i>
                </div>
            </div>
            <div class="ml-3">
                <h3 class="text-lg font-medium text-gray-900">Schedules Management Logs</h3>
                <p class="text-sm text-gray-500">Activities related to schedule creation, updates, and deletions</p>
            </div>
        </div>
    </div>

    @if($logs && $logs->count() > 0)
        <div class="space-y-4">
            @foreach($logs as $log)
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                    <div class="p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $log->action == 'create' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $log->action == 'update' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $log->action == 'delete' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ ucfirst($log->action) }}
                                    </span>
                                    <span class="text-sm font-medium text-gray-900">{{ $log->target_user_name ?? 'Unknown User' }}</span>
                                    @if($log->shift_name)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $log->shift_name }}
                                        </span>
                                    @endif
                                </div>
                                
                                <p class="text-sm text-gray-600 mb-2">{{ $log->description }}</p>
                                
                                @if($log->schedule_date)
                                    <p class="text-xs text-gray-500 mb-3 flex items-center">
                                        <i data-lucide="calendar-days" class="w-3 h-3 mr-1"></i>
                                        {{ \Carbon\Carbon::parse($log->schedule_date)->format('d M Y') }}
                                    </p>
                                @endif
                                
                                <div class="flex items-center space-x-4 text-xs text-gray-500">
                                    <span class="flex items-center">
                                        <i data-lucide="user" class="w-3 h-3 mr-1"></i>
                                        {{ $log->user->name ?? 'System' }}
                                    </span>
                                    <span class="flex items-center">
                                        <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                        {{ $log->created_at->format('d M Y, H:i') }}
                                    </span>
                                    <span class="flex items-center">
                                        <i data-lucide="globe" class="w-3 h-3 mr-1"></i>
                                        {{ $log->ip_address ?? 'Unknown IP' }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="ml-4">
                                <button onclick="toggleDetails('schedules-{{ $log->id }}')" 
                                        class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
                                    <i data-lucide="eye" class="w-3 h-3 mr-1"></i>
                                    Details
                                </button>
                            </div>
                        </div>
                        
                        <!-- Details (Hidden by default) -->
                        <div id="schedules-{{ $log->id }}" class="hidden mt-4 pt-4 border-t border-gray-200">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                @if($log->old_values)
                                    <div>
                                        <h5 class="text-sm font-medium text-gray-900 mb-2">Old Values:</h5>
                                        <div class="bg-red-50 border border-red-200 rounded-md p-3">
                                            <pre class="text-xs text-red-800 whitespace-pre-wrap">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                                        </div>
                                    </div>
                                @endif
                                
                                @if($log->new_values)
                                    <div>
                                        <h5 class="text-sm font-medium text-gray-900 mb-2">New Values:</h5>
                                        <div class="bg-green-50 border border-green-200 rounded-md p-3">
                                            <pre class="text-xs text-green-800 whitespace-pre-wrap">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            
                            @if($log->user_agent)
                                <div class="mt-4">
                                    <h5 class="text-sm font-medium text-gray-900 mb-2">User Agent:</h5>
                                    <div class="bg-gray-50 border border-gray-200 rounded-md p-3">
                                        <p class="text-xs text-gray-600">{{ $log->user_agent }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        @if($logs->hasPages())
            <div class="mt-6">
                {{ $logs->appends(request()->query())->links() }}
            </div>
        @endif
    @else
        <div class="text-center py-12">
            <div class="w-12 h-12 mx-auto bg-gray-100 rounded-lg flex items-center justify-center mb-4">
                <i data-lucide="inbox" class="w-6 h-6 text-gray-400"></i>
            </div>
            <h3 class="text-sm font-medium text-gray-900 mb-2">No Schedules Logs Found</h3>
            <p class="text-sm text-gray-500">No schedule management activities have been recorded yet.</p>
        </div>
    @endif
</div>
