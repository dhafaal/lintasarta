<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Agency Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-radial from-sky-200 via-sky-100 to-white flex items-center justify-center relative overflow-hidden">

    <div class="bg-white border border-gray-300 shadow-xl rounded-2xl px-8 py-10 w-full max-w-md z-10">
        <div class="flex justify-center mb-6">
            <img src="/Logo-Lintasarta-new.webp" alt="Logo" class="h-20">
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            <div>
                <label for="email" class="block text-gray-500 font-medium mb-1">Email Address</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-mail-icon lucide-mail">
                            <path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7" />
                            <rect x="2" y="4" width="20" height="16" rx="2" />
                        </svg>
                    </span>
                    <input type="email" name="email" id="email" required placeholder="Enter your email"
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent bg-gray-100">
                </div>
            </div>

            <div>
                <label for="password" class="block text-gray-500 font-medium mb-1">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-lock-icon lucide-lock">
                            <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                    </span>
                    <input type="password" name="password" id="password" required placeholder="Enter your password"
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent bg-gray-100">
                </div>
            </div>

            <button type="submit"
                class="w-full bg-gradient-to-r from-blue-400 to-blue-600 text-white py-2 rounded-lg font-semibold text-lg shadow hover:shadow-lg transition duration-300 flex justify-center items-center gap-2">
                Login
            </button>
        </form>

        <div class="mt-4 text-center">
            <a href="{{ route('password.request') }}" class="text-blue-500 hover:underline text-sm font-medium">
                Forgot your password?
            </a>
        </div>
    </div>
</body>

</html>