<div id="active-sessions" class="tab-content">
    <div class="mb-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="monitor" class="w-4 h-4 text-blue-600"></i>
                </div>
            </div>
            <div class="ml-3">
                <h3 class="text-lg font-medium text-gray-900">Active User Sessions</h3>
                <p class="text-sm text-gray-500">Monitor and manage currently active user sessions</p>
            </div>
        </div>
    </div>

    @if($logs && $logs->count() > 0)
        <div class="space-y-4">
            @foreach($logs as $session)
                <div class="bg-white border border-blue-200 rounded-lg shadow-sm">
                    <div class="p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <span class="text-sm font-medium text-gray-900">{{ $session->user->name }}</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ ucfirst($session->user->role) }}
                                    </span>
                                    @if($session->is_trusted_device)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i data-lucide="shield-check" class="w-3 h-3 mr-1"></i>
                                            Trusted Device
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i data-lucide="alert-triangle" class="w-3 h-3 mr-1"></i>
                                            New Device
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600 mb-3">
                                    <div class="flex items-center">
                                        <i data-lucide="globe" class="w-4 h-4 mr-2 text-gray-400"></i>
                                        <span class="font-mono">{{ $session->ip_address }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i data-lucide="smartphone" class="w-4 h-4 mr-2 text-gray-400"></i>
                                        <span class="truncate">{{ $session->user_agent }}</span>
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-4 text-xs text-gray-500">
                                    <span class="flex items-center">
                                        <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                        Last Activity: {{ $session->last_activity->format('d M Y, H:i') }}
                                    </span>
                                    <span class="flex items-center">
                                        <i data-lucide="calendar" class="w-3 h-3 mr-1"></i>
                                        Started: {{ $session->created_at->format('d M Y, H:i') }}
                                    </span>
                                    <span class="flex items-center">
                                        <i data-lucide="timer" class="w-3 h-3 mr-1"></i>
                                        Expires: {{ $session->expires_at->format('d M Y, H:i') }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="ml-4 flex space-x-2">
                                @if(!$session->is_trusted_device)
                                    <button onclick="markAsTrusted('{{ $session->id }}')"
                                            class="inline-flex items-center px-3 py-1 border border-green-300 shadow-sm text-xs font-medium rounded-md text-green-700 bg-green-50 hover:bg-green-100">
                                        <i data-lucide="shield-check" class="w-3 h-3 mr-1"></i>
                                        Trust Device
                                    </button>
                                @endif
                                
                                <form action="{{ route('admin.security.terminate-session') }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="session_id" value="{{ $session->session_id }}">
                                    <button type="submit" 
                                            onclick="return confirm('Are you sure you want to terminate this session?')"
                                            class="inline-flex items-center px-3 py-1 border border-red-300 shadow-sm text-xs font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100">
                                        <i data-lucide="x-circle" class="w-3 h-3 mr-1"></i>
                                        Terminate
                                    </button>
                                </form>
                                
                                <form action="{{ route('admin.security.terminate-all-sessions') }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $session->user_id }}">
                                    <button type="submit" 
                                            onclick="return confirm('Are you sure you want to terminate ALL sessions for this user?')"
                                            class="inline-flex items-center px-3 py-1 border border-red-300 shadow-sm text-xs font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100">
                                        <i data-lucide="power" class="w-3 h-3 mr-1"></i>
                                        Terminate All
                                    </button>
                                </form>
                            </div>
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
                <i data-lucide="monitor-off" class="w-6 h-6 text-gray-400"></i>
            </div>
            <h3 class="text-sm font-medium text-gray-900 mb-2">No Active Sessions</h3>
            <p class="text-sm text-gray-500">No users are currently logged in.</p>
        </div>
    @endif
</div>
