<div id="failed-attempts" class="tab-content">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-yellow-100">
                        <i data-lucide="x-circle" class="h-4 w-4 text-yellow-600"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-medium text-gray-900">Recent Failed Login Attempts</h3>
                    <p class="text-sm text-gray-500">Monitor failed login attempts in the last 24 hours</p>
                </div>
            </div>
        </div>
    </div>

    @if ($logs && $logs->count() > 0)
        <div class="space-y-4">
            @foreach ($logs as $attempt)
                <div class="rounded-lg border border-yellow-200 bg-white shadow-sm">
                    <div class="p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="mb-2 flex items-center space-x-3">
                                    <span class="text-sm font-medium text-gray-900">{{ $attempt->email }}</span>
                                    @if ($attempt->user)
                                        <span
                                            class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800"
                                        >
                                            {{ ucfirst($attempt->user->role) }}
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800"
                                        >
                                            Unknown User
                                        </span>
                                    @endif
                                    <span
                                        class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800"
                                    >
                                        Failed Login
                                    </span>
                                    @if ($attempt->failure_reason)
                                        <span
                                            class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800"
                                        >
                                            {{ $attempt->failure_reason }}
                                        </span>
                                    @endif
                                </div>

                                <div class="mb-3 grid grid-cols-1 gap-4 text-sm text-gray-600 md:grid-cols-2">
                                    <div class="flex items-center">
                                        <i data-lucide="globe" class="mr-2 h-4 w-4 text-gray-400"></i>
                                        <span class="font-mono">{{ $attempt->ip_address }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i data-lucide="smartphone" class="mr-2 h-4 w-4 text-gray-400"></i>
                                        <span class="truncate">{{ $attempt->user_agent }}</span>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-4 text-xs text-gray-500">
                                    <span class="flex items-center">
                                        <i data-lucide="clock" class="mr-1 h-3 w-3"></i>
                                        {{ $attempt->attempted_at->format('d M Y, H:i:s') }}
                                    </span>
                                    <span class="flex items-center">
                                        <i data-lucide="activity" class="mr-1 h-3 w-3"></i>
                                        {{ $attempt->attempted_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>

                            <div class="ml-4 flex space-x-2">
                                <button
                                    onclick="showBlockIPModal('{{ $attempt->ip_address }}')"
                                    class="inline-flex items-center rounded-md border border-red-300 bg-red-50 px-3 py-1 text-xs font-medium text-red-700 shadow-sm hover:bg-red-100"
                                >
                                    <i data-lucide="shield-x" class="mr-1 h-3 w-3"></i>
                                    Block IP
                                </button>

                                <form
                                    action="{{ route('admin.security.clear-failed-attempts') }}"
                                    method="POST"
                                    class="inline"
                                >
                                    @csrf
                                    <input type="hidden" name="email" value="{{ $attempt->email }}" />
                                    <button
                                        type="submit"
                                        onclick="return confirm('Clear all failed attempts for this email?')"
                                        class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-1 text-xs font-medium text-gray-700 shadow-sm hover:bg-gray-50"
                                    >
                                        <i data-lucide="trash-2" class="mr-1 h-3 w-3"></i>
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
        @if ($logs->count() >= 20)
            <div class="mt-6 text-center">
                <button
                    onclick="loadMoreFailedAttempts()"
                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
                >
                    <i data-lucide="chevron-down" class="mr-2 h-4 w-4"></i>
                    Load More
                </button>
            </div>
        @endif
    @else
        <div class="py-12 text-center">
            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-gray-100">
                <i data-lucide="check-circle" class="h-6 w-6 text-gray-400"></i>
            </div>
            <h3 class="mb-2 text-sm font-medium text-gray-900">No Failed Attempts</h3>
            <p class="text-sm text-gray-500">No failed login attempts in the last 24 hours.</p>
        </div>
    @endif
</div>
