<!-- Employee Information -->
<div class="bg-gradient-to-r from-sky-50 to-blue-50 rounded-xl p-6 mb-6 border border-sky-200">
    <div class="flex items-center space-x-4">
        <div class="w-16 h-16 bg-gradient-to-br from-sky-500 to-sky-600 rounded-full flex items-center justify-center shadow-sm">
            <span class="text-lg font-bold text-white">
                {{ strtoupper(substr($leaveRequest->user->name, 0, 2)) }}
            </span>
        </div>
        <div class="flex-1">
            <h3 class="text-lg font-semibold text-gray-900">{{ $leaveRequest->user->name }}</h3>
            <p class="text-sm text-gray-600">{{ $leaveRequest->user->email }}</p>
            <div class="flex items-center space-x-4 mt-2">
                <span class="text-xs text-sky-600 bg-sky-100 px-2 py-1 rounded-full font-medium">
                    {{ $permissions->count() }} schedules requested
                </span>
                <span class="text-xs text-gray-500">
                    Submitted: {{ $leaveRequest->created_at->format('d M Y, H:i') }}
                </span>
            </div>
        </div>
        <div class="text-right">
            @if($leaveRequest->status === 'pending')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-amber-100 text-amber-800">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-clock mr-1">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                    Pending Review
                </span>
            @elseif($leaveRequest->status === 'approved')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-check-circle mr-1">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    Approved
                </span>
            @elseif($leaveRequest->status === 'rejected')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-x-circle mr-1">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="m15 9-6 6"/>
                        <path d="m9 9 6 6"/>
                    </svg>
                    Rejected
                </span>
            @endif
        </div>
    </div>
</div>

<!-- Leave Reason -->
<div class="bg-white border border-gray-200 rounded-xl p-6 mb-6 shadow-sm">
    <h4 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="lucide lucide-message-square text-sky-600 mr-2">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
        </svg>
        Leave Reason
    </h4>
    <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
        <p class="text-gray-700 leading-relaxed">{{ $leaveRequest->reason }}</p>
    </div>
</div>

<!-- Schedules Selection -->
<div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
    <div class="flex items-center justify-between mb-4">
        <h4 class="text-lg font-semibold text-gray-900 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-calendar-days text-sky-600 mr-2">
                <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                <line x1="16" x2="16" y1="2" y2="6"/>
                <line x1="8" x2="8" y1="2" y2="6"/>
                <line x1="3" x2="21" y1="10" y2="10"/>
                <path d="M8 14h.01"/>
                <path d="M12 14h.01"/>
                <path d="M16 14h.01"/>
                <path d="M8 18h.01"/>
                <path d="M12 18h.01"/>
                <path d="M16 18h.01"/>
            </svg>
            Requested Schedules
        </h4>
        @if($leaveRequest->status === 'pending')
            <div class="flex space-x-2">
                <button type="button" onclick="selectAllSchedules()" 
                        class="text-xs px-3 py-1.5 bg-sky-100 text-sky-700 rounded-lg hover:bg-sky-200 transition-colors font-medium">
                    Select All
                </button>
                <button type="button" onclick="clearAllSchedules()" 
                        class="text-xs px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                    Clear All
                </button>
            </div>
        @endif
    </div>

    <form id="schedule-approval-form" action="{{ route('admin.attendances.leave-requests.process', $leaveRequest->id) }}" method="POST">
        @csrf
        <div class="space-y-3 max-h-80 overflow-y-auto">
            @foreach($permissions as $permission)
                <div class="flex items-center space-x-3 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    @if($leaveRequest->status === 'pending')
                        <input type="checkbox" 
                               name="approved_permissions[]" 
                               value="{{ $permission->id }}" 
                               id="permission_{{ $permission->id }}"
                               {{ $permission->status === 'approved' ? 'checked' : '' }}
                               class="w-4 h-4 text-sky-600 border-gray-300 rounded focus:ring-sky-500">
                    @else
                        <div class="w-4 h-4 flex items-center justify-center">
                            @if($permission->status === 'approved')
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="lucide lucide-check-circle text-green-600">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                    <polyline points="22 4 12 14.01 9 11.01"/>
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="lucide lucide-x-circle text-red-600">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="m15 9-6 6"/>
                                    <path d="m9 9 6 6"/>
                                </svg>
                            @endif
                        </div>
                    @endif
                    
                    <label for="permission_{{ $permission->id }}" class="flex-1 cursor-pointer">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-sky-100 to-sky-200 rounded-lg flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="lucide lucide-calendar text-sky-600">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                    <line x1="16" x2="16" y1="2" y2="6"/>
                                    <line x1="8" x2="8" y1="2" y2="6"/>
                                    <line x1="3" x2="21" y1="10" y2="10"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($permission->schedule->schedule_date)->format('l, d F Y') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $permission->schedule->shift->shift_name }} • 
                                    {{ $permission->schedule->shift->start_time }} - {{ $permission->schedule->shift->end_time }}
                                </div>
                            </div>
                            <div class="text-right">
                                @if($permission->status === 'approved')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Approved
                                    </span>
                                @elseif($permission->status === 'rejected')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Rejected
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                        Pending
                                    </span>
                                @endif
                            </div>
                        </div>
                    </label>
                </div>
            @endforeach
        </div>

        @if($leaveRequest->status === 'pending')
            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-100 mt-6">
                <button type="button"
                        onclick="closeLeaveDetailModal()"
                        class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Cancel
                </button>
                
                <button type="button"
                        onclick="processSelectedSchedules('reject')"
                        class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 shadow-sm">
                    <span class="flex items-center space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-x">
                            <path d="M18 6 6 18"/>
                            <path d="m6 6 12 12"/>
                        </svg>
                        <span>Reject All</span>
                    </span>
                </button>
                
                <button type="button"
                        onclick="processSelectedSchedules('approve')"
                        class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 shadow-sm">
                    <span class="flex items-center space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-check">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        <span>Approve Selected</span>
                    </span>
                </button>
            </div>
        @endif
    </form>
</div>

<script>
    function selectAllSchedules() {
        const checkboxes = document.querySelectorAll('input[name="approved_permissions[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
    }

    function clearAllSchedules() {
        const checkboxes = document.querySelectorAll('input[name="approved_permissions[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
    }

    async function processSelectedSchedules(action) {
        const form = document.getElementById('schedule-approval-form');
        const formData = new FormData(form);
        
        if (action === 'approve') {
            const selectedSchedules = formData.getAll('approved_permissions[]');
            if (selectedSchedules.length === 0) {
                alert('Please select at least one schedule to approve.');
                return;
            }
            
            if (!confirm(`Are you sure you want to approve ${selectedSchedules.length} selected schedule(s)?`)) {
                return;
            }
        } else {
            if (!confirm('Are you sure you want to reject this entire leave request?')) {
                return;
            }
        }
        
        try {
            formData.append('action', action);
            
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData
            });
            
            if (response.ok) {
                alert('Leave request processed successfully!');
                closeLeaveDetailModal();
                location.reload();
            } else {
                const errorText = await response.text();
                console.error('Error response:', errorText);
                alert('Failed to process leave request: ' + errorText);
            }
        } catch (error) {
            console.error('Error processing schedules:', error);
            alert('An error occurred while processing the schedules');
        }
    }
</script>