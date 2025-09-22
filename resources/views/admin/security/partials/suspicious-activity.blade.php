<div id="suspicious-activity" class="tab-content">
    <div class="mb-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="alert-triangle" class="w-4 h-4 text-red-600"></i>
                </div>
            </div>
            <div class="ml-3">
                <h3 class="text-lg font-medium text-gray-900">Suspicious Activities</h3>
                <p class="text-sm text-gray-500">Review suspicious login activities and security alerts</p>
            </div>
        </div>
    </div>

    @if($logs && $logs->count() > 0)
        <div class="space-y-4">
            @foreach($logs as $activity)
                <div class="bg-white border border-red-200 rounded-lg shadow-sm">
                    <div class="p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    @if($activity->user)
                                        <span class="text-sm font-medium text-gray-900">{{ $activity->user->name }}</span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ ucfirst($activity->user->role) }}
                                        </span>
                                    @elseif($activity->email)
                                        <span class="text-sm font-medium text-gray-900">{{ $activity->email }}</span>
                                    @endif
                                    
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i data-lucide="alert-triangle" class="w-3 h-3 mr-1"></i>
                                        Suspicious Activity
                                    </span>
                                    
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $activity->status == 'warning' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $activity->status == 'blocked' ? 'bg-red-100 text-red-800' : '' }}
                                        {{ $activity->status == 'success' ? 'bg-green-100 text-green-800' : '' }}">
                                        {{ ucfirst($activity->status) }}
                                    </span>
                                </div>
                                
                                <p class="text-sm text-gray-600 mb-3">{{ $activity->description }}</p>
                                
                                <div class="flex items-center space-x-4 text-xs text-gray-500">
                                    <span class="flex items-center">
                                        <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                        {{ $activity->created_at->format('d M Y, H:i:s') }}
                                    </span>
                                    <span class="flex items-center">
                                        <i data-lucide="activity" class="w-3 h-3 mr-1"></i>
                                        {{ $activity->created_at->diffForHumans() }}
                                    </span>
                                    @if($activity->ip_address)
                                        <span class="flex items-center">
                                            <i data-lucide="globe" class="w-3 h-3 mr-1"></i>
                                            {{ $activity->ip_address }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="ml-4 flex space-x-2">
                                @if($activity->ip_address)
                                    <button onclick="showBlockIPModal('{{ $activity->ip_address }}')"
                                            class="inline-flex items-center px-3 py-1 border border-red-300 shadow-sm text-xs font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100">
                                        <i data-lucide="shield-x" class="w-3 h-3 mr-1"></i>
                                        Block IP
                                    </button>
                                @endif
                                
                                @if($activity->user_id)
                                    <form action="{{ route('admin.security.terminate-all-sessions') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $activity->user_id }}">
                                        <button type="submit" 
                                                onclick="return confirm('Terminate all sessions for this user?')"
                                                class="inline-flex items-center px-3 py-1 border border-yellow-300 shadow-sm text-xs font-medium rounded-md text-yellow-700 bg-yellow-50 hover:bg-yellow-100">
                                            <i data-lucide="power" class="w-3 h-3 mr-1"></i>
                                            Terminate Sessions
                                        </button>
                                    </form>
                                @endif
                                
                                <button onclick="toggleDetails('suspicious-{{ $activity->id }}')" 
                                        class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    <i data-lucide="eye" class="w-3 h-3 mr-1"></i>
                                    Details
                                </button>
                            </div>
                        </div>
                        
                        <!-- Details (Hidden by default) -->
                        <div id="suspicious-{{ $activity->id }}" class="hidden mt-4 pt-4 border-t border-gray-200">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                <div>
                                    <h5 class="text-sm font-medium text-gray-900 mb-2">Activity Details:</h5>
                                    <div class="bg-gray-50 border border-gray-200 rounded-md p-3 space-y-1 text-xs">
                                        <div><strong>Action:</strong> {{ $activity->action }}</div>
                                        <div><strong>Status:</strong> {{ $activity->status }}</div>
                                        @if($activity->email)
                                            <div><strong>Email:</strong> {{ $activity->email }}</div>
                                        @endif
                                        @if($activity->user)
                                            <div><strong>User:</strong> {{ $activity->user->name }} ({{ $activity->user->role }})</div>
                                        @endif
                                        @if($activity->ip_address)
                                            <div><strong>IP Address:</strong> {{ $activity->ip_address }}</div>
                                        @endif
                                    </div>
                                </div>
                                
                                <div>
                                    <h5 class="text-sm font-medium text-gray-900 mb-2">Timestamp Info:</h5>
                                    <div class="bg-blue-50 border border-blue-200 rounded-md p-3 space-y-1 text-xs">
                                        <div><strong>Detected At:</strong> {{ $activity->created_at->format('d M Y, H:i:s') }}</div>
                                        <div><strong>Time Ago:</strong> {{ $activity->created_at->diffForHumans() }}</div>
                                        @if($activity->attempted_at)
                                            <div><strong>Attempted At:</strong> {{ $activity->attempted_at->format('d M Y, H:i:s') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            @if($activity->user_agent)
                                <div class="mt-4">
                                    <h5 class="text-sm font-medium text-gray-900 mb-2">User Agent:</h5>
                                    <div class="bg-gray-50 border border-gray-200 rounded-md p-3">
                                        <p class="text-xs text-gray-600">{{ $activity->user_agent }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <div class="w-12 h-12 mx-auto bg-gray-100 rounded-lg flex items-center justify-center mb-4">
                <i data-lucide="shield-check" class="w-6 h-6 text-gray-400"></i>
            </div>
            <h3 class="text-sm font-medium text-gray-900 mb-2">No Suspicious Activity</h3>
            <p class="text-sm text-gray-500">No suspicious activities detected in the last 7 days.</p>
        </div>
    @endif
</div>

<script>
function toggleDetails(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.classList.toggle('hidden');
    }
}
</script>
