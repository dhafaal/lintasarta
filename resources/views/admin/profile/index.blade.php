@extends('layouts.admin')

@section('title', 'Profile')

@section('content')
    <div class="mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center space-x-4">
            <div
                class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-sky-100 to-sky-200 shadow-sm"
            >
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
                    class="lucide lucide-circle-user-icon lucide-circle-user h-6 w-6 text-sky-600"
                >
                    <circle cx="12" cy="12" r="10" />
                    <circle cx="12" cy="10" r="3" />
                    <path d="M7 20.662V19a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v1.662" />
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-gray-700">Profile User</h1>
                <p class="mt-1 text-gray-500">Manage your profile information and account settings</p>
            </div>
        </div>

        <!-- Profile Card -->
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white">
            <!-- Profile Header -->
            <div class="relative h-32 bg-gradient-to-r from-sky-500 to-sky-600">
                <div class="absolute -bottom-12 left-6">
                    <div
                        class="flex h-24 w-24 items-center justify-center rounded-3xl border-4 border-white bg-sky-200 shadow-lg backdrop-blur-sm"
                    >
                        <span class="text-2xl font-bold text-sky-600">
                            {{ App\Http\Controllers\Admin\ProfileController::getUserInitials($user->name) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Profile Content -->
            <div class="px-6 pt-16 pb-6">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">{{ $user->name }}</h2>
                        <div class="mt-1 flex items-center">
                            <div class="mr-2 h-2 w-2 rounded-full bg-green-400"></div>
                            <span class="text-sm text-gray-600">Administrator</span>
                        </div>
                    </div>
                </div>

                <!-- Profile Fields -->
                <div class="space-y-6">
                    <!-- Username/Name -->
                    <div class="flex items-center justify-between border-b border-gray-100 py-4">
                        <div class="flex-1">
                            <label class="mb-1 block text-sm font-medium tracking-wide text-gray-500 uppercase">
                                Nama Lengkap
                            </label>
                            <div class="font-medium text-gray-900">{{ $user->name }}</div>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="flex items-center justify-between border-b border-gray-100 py-4">
                        <div class="flex-1">
                            <label class="mb-1 block text-sm font-medium tracking-wide text-gray-500 uppercase">
                                Email Address
                            </label>
                            <div class="font-medium text-gray-900">{{ $user->email }}</div>
                        </div>
                    </div>

                    <!-- Role -->
                    <div class="flex items-center justify-between border-b border-gray-100 py-4">
                        <div class="flex-1">
                            <label class="mb-1 block text-sm font-medium tracking-wide text-gray-500 uppercase">
                                Role
                            </label>
                            <div class="font-medium text-gray-900">{{ $user->role }}</div>
                        </div>
                    </div>

                    <!-- Password -->
                    <div class="flex items-center justify-between py-4">
                        <div class="flex-1">
                            <label class="mb-1 block text-sm font-medium tracking-wide text-gray-500 uppercase">
                                Password
                            </label>
                            <div class="font-medium text-gray-900">••••••••••••</div>
                        </div>
                        <button
                            type="button"
                            onclick="openChangePasswordModal()"
                            class="rounded-lg bg-sky-500 px-4 py-2 text-sm font-medium text-white transition-colors duration-200 hover:bg-sky-600"
                        >
                            Change Password
                        </button>
                    </div>
                </div>

                <!-- Account Info -->
                <div class="mt-8 border-t border-gray-100 pt-6">
                    <div class="space-y-1 text-xs text-gray-500">
                        <p>Account created: {{ $user->created_at->format('M d, Y') }}</p>
                        <p>Last updated: {{ $user->updated_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div id="changePasswordModal" class="fixed inset-0 z-50 hidden h-full w-full overflow-y-auto bg-gray-600/50">
        <div class="relative top-20 mx-auto w-full max-w-md rounded-3xl border bg-white p-5 shadow-lg">
            <div class="mt-3">
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-gray-200 pb-4">
                    <h3 class="flex items-center text-xl font-semibold text-gray-900">
                        <i data-lucide="key" class="mr-3 h-5 w-5 text-sky-600"></i>
                        Change Password
                    </h3>
                    <button
                        type="button"
                        onclick="closeChangePasswordModal()"
                        class="text-gray-400 transition-colors duration-200 hover:text-gray-600"
                    >
                        <i data-lucide="x" class="h-5 w-5"></i>
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
                            <input
                                type="password"
                                id="current_password"
                                name="current_password"
                                required
                                class="w-full rounded-xl border border-gray-300 px-4 py-3 transition-colors duration-200 focus:border-sky-500 focus:ring-2 focus:ring-sky-500"
                            />
                            <button
                                type="button"
                                onclick="togglePassword('current_password')"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                            >
                                <i data-lucide="eye" class="h-4 w-4"></i>
                            </button>
                        </div>
                    </div>

                    <!-- New Password -->
                    <div class="space-y-2">
                        <label for="new_password" class="text-sm font-semibold text-gray-700">New Password</label>
                        <div class="relative">
                            <input
                                type="password"
                                id="new_password"
                                name="new_password"
                                required
                                class="w-full rounded-xl border border-gray-300 px-4 py-3 transition-colors duration-200 focus:border-sky-500 focus:ring-2 focus:ring-sky-500"
                            />
                            <button
                                type="button"
                                onclick="togglePassword('new_password')"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                            >
                                <i data-lucide="eye" class="h-4 w-4"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Confirm New Password -->
                    <div class="space-y-2">
                        <label for="new_password_confirmation" class="text-sm font-semibold text-gray-700">
                            Confirm New Password
                        </label>
                        <div class="relative">
                            <input
                                type="password"
                                id="new_password_confirmation"
                                name="new_password_confirmation"
                                required
                                class="w-full rounded-xl border border-gray-300 px-4 py-3 transition-colors duration-200 focus:border-sky-500 focus:ring-2 focus:ring-sky-500"
                            />
                            <button
                                type="button"
                                onclick="togglePassword('new_password_confirmation')"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
                            >
                                <i data-lucide="eye" class="h-4 w-4"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Password Requirements -->
                    <div class="rounded-xl bg-gray-50 p-3 text-xs text-gray-600">
                        <p class="mb-1 font-semibold">Password harus:</p>
                        <ul class="space-y-1">
                            <li>• Minimal 8 karakter</li>
                            <li>• Mengandung huruf dan angka</li>
                            <li>• Berbeda dari password saat ini</li>
                        </ul>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex space-x-3 pt-4">
                        <button
                            type="button"
                            onclick="closeChangePasswordModal()"
                            class="flex-1 rounded-xl bg-gray-100 px-4 py-3 font-semibold text-gray-700 transition-colors duration-200 hover:bg-gray-200"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="flex-1 rounded-xl bg-sky-600 px-4 py-3 font-semibold text-white transition-colors duration-200 hover:bg-sky-700"
                        >
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
