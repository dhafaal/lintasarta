<div id="user-logs" class="tab-content">
    <div class="mb-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="user-check" class="w-4 h-4 text-indigo-600"></i>
                </div>
            </div>
            <div class="ml-3">
                <h3 class="text-lg font-medium text-gray-900">User Activities Logs</h3>
                <p class="text-sm text-gray-500">Activities performed by users (check-in, check-out, permissions)</p>
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
                                        {{ $log->action == 'checkin' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $log->action == 'checkout' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $log->action == 'request_permission' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $log->action == 'absent' ? 'bg-red-100 text-red-800' : '' }}
                                        {{ $log->action == 'delete_attendance' ? 'bg-red-100 text-red-800' : '' }}">
                                        {{ str_replace('_', ' ', ucfirst($log->action)) }}
                                    </span>
                                    <span class="text-sm font-medium text-gray-900">{{ $log->user->name ?? 'Unknown User' }}</span>
                                    @if($log->resource_type)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ ucfirst($log->resource_type) }}
                                        </span>
                                    @endif
                                </div>
                                
                                <p class="text-sm text-gray-600 mb-2">{{ $log->description }}</p>
                                
                                @if($log->resource_name)
                                    <p class="text-xs text-gray-500 mb-3 flex items-center">
                                        <i data-lucide="tag" class="w-3 h-3 mr-1"></i>
                                        {{ $log->resource_name }}
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
                                <button onclick="toggleDetails('user-{{ $log->id }}')" 
                                        class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
                                    <i data-lucide="eye" class="w-3 h-3 mr-1"></i>
                                    Details
                                </button>
                            </div>
                        </div>
                        
                        <!-- Details (Hidden by default) -->
                        <div id="user-{{ $log->id }}" class="hidden mt-4 pt-4 border-t border-gray-200">
                            @if($log->additional_data)
                                <div class="mb-4">
                                    <h5 class="text-sm font-medium text-gray-900 mb-2">Additional Data:</h5>
                                    <div class="bg-blue-50 border border-blue-200 rounded-md p-3">
                                        @if(is_array($log->additional_data))
                                            <div class="space-y-1">
                                                @foreach($log->additional_data as $key => $value)
                                                    <div class="flex justify-between text-xs">
                                                        <span class="font-medium text-gray-600">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                                        <span class="text-gray-800">
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
                                            </div>
                                        @else
                                            <pre class="text-xs text-blue-800 whitespace-pre-wrap">{{ json_encode($log->additional_data, JSON_PRETTY_PRINT) }}</pre>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            
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
            <h3 class="text-sm font-medium text-gray-900 mb-2">No User Activity Logs Found</h3>
            <p class="text-sm text-gray-500">No user activities have been recorded yet.</p>
        </div>
    @endif
</div>
