<div id="users-logs" class="tab-content">
    <div class="mb-4">
        <h3 class="mb-2 text-lg font-semibold text-gray-900">
            <i data-lucide="users" class="mr-2 inline h-5 w-5 text-purple-600"></i>
            Users Management Logs
        </h3>
        <p class="text-sm text-gray-600">Activities related to user creation, updates, and deletions</p>
    </div>

    @if ($logs && $logs->count() > 0)
        <div class="space-y-4">
            @foreach ($logs as $log)
                <div class="rounded-lg border-l-4 border-purple-500 bg-gray-50 p-4">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="mb-2 flex items-center space-x-2">
                                <span
                                    class="{{ $log->action == 'create' ? 'bg-green-100 text-green-800' : '' }} {{ $log->action == 'update' ? 'bg-yellow-100 text-yellow-800' : '' }} {{ $log->action == 'delete' ? 'bg-red-100 text-red-800' : '' }} rounded-full px-2 py-1 text-xs font-medium"
                                >
                                    {{ ucfirst($log->action) }}
                                </span>
                                <span class="text-sm font-medium text-gray-900">
                                    {{ $log->target_user_name ?? 'Unknown User' }}
                                </span>
                                @if ($log->target_user_role)
                                    <span
                                        class="{{ $log->target_user_role == 'admin' ? 'bg-red-100 text-red-800' : '' }} {{ $log->target_user_role == 'operator' ? 'bg-yellow-100 text-yellow-800' : '' }} {{ $log->target_user_role == 'user' ? 'bg-blue-100 text-blue-800' : '' }} rounded-full px-2 py-1 text-xs"
                                    >
                                        {{ ucfirst($log->target_user_role) }}
                                    </span>
                                @endif

                                @if ($log->password_changed)
                                    <span class="rounded-full bg-orange-100 px-2 py-1 text-xs text-orange-800">
                                        Password Changed
                                    </span>
                                @endif
                            </div>

                            <p class="mb-2 text-sm text-gray-700">{{ $log->description }}</p>

                            @if ($log->target_user_email)
                                <p class="mb-2 text-xs text-gray-600">
                                    <i data-lucide="mail" class="mr-1 inline h-3 w-3"></i>
                                    {{ $log->target_user_email }}
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
                                onclick="toggleDetails('users-{{ $log->id }}')"
                                class="text-sm font-medium text-purple-600 hover:text-purple-800"
                            >
                                <i data-lucide="eye" class="mr-1 inline h-4 w-4"></i>
                                Details
                            </button>
                        </div>
                    </div>

                    <!-- Details (Hidden by default) -->
                    <div id="users-{{ $log->id }}" class="mt-4 hidden border-t border-gray-200 pt-4">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            @if ($log->old_values)
                                <div>
                                    <h5 class="mb-2 text-sm font-medium text-gray-700">Old Values:</h5>
                                    <pre class="overflow-x-auto rounded border bg-red-50 p-2 text-xs">
{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre
                                    >
                                </div>
                            @endif

                            @if ($log->new_values)
                                <div>
                                    <h5 class="mb-2 text-sm font-medium text-gray-700">New Values:</h5>
                                    <pre class="overflow-x-auto rounded border bg-green-50 p-2 text-xs">
{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre
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
            {{ $logs->appends(request()->query())->fragment('users-logs')->links() }}
        </div>
    @else
        <div class="py-12 text-center">
            <i data-lucide="inbox" class="mx-auto mb-4 h-12 w-12 text-gray-400"></i>
            <h3 class="mb-2 text-lg font-medium text-gray-900">No Users Logs Found</h3>
            <p class="text-gray-500">No user management activities have been recorded yet.</p>
        </div>
    @endif
</div>
