<div id="blocked-ips" class="tab-content">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-100">
                        <i data-lucide="shield-x" class="h-4 w-4 text-red-600"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-medium text-gray-900">Blocked IP Addresses</h3>
                    <p class="text-sm text-gray-500">Manage blocked IP addresses and their restrictions</p>
                </div>
            </div>
            <button
                onclick="showBlockIPModal('')"
                class="inline-flex items-center rounded-md border border-transparent bg-red-600 px-3 py-2 text-sm font-medium text-white hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:outline-none"
            >
                <i data-lucide="plus" class="mr-2 h-4 w-4"></i>
                Block New IP
            </button>
        </div>
    </div>

    @if ($logs && $logs->count() > 0)
        <div class="space-y-4">
            @foreach ($logs as $blockedIP)
                <div class="rounded-lg border border-red-200 bg-white shadow-sm">
                    <div class="p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="mb-2 flex items-center space-x-3">
                                    <span class="font-mono text-sm font-medium text-gray-900">
                                        {{ $blockedIP->ip_address }}
                                    </span>
                                    @if ($blockedIP->is_permanent)
                                        <span
                                            class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800"
                                        >
                                            Permanent
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-medium text-yellow-800"
                                        >
                                            Temporary
                                        </span>
                                    @endif
                                    @if ($blockedIP->failed_attempts > 0)
                                        <span
                                            class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800"
                                        >
                                            {{ $blockedIP->failed_attempts }} attempts
                                        </span>
                                    @endif
                                </div>

                                <p class="mb-2 text-sm text-gray-600">{{ $blockedIP->reason }}</p>

                                <div class="flex items-center space-x-4 text-xs text-gray-500">
                                    <span class="flex items-center">
                                        <i data-lucide="clock" class="mr-1 h-3 w-3"></i>
                                        Blocked: {{ $blockedIP->blocked_at->format('d M Y, H:i') }}
                                    </span>
                                    @if ($blockedIP->blocked_until)
                                        <span class="flex items-center">
                                            <i data-lucide="calendar" class="mr-1 h-3 w-3"></i>
                                            Until: {{ $blockedIP->blocked_until->format('d M Y, H:i') }}
                                        </span>
                                        @if ($blockedIP->getTimeRemaining())
                                            <span class="flex items-center text-yellow-600">
                                                <i data-lucide="timer" class="mr-1 h-3 w-3"></i>
                                                {{ $blockedIP->getTimeRemaining() }} min remaining
                                            </span>
                                        @endif
                                    @endif

                                    @if ($blockedIP->blocked_by)
                                        <span class="flex items-center">
                                            <i data-lucide="user" class="mr-1 h-3 w-3"></i>
                                            By: {{ $blockedIP->blocked_by }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="ml-4">
                                <form action="{{ route('admin.security.unblock-ip') }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="ip_address" value="{{ $blockedIP->ip_address }}" />
                                    <button
                                        type="submit"
                                        onclick="return confirm('Are you sure you want to unblock this IP address?')"
                                        class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-1 text-xs font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:outline-none"
                                    >
                                        <i data-lucide="unlock" class="mr-1 h-3 w-3"></i>
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
        @if ($logs->hasPages())
            <div class="mt-6">
                {{ $logs->appends(request()->query())->links() }}
            </div>
        @endif
    @else
        <div class="py-12 text-center">
            <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-lg bg-gray-100">
                <i data-lucide="shield-check" class="h-6 w-6 text-gray-400"></i>
            </div>
            <h3 class="mb-2 text-sm font-medium text-gray-900">No Blocked IPs</h3>
            <p class="text-sm text-gray-500">No IP addresses are currently blocked.</p>
        </div>
    @endif
</div>
