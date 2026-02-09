<div id="active-sessions" class="tab-content">
    <div class="mb-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100">
                    <i data-lucide="monitor" class="h-4 w-4 text-blue-600"></i>
                </div>
            </div>
            <div class="ml-3">
                <h3 class="text-lg font-medium text-gray-900">Active User Sessions</h3>
                <p class="text-sm text-gray-500">Monitor and manage currently active user sessions</p>
            </div>
        </div>
    </div>

    @if ($logs && $logs->count() > 0)
        <div class="space-y-4">
            @foreach ($logs as $session)
                <div class="rounded-lg border border-blue-200 bg-white shadow-sm">
                    <div class="p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="mb-2 flex items-center space-x-3">
                                    <span class="text-sm font-medium text-gray-900">{{ $session->user->name }}</span>
                                    <span
                                        class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800"
                                    >
                                        {{ ucfirst($session->user->role) }}
                                    </span>
                                    @if ($session->is_trusted_device)
                                        <span
                                            class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800"
                                        >
                                            <i data-lucide="shield-check" class="mr-1 h-3 w-3"></i>
                                            Trusted Device
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800"
                                        >
                                            <i data-lucide="alert-triangle" class="mr-1 h-3 w-3"></i>
                                            New Device
                                        </span>
                                    @endif
                                </div>

                                <div class="mb-3 grid grid-cols-1 gap-4 text-sm text-gray-600 md:grid-cols-2">
                                    <div class="flex items-center">
                                        <i data-lucide="globe" class="mr-2 h-4 w-4 text-gray-400"></i>
                                        <span class="font-mono">{{ $session->ip_address }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i data-lucide="smartphone" class="mr-2 h-4 w-4 text-gray-400"></i>
                                        <span class="truncate">{{ $session->user_agent }}</span>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-4 text-xs text-gray-500">
                                    <span class="flex items-center">
                                        <i data-lucide="clock" class="mr-1 h-3 w-3"></i>
                                        Last Activity: {{ $session->last_activity->format('d M Y, H:i') }}
                                    </span>
                                    <span class="flex items-center">
                                        <i data-lucide="calendar" class="mr-1 h-3 w-3"></i>
                                        Started: {{ $session->created_at->format('d M Y, H:i') }}
                                    </span>
                                    <span class="flex items-center">
                                        <i data-lucide="timer" class="mr-1 h-3 w-3"></i>
                                        Expires: {{ $session->expires_at->format('d M Y, H:i') }}
                                    </span>
                                </div>
                            </div>

                            <div class="ml-4 flex space-x-2">
                                @if (! $session->is_trusted_device)
                                    <button
                                        onclick="markAsTrusted('{{ $session->id }}')"
                                        class="inline-flex items-center rounded-md border border-green-300 bg-green-50 px-3 py-1 text-xs font-medium text-green-700 shadow-sm hover:bg-green-100"
                                    >
                                        <i data-lucide="shield-check" class="mr-1 h-3 w-3"></i>
                                        Trust Device
                                    </button>
                                @endif

                                <form
                                    action="{{ route('admin.security.terminate-session') }}"
                                    method="POST"
                                    class="inline"
                                >
                                    @csrf
                                    <input type="hidden" name="session_id" value="{{ $session->session_id }}" />
                                    <button
                                        type="submit"
                                        onclick="return confirm('Are you sure you want to terminate this session?')"
                                        class="inline-flex items-center rounded-md border border-red-300 bg-red-50 px-3 py-1 text-xs font-medium text-red-700 shadow-sm hover:bg-red-100"
                                    >
                                        <i data-lucide="x-circle" class="mr-1 h-3 w-3"></i>
                                        Terminate
                                    </button>
                                </form>

                                <form
                                    action="{{ route('admin.security.terminate-all-sessions') }}"
                                    method="POST"
                                    class="inline"
                                >
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $session->user_id }}" />
                                    <button
                                        type="submit"
                                        onclick="return confirm('Are you sure you want to terminate ALL sessions for this user?')"
                                        class="inline-flex items-center rounded-md border border-red-300 bg-red-50 px-3 py-1 text-xs font-medium text-red-700 shadow-sm hover:bg-red-100"
                                    >
                                        <i data-lucide="power" class="mr-1 h-3 w-3"></i>
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
        @if ($logs->hasPages())
            <div class="mt-6">
                {{ $logs->appends(request()->query())->links() }}
            </div>
        @endif
    @else
        <div class="py-12 text-center">
            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-gray-100">
                <i data-lucide="monitor-off" class="h-6 w-6 text-gray-400"></i>
            </div>
            <h3 class="mb-2 text-sm font-medium text-gray-900">No Active Sessions</h3>
            <p class="text-sm text-gray-500">No users are currently logged in.</p>
        </div>
    @endif
</div>
