@extends('layouts.admin')

@section('title', 'Leave Requests Management')

@section('content')
<div class="min-h-screen bg-white">
    {{-- Header Section --}}
    <div class="bg-whitepx-6 py-4">
        <div class="max-w-[1600px] mx-auto">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-calendar-x text-sky-600">
                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                            <line x1="16" x2="16" y1="2" y2="6"/>
                            <line x1="8" x2="8" y1="2" y2="6"/>
                            <line x1="3" x2="21" y1="10" y2="10"/>
                            <path d="m14 14-4 4"/>
                            <path d="m10 14 4 4"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Leave Requests Management</h1>
                        <p class="text-sm text-gray-500">Review and manage employee leave requests</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm font-medium text-gray-700">{{ now()->format('l, d F Y') }}</div>
                    <div class="text-xs text-gray-500">Total Requests: {{ $leaveRequests->count() }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="max-w-[1600px] mx-auto px-6 py-6">
        {{-- Success/Error Messages --}}
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-check-circle text-green-600 mr-3">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    <p class="text-green-700 font-medium">{{ session('success') }}</p>
                    <button type="button" class="ml-auto text-green-500 hover:text-green-700" onclick="this.closest('.mb-6').style.display='none';">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-x">
                            <path d="M18 6 6 18"/>
                            <path d="m6 6 12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-alert-circle text-red-600 mr-3">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" x2="12" y1="8" y2="12"/>
                        <line x1="12" x2="12.01" y1="16" y2="16"/>
                    </svg>
                    <p class="text-red-700 font-medium">{{ session('error') }}</p>
                    <button type="button" class="ml-auto text-red-500 hover:text-red-700" onclick="this.closest('.mb-6').style.display='none';">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-x">
                            <path d="M18 6 6 18"/>
                            <path d="m6 6 12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        {{-- Filter Tabs --}}
        <div class="mb-6">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.attendances.leave-requests') }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ !request('status') ? 'bg-sky-600 text-white shadow-sm' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">
                    All Requests
                </a>
                <a href="{{ route('admin.attendances.leave-requests', ['status' => 'pending']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('status') === 'pending' ? 'bg-amber-600 text-white shadow-sm' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">
                    Pending
                </a>
                <a href="{{ route('admin.attendances.leave-requests', ['status' => 'approved']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('status') === 'approved' ? 'bg-green-600 text-white shadow-sm' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">
                    Approved
                </a>
                <a href="{{ route('admin.attendances.leave-requests', ['status' => 'rejected']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('status') === 'rejected' ? 'bg-red-600 text-white shadow-sm' : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-200' }}">
                    Rejected
                </a>
            </div>
        </div>

        {{-- Leave Requests Table --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-sky-50 to-blue-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Employee</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Total Schedules</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Date Range</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Reason</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Submitted</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($leaveRequests as $request)
                            <tr class="hover:bg-gray-50 transition-colors">
                                {{-- Employee --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-br from-sky-100 to-sky-200 rounded-full flex items-center justify-center mr-3">
                                            <span class="text-sm font-semibold text-sky-700">
                                                {{ strtoupper(substr($request->user->name, 0, 2)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $request->user->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $request->user->email }}</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Total Schedules --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-gradient-to-br from-sky-100 to-sky-200 rounded-lg flex items-center justify-center mr-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="lucide lucide-calendar text-sky-600">
                                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                                <line x1="16" x2="16" y1="2" y2="6"/>
                                                <line x1="8" x2="8" y1="2" y2="6"/>
                                                <line x1="3" x2="21" y1="10" y2="10"/>
                                            </svg>
                                        </div>
                                        <span class="text-sm font-semibold text-gray-900">{{ $request->schedules_count }}</span>
                                        <span class="text-xs text-gray-500 ml-1">schedules</span>
                                    </div>
                                </td>

                                {{-- Date Range --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $request->date_range }}</div>
                                </td>

                                {{-- Reason --}}
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $request->reason }}">
                                        {{ $request->reason }}
                                    </div>
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($request->status === 'pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="lucide lucide-clock mr-1">
                                                <circle cx="12" cy="12" r="10"/>
                                                <polyline points="12 6 12 12 16 14"/>
                                            </svg>
                                            Pending
                                        </span>
                                    @elseif($request->status === 'approved')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="lucide lucide-check-circle mr-1">
                                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                                <polyline points="22 4 12 14.01 9 11.01"/>
                                            </svg>
                                            Approved
                                        </span>
                                    @elseif($request->status === 'rejected')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="lucide lucide-x-circle mr-1">
                                                <circle cx="12" cy="12" r="10"/>
                                                <path d="m15 9-6 6"/>
                                                <path d="m9 9 6 6"/>
                                            </svg>
                                            Rejected
                                        </span>
                                    @endif
                                </td>

                                {{-- Submitted --}}
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $request->created_at->format('d M Y') }}
                                    <div class="text-xs text-gray-400">{{ $request->created_at->format('H:i') }}</div>
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        <button type="button"
                                                onclick="viewLeaveRequest({{ $request->id }})"
                                                class="inline-flex items-center px-3 py-1.5 bg-sky-100 hover:bg-sky-200 text-sky-700 text-xs font-medium rounded-lg transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="lucide lucide-eye mr-1">
                                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                            View
                                        </button>
                                        
                                        @if($request->status === 'pending')
                                            <button type="button"
                                                    onclick="processLeaveRequest({{ $request->id }}, 'approve')"
                                                    class="inline-flex items-center px-3 py-1.5 bg-green-100 hover:bg-green-200 text-green-700 text-xs font-medium rounded-lg transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="lucide lucide-check mr-1">
                                                    <polyline points="20 6 9 17 4 12"/>
                                                </svg>
                                                Approve
                                            </button>
                                            
                                            <button type="button"
                                                    onclick="processLeaveRequest({{ $request->id }}, 'reject')"
                                                    class="inline-flex items-center px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 text-xs font-medium rounded-lg transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="lucide lucide-x mr-1">
                                                    <path d="M18 6 6 18"/>
                                                    <path d="m6 6 12 12"/>
                                                </svg>
                                                Reject
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="lucide lucide-calendar-x text-gray-400">
                                                <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                                                <line x1="16" x2="16" y1="2" y2="6"/>
                                                <line x1="8" x2="8" y1="2" y2="6"/>
                                                <line x1="3" x2="21" y1="10" y2="10"/>
                                                <path d="m14 14-4 4"/>
                                                <path d="m10 14 4 4"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-sm font-medium text-gray-900 mb-1">No leave requests found</h3>
                                        <p class="text-sm text-gray-500">There are no leave requests to display.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if($leaveRequests->hasPages())
            <div class="mt-6">
                {{ $leaveRequests->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Leave Request Detail Modal --}}
<div id="leave-detail-modal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl relative transform transition-all border border-gray-100 max-h-[90vh] overflow-y-auto">
        {{-- Modal Header --}}
        <div class="flex items-center justify-between p-6 border-b border-gray-100 bg-gradient-to-r from-sky-50 to-blue-50 rounded-t-2xl sticky top-0 z-10">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-gradient-to-br from-sky-500 to-sky-600 rounded-xl flex items-center justify-center shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-calendar-x text-white">
                        <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                        <line x1="16" x2="16" y1="2" y2="6"/>
                        <line x1="8" x2="8" y1="2" y2="6"/>
                        <line x1="3" x2="21" y1="10" y2="10"/>
                        <path d="m14 14-4 4"/>
                        <path d="m10 14 4 4"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Leave Request Details</h2>
                    <p class="text-sm text-sky-600">Review and manage leave request</p>
                </div>
            </div>
            <button type="button"
                    onclick="closeLeaveDetailModal()"
                    class="w-10 h-10 flex items-center justify-center rounded-xl text-gray-400 hover:text-gray-600 hover:bg-white/50 transition-all duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-x">
                    <path d="M18 6 6 18"/>
                    <path d="m6 6 12 12"/>
                </svg>
            </button>
        </div>

        {{-- Modal Content --}}
        <div id="modal-content" class="p-6">
            {{-- Content will be loaded here --}}
        </div>
    </div>
</div>

<script>
    async function viewLeaveRequest(requestId) {
        const modal = document.getElementById('leave-detail-modal');
        const content = document.getElementById('modal-content');
        
        try {
            content.innerHTML = `
                <div class="text-center py-8">
                    <div class="inline-flex items-center space-x-2 text-gray-500">
                        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-sky-600"></div>
                        <span>Loading leave request details...</span>
                    </div>
                </div>
            `;
            modal.classList.remove('hidden');
            
            const response = await fetch(`/admin/attendances/leave-requests/${requestId}`, {
                headers: {
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) {
                throw new Error('Failed to load leave request details');
            }
            
            const html = await response.text();
            content.innerHTML = html;
            
        } catch (error) {
            console.error('Error loading leave request:', error);
            content.innerHTML = `
                <div class="text-center py-8">
                    <div class="text-red-500">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-alert-circle mx-auto mb-2">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" x2="12" y1="8" y2="12"/>
                            <line x1="12" x2="12.01" y1="16" y2="16"/>
                        </svg>
                        <p>Failed to load leave request details</p>
                    </div>
                </div>
            `;
        }
    }
    
    function closeLeaveDetailModal() {
        document.getElementById('leave-detail-modal').classList.add('hidden');
    }
    
    async function processLeaveRequest(requestId, action) {
        if (!confirm(`Are you sure you want to ${action} this leave request?`)) {
            return;
        }
        
        try {
            const response = await fetch(`/admin/attendances/leave-requests/${requestId}/process-simple`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ action: action })
            });
            
            if (response.ok) {
                const result = await response.json();
                alert(result.message);
                location.reload();
            } else {
                const error = await response.json();
                alert(error.message || 'Failed to process leave request');
            }
        } catch (error) {
            console.error('Error processing leave request:', error);
            alert('An error occurred while processing the request');
        }
    }
    
    document.getElementById('leave-detail-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeLeaveDetailModal();
        }
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeLeaveDetailModal();
        }
    });
</script>
@endsection