<div id="blocked-ips" class="tab-content">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="shield-x" class="w-4 h-4 text-red-600"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-medium text-gray-900">Blocked IP Addresses</h3>
                    <p class="text-sm text-gray-500">Manage blocked IP addresses and their restrictions</p>
                </div>
            </div>
            <button onclick="showBlockIPModal('')" 
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                Block New IP
            </button>
        </div>
    </div>

    @if($logs && $logs->count() > 0)
        <div class="space-y-4">
            @foreach($logs as $blockedIP)
                <div class="bg-white border border-red-200 rounded-lg shadow-sm">
                    <div class="p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <span class="font-mono text-sm font-medium text-gray-900">{{ $blockedIP->ip_address }}</span>
                                    @if($blockedIP->is_permanent)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Permanent
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Temporary
                                        </span>
                                    @endif
                                    @if($blockedIP->failed_attempts > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ $blockedIP->failed_attempts }} attempts
                                        </span>
                                    @endif
                                </div>
                                
                                <p class="text-sm text-gray-600 mb-2">{{ $blockedIP->reason }}</p>
                                
                                <div class="flex items-center space-x-4 text-xs text-gray-500">
                                    <span class="flex items-center">
                                        <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                        Blocked: {{ $blockedIP->blocked_at->format('d M Y, H:i') }}
                                    </span>
                                    @if($blockedIP->blocked_until)
                                        <span class="flex items-center">
                                            <i data-lucide="calendar" class="w-3 h-3 mr-1"></i>
                                            Until: {{ $blockedIP->blocked_until->format('d M Y, H:i') }}
                                        </span>
                                        @if($blockedIP->getTimeRemaining())
                                            <span class="flex items-center text-yellow-600">
                                                <i data-lucide="timer" class="w-3 h-3 mr-1"></i>
                                                {{ $blockedIP->getTimeRemaining() }} min remaining
                                            </span>
                                        @endif
                                    @endif
                                    @if($blockedIP->blocked_by)
                                        <span class="flex items-center">
                                            <i data-lucide="user" class="w-3 h-3 mr-1"></i>
                                            By: {{ $blockedIP->blocked_by }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="ml-4">
                                <form action="{{ route('admin.security.unblock-ip') }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="ip_address" value="{{ $blockedIP->ip_address }}">
                                    <button type="submit" 
                                            onclick="return confirm('Are you sure you want to unblock this IP address?')"
                                            class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        <i data-lucide="unlock" class="w-3 h-3 mr-1"></i>
                                        Unblock
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
                <i data-lucide="shield-check" class="w-6 h-6 text-gray-400"></i>
            </div>
            <h3 class="text-sm font-medium text-gray-900 mb-2">No Blocked IPs</h3>
            <p class="text-sm text-gray-500">No IP addresses are currently blocked.</p>
        </div>
    @endif
</div>
