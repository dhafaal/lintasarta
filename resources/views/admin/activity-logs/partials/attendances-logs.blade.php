<div id="attendances-logs" class="tab-content">
    <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-2">
            <i data-lucide="user-check" class="w-5 h-5 inline mr-2 text-sky-600"></i>
            Attendances Logs
        </h3>
        <p class="text-sm text-gray-600">User attendance activities (check-in, check-out, absent)</p>
    </div>

    @if($logs && $logs->count() > 0)
        <div class="space-y-4">
            @foreach($logs as $log)
                <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-sky-500">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $log->action == 'checkin' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $log->action == 'checkout' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $log->action == 'absent' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ strtoupper(str_replace('_', ' ', $log->action)) }}
                                </span>
                                <span class="text-sm font-medium text-gray-900">{{ $log->user->name ?? 'Unknown User' }}</span>
                                @if($log->resource_name)
                                    <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full">{{ $log->resource_name }}</span>
                                @endif
                            </div>

                            <p class="text-sm text-gray-700 mb-2">{{ $log->description }}</p>

                            @php
                                $add = is_array($log->additional_data) ? $log->additional_data : (json_decode($log->additional_data, true) ?: []);
                            @endphp

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs text-gray-600">
                                @if(isset($add['schedule_date']))
                                    <div>
                                        <i data-lucide="calendar" class="w-3 h-3 inline mr-1"></i>
                                        Tanggal: {{ \Illuminate\Support\Carbon::parse($add['schedule_date'])->format('d M Y') }}
                                    </div>
                                @endif
                                @if(isset($add['shift_name']))
                                    <div>
                                        <i data-lucide="clock" class="w-3 h-3 inline mr-1"></i>
                                        Shift: {{ $add['shift_name'] }}
                                    </div>
                                @endif
                                @if(isset($add['location_name']))
                                    <div>
                                        <i data-lucide="map-pin" class="w-3 h-3 inline mr-1"></i>
                                        Lokasi: {{ $add['location_name'] }}
                                    </div>
                                @endif
                                @if(isset($add['distance_m']))
                                    <div>
                                        <i data-lucide="navigation" class="w-3 h-3 inline mr-1"></i>
                                        Jarak: {{ $add['distance_m'] }} m
                                    </div>
                                @endif
                            </div>

                            <div class="flex items-center space-x-4 text-xs text-gray-500 mt-2">
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
                            <button onclick="toggleDetails('attendance-{{ $log->id }}')"
                                    class="text-sky-600 hover:text-sky-800 text-sm font-medium">
                                <i data-lucide="eye" class="w-4 h-4 inline mr-1"></i>
                                Details
                            </button>
                        </div>
                    </div>

                    <!-- Details (Hidden by default) -->
                    <div id="attendance-{{ $log->id }}" class="hidden mt-4 pt-4 border-t border-gray-200">
                        @if($log->additional_data)
                            <div class="mb-4">
                                <h5 class="text-sm font-medium text-gray-700 mb-2">Additional Data:</h5>
                                <div class="bg-blue-50 p-3 rounded border">
                                    @if(is_array($log->additional_data))
                                        @foreach($log->additional_data as $key => $value)
                                            <div class="flex justify-between py-1">
                                                <span class="text-xs font-medium text-gray-600">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                                <span class="text-xs text-gray-800">
                                                    @if(is_bool($value))
                                                        {{ $value ? 'Yes' : 'No' }}
                                                    @elseif(is_array($value))
                                                        {{ json_encode($value) }}
                                                    @else
                                                        {{ $value }}
                                                    @endif
                                                </span>
                                            </div>
                                        @endforeach
                                    @else
                                        <pre class="text-xs overflow-x-auto">{{ json_encode($log->additional_data, JSON_PRETTY_PRINT) }}</pre>
                                    @endif
                                </div>
                            </div>
                        @endif

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
            {{ $logs->appends(request()->query())->fragment('attendances-logs')->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <i data-lucide="inbox" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Attendance Logs Found</h3>
            <p class="text-gray-500">No attendance activities have been recorded yet.</p>
        </div>
    @endif
</div>
