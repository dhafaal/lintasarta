<div id="user-logs" class="tab-content">
    <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-2">
            <i data-lucide="user-check" class="w-5 h-5 inline mr-2 text-indigo-600"></i>
            User Activities Logs
        </h3>
        <p class="text-sm text-gray-600">Activities performed by users (check-in, check-out, permissions)</p>
    </div>

    @if($logs && $logs->count() > 0)
        <div class="space-y-4">
            @foreach($logs as $log)
                <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-indigo-500">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $log->action == 'checkin' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $log->action == 'checkout' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $log->action == 'request_permission' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $log->action == 'absent' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $log->action == 'delete_attendance' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ str_replace('_', ' ', ucfirst($log->action)) }}
                                </span>
                                <span class="text-sm font-medium text-gray-900">{{ $log->user->name ?? 'Unknown User' }}</span>
                                @if($log->resource_type)
                                    <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full">{{ ucfirst($log->resource_type) }}</span>
                                @endif
                            </div>

                            <p class="text-sm text-gray-700 mb-2">{{ $log->description }}</p>

                            @if($log->resource_name)
                                <p class="text-xs text-gray-600 mb-2">
                                    <i data-lucide="tag" class="w-3 h-3 inline mr-1"></i>
                                    {{ $log->resource_name }}
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
                            <button onclick="toggleDetails('user-{{ $log->id }}')"
                                    class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                <i data-lucide="eye" class="w-4 h-4 inline mr-1"></i>
                                Details
                            </button>
                        </div>
                    </div>

                    <!-- Details (Hidden by default) -->
                    <div id="user-{{ $log->id }}" class="hidden mt-4 pt-4 border-t border-gray-200">
                        @if($log->additional_data)
                            <div class="mb-4">
                                <h5 class="text-sm font-medium text-gray-700 mb-2">Additional Data:</h5>
                                <div class="bg-blue-50 p-3 rounded border">
                                    @if(is_array($log->additional_data))
                                        @foreach($log->additional_data as $key => $value)
                                            <div class="flex justify-between py-1">
                                                <span class="text-xs font-medium text-gray-600">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                                <span class="text-xs text-gray-800">
                                                    @if(is_bool($value))
                                                        {{ $value ? 'Yes' : 'No' }}
                                                    @elseif(is_array($value))
                                                        {{ json_encode($value) }}
                                                    @else
                                                        {{ $value }}
                                                    @endif
                                                </span>
                                            </div>
                                        @endforeach
                                    @else
                                        <pre class="text-xs overflow-x-auto">{{ json_encode($log->additional_data, JSON_PRETTY_PRINT) }}</pre>
                                    @endif
                                </div>
                            </div>
                        @endif

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
            {{ $logs->appends(request()->query())->fragment('user-logs')->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <i data-lucide="inbox" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No User Activity Logs Found</h3>
            <p class="text-gray-500">No user activities have been recorded yet.</p>
        </div>
    @endif
</div>
