<div id="auth-logs" class="tab-content">
    <div class="mb-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="lock" class="w-4 h-4 text-red-600"></i>
                </div>
            </div>
            <div class="ml-3">
                <h3 class="text-lg font-medium text-gray-900">Authentication Logs</h3>
                <p class="text-sm text-gray-500">Login, logout, and authentication-related activities</p>
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
                                        {{ $log->action == 'login' && $log->status == 'success' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $log->action == 'logout' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $log->action == 'failed_login' ? 'bg-red-100 text-red-800' : '' }}
                                        {{ $log->action == 'password_reset' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                        {{ str_replace('_', ' ', ucfirst($log->action)) }}
                                    </span>
                                    
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $log->status == 'success' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $log->status == 'failed' ? 'bg-red-100 text-red-800' : '' }}
                                        {{ $log->status == 'blocked' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                        {{ ucfirst($log->status) }}
                                    </span>
                                    
                                    @if($log->user)
                                        <span class="text-sm font-medium text-gray-900">{{ $log->user->name }}</span>
                                    @elseif($log->email)
                                        <span class="text-sm font-medium text-gray-900">{{ $log->email }}</span>
                                    @endif
                                </div>
                                
                                <p class="text-sm text-gray-600 mb-2">{{ $log->description }}</p>
                                
                                @if($log->email && !$log->user)
                                    <p class="text-xs text-gray-500 mb-3 flex items-center">
                                        <i data-lucide="mail" class="w-3 h-3 mr-1"></i>
                                        {{ $log->email }}
                                    </p>
                                @endif
                                
                                <div class="flex items-center space-x-4 text-xs text-gray-500">
                                    @if($log->attempted_at)
                                        <span class="flex items-center">
                                            <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                            {{ $log->attempted_at->format('d M Y, H:i') }}
                                        </span>
                                    @else
                                        <span class="flex items-center">
                                            <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                            {{ $log->created_at->format('d M Y, H:i') }}
                                        </span>
                                    @endif
                                    <span class="flex items-center">
                                        <i data-lucide="globe" class="w-3 h-3 mr-1"></i>
                                        {{ $log->ip_address ?? 'Unknown IP' }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="ml-4">
                                <button onclick="toggleDetails('auth-{{ $log->id }}')" 
                                        class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500">
                                    <i data-lucide="eye" class="w-3 h-3 mr-1"></i>
                                    Details
                                </button>
                            </div>
                        </div>
                        
                        <!-- Details (Hidden by default) -->
                        <div id="auth-{{ $log->id }}" class="hidden mt-4 pt-4 border-t border-gray-200">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <div>
                                    <h5 class="text-sm font-medium text-gray-900 mb-2">Authentication Details:</h5>
                                    <div class="bg-gray-50 border border-gray-200 rounded-md p-3 space-y-1 text-xs">
                                        <div><strong>Action:</strong> {{ $log->action }}</div>
                                        <div><strong>Status:</strong> {{ $log->status }}</div>
                                        @if($log->email)
                                            <div><strong>Email:</strong> {{ $log->email }}</div>
                                        @endif
                                        @if($log->user)
                                            <div><strong>User:</strong> {{ $log->user->name }} ({{ $log->user->role }})</div>
                                        @endif
                                    </div>
                                </div>
                                
                                <div>
                                    <h5 class="text-sm font-medium text-gray-900 mb-2">Session Info:</h5>
                                    <div class="bg-blue-50 border border-blue-200 rounded-md p-3 space-y-1 text-xs">
                                        <div><strong>IP Address:</strong> {{ $log->ip_address ?? 'Unknown' }}</div>
                                        @if($log->attempted_at)
                                            <div><strong>Attempted At:</strong> {{ $log->attempted_at->format('d M Y, H:i:s') }}</div>
                                        @endif
                                        <div><strong>Recorded At:</strong> {{ $log->created_at->format('d M Y, H:i:s') }}</div>
                                    </div>
                                </div>
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
            <h3 class="text-sm font-medium text-gray-900 mb-2">No Authentication Logs Found</h3>
            <p class="text-sm text-gray-500">No authentication activities have been recorded yet.</p>
        </div>
    @endif
</div>
