<div id="permissions-logs" class="tab-content">
    <div class="mb-4">
        <h3 class="mb-2 text-lg font-semibold text-gray-900">
            <i data-lucide="shield-check" class="mr-2 inline h-5 w-5 text-amber-600"></i>
            Permissions Management Logs
        </h3>
        <p class="text-sm text-gray-600">Activities related to permission approvals and rejections</p>
    </div>

    @if ($logs && $logs->count() > 0)
        <div class="space-y-4">
            @foreach ($logs as $log)
                <div class="rounded-lg border-l-4 border-amber-500 bg-gray-50 p-4">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="mb-2 flex items-center space-x-2">
                                <span
                                    class="{{ $log->action == 'approve' ? 'bg-green-100 text-green-800' : '' }} {{ $log->action == 'reject' ? 'bg-red-100 text-red-800' : '' }} rounded-full px-2 py-1 text-xs font-medium"
                                >
                                    {{ ucfirst($log->action) }}
                                </span>
                                <span class="text-sm font-medium text-gray-900">
                                    {{ $log->target_user_name ?? 'Unknown User' }}
                                </span>
                                @if ($log->permission_type)
                                    <span
                                        class="{{ $log->permission_type == 'izin' ? 'bg-blue-100 text-blue-800' : '' }} {{ $log->permission_type == 'sakit' ? 'bg-yellow-100 text-yellow-800' : '' }} {{ $log->permission_type == 'cuti' ? 'bg-purple-100 text-purple-800' : '' }} rounded-full px-2 py-1 text-xs"
                                    >
                                        {{ ucfirst($log->permission_type) }}
                                    </span>
                                @endif

                                <span
                                    class="{{ $log->new_status == 'approved' ? 'bg-green-100 text-green-800' : '' }} {{ $log->new_status == 'rejected' ? 'bg-red-100 text-red-800' : '' }} rounded-full px-2 py-1 text-xs"
                                >
                                    {{ ucfirst($log->new_status) }}
                                </span>
                            </div>

                            <p class="mb-2 text-sm text-gray-700">{{ $log->description }}</p>

                            @if ($log->permission_reason)
                                <p class="mb-2 text-xs text-gray-600">
                                    <i data-lucide="message-circle" class="mr-1 inline h-3 w-3"></i>
                                    <strong>Reason:</strong>
                                    {{ $log->permission_reason }}
                                </p>
                            @endif

                            @if ($log->permission_date)
                                <p class="mb-2 text-xs text-gray-600">
                                    <i data-lucide="calendar-days" class="mr-1 inline h-3 w-3"></i>
                                    {{ \Carbon\Carbon::parse($log->permission_date)->format('d M Y') }}
                                </p>
                            @endif

                            <div class="flex items-center space-x-4 text-xs text-gray-500">
                                <span>
                                    <i data-lucide="user" class="mr-1 inline h-3 w-3"></i>
                                    {{ $log->user->name ?? 'System' }}
                                </span>
                                <span>
                                    <i data-lucide="clock" class="mr-1 inline h-3 w-3"></i>
                                    {{ $log->created_at->format('d M Y, H:i') }}
                                </span>
                                <span>
                                    <i data-lucide="globe" class="mr-1 inline h-3 w-3"></i>
                                    {{ $log->ip_address ?? 'Unknown IP' }}
                                </span>
                            </div>
                        </div>

                        <div class="ml-4">
                            <button
                                onclick="toggleDetails('permissions-{{ $log->id }}')"
                                class="text-sm font-medium text-amber-600 hover:text-amber-800"
                            >
                                <i data-lucide="eye" class="mr-1 inline h-4 w-4"></i>
                                Details
                            </button>
                        </div>
                    </div>

                    <!-- Details (Hidden by default) -->
                    <div id="permissions-{{ $log->id }}" class="mt-4 hidden border-t border-gray-200 pt-4">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <h5 class="mb-2 text-sm font-medium text-gray-700">Status Change:</h5>
                                <div class="rounded border bg-gray-50 p-2 text-xs">
                                    <span class="text-red-600">{{ $log->old_status }}</span>
                                    <i data-lucide="arrow-right" class="mx-2 inline h-3 w-3"></i>
                                    <span class="text-green-600">{{ $log->new_status }}</span>
                                </div>
                            </div>

                            @if ($log->additional_data)
                                <div>
                                    <h5 class="mb-2 text-sm font-medium text-gray-700">Additional Data:</h5>
                                    <pre class="overflow-x-auto rounded border bg-blue-50 p-2 text-xs">
{{ json_encode($log->additional_data, JSON_PRETTY_PRINT) }}</pre
                                    >
                                </div>
                            @endif
                        </div>

                        @if ($log->user_agent)
                            <div class="mt-3">
                                <h5 class="mb-1 text-sm font-medium text-gray-700">User Agent:</h5>
                                <p class="rounded bg-gray-100 p-2 text-xs text-gray-600">{{ $log->user_agent }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $logs->appends(request()->query())->fragment('permissions-logs')->links() }}
        </div>
    @else
        <div class="py-12 text-center">
            <i data-lucide="inbox" class="mx-auto mb-4 h-12 w-12 text-gray-400"></i>
            <h3 class="mb-2 text-lg font-medium text-gray-900">No Permissions Logs Found</h3>
            <p class="text-gray-500">No permission management activities have been recorded yet.</p>
        </div>
    @endif
</div>
