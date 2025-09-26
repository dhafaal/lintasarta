<div id="locations-logs" class="tab-content">
    <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-2">
            <i data-lucide="map-pin" class="w-5 h-5 inline mr-2 text-sky-600"></i>
            Locations Management Logs
        </h3>
        <p class="text-sm text-gray-600">Activities related to location creation, updates, and deletions</p>
    </div>

    @if($locationLogs && $locationLogs->count() > 0)
        <div class="space-y-4">
            @foreach($locationLogs as $log)
                <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-sky-500">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $log->action == 'create' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $log->action == 'update' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $log->action == 'delete' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($log->action) }}
                                </span>
                                <span class="text-sm font-medium text-gray-900">
                                    @php
                                        $locationName = 'Location #' . $log->resource_id;
                                        
                                        // Handle old_values
                                        if($log->old_values) {
                                            $oldData = is_string($log->old_values) ? json_decode($log->old_values, true) : $log->old_values;
                                            if(is_array($oldData)) {
                                                $locationName = $oldData['name'] ?? $oldData['location_name'] ?? $locationName;
                                            }
                                        }
                                        
                                        // Handle new_values if old_values not available
                                        if($locationName === 'Location #' . $log->resource_id && $log->new_values) {
                                            $newData = is_string($log->new_values) ? json_decode($log->new_values, true) : $log->new_values;
                                            if(is_array($newData)) {
                                                $locationName = $newData['name'] ?? $newData['location_name'] ?? $locationName;
                                            }
                                        }
                                    @endphp
                                    {{ $locationName }}
                                </span>
                                @php
                                    $radius = null;
                                    if($log->old_values) {
                                        $oldData = is_string($log->old_values) ? json_decode($log->old_values, true) : $log->old_values;
                                        if(is_array($oldData) && isset($oldData['radius'])) {
                                            $radius = $oldData['radius'];
                                        }
                                    }
                                @endphp
                                @if($radius)
                                    <span class="px-2 py-1 text-xs bg-sky-100 text-sky-800 rounded-full">{{ $radius }}m radius</span>
                                @endif
                            </div>

                            <p class="text-sm text-gray-700 mb-2">{{ $log->description }}</p>

                            <div class="flex items-center space-x-4 text-xs text-gray-500">
                                <span>
                                    <i data-lucide="user" class="w-3 h-3 inline mr-1"></i>
                                    {{ $log->user->name ?? 'System' }}
                                </span>
                                <span>
                                    <i data-lucide="clock" class="w-3 h-3 inline mr-1"></i>
                                    {{ $log->created_at->format('d M Y, H:i') }}
                                </span>
                                <span>
                                    <i data-lucide="globe" class="w-3 h-3 inline mr-1"></i>
                                    {{ $log->ip_address ?? 'Unknown IP' }}
                                </span>
                            </div>
                        </div>

                        <div class="ml-4">
                            <button onclick="toggleDetails('locations-{{ $log->id }}')"
                                    class="text-sky-600 hover:text-sky-800 text-sm font-medium">
                                <i data-lucide="eye" class="w-4 h-4 inline mr-1"></i>
                                Details
                            </button>
                        </div>
                    </div>

                    <!-- Details (Hidden by default) -->
                    <div id="locations-{{ $log->id }}" class="hidden mt-4 pt-4 border-t border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @if($log->old_values)
                                <div>
                                    <h5 class="text-sm font-medium text-gray-700 mb-2">Old Values:</h5>
                                    @php
                                        $oldData = is_string($log->old_values) ? json_decode($log->old_values, true) : $log->old_values;
                                    @endphp
                                    <pre class="text-xs bg-red-50 p-2 rounded border overflow-x-auto">{{ json_encode($oldData, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            @endif

                            @if($log->new_values)
                                <div>
                                    <h5 class="text-sm font-medium text-gray-700 mb-2">New Values:</h5>
                                    @php
                                        $newData = is_string($log->new_values) ? json_decode($log->new_values, true) : $log->new_values;
                                    @endphp
                                    <pre class="text-xs bg-green-50 p-2 rounded border overflow-x-auto">{{ json_encode($newData, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            @endif
                        </div>

                        @if($log->user_agent)
                            <div class="mt-3">
                                <h5 class="text-sm font-medium text-gray-700 mb-1">User Agent:</h5>
                                <p class="text-xs text-gray-600 bg-gray-100 p-2 rounded">{{ $log->user_agent }}</p>
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
        <div class="text-center py-12">
            <i data-lucide="map-pin" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Location Logs Found</h3>
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
