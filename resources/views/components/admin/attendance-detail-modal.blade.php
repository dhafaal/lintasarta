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
            
            {{-- Filter Section --}}
            <div id="filter-section" class="mt-4 space-y-3" style="display: none;">
                {{-- Search Bar --}}
                <div class="relative">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                    <input type="text" id="search-employee" placeholder="Cari nama karyawan..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm">
                </div>
                
                {{-- Filters Row --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    {{-- Category Filter --}}
                    <div class="relative">
                        <i data-lucide="clock" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                        <select id="filter-category" 
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm appearance-none bg-white">
                            <option value="">Semua Kategori</option>
                            <option value="Pagi">Pagi</option>
                            <option value="Siang">Siang</option>
                            <option value="Malam">Malam</option>
                        </select>
                        <i data-lucide="chevron-down" class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
                    </div>
                    
                    {{-- Shift Name Filter with Search --}}
                    <div class="relative">
                        <i data-lucide="layers" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 z-10"></i>
                        <input type="text" id="filter-shift-search" placeholder="Cari atau pilih nama shift..." 
                               class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 text-sm"
                               autocomplete="off">
                        <i data-lucide="chevron-down" class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
                        
                        {{-- Shift Dropdown --}}
                        <div id="shift-dropdown" class="absolute top-full left-0 right-0 mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-48 overflow-y-auto z-20 hidden">
                            <div id="shift-options" class="py-1">
                                {{-- Options will be populated dynamically --}}
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Reset Filter Button --}}
                <div class="flex justify-end">
                    <button type="button" id="reset-filters" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors flex items-center space-x-2">
                        <i data-lucide="x" class="w-4 h-4"></i>
                        <span>Reset Filter</span>
                    </button>
                </div>
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
// Global variables for filtering
let originalAttendanceData = null;
let allShiftNames = new Set();
let employeeShiftSummary = new Map();

// Status configuration with improved styling
const statusConfig = {
    'hadir': {
        label: 'Hadir',
        class: 'bg-green-100 border-green-200 text-green-800',
        icon: 'check-circle',
        iconClass: 'text-green-600'
    },
    'telat': {
        label: 'Telat',
        class: 'bg-orange-100 border-orange-200 text-orange-800',
        icon: 'clock-alert',
        iconClass: 'text-orange-600'
    },
    'izin': {
        label: 'Izin',
        class: 'bg-yellow-100 border-yellow-200 text-yellow-800',
        icon: 'clock',
        iconClass: 'text-yellow-600'
    },
    'alpha': {
        label: 'Alpha',
        class: 'bg-red-100 border-red-200 text-red-800',
        icon: 'x-circle',
        iconClass: 'text-red-600'
    },
    'early_checkout': {
        label: 'Early Checkout',
        class: 'bg-amber-100 border-amber-200 text-amber-800',
        icon: 'log-out',
        iconClass: 'text-amber-600'
    },
    'forgot_checkout': {
        label: 'Forgot Checkout',
        class: 'bg-rose-100 border-rose-200 text-rose-800',
        icon: 'alert-circle',
        iconClass: 'text-rose-600'
    }
};

// Badge configuration for additional statuses
const badgeConfig = {
    'early_checkout': {
        label: 'Early Checkout',
        class: 'bg-amber-50 border-amber-200 text-amber-700',
        icon: 'log-out',
        iconClass: 'text-amber-500'
    },
    'forgot_checkout': {
        label: 'Forgot Checkout',
        class: 'bg-rose-50 border-rose-200 text-rose-700',
        icon: 'alert-circle',
        iconClass: 'text-rose-500'
    },
    'double_shift': {
        label: 'Double Shift',
        class: 'bg-sky-50 border-sky-200 text-sky-700',
        icon: 'layers',
        iconClass: 'text-sky-500'
    },
    'long_shift': {
        label: 'Long Shift',
        class: 'bg-purple-50 border-purple-200 text-purple-700',
        icon: 'clock',
        iconClass: 'text-purple-500'
    }
};

// Attendance Detail Modal Functions
function showAttendanceDetail(status) {
    const modal = document.getElementById('attendance-detail-modal');
    const modalTitle = document.getElementById('modal-title');
    const modalIcon = document.getElementById('modal-icon');
    const modalContent = document.getElementById('modal-content');
    
    // Set title and icon based on status
    const modalStatusConfig = {
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
        },
        'early_checkout': {
            title: 'Early Checkout',
            icon: '<i data-lucide="log-out" class="w-6 h-6 text-amber-600"></i>',
            bgColor: 'bg-amber-100'
        },
        'forgot_checkout': {
            title: 'Forgot Checkout',
            icon: '<i data-lucide="alert-circle" class="w-6 h-6 text-rose-600"></i>',
            bgColor: 'bg-rose-100'
        }
    };
    
    const config = modalStatusConfig[status];
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
            originalAttendanceData = data;
            
            // Collect all shift names
            allShiftNames.clear();
            data.data.forEach(shift => {
                shift.employees.forEach(emp => {
                    allShiftNames.add(emp.shift_name);
                });
            });

            // Build per-employee shift summary across categories
            buildEmployeeShiftSummary(data.data);
            
            // Populate shift dropdown
            populateShiftDropdown();
            
            // Show filter section
            document.getElementById('filter-section').style.display = 'block';
            
            // Reset filters
            resetFilters();
            
            renderAttendanceDetail(data);
            
            // Initialize filter event listeners
            initializeFilters();
            
            // Reinitialize lucide icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        })
        .catch(error => {
            console.error('Error fetching attendance data:', error);
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

const permissionAttachmentUrlTemplate = "{{ route('admin.permissions.attachment', ['permission' => '__PERMISSION_ID__']) }}";

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
    
    const renderedEmployees = new Set();
    data.forEach(shift => {
        html += `
            <div class="bg-gradient-to-r from-sky-50 to-blue-50 rounded-2xl p-6 border-2 border-sky-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-sky-100 rounded-lg">
                            <i data-lucide="clock" class="w-5 h-5 text-sky-600"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-bold text-gray-900">${shift.category}</h4>
                        </div>
                    </div>
                    <span class="px-3 py-1 bg-sky-100 text-sky-700 rounded-full text-sm font-semibold">
                        ${shift.employees.length} Employee${shift.employees.length > 1 ? 's' : ''}
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 bg-white rounded-xl overflow-hidden">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Kategori Shift</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Nama Karyawan</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Shift 1 - Shift 2</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Status</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Keterangan</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Jam Check In</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Jam Check Out</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100" id="tbody-${shift.category}">
        `;
        
        // Track unique rendered employees per group for correct count, if needed
        let groupRendered = new Set();
        shift.employees.forEach(employee => {
            // Lookup shift summary for this employee
            const sum = employeeShiftSummary.get(employee.name) || { count: 1, isDouble: false, isLong: false, shifts: [] };

            // Skip duplicate rendering for double shift users
            if (sum.isDouble) {
                if (renderedEmployees.has(employee.name)) {
                    return; // already rendered in another group
                }
            }
            // Normalize status for display (early_checkout/forgot_checkout should not be primary status)
            let displayStatus = employee.status;
            if (displayStatus === 'early_checkout' || displayStatus === 'forgot_checkout') {
                displayStatus = 'hadir'; // Default to hadir, will be corrected by double shift logic if needed
            }
            
            // Determine label based on status (will be updated by double shift logic if needed)
            let statusLabel = statusConfig[displayStatus]?.label || displayStatus;
            
            // For izin status, show permission type (Izin or Cuti)
            if (displayStatus === 'izin' && employee.permission_type) {
                statusLabel = employee.permission_type.charAt(0).toUpperCase() + employee.permission_type.slice(1);
            }
            
            // Determine if early checkout (any entry if double)
            let earlyCheckoutBadge = '';
            let hasEarly = !!employee.is_early_checkout;
            // Prefer server-provided shifts for doubles; fallback to scan original data if not present
            let serverShifts = Array.isArray(employee.shifts) ? employee.shifts.slice() : [];
            if (serverShifts.length <= 1 && originalAttendanceData && Array.isArray(originalAttendanceData.data)) {
                // collect across all groups
                const collected = [];
                originalAttendanceData.data.forEach(g => {
                    (g.employees || []).forEach(e => {
                        if (e.name === employee.name) {
                            collected.push({
                                category: g.category,
                                shift_name: e.shift_name,
                                shift_start: g.shift_start || null,
                                shift_end: g.shift_end || null,
                                check_in: e.check_in,
                                check_out: e.check_out,
                                status: e.status,
                                is_early_checkout: !!e.is_early_checkout,
                                permission_type: e.permission_type || null,
                            });
                        }
                    })
                });
                if (collected.length > serverShifts.length) {
                    serverShifts = collected;
                }
            }
            const isServerDouble = serverShifts.length > 1;
            if (isServerDouble) {
                hasEarly = serverShifts.some(s => !!s.is_early_checkout);
                const earliestEntry = serverShifts.slice().sort((a,b)=> (a.shift_start||'23:59').localeCompare(b.shift_start||'23:59'))[0];
                if (earliestEntry && earliestEntry.status) {
                    // Use normalized status from server (already normalized in controller)
                    const lbl = earliestEntry.status === 'izin' && earliestEntry.permission_type
                        ? earliestEntry.permission_type
                        : earliestEntry.status;
                    statusLabel = statusConfig[lbl]?.label || lbl;
                    displayStatus = earliestEntry.status; // Update displayStatus for correct color
                }
            } else if (sum.isDouble) {
                hasEarly = sum.shifts.some(s => s.is_early_checkout);
                const earliestEntry = sum.shifts.slice().sort((a,b)=> (a.shift_start||'23:59').localeCompare(b.shift_start||'23:59'))[0];
                if (earliestEntry && earliestEntry.status) {
                    // Use normalized status
                    const lbl = earliestEntry.status === 'izin' && earliestEntry.permission_type
                        ? earliestEntry.permission_type
                        : earliestEntry.status;
                    statusLabel = statusConfig[lbl]?.label || lbl;
                    displayStatus = earliestEntry.status; // Update displayStatus for correct color
                }
            }
            
            // Assign statusClass after displayStatus is finalized
            const statusClass = statusConfig[displayStatus]?.class || 'bg-gray-50 border-gray-200 text-gray-700';
            const statusIcon = statusConfig[displayStatus]?.icon || 'circle';
            const statusIconClass = statusConfig[displayStatus]?.iconClass || 'text-gray-500';

            // Generate status badges HTML with improved layout
            let badgesHtml = '';
            
            // Early checkout badge
            if (hasEarly) {
                const badge = badgeConfig['early_checkout'];
                badgesHtml += `
                    <span class="inline-flex items-center px-2 py-1 ${badge.class} border rounded-full text-xs font-medium ml-1 transition-all hover:shadow-sm">
                        <i data-lucide="${badge.icon}" class="w-3 h-3 ${badge.iconClass} mr-1"></i>
                        ${badge.label}
                    </span>
                `;
            }
            
            // Double shift badge
            if (sum.isDouble) {
                const badge = badgeConfig['double_shift'];
                badgesHtml += `
                    <span class="inline-flex items-center px-2 py-1 ${badge.class} border rounded-full text-xs font-medium ml-1 transition-all hover:shadow-sm">
                        <i data-lucide="${badge.icon}" class="w-3 h-3 ${badge.iconClass} mr-1"></i>
                        ${badge.label}
                    </span>
                `;
            }
            
            // Long shift badge
            if (sum.isLong) {
                const badge = badgeConfig['long_shift'];
                badgesHtml += `
                    <span class="inline-flex items-center px-2 py-1 ${badge.class} border rounded-full text-xs font-medium ml-1 transition-all hover:shadow-sm">
                        <i data-lucide="${badge.icon}" class="w-3 h-3 ${badge.iconClass} mr-1"></i>
                        ${badge.label}
                    </span>
                `;
            }
            
            // Combined check-in/out for double shift
            let combinedCheckIn = employee.check_in;
            let combinedCheckOut = employee.check_out;
            if (isServerDouble) {
                const ins = serverShifts.map(s=>s.check_in).filter(Boolean).sort();
                const outs = serverShifts.map(s=>s.check_out).filter(Boolean).sort();
                combinedCheckIn = ins.length ? ins[0] : combinedCheckIn;
                combinedCheckOut = outs.length ? outs[outs.length-1] : combinedCheckOut;
            } else if (sum.isDouble) {
                const times = sum.shifts.map(s => ({ci: s.check_in, co: s.check_out}));
                const validIns = times.map(t=>t.ci).filter(Boolean).sort();
                const validOuts = times.map(t=>t.co).filter(Boolean).sort();
                combinedCheckIn = validIns.length ? validIns[0] : employee.check_in;
                combinedCheckOut = validOuts.length ? validOuts[validOuts.length-1] : employee.check_out;
            }

            // Shift chips line
            let shiftChips = '';
            if (isServerDouble) {
                const ordered = serverShifts.slice().sort((a,b)=> (a.shift_start||'23:59').localeCompare(b.shift_start||'23:59'));
                const chips = ordered.map(s => `${s.category} • ${s.shift_name}`);
                shiftChips = chips.map(ch => `<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-sky-50 text-sky-700 border border-sky-200">${ch}</span>`).join('<span class="mx-1 text-gray-300">|</span>');
            } else if (sum.isDouble) {
                const chips = sum.shifts.map(s => `${s.category} • ${s.shift_name}`);
                shiftChips = chips.map(ch => `<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-sky-50 text-sky-700 border border-sky-200">${ch}</span>`).join('<span class="mx-1 text-gray-300">|</span>');
            } else {
                shiftChips = `<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium bg-sky-50 text-sky-700 border border-sky-200">${shift.category} • ${employee.shift_name}</span>`;
            }

            // Compute Shift 1 - Shift 2 text for table column
            let shiftPair = '';
            if (isServerDouble) {
                const ordered = serverShifts.slice().sort((a,b)=> (a.shift_start||'23:59').localeCompare(b.shift_start||'23:59'));
                const names = ordered.map(s => s.shift_name).filter(Boolean);
                if (names.length >= 2) shiftPair = `${names[0]} - ${names[1]}`; else shiftPair = names.join('');
            } else if (sum.isDouble && sum.shifts.length > 1) {
                const ordered = sum.shifts.slice().sort((a,b)=> (a.shift_start||'23:59').localeCompare(b.shift_start||'23:59'));
                const names = ordered.map(s => s.shift_name).filter(Boolean);
                if (names.length >= 2) shiftPair = `${names[0]} - ${names[1]}`; else shiftPair = names.join('');
            } else {
                shiftPair = employee.shift_name || '';
            }

            html += `
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-2 text-sm text-gray-700">${shift.category}</td>
                    <td class="px-4 py-2 text-sm font-medium text-gray-900">${employee.name}</td>
                    <td class="px-4 py-2 text-sm text-gray-700">${shiftPair}</td>
                    <td class="px-4 py-2 text-sm">
                        <div class="flex flex-wrap items-center gap-1">
                            <span class="inline-flex items-center px-2 py-1 ${statusClass} border rounded-full text-xs font-semibold transition-all hover:shadow-sm">
                                <i data-lucide="${statusIcon}" class="w-3 h-3 ${statusIconClass} mr-1"></i>
                                ${statusLabel}
                            </span>
                            ${badgesHtml}
                        </div>
                    </td>
                    <td class="px-4 py-2 text-sm text-gray-700">
                        ${employee.permission_type ? employee.permission_type : '-'}
                        ${employee.permission_file && employee.permission_id ? `
                            <a href="${permissionAttachmentUrlTemplate.replace('__PERMISSION_ID__', employee.permission_id)}" target="_blank" class="inline-flex items-center px-2 py-1 ml-2 text-xs font-medium text-sky-700 bg-sky-50 border border-sky-200 rounded-full hover:bg-sky-100">
                                <i data-lucide="paperclip" class="w-3 h-3 mr-1"></i>
                                Lampiran
                            </a>
                        ` : ''}
                    </td>
                    <td class="px-4 py-2 text-sm text-gray-700">${combinedCheckIn ? combinedCheckIn : '-'}</td>
                    <td class="px-4 py-2 text-sm text-gray-700">${combinedCheckOut ? combinedCheckOut : '-'}</td>
                </tr>
            `;

            // Mark as rendered to avoid duplicates for double shift
            renderedEmployees.add(employee.name);
            groupRendered.add(employee.name);
        });
        
        html += `
                        </tbody>
                    </table>
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
    
    // Hide filter section and reset
    document.getElementById('filter-section').style.display = 'none';
    resetFilters();
}

// Populate shift dropdown
function populateShiftDropdown() {
    const shiftOptions = document.getElementById('shift-options');
    let html = '<div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Pilih Shift</div>';
    
    // Add "All Shifts" option
    html += `
        <div class="shift-option px-3 py-2 hover:bg-sky-50 cursor-pointer text-sm" data-shift="">
            <span class="font-medium">Semua Shift</span>
        </div>
    `;
    
    // Add shift options
    Array.from(allShiftNames).sort().forEach(shiftName => {
        html += `
            <div class="shift-option px-3 py-2 hover:bg-sky-50 cursor-pointer text-sm" data-shift="${shiftName}">
                <span class="font-medium">${shiftName}</span>
            </div>
        `;
    });
    
    shiftOptions.innerHTML = html;
    
    // Add click handlers to options
    document.querySelectorAll('.shift-option').forEach(option => {
        option.addEventListener('click', function() {
            const shiftName = this.dataset.shift;
            document.getElementById('filter-shift-search').value = shiftName || '';
            document.getElementById('shift-dropdown').classList.add('hidden');
            applyFilters();
        });
    });
}

// Initialize filter event listeners
let filtersInitialized = false;
function initializeFilters() {
    if (filtersInitialized) return; // Prevent duplicate listeners
    
    const searchInput = document.getElementById('search-employee');
    const categoryFilter = document.getElementById('filter-category');
    const shiftSearchInput = document.getElementById('filter-shift-search');
    const shiftDropdown = document.getElementById('shift-dropdown');
    const resetBtn = document.getElementById('reset-filters');
    
    // Search employee - realtime
    searchInput.addEventListener('input', function() {
        applyFilters();
    });
    
    // Category filter
    categoryFilter.addEventListener('change', function() {
        applyFilters();
    });
    
    // Shift search input - show dropdown and filter
    shiftSearchInput.addEventListener('focus', function() {
        shiftDropdown.classList.remove('hidden');
        filterShiftDropdown();
    });
    
    shiftSearchInput.addEventListener('input', function() {
        filterShiftDropdown();
        applyFilters();
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#filter-shift-search') && !e.target.closest('#shift-dropdown')) {
            shiftDropdown.classList.add('hidden');
        }
    });
    
    // Reset filters
    resetBtn.addEventListener('click', function() {
        resetFilters();
        applyFilters();
    });
    
    filtersInitialized = true;
}

// Filter shift dropdown based on search
function filterShiftDropdown() {
    const searchTerm = document.getElementById('filter-shift-search').value.toLowerCase();
    const options = document.querySelectorAll('.shift-option');
    
    options.forEach(option => {
        const shiftName = option.dataset.shift.toLowerCase();
        if (shiftName.includes(searchTerm) || shiftName === '') {
            option.style.display = 'block';
        } else {
            option.style.display = 'none';
        }
    });
}

// Apply filters
function applyFilters() {
    if (!originalAttendanceData) return;
    
    const searchTerm = document.getElementById('search-employee').value.toLowerCase();
    const categoryFilter = document.getElementById('filter-category').value;
    const shiftFilter = document.getElementById('filter-shift-search').value;
    
    // Build employee shift summary from ORIGINAL data before filtering
    // This ensures double/long detection works correctly for badges
    buildEmployeeShiftSummary(originalAttendanceData.data);
    
    // Clone original data
    let filteredData = JSON.parse(JSON.stringify(originalAttendanceData));
    
    // Filter by category
    if (categoryFilter) {
        filteredData.data = filteredData.data.filter(shift => shift.category === categoryFilter);
    }
    
    // Filter by shift name and employee name
    filteredData.data = filteredData.data.map(shift => {
        let filteredEmployees = shift.employees;
        
        // Filter by shift name
        if (shiftFilter) {
            filteredEmployees = filteredEmployees.filter(emp => emp.shift_name === shiftFilter);
        }
        
        // Filter by employee name
        if (searchTerm) {
            filteredEmployees = filteredEmployees.filter(emp => 
                emp.name.toLowerCase().includes(searchTerm)
            );
        }
        
        return {
            ...shift,
            employees: filteredEmployees
        };
    }).filter(shift => shift.employees.length > 0); // Remove empty shifts
    
    // Render filtered data
    renderAttendanceDetail(filteredData);
    
    // Reinitialize lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

// Reset filters
function resetFilters() {
    document.getElementById('search-employee').value = '';
    document.getElementById('filter-category').value = '';
    document.getElementById('filter-shift-search').value = '';
    document.getElementById('shift-dropdown').classList.add('hidden');
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

// Build employee shift summary across categories
function buildEmployeeShiftSummary(groupedData) {
    employeeShiftSummary.clear();
    const byName = new Map();
    // Collect entries per employee
    groupedData.forEach(shift => {
        shift.employees.forEach(emp => {
            if (!byName.has(emp.name)) byName.set(emp.name, []);
            byName.get(emp.name).push({
                category: shift.category,
                shift_name: emp.shift_name,
                check_in: emp.check_in,
                check_out: emp.check_out,
                status: emp.status,
                is_early_checkout: !!emp.is_early_checkout,
                permission_type: emp.permission_type || null,
                shift_start: shift.shift_start || null
            });
        });
    });
    // Compute summary
    byName.forEach((entries, name) => {
        const isDouble = entries.length > 1;
        let isLong = false;
        // Heuristics: long if any entry duration >= 10 hours or shift name hints long
        entries.forEach(en => {
            const hint = (en.shift_name || '').toLowerCase();
            if (hint.includes('long') || hint.includes('panjang')) {
                isLong = true;
                return;
            }
            if (en.check_in && en.check_out) {
                // Compute naive duration HH:MM -> minutes
                const mins = tryDiffMinutes(en.check_in, en.check_out);
                if (mins >= 10 * 60) {
                    isLong = true;
                }
            }
        });
        employeeShiftSummary.set(name, { count: entries.length, isDouble, isLong, shifts: entries });
    });
}

function tryDiffMinutes(t1, t2) {
    // Accept formats like 'HH:MM' or 'YYYY-MM-DD HH:MM:SS'
    try {
        const d1 = parseTimeToDate(t1);
        const d2 = parseTimeToDate(t2);
        let diff = (d2 - d1) / 60000;
        if (diff < 0) diff += 24 * 60; // cross midnight safeguard
        return diff;
    } catch (e) {
        return 0;
    }
}

function parseTimeToDate(val) {
    // If only time, put on epoch date
    if (/^\d{2}:\d{2}(:\d{2})?$/.test(val)) {
        const [hh, mm, ss] = val.split(':').map(n => parseInt(n, 10));
        const d = new Date(2000, 0, 1, hh, mm || 0, ss || 0);
        return d;
    }
    // Else parse as Date
    return new Date(val);
}
</script>   