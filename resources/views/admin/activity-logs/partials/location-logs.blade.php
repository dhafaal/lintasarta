<div id="locations-logs" class="tab-content">
    <div class="mb-4">
        <h3 class="mb-2 text-lg font-semibold text-gray-900">
            <i data-lucide="map-pin" class="mr-2 inline h-5 w-5 text-sky-600"></i>
            Locations Management Logs
        </h3>
        <p class="text-sm text-gray-600">Activities related to location creation, updates, and deletions</p>
    </div>

    @if ($locationLogs && $locationLogs->count() > 0)
        <div class="space-y-4">
            @foreach ($locationLogs as $log)
                <div class="rounded-lg border-l-4 border-sky-500 bg-gray-50 p-4">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="mb-2 flex items-center space-x-2">
                                <span
                                    class="{{ $log->action == 'create' ? 'bg-green-100 text-green-800' : '' }} {{ $log->action == 'update' ? 'bg-yellow-100 text-yellow-800' : '' }} {{ $log->action == 'delete' ? 'bg-red-100 text-red-800' : '' }} rounded-full px-2 py-1 text-xs font-medium"
                                >
                                    {{ ucfirst($log->action) }}
                                </span>
                                <span class="text-sm font-medium text-gray-900">
                                    @php
                                        $locationName = 'Location #' . $log->resource_id;

                                        // Handle old_values
                                        if ($log->old_values) {
                                            $oldData = is_string($log->old_values) ? json_decode($log->old_values, true) : $log->old_values;
                                            if (is_array($oldData)) {
                                                $locationName = $oldData['name'] ?? ($oldData['location_name'] ?? $locationName);
                                            }
                                        }

                                        // Handle new_values if old_values not available
                                        if ($locationName === 'Location #' . $log->resource_id && $log->new_values) {
                                            $newData = is_string($log->new_values) ? json_decode($log->new_values, true) : $log->new_values;
                                            if (is_array($newData)) {
                                                $locationName = $newData['name'] ?? ($newData['location_name'] ?? $locationName);
                                            }
                                        }
                                    @endphp

                                    {{ $locationName }}
                                </span>
                                @php
                                    $radius = null;
                                    if ($log->old_values) {
                                        $oldData = is_string($log->old_values) ? json_decode($log->old_values, true) : $log->old_values;
                                        if (is_array($oldData) && isset($oldData['radius'])) {
                                            $radius = $oldData['radius'];
                                        }
                                    }
                                @endphp

                                @if ($radius)
                                    <span class="rounded-full bg-sky-100 px-2 py-1 text-xs text-sky-800">
                                        {{ $radius }}m radius
                                    </span>
                                @endif
                            </div>

                            <p class="mb-2 text-sm text-gray-700">{{ $log->description }}</p>

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
                                onclick="toggleDetails('locations-{{ $log->id }}')"
                                class="text-sm font-medium text-sky-600 hover:text-sky-800"
                            >
                                <i data-lucide="eye" class="mr-1 inline h-4 w-4"></i>
                                Details
                            </button>
                        </div>
                    </div>

                    <!-- Details (Hidden by default) -->
                    <div id="locations-{{ $log->id }}" class="mt-4 hidden border-t border-gray-200 pt-4">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            @if ($log->old_values)
                                <div>
                                    <h5 class="mb-2 text-sm font-medium text-gray-700">Old Values:</h5>
                                    @php
                                        $oldData = is_string($log->old_values) ? json_decode($log->old_values, true) : $log->old_values;
                                    @endphp

                                    <pre class="overflow-x-auto rounded border bg-red-50 p-2 text-xs">
{{ json_encode($oldData, JSON_PRETTY_PRINT) }}</pre
                                    >
                                </div>
                            @endif

                            @if ($log->new_values)
                                <div>
                                    <h5 class="mb-2 text-sm font-medium text-gray-700">New Values:</h5>
                                    @php
                                        $newData = is_string($log->new_values) ? json_decode($log->new_values, true) : $log->new_values;
                                    @endphp

                                    <pre class="overflow-x-auto rounded border bg-green-50 p-2 text-xs">
{{ json_encode($newData, JSON_PRETTY_PRINT) }}</pre
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
            {{ $locationLogs->appends(request()->query())->fragment('locations-logs')->links() }}
        </div>
    @else
        <div class="py-12 text-center">
            <i data-lucide="map-pin" class="mx-auto mb-4 h-12 w-12 text-gray-400"></i>
            <h3 class="mb-2 text-lg font-medium text-gray-900">No Location Logs Found</h3>
            <p class="text-gray-500">No location management activities have been recorded yet.</p>
        </div>
    @endif
</div>

<script>
    function toggleDetails(id) {
        const element = document.getElementById(id);
        element.classList.toggle('hidden');
    }
</script>
