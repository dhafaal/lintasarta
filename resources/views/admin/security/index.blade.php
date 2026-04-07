@extends('layouts.admin')

@section('title', 'Security Management')

@section('content')
    <div class="space-y-6">
        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i data-lucide="check-circle" class="h-5 w-5 text-green-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i data-lucide="x-circle" class="h-5 w-5 text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i data-lucide="alert-circle" class="h-5 w-5 text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc space-y-1 pl-5">
                                @foreach ($errors->all() as $error)
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
                class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-red-100 to-red-200 shadow-sm"
            >
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"
                    />
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
                    class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-800"
                >
                    <svg class="mr-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12l2 2 4-4m5 4V7a2 2 0 00-2-2H6a2 2 0 00-2 2v10a2 2 0 002 2h7"
                        />
                    </svg>
                    Security Active
                </span>
                <button
                    onclick="refreshStats()"
                    class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-800 transition-colors hover:bg-blue-200"
                >
                    <svg class="mr-1 h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 4v6h6M20 20v-6h-6M5 19A9 9 0 0119 5"
                        />
                    </svg>
                    Refresh
                </button>
            </div>
        </div>

        <!-- Security Statistics Cards -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
            <!-- Login Attempts -->
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100">
                            <i data-lucide="log-in" class="h-4 w-4 text-blue-600"></i>
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
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-100">
                            <i data-lucide="shield-x" class="h-4 w-4 text-red-600"></i>
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
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-green-100">
                            <i data-lucide="users" class="h-4 w-4 text-green-600"></i>
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
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-yellow-100">
                            <i data-lucide="alert-triangle" class="h-4 w-4 text-yellow-600"></i>
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
            <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h3 class="text-lg font-medium text-gray-900">Top Failed Login IPs (7 days)</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        @foreach ($stats['top_failed_ips'] as $ip)
                            <div class="flex items-center justify-between rounded-lg bg-red-50 p-3">
                                <div class="flex items-center space-x-3">
                                    <i data-lucide="globe" class="h-4 w-4 text-red-600"></i>
                                    <span class="font-mono text-sm">{{ $ip->ip_address }}</span>
                                    <span class="rounded-full bg-red-100 px-2 py-1 text-xs text-red-800">
                                        {{ $ip->failed_count }} attempts
                                    </span>
                                </div>
                                <button
                                    onclick="showBlockIPModal('{{ $ip->ip_address }}')"
                                    class="text-sm font-medium text-red-600 hover:text-red-800"
                                >
                                    Block IP
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Tabs for different sections -->
        <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                    <button
                        onclick="showTab('blocked-ips')"
                        class="tab-button border-b-2 border-red-500 px-1 py-4 text-sm font-medium whitespace-nowrap text-red-600"
                        data-tab="blocked-ips"
                    >
                        <i data-lucide="shield-x" class="mr-2 inline h-4 w-4"></i>
                        Blocked IPs ({{ $blockedIPs->total() }})
                    </button>
                    <button
                        onclick="showTab('active-sessions')"
                        class="tab-button border-b-2 border-transparent px-1 py-4 text-sm font-medium whitespace-nowrap text-gray-500 hover:border-gray-300 hover:text-gray-700"
                        data-tab="active-sessions"
                    >
                        <i data-lucide="monitor" class="mr-2 inline h-4 w-4"></i>
                        Active Sessions ({{ $activeSessions->total() }})
                    </button>
                    <button
                        onclick="showTab('failed-attempts')"
                        class="tab-button border-b-2 border-transparent px-1 py-4 text-sm font-medium whitespace-nowrap text-gray-500 hover:border-gray-300 hover:text-gray-700"
                        data-tab="failed-attempts"
                    >
                        <i data-lucide="x-circle" class="mr-2 inline h-4 w-4"></i>
                        Failed Attempts ({{ $recentFailedAttempts->count() }})
                    </button>
                    <button
                        onclick="showTab('suspicious-activity')"
                        class="tab-button border-b-2 border-transparent px-1 py-4 text-sm font-medium whitespace-nowrap text-gray-500 hover:border-gray-300 hover:text-gray-700"
                        data-tab="suspicious-activity"
                    >
                        <i data-lucide="alert-triangle" class="mr-2 inline h-4 w-4"></i>
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
    <div id="blockIPModal" class="fixed inset-0 z-50 hidden h-full w-full overflow-y-auto bg-gray-600/50">
        <div class="relative top-20 mx-auto w-11/12 rounded-2xl border bg-white p-5 shadow-lg md:w-3/4 lg:w-1/2">
            <div class="mt-3">
                <!-- Modal Header -->
                <div class="mb-6 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-red-100 to-red-200"
                        >
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
                                class="text-red-600"
                            >
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                <path d="M8 11V7a4 4 0 1 1 8 0v4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Block IP Address</h3>
                            <p class="text-sm text-gray-600">Restrict access from suspicious IP address</p>
                        </div>
                    </div>
                    <button onclick="closeBlockIPModal()" class="text-gray-400 transition-colors hover:text-gray-600">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Security Warning Info -->
                <div class="mb-6 rounded-xl border border-red-100 bg-red-50 p-4">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
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
                                class="mt-0.5 text-red-600"
                            >
                                <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z" />
                                <path d="M12 9v4" />
                                <path d="m12 17 .01 0" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="mb-1 font-semibold text-red-800">Security Action</h4>
                            <p class="text-sm text-red-700">
                                This action will prevent the specified IP address from accessing the system. Make sure
                                you have a valid reason for blocking this IP.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Block IP Form -->
                <form action="{{ route('admin.security.block-ip') }}" method="POST">
                    @csrf
                    <div class="space-y-6">
                        <!-- IP Address Input -->
                        <div>
                            <label class="mb-2 block text-sm font-bold text-gray-700">
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
                                    class="mr-2 inline text-gray-500"
                                >
                                    <circle cx="12" cy="12" r="10" />
                                    <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20" />
                                    <path d="M2 12h20" />
                                </svg>
                                IP Address
                            </label>
                            <input
                                type="text"
                                name="ip_address"
                                id="blockIPAddress"
                                class="w-full rounded-lg border border-gray-300 px-4 py-3 transition-colors focus:border-red-500 focus:ring-2 focus:ring-red-500"
                                placeholder="Enter IP address (e.g., 192.168.1.1)"
                                pattern="^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$"
                                required
                            />
                        </div>

                        <!-- Reason Input -->
                        <div>
                            <label class="mb-2 block text-sm font-bold text-gray-700">
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
                                    class="mr-2 inline text-gray-500"
                                >
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                    <polyline points="14 2 14 8 20 8" />
                                    <line x1="16" y1="13" x2="8" y2="13" />
                                    <line x1="16" y1="17" x2="8" y2="17" />
                                    <polyline points="10 9 9 9 8 9" />
                                </svg>
                                Reason for Blocking
                            </label>
                            <textarea
                                name="reason"
                                rows="4"
                                class="w-full resize-none rounded-lg border border-gray-300 px-4 py-3 transition-colors focus:border-red-500 focus:ring-2 focus:ring-red-500"
                                placeholder="Enter detailed reason for blocking this IP address..."
                                minlength="5"
                            ></textarea>
                            <p class="mt-1 text-xs text-gray-500">Minimum 5 characters required</p>
                        </div>

                        <!-- Duration Selection -->
                        <div>
                            <label class="mb-2 block text-sm font-bold text-gray-700">
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
                                    class="mr-2 inline text-gray-500"
                                >
                                    <circle cx="12" cy="12" r="10" />
                                    <polyline points="12 6 12 12 16 14" />
                                </svg>
                                Block Duration
                            </label>
                            <select
                                name="duration"
                                class="w-full rounded-lg border border-gray-300 px-4 py-3 transition-colors focus:border-red-500 focus:ring-2 focus:ring-red-500"
                                required
                            >
                                <option value="">Select block duration</option>
                                <option value="1">1 Hours - Temporary block</option>
                                <option value="24" selected>24 Hours - Standard block</option>
                                <option value="168">1 Week - Extended block</option>
                                <option value="permanent">Permanent - Indefinite block</option>
                            </select>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-6 flex items-center justify-end space-x-3 border-t border-gray-200 pt-6">
                        <button
                            type="button"
                            onclick="closeBlockIPModal()"
                            class="rounded-lg bg-gray-200 px-6 py-3 font-semibold text-gray-700 transition-colors hover:bg-gray-300"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="flex items-center space-x-2 rounded-lg bg-red-600 px-6 py-3 font-semibold text-white transition-colors hover:bg-red-700"
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
                            >
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                <path d="M8 11V7a4 4 0 1 1 8 0v4" />
                            </svg>
                            <span>Block IP Address</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize first tab as active
            showTab('blocked-ips');
        });

        function showTab(tabId) {
            // Hide all tab contents
            const allTabs = document.querySelectorAll('.tab-content');
            allTabs.forEach((tab) => {
                tab.style.display = 'none';
            });

            // Remove active class from all tab buttons
            const allButtons = document.querySelectorAll('.tab-button');
            allButtons.forEach((button) => {
                button.classList.remove(
                    'border-red-500',
                    'text-red-600',
                    'border-blue-500',
                    'text-blue-600',
                    'border-yellow-500',
                    'text-yellow-600'
                );
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
        document.getElementById('blockIPModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeBlockIPModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('blockIPModal');
                if (!modal.classList.contains('hidden')) {
                    closeBlockIPModal();
                }
            }
        });

        // Form validation
        document.querySelector('#blockIPModal form').addEventListener('submit', function (e) {
            const ipAddress = document.getElementById('blockIPAddress').value.trim();
            const reason = document.querySelector('textarea[name="reason"]').value.trim();
            const duration = document.querySelector('select[name="duration"]').value;

            if (!ipAddress) {
                alert('Please enter an IP address.');
                e.preventDefault();
                return false;
            }

            if (!reason || reason.length < 5) {
                alert('Please provide a detailed reason (at least 5 characters).');
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
            switch (duration) {
                case '1':
                    durationText = '1 Hours';
                    break;
                case '24':
                    durationText = '24 hours';
                    break;
                case '168':
                    durationText = '1 week';
                    break;
                case 'permanent':
                    durationText = 'permanently';
                    break;
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
