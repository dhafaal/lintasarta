<div id="auth-logs" class="tab-content">
    <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-2">
            <i data-lucide="lock" class="w-5 h-5 inline mr-2 text-red-600"></i>
            Authentication Logs
        </h3>
        <p class="text-sm text-gray-600">Login, logout, and authentication-related activities</p>
    </div>

    @if($logs && $logs->count() > 0)
        <div class="space-y-4">
            @foreach($logs as $log)
                <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-red-500">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $log->action == 'login' && $log->status == 'success' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $log->action == 'logout' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $log->action == 'failed_login' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $log->action == 'password_reset' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                    {{ str_replace('_', ' ', ucfirst($log->action)) }}
                                </span>

                                <span class="px-2 py-1 text-xs font-medium rounded-full
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

                            <p class="text-sm text-gray-700 mb-2">{{ $log->description }}</p>

                            @if($log->email && !$log->user)
                                <p class="text-xs text-gray-600 mb-2">
                                    <i data-lucide="mail" class="w-3 h-3 inline mr-1"></i>
                                    {{ $log->email }}
                                </p>
                            @endif

                            <div class="flex items-center space-x-4 text-xs text-gray-500">
                                @if($log->attempted_at)
                                    <span>
                                        <i data-lucide="clock" class="w-3 h-3 inline mr-1"></i>
                                        {{ $log->attempted_at->format('d M Y, H:i') }}
                                    </span>
                                @else
                                    <span>
                                        <i data-lucide="clock" class="w-3 h-3 inline mr-1"></i>
                                        {{ $log->created_at->format('d M Y, H:i') }}
                                    </span>
                                @endif
                                <span>
                                    <i data-lucide="globe" class="w-3 h-3 inline mr-1"></i>
                                    {{ $log->ip_address ?? 'Unknown IP' }}
                                </span>
                            </div>
                        </div>

                        <div class="ml-4">
                            <button onclick="toggleDetails('auth-{{ $log->id }}')"
                                    class="text-red-600 hover:text-red-800 text-sm font-medium">
                                <i data-lucide="eye" class="w-4 h-4 inline mr-1"></i>
                                Details
                            </button>
                        </div>
                    </div>

                    <!-- Details (Hidden by default) -->
                    <div id="auth-{{ $log->id }}" class="hidden mt-4 pt-4 border-t border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h5 class="text-sm font-medium text-gray-700 mb-2">Authentication Details:</h5>
                                <div class="text-xs bg-gray-50 p-2 rounded border space-y-1">
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
                                <h5 class="text-sm font-medium text-gray-700 mb-2">Session Info:</h5>
                                <div class="text-xs bg-blue-50 p-2 rounded border space-y-1">
                                    <div><strong>IP Address:</strong> {{ $log->ip_address ?? 'Unknown' }}</div>
                                    @if($log->attempted_at)
                                        <div><strong>Attempted At:</strong> {{ $log->attempted_at->format('d M Y, H:i:s') }}</div>
                                    @endif
                                    <div><strong>Recorded At:</strong> {{ $log->created_at->format('d M Y, H:i:s') }}</div>
                                </div>
                            </div>
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
            {{ $logs->appends(request()->query())->fragment('auth-logs')->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <i data-lucide="inbox" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Authentication Logs Found</h3>
            <p class="text-gray-500">No authentication activities have been recorded yet.</p>
        </div>
    @endif
</div>
