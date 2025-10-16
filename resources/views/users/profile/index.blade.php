@extends('layouts.user')

@section('title', 'Profile')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-sky-500 rounded-xl flex items-center justify-center">
            <i data-lucide="user" class="w-6 h-6 text-white"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Profile</h1>
            <p class="text-sm text-gray-600">Manage your account settings</p>
        </div>
    </div>

    <!-- Profile Card -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <!-- Profile Header -->
        <div class="relative bg-sky-500 h-24">
            <div class="absolute -bottom-10 left-5">
                <div class="w-20 h-20 rounded-xl bg-white flex items-center justify-center border-2 border-gray-200 shadow-sm">
                    <span class="text-xl font-bold text-sky-600">
                        {{ App\Http\Controllers\Users\ProfileController::getUserInitials($user->name) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Profile Content -->
        <div class="pt-14 pb-5 px-5">
            <div class="mb-6">
                <h2 class="text-lg font-bold text-gray-900">{{ $user->name }}</h2>
                <div class="flex items-center mt-1">
                    <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                    <span class="text-sm text-gray-600">Employee</span>
                </div>
            </div>

            <!-- Profile Fields -->
            <div class="space-y-4">
                <!-- Username/Name -->
                <div class="py-3 border-b border-gray-200">
                    <label class="block text-xs font-medium text-gray-500 uppercase mb-1">
                        Nama Lengkap
                    </label>
                    <div class="text-gray-900 font-medium">{{ $user->name }}</div>
                </div>

                <!-- Email -->
                <div class="py-3 border-b border-gray-200">
                    <label class="block text-xs font-medium text-gray-500 uppercase mb-1">
                        Email Address
                    </label>
                    <div class="text-gray-900 font-medium">{{ $user->email }}</div>
                </div>

                <!-- Role -->
                <div class="py-3 border-b border-gray-200">
                    <label class="block text-xs font-medium text-gray-500 uppercase mb-1">
                        Role
                    </label>
                    <div class="text-gray-900 font-medium">{{ $user->role }}</div>
                </div>

                <!-- Password -->
                <div class="flex items-center justify-between py-3">
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-500 uppercase mb-1">
                            Password
                        </label>
                        <div class="text-gray-900 font-medium">••••••••••••</div>
                    </div>
                    <button type="button" onclick="openChangePasswordModal()"
                        class="px-4 py-2 bg-sky-500 hover:bg-sky-600 text-white text-sm font-medium rounded-lg transition-colors">
                        Change
                    </button>
                </div>
            </div>

            <!-- Account Info -->
            <div class="mt-6 pt-4 border-t border-gray-200">
                <div class="text-xs text-gray-500 space-y-1">
                    <p>Account created: {{ $user->created_at->format('M d, Y') }}</p>
                    <p>Last updated: {{ $user->updated_at->format('M d, Y') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div id="changePasswordModal" class="fixed inset-0 bg-black/50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 w-full max-w-md">
        <div class="bg-white rounded-xl shadow-lg">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-5 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i data-lucide="key" class="w-5 h-5 text-sky-600"></i>
                    Change Password
                </h3>
                <button type="button" onclick="closeChangePasswordModal()" 
                    class="text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <form action="{{ route('user.profile.change-password') }}" method="POST" class="p-5 space-y-4">
                @csrf
                
                <!-- Current Password -->
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">
                        Current Password
                    </label>
                    <div class="relative">
                        <input type="password" id="current_password" name="current_password" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <button type="button" onclick="togglePassword('current_password')"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- New Password -->
                <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">
                        New Password
                    </label>
                    <div class="relative">
                        <input type="password" id="new_password" name="new_password" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <button type="button" onclick="togglePassword('new_password')"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- Confirm New Password -->
                <div>
                    <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                        Confirm New Password
                    </label>
                    <div class="relative">
                        <input type="password" id="new_password_confirmation" name="new_password_confirmation" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        <button type="button" onclick="togglePassword('new_password_confirmation')"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- Password Requirements -->
                <div class="bg-sky-50 rounded-lg p-3 text-xs text-gray-700">
                    <p class="font-medium mb-1">Password requirements:</p>
                    <ul class="space-y-1">
                        <li>• Minimal 8 karakter</li>
                        <li>• Mengandung huruf dan angka</li>
                        <li>• Berbeda dari password saat ini</li>
                    </ul>
                </div>

                <!-- Modal Footer -->
                <div class="flex gap-3">
                    <button type="button" onclick="closeChangePasswordModal()"
                        class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-sky-500 text-white rounded-lg hover:bg-sky-600 transition-colors font-medium">
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
