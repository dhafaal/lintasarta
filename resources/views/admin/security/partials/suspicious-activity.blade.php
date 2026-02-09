<div id="suspicious-activity" class="tab-content">
    <div class="mb-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-100">
                    <i data-lucide="alert-triangle" class="h-4 w-4 text-red-600"></i>
                </div>
            </div>
            <div class="ml-3">
                <h3 class="text-lg font-medium text-gray-900">Suspicious Activities</h3>
                <p class="text-sm text-gray-500">Review suspicious login activities and security alerts</p>
            </div>
        </div>
    </div>

    @if ($logs && $logs->count() > 0)
        <div class="space-y-4">
            @foreach ($logs as $activity)
                <div class="rounded-lg border border-red-200 bg-white shadow-sm">
                    <div class="p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="mb-2 flex items-center space-x-3">
                                    @if ($activity->user)
                                        <span class="text-sm font-medium text-gray-900">
                                            {{ $activity->user->name }}
                                        </span>
                                        <span
                                            class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800"
                                        >
                                            {{ ucfirst($activity->user->role) }}
                                        </span>
                                    @elseif ($activity->email)
                                        <span class="text-sm font-medium text-gray-900">{{ $activity->email }}</span>
                                    @endif

                                    <span
                                        class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800"
                                    >
                                        <i data-lucide="alert-triangle" class="mr-1 h-3 w-3"></i>
                                        Suspicious Activity
                                    </span>

                                    <span
                                        class="{{ $activity->status == 'warning' ? 'bg-yellow-100 text-yellow-800' : '' }} {{ $activity->status == 'blocked' ? 'bg-red-100 text-red-800' : '' }} {{ $activity->status == 'success' ? 'bg-green-100 text-green-800' : '' }} inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                                    >
                                        {{ ucfirst($activity->status) }}
                                    </span>
                                </div>

                                <p class="mb-3 text-sm text-gray-600">{{ $activity->description }}</p>

                                <div class="flex items-center space-x-4 text-xs text-gray-500">
                                    <span class="flex items-center">
                                        <i data-lucide="clock" class="mr-1 h-3 w-3"></i>
                                        {{ $activity->created_at->format('d M Y, H:i:s') }}
                                    </span>
                                    <span class="flex items-center">
                                        <i data-lucide="activity" class="mr-1 h-3 w-3"></i>
                                        {{ $activity->created_at->diffForHumans() }}
                                    </span>
                                    @if ($activity->ip_address)
                                        <span class="flex items-center">
                                            <i data-lucide="globe" class="mr-1 h-3 w-3"></i>
                                            {{ $activity->ip_address }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="ml-4 flex space-x-2">
                                @if ($activity->ip_address)
                                    <button
                                        onclick="showBlockIPModal('{{ $activity->ip_address }}')"
                                        class="inline-flex items-center rounded-md border border-red-300 bg-red-50 px-3 py-1 text-xs font-medium text-red-700 shadow-sm hover:bg-red-100"
                                    >
                                        <i data-lucide="shield-x" class="mr-1 h-3 w-3"></i>
                                        Block IP
                                    </button>
                                @endif

                                @if ($activity->user_id)
                                    <form
                                        action="{{ route('admin.security.terminate-all-sessions') }}"
                                        method="POST"
                                        class="inline"
                                    >
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $activity->user_id }}" />
                                        <button
                                            type="submit"
                                            onclick="return confirm('Terminate all sessions for this user?')"
                                            class="inline-flex items-center rounded-md border border-yellow-300 bg-yellow-50 px-3 py-1 text-xs font-medium text-yellow-700 shadow-sm hover:bg-yellow-100"
                                        >
                                            <i data-lucide="power" class="mr-1 h-3 w-3"></i>
                                            Terminate Sessions
                                        </button>
                                    </form>
                                @endif

                                <button
                                    onclick="toggleDetails('suspicious-{{ $activity->id }}')"
                                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-1 text-xs font-medium text-gray-700 shadow-sm hover:bg-gray-50"
                                >
                                    <i data-lucide="eye" class="mr-1 h-3 w-3"></i>
                                    Details
                                </button>
                            </div>
                        </div>

                        <!-- Details (Hidden by default) -->
                        <div id="suspicious-{{ $activity->id }}" class="mt-4 hidden border-t border-gray-200 pt-4">
                            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                                <div>
                                    <h5 class="mb-2 text-sm font-medium text-gray-900">Activity Details:</h5>
                                    <div class="space-y-1 rounded-md border border-gray-200 bg-gray-50 p-3 text-xs">
                                        <div>
                                            <strong>Action:</strong>
                                            {{ $activity->action }}
                                        </div>
                                        <div>
                                            <strong>Status:</strong>
                                            {{ $activity->status }}
                                        </div>
                                        @if ($activity->email)
                                            <div>
                                                <strong>Email:</strong>
                                                {{ $activity->email }}
                                            </div>
                                        @endif

                                        @if ($activity->user)
                                            <div>
                                                <strong>User:</strong>
                                                {{ $activity->user->name }} ({{ $activity->user->role }})
                                            </div>
                                        @endif

                                        @if ($activity->ip_address)
                                            <div>
                                                <strong>IP Address:</strong>
                                                {{ $activity->ip_address }}
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div>
                                    <h5 class="mb-2 text-sm font-medium text-gray-900">Timestamp Info:</h5>
                                    <div class="space-y-1 rounded-md border border-blue-200 bg-blue-50 p-3 text-xs">
                                        <div>
                                            <strong>Detected At:</strong>
                                            {{ $activity->created_at->format('d M Y, H:i:s') }}
                                        </div>
                                        <div>
                                            <strong>Time Ago:</strong>
                                            {{ $activity->created_at->diffForHumans() }}
                                        </div>
                                        @if ($activity->attempted_at)
                                            <div>
                                                <strong>Attempted At:</strong>
                                                {{ $activity->attempted_at->format('d M Y, H:i:s') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if ($activity->user_agent)
                                <div class="mt-4">
                                    <h5 class="mb-2 text-sm font-medium text-gray-900">User Agent:</h5>
                                    <div class="rounded-md border border-gray-200 bg-gray-50 p-3">
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
        <div class="py-12 text-center">
            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-gray-100">
                <i data-lucide="shield-check" class="h-6 w-6 text-gray-400"></i>
            </div>
            <h3 class="mb-2 text-sm font-medium text-gray-900">No Suspicious Activity</h3>
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
