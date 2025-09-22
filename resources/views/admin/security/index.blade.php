@extends('layouts.admin')

@section('title', 'Security Management')

@section('content')
    <div class="space-y-6">
        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i data-lucide="check-circle" class="w-5 h-5 text-green-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i data-lucide="x-circle" class="w-5 h-5 text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i data-lucide="alert-circle" class="w-5 h-5 text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Header Section -->
        <div class="flex items-center space-x-4">
            <!-- Ikon dengan background gradient -->
            <div
                class="w-12 h-12 bg-gradient-to-br from-red-100 to-red-200 rounded-xl flex items-center justify-center shadow-sm">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                </svg>
            </div>

            <!-- Judul + Deskripsi -->
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-900">Security Management</h1>
                <p class="mt-1 text-sm text-gray-600">Monitor and manage system security</p>
            </div>

            <!-- Status + Tombol Refresh -->
            <div class="flex space-x-2">
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5 4V7a2 2 0 00-2-2H6a2 2 0 00-2 2v10a2 2 0 002 2h7" />
                    </svg>
                    Security Active
                </span>
                <button onclick="refreshStats()"
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 hover:bg-blue-200 transition-colors">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v6h6M20 20v-6h-6M5 19A9 9 0 0119 5" />
                    </svg>
                    Refresh
                </button>
            </div>
        </div>


        <!-- Security Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Login Attempts -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="log-in" class="w-4 h-4 text-blue-600"></i>
                        </div>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-gray-500">Login Attempts (24h)</p>
                        <div class="flex items-baseline">
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_attempts_24h'] }}</p>
                            <p class="ml-2 text-sm text-green-600">{{ $stats['successful_attempts_24h'] }} success</p>
                        </div>
                        @if ($stats['failed_attempts_24h'] > 0)
                            <p class="text-xs text-red-600">{{ $stats['failed_attempts_24h'] }} failed</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Blocked IPs -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="shield-x" class="w-4 h-4 text-red-600"></i>
                        </div>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-gray-500">Blocked IPs</p>
                        <div class="flex items-baseline">
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_blocked_ips'] }}</p>
                        </div>
                        <div class="flex space-x-2 text-xs">
                            <span class="text-red-600">{{ $stats['permanent_blocks'] }} permanent</span>
                            <span class="text-yellow-600">{{ $stats['temporary_blocks'] }} temporary</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Sessions -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="users" class="w-4 h-4 text-green-600"></i>
                        </div>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-gray-500">Active Sessions</p>
                        <div class="flex items-baseline">
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['active_sessions'] }}</p>
                        </div>
                        <p class="text-xs text-gray-500">{{ $stats['total_sessions_24h'] }} sessions today</p>
                    </div>
                </div>
            </div>

            <!-- Suspicious Activities -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="alert-triangle" class="w-4 h-4 text-yellow-600"></i>
                        </div>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium text-gray-500">Suspicious Activity</p>
                        <div class="flex items-baseline">
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['suspicious_logins_24h'] }}</p>
                        </div>
                        <p class="text-xs text-gray-500">{{ $stats['blocked_attempts_24h'] }} blocked attempts</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Failed IPs -->
        @if ($stats['top_failed_ips']->count() > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Top Failed Login IPs (7 days)</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        @foreach ($stats['top_failed_ips'] as $ip)
                            <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <i data-lucide="globe" class="w-4 h-4 text-red-600"></i>
                                    <span class="font-mono text-sm">{{ $ip->ip_address }}</span>
                                    <span
                                        class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">{{ $ip->failed_count }}
                                        attempts</span>
                                </div>
                                <button onclick="showBlockIPModal('{{ $ip->ip_address }}')"
                                    class="text-red-600 hover:text-red-800 text-sm font-medium">
                                    Block IP
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Tabs for different sections -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                    <button onclick="showTab('blocked-ips')"
                        class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-red-500 text-red-600"
                        data-tab="blocked-ips">
                        <i data-lucide="shield-x" class="w-4 h-4 inline mr-2"></i>
                        Blocked IPs ({{ $blockedIPs->total() }})
                    </button>
                    <button onclick="showTab('active-sessions')"
                        class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300"
                        data-tab="active-sessions">
                        <i data-lucide="monitor" class="w-4 h-4 inline mr-2"></i>
                        Active Sessions ({{ $activeSessions->total() }})
                    </button>
                    <button onclick="showTab('failed-attempts')"
                        class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300"
                        data-tab="failed-attempts">
                        <i data-lucide="x-circle" class="w-4 h-4 inline mr-2"></i>
                        Failed Attempts ({{ $recentFailedAttempts->count() }})
                    </button>
                    <button onclick="showTab('suspicious-activity')"
                        class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300"
                        data-tab="suspicious-activity">
                        <i data-lucide="alert-triangle" class="w-4 h-4 inline mr-2"></i>
                        Suspicious Activity ({{ $suspiciousActivities->count() }})
                    </button>
                </nav>
            </div>

            <!-- Tab Contents -->
            <div class="p-6">
                @include('admin.security.partials.blocked-ips', ['logs' => $blockedIPs])
                @include('admin.security.partials.active-sessions', ['logs' => $activeSessions])
                @include('admin.security.partials.failed-attempts', ['logs' => $recentFailedAttempts])
                @include('admin.security.partials.suspicious-activity', ['logs' => $suspiciousActivities])
            </div>
        </div>
    </div>

    <!-- Block IP Modal -->
    <div id="blockIPModal" class="fixed inset-0 bg-gray-600/50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-2xl bg-white">
            <div class="mt-3">
                <!-- Modal Header -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-red-100 to-red-200 rounded-xl flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-red-600">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                <path d="M8 11V7a4 4 0 1 1 8 0v4"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Block IP Address</h3>
                            <p class="text-gray-600 text-sm">Restrict access from suspicious IP address</p>
                        </div>
                    </div>
                    <button onclick="closeBlockIPModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6 6 18"/>
                            <path d="m6 6 12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Security Warning Info -->
                <div class="bg-red-50 rounded-xl p-4 mb-6 border border-red-100">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-red-600 mt-0.5">
                                <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/>
                                <path d="M12 9v4"/>
                                <path d="m12 17 .01 0"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-red-800 mb-1">Security Action</h4>
                            <p class="text-red-700 text-sm">This action will prevent the specified IP address from accessing the system. Make sure you have a valid reason for blocking this IP.</p>
                        </div>
                    </div>
                </div>

                <!-- Block IP Form -->
                <form action="{{ route('admin.security.block-ip') }}" method="POST">
                    @csrf
                    <div class="space-y-6">
                        <!-- IP Address Input -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline mr-2 text-gray-500">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/>
                                    <path d="M2 12h20"/>
                                </svg>
                                IP Address
                            </label>
                            <input type="text" name="ip_address" id="blockIPAddress"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                                placeholder="Enter IP address (e.g., 192.168.1.1)"
                                pattern="^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$"
                                required>
                        </div>

                        <!-- Reason Input -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline mr-2 text-gray-500">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                    <line x1="16" y1="13" x2="8" y2="13"/>
                                    <line x1="16" y1="17" x2="8" y2="17"/>
                                    <polyline points="10 9 9 9 8 9"/>
                                </svg>
                                Reason for Blocking
                            </label>
                            <textarea name="reason" rows="4"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors resize-none"
                                placeholder="Enter detailed reason for blocking this IP address..."
                                required
                                minlength="10"></textarea>
                            <p class="text-xs text-gray-500 mt-1">Minimum 10 characters required</p>
                        </div>

                        <!-- Duration Selection -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline mr-2 text-gray-500">
                                    <circle cx="12" cy="12" r="10"/>
                                    <polyline points="12 6 12 12 16 14"/>
                                </svg>
                                Block Duration
                            </label>
                            <select name="duration"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors"
                                required>
                                <option value="">Select block duration</option>
                                <option value="1">1 Hour - Temporary block</option>
                                <option value="24" selected>24 Hours - Standard block</option>
                                <option value="168">1 Week - Extended block</option>
                                <option value="permanent">Permanent - Indefinite block</option>
                            </select>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end space-x-3 pt-6 mt-6 border-t border-gray-200">
                        <button type="button" onclick="closeBlockIPModal()"
                            class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors flex items-center space-x-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                <path d="M8 11V7a4 4 0 1 1 8 0v4"/>
                            </svg>
                            <span>Block IP Address</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize first tab as active
            showTab('blocked-ips');
        });

        function showTab(tabId) {
            // Hide all tab contents
            const allTabs = document.querySelectorAll('.tab-content');
            allTabs.forEach(tab => {
                tab.style.display = 'none';
            });

            // Remove active class from all tab buttons
            const allButtons = document.querySelectorAll('.tab-button');
            allButtons.forEach(button => {
                button.classList.remove('border-red-500', 'text-red-600', 'border-blue-500', 'text-blue-600',
                    'border-yellow-500', 'text-yellow-600');
                button.classList.add('border-transparent', 'text-gray-500');
            });

            // Show selected tab content
            const selectedTab = document.getElementById(tabId);
            if (selectedTab) {
                selectedTab.style.display = 'block';
            }

            // Add active class to clicked button
            const clickedButton = document.querySelector(`[data-tab="${tabId}"]`);
            if (clickedButton) {
                clickedButton.classList.remove('border-transparent', 'text-gray-500');

                // Set appropriate color based on tab
                if (tabId === 'blocked-ips') {
                    clickedButton.classList.add('border-red-500', 'text-red-600');
                } else if (tabId === 'active-sessions') {
                    clickedButton.classList.add('border-blue-500', 'text-blue-600');
                } else if (tabId === 'failed-attempts') {
                    clickedButton.classList.add('border-yellow-500', 'text-yellow-600');
                } else {
                    clickedButton.classList.add('border-red-500', 'text-red-600');
                }
            }
        }

        function showBlockIPModal(ipAddress = '') {
            const modal = document.getElementById('blockIPModal');
            const ipInput = document.getElementById('blockIPAddress');
            const reasonTextarea = document.querySelector('textarea[name="reason"]');
            const durationSelect = document.querySelector('select[name="duration"]');
            
            // Reset form
            ipInput.value = ipAddress;
            reasonTextarea.value = '';
            durationSelect.value = '24'; // Default to 24 hours
            
            // If IP is provided, make input readonly and focus on reason
            if (ipAddress) {
                ipInput.readOnly = true;
                ipInput.classList.add('bg-gray-50');
                reasonTextarea.placeholder = `Enter reason for blocking IP ${ipAddress}...`;
                setTimeout(() => reasonTextarea.focus(), 100);
            } else {
                ipInput.readOnly = false;
                ipInput.classList.remove('bg-gray-50');
                reasonTextarea.placeholder = 'Enter reason for blocking this IP...';
                setTimeout(() => ipInput.focus(), 100);
            }
            
            modal.classList.remove('hidden');
        }

        function closeBlockIPModal() {
            const modal = document.getElementById('blockIPModal');
            modal.classList.add('hidden');
            
            // Reset form
            const form = modal.querySelector('form');
            form.reset();
            
            // Reset IP input state
            const ipInput = document.getElementById('blockIPAddress');
            ipInput.readOnly = false;
            ipInput.classList.remove('bg-gray-50');
        }

        // Close modal when clicking outside
        document.getElementById('blockIPModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeBlockIPModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('blockIPModal');
                if (!modal.classList.contains('hidden')) {
                    closeBlockIPModal();
                }
            }
        });

        // Form validation
        document.querySelector('#blockIPModal form').addEventListener('submit', function(e) {
            const ipAddress = document.getElementById('blockIPAddress').value.trim();
            const reason = document.querySelector('textarea[name="reason"]').value.trim();
            const duration = document.querySelector('select[name="duration"]').value;
            
            if (!ipAddress) {
                alert('Please enter an IP address.');
                e.preventDefault();
                return false;
            }
            
            if (!reason || reason.length < 10) {
                alert('Please provide a detailed reason (at least 10 characters).');
                e.preventDefault();
                return false;
            }
            
            if (!duration) {
                alert('Please select a duration.');
                e.preventDefault();
                return false;
            }
            
            // Confirm action
            let durationText = '';
            switch(duration) {
                case '1': durationText = '1 hour'; break;
                case '24': durationText = '24 hours'; break;
                case '168': durationText = '1 week'; break;
                case 'permanent': durationText = 'permanently'; break;
            }
            
            if (!confirm(`Are you sure you want to block IP ${ipAddress} for ${durationText}?\n\nReason: ${reason}`)) {
                e.preventDefault();
                return false;
            }
        });

        function refreshStats() {
            window.location.reload();
        }
    </script>
@endsection
