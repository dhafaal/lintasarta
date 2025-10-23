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
            originalAttendanceData = data;
            
            // Collect all shift names
            allShiftNames.clear();
            data.data.forEach(shift => {
                shift.employees.forEach(emp => {
                    allShiftNames.add(emp.shift_name);
                });
            });
            
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
                            <h4 class="text-lg font-bold text-gray-900">${shift.category}</h4>
                            
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
            let statusLabel = employee.status.charAt(0).toUpperCase() + employee.status.slice(1);
            
            // For izin status, show permission type (Izin or Cuti)
            if (employee.status === 'izin' && employee.permission_type) {
                statusLabel = employee.permission_type.charAt(0).toUpperCase() + employee.permission_type.slice(1);
            }
            
            // Determine if early checkout
            let earlyCheckoutBadge = '';
            if (employee.is_early_checkout) {
                earlyCheckoutBadge = `
                    <span class="px-2 py-1 bg-amber-50 border-amber-200 text-amber-700 border rounded-full text-xs font-semibold ml-2">
                        Early Checkout
                    </span>
                `;
            }
            
            html += `
                <div class="bg-white rounded-xl p-4 border-2 border-gray-100 hover:border-sky-200 transition-colors">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-sky-500 to-sky-600 rounded-full flex items-center justify-center shadow-sm">
                                <span class="text-sm font-bold text-white">${employee.name.substring(0, 2).toUpperCase()}</span>
                            </div>
                            <div>
                                <p class="font-bold text-gray-900">${employee.name}</p>
                                <p class="text-xs text-gray-500">${employee.shift_name}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 ${statusClass} border rounded-full text-xs font-semibold">
                                ${statusLabel}
                            </span>
                            ${earlyCheckoutBadge}
                        </div>
                    </div>
                    <div class="flex items-center space-x-4 text-xs text-gray-600 bg-gray-50 rounded-lg p-2">
            `;
            
            if (employee.check_in) {
                html += `
                    <span class="flex items-center space-x-1">
                        <i data-lucide="log-in" class="w-3 h-3 text-green-600"></i>
                        <span class="font-medium">Check In: ${employee.check_in}</span>
                    </span>
                `;
            }
            
            if (employee.check_out) {
                html += `
                    <span class="flex items-center space-x-1">
                        <i data-lucide="log-out" class="w-3 h-3 text-red-600"></i>
                        <span class="font-medium">Check Out: ${employee.check_out}</span>
                    </span>
                `;
            }
            
            if (!employee.check_in && !employee.check_out) {
                html += '<span class="text-gray-400 font-medium">No attendance record</span>';
            }
            
            html += `
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
</script>
