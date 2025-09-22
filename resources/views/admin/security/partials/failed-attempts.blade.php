<div id="failed-attempts" class="tab-content">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="x-circle" class="w-4 h-4 text-yellow-600"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-medium text-gray-900">Recent Failed Login Attempts</h3>
                    <p class="text-sm text-gray-500">Monitor failed login attempts in the last 24 hours</p>
                </div>
            </div>
        </div>
    </div>

    @if($logs && $logs->count() > 0)
        <div class="space-y-4">
            @foreach($logs as $attempt)
                <div class="bg-white border border-yellow-200 rounded-lg shadow-sm">
                    <div class="p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <span class="text-sm font-medium text-gray-900">{{ $attempt->email }}</span>
                                    @if($attempt->user)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ ucfirst($attempt->user->role) }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Unknown User
                                        </span>
                                    @endif
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Failed Login
                                    </span>
                                    @if($attempt->failure_reason)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $attempt->failure_reason }}
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600 mb-3">
                                    <div class="flex items-center">
                                        <i data-lucide="globe" class="w-4 h-4 mr-2 text-gray-400"></i>
                                        <span class="font-mono">{{ $attempt->ip_address }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i data-lucide="smartphone" class="w-4 h-4 mr-2 text-gray-400"></i>
                                        <span class="truncate">{{ $attempt->user_agent }}</span>
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-4 text-xs text-gray-500">
                                    <span class="flex items-center">
                                        <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                        {{ $attempt->attempted_at->format('d M Y, H:i:s') }}
                                    </span>
                                    <span class="flex items-center">
                                        <i data-lucide="activity" class="w-3 h-3 mr-1"></i>
                                        {{ $attempt->attempted_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="ml-4 flex space-x-2">
                                <button onclick="showBlockIPModal('{{ $attempt->ip_address }}')"
                                        class="inline-flex items-center px-3 py-1 border border-red-300 shadow-sm text-xs font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100">
                                    <i data-lucide="shield-x" class="w-3 h-3 mr-1"></i>
                                    Block IP
                                </button>
                                
                                <form action="{{ route('admin.security.clear-failed-attempts') }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="email" value="{{ $attempt->email }}">
                                    <button type="submit" 
                                            onclick="return confirm('Clear all failed attempts for this email?')"
                                            class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        <i data-lucide="trash-2" class="w-3 h-3 mr-1"></i>
                                        Clear
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Show more button if there are more attempts -->
        @if($logs->count() >= 20)
            <div class="mt-6 text-center">
                <button onclick="loadMoreFailedAttempts()" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <i data-lucide="chevron-down" class="w-4 h-4 mr-2"></i>
                    Load More
                </button>
            </div>
        @endif
    @else
        <div class="text-center py-12">
            <div class="w-12 h-12 mx-auto bg-gray-100 rounded-lg flex items-center justify-center mb-4">
                <i data-lucide="check-circle" class="w-6 h-6 text-gray-400"></i>
            </div>
            <h3 class="text-sm font-medium text-gray-900 mb-2">No Failed Attempts</h3>
            <p class="text-sm text-gray-500">No failed login attempts in the last 24 hours.</p>
        </div>
    @endif
</div>
