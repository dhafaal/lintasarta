@extends('layouts.user')

@section('title', 'Profile')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-500">
                <i data-lucide="user" class="h-6 w-6 text-white"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Profile</h1>
                <p class="text-sm text-gray-600">Manage your account settings</p>
            </div>
        </div>

        <!-- Profile Card -->
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
            <!-- Profile Header -->
            <div class="relative h-24 bg-sky-500">
                <div class="absolute -bottom-10 left-5">
                    <div
                        class="flex h-20 w-20 items-center justify-center rounded-xl border-2 border-gray-200 bg-white shadow-sm"
                    >
                        <span class="text-xl font-bold text-sky-600">
                            {{ App\Http\Controllers\Users\ProfileController::getUserInitials($user->name) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Profile Content -->
            <div class="px-5 pt-14 pb-5">
                <div class="mb-6">
                    <h2 class="text-lg font-bold text-gray-900">{{ $user->name }}</h2>
                    <div class="mt-1 flex items-center">
                        <div class="mr-2 h-2 w-2 rounded-full bg-green-500"></div>
                        <span class="text-sm text-gray-600">Employee</span>
                    </div>
                </div>

                <!-- Profile Fields -->
                <div class="space-y-4">
                    <!-- Username/Name -->
                    <div class="border-b border-gray-200 py-3">
                        <label class="mb-1 block text-xs font-medium text-gray-500 uppercase">Nama Lengkap</label>
                        <div class="font-medium text-gray-900">{{ $user->name }}</div>
                    </div>

                    <!-- Email -->
                    <div class="border-b border-gray-200 py-3">
                        <label class="mb-1 block text-xs font-medium text-gray-500 uppercase">Email Address</label>
                        <div class="font-medium text-gray-900">{{ $user->email }}</div>
                    </div>

                    <!-- Role -->
                    <div class="border-b border-gray-200 py-3">
                        <label class="mb-1 block text-xs font-medium text-gray-500 uppercase">Role</label>
                        <div class="font-medium text-gray-900">{{ $user->role }}</div>
                    </div>

                    <!-- Password -->
                    <div class="flex items-center justify-between py-3">
                        <div class="flex-1">
                            <label class="mb-1 block text-xs font-medium text-gray-500 uppercase">Password</label>
                            <div class="font-medium text-gray-900">••••••••••••</div>
                        </div>
                        <button
                            type="button"
                            onclick="openChangePasswordModal()"
                            class="rounded-lg bg-sky-500 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-sky-600"
                        >
                            Change Password
                        </button>
                    </div>
                </div>

                <!-- Account Info -->
                <div class="mt-6 border-t border-gray-200 pt-4">
                    <div class="space-y-1 text-xs text-gray-500">
                        <p>Account created: {{ $user->created_at->format('M d, Y') }}</p>
                        <p>Last updated: {{ $user->updated_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div id="changePasswordModal" class="fixed inset-0 z-50 hidden h-full w-full overflow-y-auto bg-black/50">
        <div class="relative top-20 mx-auto w-full max-w-md p-5">
            <div class="rounded-xl bg-white shadow-lg">
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-gray-200 p-5">
                    <h3 class="flex items-center gap-2 text-lg font-bold text-gray-900">
                        <i data-lucide="key" class="h-5 w-5 text-sky-600"></i>
                        Change Password
                    </h3>
                    <button
                        type="button"
                        onclick="closeChangePasswordModal()"
                        class="text-gray-400 hover:text-gray-600"
                    >
                        <i data-lucide="x" class="h-5 w-5"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <form action="{{ route('user.profile.change-password') }}" method="POST" class="space-y-4 p-5">
                    @csrf

                    <!-- Current Password -->
                    <div>
                        <label for="current_password" class="mb-1 block text-sm font-medium text-gray-700">
                            Current Password
                        </label>
                        <div class="relative">
                            <input
                                type="password"
                                id="current_password"
                                name="current_password"
                                required
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-sky-500 focus:ring-2 focus:ring-sky-500"
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
                    <div>
                        <label for="new_password" class="mb-1 block text-sm font-medium text-gray-700">
                            New Password
                        </label>
                        <div class="relative">
                            <input
                                type="password"
                                id="new_password"
                                name="new_password"
                                required
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-sky-500 focus:ring-2 focus:ring-sky-500"
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
                    <div>
                        <label for="new_password_confirmation" class="mb-1 block text-sm font-medium text-gray-700">
                            Confirm New Password
                        </label>
                        <div class="relative">
                            <input
                                type="password"
                                id="new_password_confirmation"
                                name="new_password_confirmation"
                                required
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:border-sky-500 focus:ring-2 focus:ring-sky-500"
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
                    <div class="rounded-lg bg-sky-50 p-3 text-xs text-gray-700">
                        <p class="mb-1 font-medium">Password requirements:</p>
                        <ul class="space-y-1">
                            <li>• Minimal 8 karakter</li>
                            <li>• Mengandung huruf dan angka</li>
                            <li>• Berbeda dari password saat ini</li>
                        </ul>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex gap-3">
                        <button
                            type="button"
                            onclick="closeChangePasswordModal()"
                            class="flex-1 rounded-lg bg-gray-100 px-4 py-2 font-medium text-gray-700 transition-colors hover:bg-gray-200"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="flex-1 rounded-lg bg-sky-500 px-4 py-2 font-medium text-white transition-colors hover:bg-sky-600"
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
                @if(session('success'))
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

                @if($errors->any())
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
