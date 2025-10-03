<!-- Employee Information -->
<div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-xl p-6 mb-6">
    <div class="flex items-center space-x-4">
        <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center">
            <span class="text-lg font-bold text-white">
                {{ strtoupper(substr($leaveRequest->user->name, 0, 2)) }}
            </span>
        </div>
        <div class="flex-1">
            <h3 class="text-lg font-semibold text-gray-900">{{ $leaveRequest->user->name }}</h3>
            <p class="text-sm text-gray-600">{{ $leaveRequest->user->email }}</p>
            <div class="flex items-center space-x-4 mt-2">
                <span class="text-xs text-purple-600 bg-purple-100 px-2 py-1 rounded-full">
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
                    <i data-lucide="clock" class="w-4 h-4 mr-1"></i>
                    Pending Review
                </span>
            @elseif($leaveRequest->status === 'approved')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    <i data-lucide="check-circle" class="w-4 h-4 mr-1"></i>
                    Approved
                </span>
            @elseif($leaveRequest->status === 'rejected')
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                    <i data-lucide="x-circle" class="w-4 h-4 mr-1"></i>
                    Rejected
                </span>
            @endif
        </div>
    </div>
</div>

<!-- Leave Reason -->
<div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
    <h4 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
        <i data-lucide="message-square" class="w-5 h-5 text-purple-600 mr-2"></i>
        Leave Reason
    </h4>
    <div class="bg-gray-50 rounded-lg p-4">
        <p class="text-gray-700 leading-relaxed">{{ $leaveRequest->reason }}</p>
    </div>
</div>

<!-- Schedules Selection -->
<div class="bg-white border border-gray-200 rounded-xl p-6">
    <div class="flex items-center justify-between mb-4">
        <h4 class="text-lg font-semibold text-gray-900 flex items-center">
            <i data-lucide="calendar-days" class="w-5 h-5 text-purple-600 mr-2"></i>
            Requested Schedules
        </h4>
        @if($leaveRequest->status === 'pending')
            <div class="flex space-x-2">
                <button type="button" onclick="selectAllSchedules()" 
                        class="text-xs px-3 py-1 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition-colors">
                    Select All
                </button>
                <button type="button" onclick="clearAllSchedules()" 
                        class="text-xs px-3 py-1 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
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
                               class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                    @else
                        <div class="w-4 h-4 flex items-center justify-center">
                            @if($permission->status === 'approved')
                                <i data-lucide="check-circle" class="w-4 h-4 text-green-600"></i>
                            @else
                                <i data-lucide="x-circle" class="w-4 h-4 text-red-600"></i>
                            @endif
                        </div>
                    @endif
                    
                    <label for="permission_{{ $permission->id }}" class="flex-1 cursor-pointer">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg flex items-center justify-center">
                                <i data-lucide="calendar" class="w-4 h-4 text-blue-600"></i>
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
                        <i data-lucide="x" class="w-4 h-4"></i>
                        <span>Reject All</span>
                    </span>
                </button>
                
                <button type="button"
                        onclick="processSelectedSchedules('approve')"
                        class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 shadow-sm">
                    <span class="flex items-center space-x-2">
                        <i data-lucide="check" class="w-4 h-4"></i>
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
                // Show success message and close modal
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
