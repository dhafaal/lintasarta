<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-radial from-sky-300 to-white flex items-center justify-center">
    <div class="w-full max-w-md bg-white border-b-2 border-slate-300 rounded-2xl shadow-xl p-8">
        <div class="flex items-start justify-center mb-4">
            <img src="/Logo-Lintasarta-new.webp" class="h-16" alt="Logo">
        </div>

        @if ($errors->any())
            <div class="mb-4 text-sm text-red-600 bg-red-50 border border-red-200 rounded-lg p-3">
                {{ $errors->first() }}
            </div>
        @endif

        <form id="loginForm" method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" required value="{{ old('email') }}"
                    class="input-style"
                >
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <div class="relative">
                    <input type="password" name="password" id="password" required
                        class="input-style"
                    >
                    {{-- <button type="button" id="togglePassword"
                        class="absolute inset-y-0 right-3 text-gray-400 hover:text-gray-600"
                    >
                        Show
                    </button> --}}
                </div>
            </div>

            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center gap-2 text-gray-600">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded border border-gray-200">
                    Remember me
                </label>
                <a href="{{ route('password.request') }}" class="text-sky-600 hover:underline">
                    Forgot password?
                </a>
            </div>

            <button type="submit" id="submitBtn"
                class="button-style mb-4"
            >
                Login
            </button>
        </form>
    </div>

    <script>
        const form = document.getElementById('loginForm');
        const btn = document.getElementById('submitBtn');
        const toggle = document.getElementById('togglePassword');
        const password = document.getElementById('password');

        toggle.addEventListener('click', () => {
            const isHidden = password.type === 'password';
            password.type = isHidden ? 'text' : 'password';
            toggle.textContent = isHidden ? 'Hide' : 'Show';
        });

        form.addEventListener('submit', () => {
            btn.textContent = 'Logging in...';
            btn.disabled = true;
        });
    </script>
</body>
</html>
