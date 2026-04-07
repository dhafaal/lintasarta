<!-- Employee Information -->
<div class="mb-6 rounded-2xl border-2 border-sky-200 bg-gradient-to-r from-sky-50 to-blue-50 p-6">
    <div class="flex items-center space-x-4">
        <div
            class="flex h-16 w-16 items-center justify-center rounded-xl bg-gradient-to-br from-sky-500 to-sky-600 shadow-lg"
        >
            <span class="text-lg font-bold text-white">
                {{ strtoupper(substr($leaveRequest->user->name, 0, 2)) }}
            </span>
        </div>
        <div class="flex-1">
            <h3 class="text-lg font-semibold text-gray-900">{{ $leaveRequest->user->name }}</h3>
            <p class="text-sm text-gray-600">{{ $leaveRequest->user->email }}</p>
            <div class="mt-2 flex items-center space-x-4">
                <span class="rounded-full bg-sky-100 px-2 py-1 text-xs font-medium text-sky-600">
                    {{ $permissions->count() }} schedules requested
                </span>
                <span class="text-xs text-gray-500">
                    Submitted: {{ $leaveRequest->created_at->format('d M Y, H:i') }}
                </span>
            </div>
        </div>
        <div class="text-right">
            @if ($leaveRequest->status === 'pending')
                <span
                    class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-sm font-medium text-amber-800"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="16"
                        height="16"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="lucide lucide-clock mr-1"
                    >
                        <circle cx="12" cy="12" r="10" />
                        <polyline points="12 6 12 12 16 14" />
                    </svg>
                    Pending Review
                </span>
            @elseif ($leaveRequest->status === 'approved')
                <span
                    class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-800"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="16"
                        height="16"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="lucide lucide-check-circle mr-1"
                    >
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                        <polyline points="22 4 12 14.01 9 11.01" />
                    </svg>
                    Approved
                </span>
            @elseif ($leaveRequest->status === 'rejected')
                <span
                    class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-sm font-medium text-red-800"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="16"
                        height="16"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="lucide lucide-x-circle mr-1"
                    >
                        <circle cx="12" cy="12" r="10" />
                        <path d="m15 9-6 6" />
                        <path d="m9 9 6 6" />
                    </svg>
                    Rejected
                </span>
            @endif
        </div>
    </div>
</div>

<!-- Leave Reason -->
<div class="mb-6 rounded-2xl border-2 border-gray-200 bg-white p-6 shadow-lg">
    <h4 class="mb-3 flex items-center text-lg font-bold text-gray-900">
        <svg
            xmlns="http://www.w3.org/2000/svg"
            width="20"
            height="20"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
            class="lucide lucide-message-square mr-2 text-sky-600"
        >
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
        </svg>
        Leave Reason
    </h4>
    <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
        <p class="leading-relaxed text-gray-700">{{ $leaveRequest->reason }}</p>
    </div>
</div>

@if ($permissions->first()->admin_note)
<!-- Admin Note -->
<div class="mb-6 rounded-2xl border-2 border-sky-200 bg-sky-50 p-6 shadow-lg">
    <h4 class="mb-3 flex items-center text-lg font-bold text-sky-900">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clipboard-edit mr-2 text-sky-600">
          <rect width="8" height="4" x="8" y="2" rx="1" ry="1"/>
          <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
          <path d="M10.42 12.61a2.1 2.1 0 1 1 2.97 2.97L7.95 21 4 22l.99-3.95 5.43-5.44Z"/>
        </svg>
        Admin Note
    </h4>
    <div class="rounded-xl border border-sky-100 bg-white p-4">
        <p class="leading-relaxed text-sky-800">{{ $permissions->first()->admin_note }}</p>
    </div>
</div>
@endif

<!-- Schedules Selection -->
<div class="rounded-2xl border-2 border-gray-200 bg-white p-6 shadow-lg">
    <div class="mb-4 flex items-center justify-between">
        <h4 class="flex items-center text-lg font-bold text-gray-900">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                class="lucide lucide-calendar-days mr-2 text-sky-600"
            >
                <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                <line x1="16" x2="16" y1="2" y2="6" />
                <line x1="8" x2="8" y1="2" y2="6" />
                <line x1="3" x2="21" y1="10" y2="10" />
                <path d="M8 14h.01" />
                <path d="M12 14h.01" />
                <path d="M16 14h.01" />
                <path d="M8 18h.01" />
                <path d="M12 18h.01" />
                <path d="M16 18h.01" />
            </svg>
            Requested Schedules
        </h4>
        @if ($leaveRequest->status === 'pending')
            <div class="flex space-x-2">
                <button
                    type="button"
                    onclick="document.querySelectorAll('input[name=\'approved_permissions[]\']').forEach(cb=>cb.checked=true)"
                    class="rounded-lg bg-sky-100 px-3 py-1.5 text-xs font-medium text-sky-700 transition-colors hover:bg-sky-200"
                >
                    Select All
                </button>
                <button
                    type="button"
                    onclick="document.querySelectorAll('input[name=\'approved_permissions[]\']').forEach(cb=>cb.checked=false)"
                    class="rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-700 transition-colors hover:bg-gray-200"
                >
                    Clear All
                </button>
            </div>
        @endif
    </div>

    <form
        id="schedule-approval-form"
        action="{{ route('admin.attendances.leave-requests.process', $leaveRequest->id) }}"
        method="POST"
        onsubmit="return validateLeaveFormSubmit(event)"
    >
        @csrf
        <input type="hidden" name="action" id="lr-action" value="" />
        <div class="max-h-80 space-y-3 overflow-y-auto">
            @foreach ($permissions as $permission)
                <div
                    class="flex items-center space-x-3 rounded-xl border-2 border-gray-200 p-4 transition-colors hover:bg-sky-50"
                >
                    @if ($leaveRequest->status === 'pending')
                        <input
                            type="checkbox"
                            name="approved_permissions[]"
                            value="{{ $permission->id }}"
                            id="permission_{{ $permission->id }}"
                            {{ $permission->status === 'approved' ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-gray-300 text-sky-600 focus:ring-sky-500"
                        />
                    @else
                        <div class="flex h-4 w-4 items-center justify-center">
                            @if ($permission->status === 'approved')
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="16"
                                    height="16"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="lucide lucide-check-circle text-green-600"
                                >
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                    <polyline points="22 4 12 14.01 9 11.01" />
                                </svg>
                            @else
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="16"
                                    height="16"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="lucide lucide-x-circle text-red-600"
                                >
                                    <circle cx="12" cy="12" r="10" />
                                    <path d="m15 9-6 6" />
                                    <path d="m9 9 6 6" />
                                </svg>
                            @endif
                        </div>
                    @endif

                    <label
                        for="permission_{{ $permission->id }}"
                        class="{{ $leaveRequest->status === 'pending' ? 'cursor-pointer' : '' }} flex-1"
                    >
                        <div class="flex items-center space-x-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-sky-100 to-sky-200 shadow-sm"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="16"
                                    height="16"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="lucide lucide-calendar text-sky-600"
                                >
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                                    <line x1="16" x2="16" y1="2" y2="6" />
                                    <line x1="8" x2="8" y1="2" y2="6" />
                                    <line x1="3" x2="21" y1="10" y2="10" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ \Carbon\Carbon::parse($permission->schedule->schedule_date)->format('l, d F Y') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $permission->schedule->shift->shift_name }} •
                                    {{ $permission->schedule->shift->start_time }} -
                                    {{ $permission->schedule->shift->end_time }}
                                </div>
                            </div>
                            <div class="text-right">
                                @if ($permission->status === 'approved')
                                    <span
                                        class="inline-flex items-center rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800"
                                    >
                                        Approved
                                    </span>
                                @elseif ($permission->status === 'rejected')
                                    <span
                                        class="inline-flex items-center rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-800"
                                    >
                                        Rejected
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center rounded-full bg-amber-100 px-2 py-1 text-xs font-medium text-amber-800"
                                    >
                                        Pending
                                    </span>
                                @endif
                            </div>
                        </div>
                    </label>
                </div>
            @endforeach
        </div>

        @if ($leaveRequest->status === 'pending')
            <!-- Admin Note Input -->
            <div class="mt-6 border-t-2 border-gray-100 pt-6">
                <label for="admin_note_{{ $leaveRequest->id }}" class="block text-sm font-semibold text-gray-900 mb-2">Admin Note <span class="text-red-500">*</span></label>
                <textarea
                    id="admin_note_{{ $leaveRequest->id }}"
                    name="admin_note"
                    rows="3"
                    class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 sm:text-sm p-3"
                    placeholder="Catatan wajib diisi (minimal 5 karakter)..."
                    required minlength="5"
                ></textarea>
                <p class="mt-2 text-xs text-red-500 hidden" id="admin_note_error_{{ $leaveRequest->id }}">Catatan wajib diisi (min 5 karakter).</p>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 flex justify-end space-x-3 pt-4">
                <button
                    type="button"
                    onclick="closeLeaveDetailModal()"
                    class="rounded-xl bg-gray-100 px-6 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition-all hover:bg-gray-200 focus:ring-2 focus:ring-gray-300 focus:outline-none"
                >
                    Cancel
                </button>

                <button
                    type="button"
                    onclick="(function(){
                        const f=document.getElementById('schedule-approval-form');
                        document.getElementById('lr-action').value='reject'; 
                        
                        const noteInput = document.getElementById('admin_note_{{ $leaveRequest->id }}');
                        const errorMsg = document.getElementById('admin_note_error_{{ $leaveRequest->id }}');
                        if (!noteInput.value.trim() || noteInput.value.trim().length < 5) {
                            errorMsg.classList.remove('hidden');
                            noteInput.classList.add('border-red-300', 'focus:border-red-500', 'focus:ring-red-500');
                            noteInput.focus();
                            return;
                        } else {
                            errorMsg.classList.add('hidden');
                            noteInput.classList.remove('border-red-300', 'focus:border-red-500', 'focus:ring-red-500');
                        }

                        if(confirm('Reject ALL schedules in this leave request?')) f.submit();
                    })();"
                    class="rounded-xl bg-red-600 px-6 py-2.5 text-sm font-semibold text-white shadow-lg transition-all hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:outline-none"
                >
                    <span class="flex items-center space-x-2">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="16"
                            height="16"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="lucide lucide-x"
                        >
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                        <span>Reject All</span>
                    </span>
                </button>

                <button
                    type="button"
                    onclick="(function(){
                            const f=document.getElementById('schedule-approval-form');
                            document.getElementById('lr-action').value='approve';
                            const selected=[...document.querySelectorAll('input[name=\'approved_permissions[]\']:checked')];
                            if(selected.length===0){ alert('Please select at least one schedule to approve.'); return; }
                            
                            const noteInput = document.getElementById('admin_note_{{ $leaveRequest->id }}');
                            const errorMsg = document.getElementById('admin_note_error_{{ $leaveRequest->id }}');
                            if (!noteInput.value.trim() || noteInput.value.trim().length < 5) {
                                errorMsg.classList.remove('hidden');
                                noteInput.classList.add('border-red-300', 'focus:border-red-500', 'focus:ring-red-500');
                                noteInput.focus();
                                return;
                            } else {
                                errorMsg.classList.add('hidden');
                                noteInput.classList.remove('border-red-300', 'focus:border-red-500', 'focus:ring-red-500');
                            }

                            if(confirm(`Approve ${selected.length} selected schedule(s)?`)) f.submit();
                        })();"
                    class="rounded-xl bg-green-600 px-6 py-2.5 text-sm font-semibold text-white shadow-lg transition-all hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:outline-none"
                >
                    <span class="flex items-center space-x-2">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="16"
                            height="16"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="lucide lucide-check"
                        >
                            <polyline points="20 6 9 17 4 12" />
                        </svg>
                        <span>Approve Selected</span>
                    </span>
                </button>
            </div>
        @endif
    </form>
</div>

<script>
    // Initialize Lucide icons after content is loaded
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    function validateLeaveFormSubmit(e) {
        // Kept for safety if form submit used elsewhere
        const actionInput = document.getElementById('lr-action');
        const action = actionInput ? actionInput.value : '';
        const noteInput = e.target.querySelector('textarea[name="admin_note"]');
        const errorMsg = e.target.querySelector('p[id^="admin_note_error_"]');

        if (noteInput && (!noteInput.value.trim() || noteInput.value.trim().length < 5)) {
            if (errorMsg) errorMsg.classList.remove('hidden');
            if (noteInput) {
                noteInput.classList.add('border-red-300', 'focus:border-red-500', 'focus:ring-red-500');
                noteInput.focus();
            }
            e.preventDefault();
            return false;
        }

        if (action === 'approve') {
            const selected = Array.from(document.querySelectorAll('input[name="approved_permissions[]"]:checked'));
            if (selected.length === 0) {
                alert('Please select at least one schedule to approve.');
                e.preventDefault();
                return false;
            }
            return confirm(`Approve ${selected.length} selected schedule(s)?`);
        }
        if (action === 'reject') {
            return confirm('Reject ALL schedules in this leave request?');
        }
        return true;
    }
</script>
