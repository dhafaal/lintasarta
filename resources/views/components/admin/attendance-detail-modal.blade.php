@props(['route'])

{{-- Attendance Detail Modal --}}
<div id="attendance-detail-modal" class="fixed inset-0 bg-black/50 hidden z-50 p-4" style="display: none;">
    <div class="bg-white rounded-2xl max-w-5xl w-full max-h-[90vh] overflow-y-auto shadow-2xl mx-auto my-8">
        <div class="sticky top-0 bg-white border-b-2 border-gray-100 p-6 rounded-t-2xl z-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div id="modal-icon" class="p-3 rounded-xl"></div>
                    <div>
                        <h3 id="modal-title" class="text-xl font-bold text-gray-900"></h3>
                        <p class="text-sm text-gray-600 mt-1">{{ now()->format('l, d F Y') }}</p>
                    </div>
                </div>
                <button type="button" onclick="closeAttendanceModal()" class="text-gray-400 hover:text-gray-600 p-2 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18"/>
                        <path d="m6 6 12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        <div id="modal-content" class="p-6">
            <div class="flex items-center justify-center py-12">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-sky-600"></div>
            </div>
        </div>
    </div>
</div>

<script>
// Attendance Detail Modal Functions
function showAttendanceDetail(status) {
    const modal = document.getElementById('attendance-detail-modal');
    const modalTitle = document.getElementById('modal-title');
    const modalIcon = document.getElementById('modal-icon');
    const modalContent = document.getElementById('modal-content');
    
    // Set title and icon based on status
    const statusConfig = {
        'all': {
            title: 'All Schedules',
            icon: '<i data-lucide="calendar" class="w-6 h-6 text-gray-600"></i>',
            bgColor: 'bg-gray-100'
        },
        'hadir': {
            title: 'Hadir',
            icon: '<i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>',
            bgColor: 'bg-green-100'
        },
        'telat': {
            title: 'Telat',
            icon: '<i data-lucide="clock-alert" class="w-6 h-6 text-orange-600"></i>',
            bgColor: 'bg-orange-100'
        },
        'izin': {
            title: 'Izin',
            icon: '<i data-lucide="clock" class="w-6 h-6 text-yellow-600"></i>',
            bgColor: 'bg-yellow-100'
        },
        'alpha': {
            title: 'Alpha',
            icon: '<i data-lucide="x-circle" class="w-6 h-6 text-red-600"></i>',
            bgColor: 'bg-red-100'
        }
    };
    
    const config = statusConfig[status];
    modalTitle.textContent = config.title;
    modalIcon.innerHTML = config.icon;
    modalIcon.className = `p-3 rounded-xl ${config.bgColor}`;
    
    // Show loading
    modalContent.innerHTML = `
        <div class="flex items-center justify-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-sky-600"></div>
        </div>
    `;
    
    // Show modal
    modal.classList.remove('hidden');
    modal.style.display = 'flex';
    modal.style.alignItems = 'center';
    modal.style.justifyContent = 'center';
    
    // Fetch data
    fetch(`{{ $route }}?status=${status}`)
        .then(response => response.json())
        .then(data => {
            renderAttendanceDetail(data);
            // Reinitialize lucide icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        })
        .catch(error => {
            modalContent.innerHTML = `
                <div class="text-center py-12">
                    <i data-lucide="alert-circle" class="w-12 h-12 text-red-500 mx-auto mb-4"></i>
                    <p class="text-gray-600">Failed to load attendance details</p>
                </div>
            `;
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
}

function renderAttendanceDetail(response) {
    const modalContent = document.getElementById('modal-content');
    const data = response.data;
    
    if (data.length === 0) {
        modalContent.innerHTML = `
            <div class="text-center py-12">
                <i data-lucide="inbox" class="w-16 h-16 text-gray-400 mx-auto mb-4"></i>
                <p class="text-gray-600 text-lg font-medium">No data available</p>
            </div>
        `;
        return;
    }
    
    let html = '<div class="space-y-6">';
    
    data.forEach(shift => {
        const statusColors = {
            'hadir': 'bg-green-50 border-green-200 text-green-700',
            'telat': 'bg-orange-50 border-orange-200 text-orange-700',
            'izin': 'bg-yellow-50 border-yellow-200 text-yellow-700',
            'alpha': 'bg-red-50 border-red-200 text-red-700'
        };
        
        const checkoutStatusColors = {
            'early': 'bg-amber-50 border-amber-200 text-amber-700',
            
        };
        
        html += `
            <div class="bg-gradient-to-r from-sky-50 to-blue-50 rounded-2xl p-6 border-2 border-sky-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-sky-100 rounded-lg">
                            <i data-lucide="clock" class="w-5 h-5 text-sky-600"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-gray-900">${shift.shift_name}</h4>
                            <p class="text-sm text-gray-600">${shift.shift_start} - ${shift.shift_end}</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 bg-sky-100 text-sky-700 rounded-full text-sm font-semibold">
                        ${shift.employees.length} Employee${shift.employees.length > 1 ? 's' : ''}
                    </span>
                </div>
                <div class="space-y-2">
        `;
        
        shift.employees.forEach(employee => {
            const statusClass = statusColors[employee.status] || 'bg-gray-50 border-gray-200 text-gray-700';
            const statusLabel = employee.status.charAt(0).toUpperCase() + employee.status.slice(1);
            
            // Determine checkout status
            let checkoutStatusBadge = '';
            if (employee.checkout_status) {
                const checkoutClass = checkoutStatusColors[employee.checkout_status] || 'bg-gray-50 border-gray-200 text-gray-700';
                const checkoutLabel = employee.checkout_status === 'early' ? 'Early Checkout' : 'Forgot Checkout';
                checkoutStatusBadge = `
                    <span class="px-2 py-1 ${checkoutClass} border rounded-full text-xs font-semibold ml-2">
                        ${checkoutLabel}
                    </span>
                `;
            }
            
            html += `
                <div class="bg-white rounded-xl p-4 border-2 border-gray-100 hover:border-sky-200 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3 flex-1">
                            <div class="w-10 h-10 bg-gradient-to-br from-sky-500 to-sky-600 rounded-full flex items-center justify-center shadow-sm">
                                <span class="text-sm font-bold text-white">${employee.name.substring(0, 2).toUpperCase()}</span>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900">${employee.name}</p>
                                <div class="flex items-center space-x-4 mt-1 text-xs text-gray-600">
            `;
            
            if (employee.check_in) {
                html += `
                    <span class="flex items-center space-x-1">
                        <i data-lucide="log-in" class="w-3 h-3"></i>
                        <span>In: ${employee.check_in}</span>
                    </span>
                `;
            }
            
            if (employee.check_out) {
                html += `
                    <span class="flex items-center space-x-1">
                        <i data-lucide="log-out" class="w-3 h-3"></i>
                        <span>Out: ${employee.check_out}</span>
                    </span>
                `;
            }
            
            if (!employee.check_in && !employee.check_out) {
                html += '<span class="text-gray-400">No attendance record</span>';
            }
            
            html += `
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <span class="px-3 py-1 ${statusClass} border rounded-full text-xs font-semibold">
                                ${statusLabel}
                            </span>
                            ${checkoutStatusBadge}
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += `
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    modalContent.innerHTML = html;
}

function closeAttendanceModal() {
    const modal = document.getElementById('attendance-detail-modal');
    modal.classList.add('hidden');
    modal.style.display = 'none';
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAttendanceModal();
    }
});

// Close modal on backdrop click
document.getElementById('attendance-detail-modal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeAttendanceModal();
    }
});
</script>
