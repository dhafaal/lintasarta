@extends('layouts.admin')

@section('title', 'Profile')

@section('content')
    <div class="mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gradient-to-br from-sky-100 to-sky-200 rounded-xl flex items-center justify-center shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="lucide lucide-circle-user-icon lucide-circle-user text-sky-600 w-6 h-6">
                    <circle cx="12" cy="12" r="10" />
                    <circle cx="12" cy="10" r="3" />
                    <path d="M7 20.662V19a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v1.662" />
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-700 tracking-tight">Profile User</h1>
                <p class="text-gray-500 mt-1">Manage your profile information and account settings</p>
            </div>
        </div>

        <!-- Profile Card -->
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <!-- Profile Header -->
            <div class="relative bg-gradient-to-r from-sky-500 to-sky-600 h-32">
                <div class="absolute -bottom-12 left-6">
                    <div
                        class="w-24 h-24 rounded-3xl bg-sky-200 backdrop-blur-sm flex items-center justify-center border-4 border-white shadow-lg">
                        <span class="text-2xl font-bold text-sky-600">
                            {{ App\Http\Controllers\Admin\ProfileController::getUserInitials($user->name) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Profile Content -->
            <div class="pt-16 pb-6 px-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">{{ $user->name }}</h2>
                        <div class="flex items-center mt-1">
                            <div class="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
                            <span class="text-sm text-gray-600">Administrator</span>
                        </div>
                    </div>
                </div>

                <!-- Profile Fields -->
                <div class="space-y-6">
                    <!-- Username/Name -->
                    <div class="flex items-center justify-between py-4 border-b border-gray-100">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-500 uppercase tracking-wide mb-1">
                                Nama Lengkap
                            </label>
                            <div class="text-gray-900 font-medium">{{ $user->name }}</div>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="flex items-center justify-between py-4 border-b border-gray-100">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-500 uppercase tracking-wide mb-1">
                                Email Address
                            </label>
                            <div class="text-gray-900 font-medium">{{ $user->email }}</div>
                        </div>
                    </div>

                    <!-- Role -->
                    <div class="flex items-center justify-between py-4 border-b border-gray-100">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-500 uppercase tracking-wide mb-1">
                                Role
                            </label>
                            <div class="text-gray-900 font-medium">{{ $user->role }}</div>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="flex items-center justify-between py-4">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-500 uppercase tracking-wide mb-1">
                                Password
                            </label>
                            <div class="text-gray-900 font-medium">••••••••••••</div>
                        </div>
                        <button type="button" onclick="openChangePasswordModal()"
                            class="px-4 py-2 bg-sky-500 hover:bg-sky-600 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            Change Password
                        </button>
                    </div>
                </div>

                <!-- Account Info -->
                <div class="mt-8 pt-6 border-t border-gray-100">
                    <div class="text-xs text-gray-500 space-y-1">
                        <p>Account created: {{ $user->created_at->format('M d, Y') }}</p>
                        <p>Last updated: {{ $user->updated_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div id="changePasswordModal" class="fixed inset-0 bg-gray-600/50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-3xl bg-white">
            <div class="mt-3">
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                        <i data-lucide="key" class="w-5 h-5 mr-3 text-sky-600"></i>
                        Change Password
                    </h3>
                    <button type="button" onclick="closeChangePasswordModal()"
                        class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <form action="{{ route('admin.profile.change-password') }}" method="POST" class="mt-6 space-y-4">
                    @csrf

                    <!-- Current Password -->
                    <div class="space-y-2">
                        <label for="current_password" class="text-sm font-semibold text-gray-700">
                            Current Password
                        </label>
                        <div class="relative">
                            <input type="password" id="current_password" name="current_password" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors duration-200">
                            <button type="button" onclick="togglePassword('current_password')"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>

                    <!-- New Password -->
                    <div class="space-y-2">
                        <label for="new_password" class="text-sm font-semibold text-gray-700">
                            New Password
                        </label>
                        <div class="relative">
                            <input type="password" id="new_password" name="new_password" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors duration-200">
                            <button type="button" onclick="togglePassword('new_password')"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Confirm New Password -->
                    <div class="space-y-2">
                        <label for="new_password_confirmation" class="text-sm font-semibold text-gray-700">
                            Confirm New Password
                        </label>
                        <div class="relative">
                            <input type="password" id="new_password_confirmation" name="new_password_confirmation" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors duration-200">
                            <button type="button" onclick="togglePassword('new_password_confirmation')"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Password Requirements -->
                    <div class="bg-gray-50 rounded-xl p-3 text-xs text-gray-600">
                        <p class="font-semibold mb-1">Password harus:</p>
                        <ul class="space-y-1">
                            <li>• Minimal 8 karakter</li>
                            <li>• Mengandung huruf dan angka</li>
                            <li>• Berbeda dari password saat ini</li>
                        </ul>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex space-x-3 pt-4">
                        <button type="button" onclick="closeChangePasswordModal()"
                            class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors duration-200 font-semibold">
                            Cancel
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-3 bg-sky-600 text-white rounded-xl hover:bg-sky-700 transition-colors duration-200 font-semibold">
                            Change Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Modal functions
            function openChangePasswordModal() {
                document.getElementById('changePasswordModal').classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeChangePasswordModal() {
                document.getElementById('changePasswordModal').classList.add('hidden');
                document.body.style.overflow = 'auto';
                // Clear form
                document.querySelector('#changePasswordModal form').reset();
            }

            // Toggle password visibility
            function togglePassword(fieldId) {
                const field = document.getElementById(fieldId);
                const icon = field.nextElementSibling.querySelector('i');

                if (field.type === 'password') {
                    field.type = 'text';
                    icon.setAttribute('data-lucide', 'eye-off');
                } else {
                    field.type = 'password';
                    icon.setAttribute('data-lucide', 'eye');
                }

                // Re-initialize lucide icons
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }

            // Close modal when clicking outside
            document.getElementById('changePasswordModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeChangePasswordModal();
                }
            });

            // Close modal with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeChangePasswordModal();
                }
            });

            // Show success/error messages
            @if (session('success'))
                // Show success notification
                const successDiv = document.createElement('div');
                successDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-xl shadow-lg z-50';
                successDiv.innerHTML = `
            <div class="flex items-center space-x-2">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                <span>{{ session('success') }}</span>
            </div>
        `;
                document.body.appendChild(successDiv);

                // Initialize icons
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }

                // Remove after 5 seconds
                setTimeout(() => {
                    successDiv.remove();
                }, 5000);
            @endif

            @if ($errors->any())
                openChangePasswordModal();

                // Show error notification
                const errorDiv = document.createElement('div');
                errorDiv.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-xl shadow-lg z-50';
                errorDiv.innerHTML = `
            <div class="flex items-center space-x-2">
                <i data-lucide="alert-circle" class="w-5 h-5"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        `;
                document.body.appendChild(errorDiv);

                // Initialize icons
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }

                // Remove after 5 seconds
                setTimeout(() => {
                    errorDiv.remove();
                }, 5000);
            @endif
        </script>
    @endpush
@endsection
