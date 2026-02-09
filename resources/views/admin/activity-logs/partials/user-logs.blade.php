<div id="user-logs" class="tab-content">
    <div class="mb-4">
        <h3 class="mb-2 text-lg font-semibold text-gray-900">
            <i data-lucide="user-check" class="mr-2 inline h-5 w-5 text-indigo-600"></i>
            User Activities Logs
        </h3>
        <p class="text-sm text-gray-600">Activities performed by users (check-in, check-out, permissions)</p>
    </div>

    @if ($logs && $logs->count() > 0)
        <div class="space-y-4">
            @foreach ($logs as $log)
                <div class="rounded-lg border-l-4 border-indigo-500 bg-gray-50 p-4">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="mb-2 flex items-center space-x-2">
                                <span
                                    class="{{ $log->action == 'checkin' ? 'bg-green-100 text-green-800' : '' }} {{ $log->action == 'checkout' ? 'bg-blue-100 text-blue-800' : '' }} {{ $log->action == 'request_permission' ? 'bg-yellow-100 text-yellow-800' : '' }} {{ $log->action == 'absent' ? 'bg-red-100 text-red-800' : '' }} {{ $log->action == 'delete_attendance' ? 'bg-red-100 text-red-800' : '' }} rounded-full px-2 py-1 text-xs font-medium"
                                >
                                    {{ str_replace('_', ' ', ucfirst($log->action)) }}
                                </span>
                                <span class="text-sm font-medium text-gray-900">
                                    {{ $log->user->name ?? 'Unknown User' }}
                                </span>
                                @if ($log->resource_type)
                                    <span class="rounded-full bg-gray-100 px-2 py-1 text-xs text-gray-800">
                                        {{ ucfirst($log->resource_type) }}
                                    </span>
                                @endif
                            </div>

                            <p class="mb-2 text-sm text-gray-700">{{ $log->description }}</p>

                            @if ($log->resource_name)
                                <p class="mb-2 text-xs text-gray-600">
                                    <i data-lucide="tag" class="mr-1 inline h-3 w-3"></i>
                                    {{ $log->resource_name }}
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
                                onclick="toggleDetails('user-{{ $log->id }}')"
                                class="text-sm font-medium text-indigo-600 hover:text-indigo-800"
                            >
                                <i data-lucide="eye" class="mr-1 inline h-4 w-4"></i>
                                Details
                            </button>
                        </div>
                    </div>

                    <!-- Details (Hidden by default) -->
                    <div id="user-{{ $log->id }}" class="mt-4 hidden border-t border-gray-200 pt-4">
                        @if ($log->additional_data)
                            <div class="mb-4">
                                <h5 class="mb-2 text-sm font-medium text-gray-700">Additional Data:</h5>
                                <div class="rounded border bg-blue-50 p-3">
                                    @if (is_array($log->additional_data))
                                        @foreach ($log->additional_data as $key => $value)
                                            <div class="flex justify-between py-1">
                                                <span class="text-xs font-medium text-gray-600">
                                                    {{ ucfirst(str_replace('_', ' ', $key)) }}:
                                                </span>
                                                <span class="text-xs text-gray-800">
                                                    @if (is_bool($value))
                                                        {{ $value ? 'Yes' : 'No' }}
                                                    @elseif (is_array($value))
                                                        {{ json_encode($value) }}
                                                    @else
                                                        {{ $value }}
                                                    @endif
                                                </span>
                                            </div>
                                        @endforeach
                                    @else
                                        <pre class="overflow-x-auto text-xs">
{{ json_encode($log->additional_data, JSON_PRETTY_PRINT) }}</pre
                                        >
                                    @endif
                                </div>
                            </div>
                        @endif

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
            {{ $logs->appends(request()->query())->fragment('user-logs')->links() }}
        </div>
    @else
        <div class="py-12 text-center">
            <i data-lucide="inbox" class="mx-auto mb-4 h-12 w-12 text-gray-400"></i>
            <h3 class="mb-2 text-lg font-medium text-gray-900">No User Activity Logs Found</h3>
            <p class="text-gray-500">No user activities have been recorded yet.</p>
        </div>
    @endif
</div>
