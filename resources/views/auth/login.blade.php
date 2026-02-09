<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Login to Dashboard</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            @keyframes shake {
                10%,
                90% {
                    transform: translateX(-2px);
                }

                20%,
                80% {
                    transform: translateX(4px);
                }

                30%,
                50%,
                70% {
                    transform: translateX(-6px);
                }

                40%,
                60% {
                    transform: translateX(6px);
                }
            }

            .animate-shake {
                animation: shake 0.6s;
            }
        </style>
    </head>

    <body
        class="relative flex min-h-screen items-center justify-center overflow-hidden bg-radial from-sky-200 via-sky-100 to-white"
    >
        <div
            id="loginBox"
            class="w-full max-w-md rounded-2xl border border-sky-200 bg-white/90 p-8 shadow-2xl backdrop-blur-lg"
        >
            <!-- Logo -->
            <div class="mb-6 flex justify-center">
                <img src="/Logo-Lintasarta-new.webp" alt="Logo" class="h-20" />
            </div>

            <!-- Error message dari server -->
            @if ($errors->any())
                <div id="serverError" class="animate-shake mb-4 rounded-lg bg-red-100 p-3 text-sm text-red-600">
                    {{ $errors->first() }}
                </div>
            @endif

            <form id="loginForm" method="POST" action="{{ route('login') }}" class="space-y-6" novalidate>
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="mb-1 block text-sm font-semibold text-gray-700">Email Address</label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="{{ old('email') }}"
                        placeholder="Enter your email"
                        class="w-full rounded-lg border border-sky-300 bg-sky-50/50 px-4 py-2 transition duration-300 hover:shadow-md focus:ring-2 focus:ring-sky-400 focus:outline-none"
                    />
                    <p id="emailError" class="mt-1 hidden text-sm text-red-600"></p>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="mb-1 block text-sm font-semibold text-gray-700">Password</label>
                    <div class="relative">
                        <input
                            type="password"
                            name="password"
                            id="password"
                            placeholder="Enter your password"
                            class="w-full rounded-lg border border-sky-300 bg-sky-50/50 px-4 py-2 pr-12 transition duration-300 hover:shadow-md focus:ring-2 focus:ring-sky-400 focus:outline-none"
                        />
                        <button
                            type="button"
                            id="togglePassword"
                            class="absolute inset-y-0 right-2 flex h-full w-12 items-center justify-center text-gray-500 transition hover:text-sky-600"
                        >
                            <!-- Eye open -->
                            <svg
                                id="eyeOpen"
                                xmlns="http://www.w3.org/2000/svg"
                                width="24"
                                height="24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                class="lucide lucide-eye scale-100 opacity-100 transition-all duration-300 ease-in-out"
                            >
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                            <!-- Eye closed -->
                            <svg
                                id="eyeClosed"
                                xmlns="http://www.w3.org/2000/svg"
                                width="24"
                                height="24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                class="lucide lucide-eye-off hidden scale-90 opacity-0 transition-all duration-300 ease-in-out"
                            >
                                <path
                                    d="M17.94 17.94A10.94 10.94 0 0 1 12 20C5 20 1 12 1 12
                       a21.86 21.86 0 0 1 5.17-6.88M9.9 4.24
                       A10.94 10.94 0 0 1 12 4c7 0 11 8 11 8
                       a21.86 21.86 0 0 1-2.88 4.27M12 12
                       a3 3 0 0 1-3-3m6 0a3 3 0 0 1-3 3z"
                                />
                                <line x1="1" y1="1" x2="23" y2="23" />
                            </svg>
                        </button>
                    </div>
                    <p id="passwordError" class="mt-1 hidden text-sm text-red-600"></p>
                </div>

                <!-- Remember Me + Forgot Password -->
                <div class="flex items-center justify-between text-sm">
                    <label class="group flex cursor-pointer items-center gap-2 text-gray-700">
                        <input
                            type="checkbox"
                            name="remember"
                            id="remember"
                            class="rounded border-sky-300 text-sky-500 transition-colors focus:ring-sky-400"
                            {{ old('remember') ? 'checked' : '' }}
                        />
                        <span class="transition-colors select-none group-hover:text-sky-600">
                            Remember me for 30 days
                        </span>
                        <div class="relative">
                            <svg
                                class="h-4 w-4 cursor-help text-gray-400 transition-colors hover:text-sky-500"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                onclick="toggleRememberInfo()"
                            >
                                <circle cx="12" cy="12" r="10"></circle>
                                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                <line x1="12" y1="17" x2="12.01" y2="17"></line>
                            </svg>
                            <div
                                id="rememberInfo"
                                class="absolute bottom-6 left-0 z-10 hidden w-64 rounded-lg bg-gray-800 p-3 text-xs text-white shadow-lg"
                            >
                                <div class="mb-2 font-semibold">Secure Remember Me</div>
                                <ul class="space-y-1 text-gray-300">
                                    <li>• Uses encrypted tokens</li>
                                    <li>• Device fingerprinting</li>
                                    <li>• Automatic expiry in 30 days</li>
                                    <li>• Revoked on logout</li>
                                </ul>
                                <div class="absolute -bottom-1 left-4 h-2 w-2 rotate-45 transform bg-gray-800"></div>
                            </div>
                        </div>
                    </label>
                    <a
                        href="{{ route('password.request') }}"
                        class="font-medium text-sky-500 transition-colors hover:text-sky-600 hover:underline"
                    >
                        Forgot your password?
                    </a>
                </div>

                <!-- Submit -->
                <button
                    id="submitBtn"
                    type="submit"
                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#1E90FF] py-2 text-lg font-semibold text-white shadow-md transition duration-300 hover:bg-[#1E90FF]/90 hover:shadow-lg"
                >
                    <svg
                        id="loadingSpinner"
                        class="mr-2 hidden h-6 w-6 text-white/70"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        aria-hidden="true"
                    >
                        <path
                            fill="currentColor"
                            d="M12 1a11 11 0 1 0 11 11A11 11 0 0 0 12 1Zm0 19a8 8 0 1 1 8-8 8 8 0 0 1-8 8Z"
                            opacity=".25"
                        />
                        <path
                            fill="currentColor"
                            d="M10.14 1.16a11 11 0 0 0-9 8.92A1.59 1.59 0 0 0 2.46 12 1.52 1.52 0 0 0 4.11 10.7a8 8 0 0 1 6.66-6.61A1.42 1.42 0 0 0 12 2.69a1.57 1.57 0 0 0-1.86-1.53Z"
                        >
                            <animateTransform
                                attributeName="transform"
                                dur="0.75s"
                                repeatCount="indefinite"
                                type="rotate"
                                values="0 12 12;360 12 12"
                            />
                        </path>
                    </svg>
                    <span id="btnText">Login</span>
                </button>
            </form>
        </div>

        <script>
            const loginForm = document.getElementById('loginForm');
            const loginBox = document.getElementById('loginBox');
            const submitBtn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const loadingSpinner = document.getElementById('loadingSpinner');

            // Toggle show/hide password with animation
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const eyeOpen = document.getElementById('eyeOpen');
            const eyeClosed = document.getElementById('eyeClosed');

            togglePassword.addEventListener('click', () => {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';

                if (isPassword) {
                    eyeOpen.classList.add('opacity-0', 'scale-90', 'hidden');
                    eyeClosed.classList.remove('hidden');
                    setTimeout(() => eyeClosed.classList.remove('opacity-0', 'scale-90'), 10);
                } else {
                    eyeClosed.classList.add('opacity-0', 'scale-90', 'hidden');
                    eyeOpen.classList.remove('hidden');
                    setTimeout(() => eyeOpen.classList.remove('opacity-0', 'scale-90'), 10);
                }
            });

            // Remember me info toggle
            function toggleRememberInfo() {
                const info = document.getElementById('rememberInfo');
                info.classList.toggle('hidden');
            }

            // Close remember info when clicking outside
            document.addEventListener('click', function (e) {
                const info = document.getElementById('rememberInfo');
                const trigger = e.target.closest('svg');
                if (!trigger && !info.contains(e.target)) {
                    info.classList.add('hidden');
                }
            });

            // Enhanced security features
            function detectDeviceFingerprint() {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                ctx.textBaseline = 'top';
                ctx.font = '14px Arial';
                ctx.fillText('Device fingerprint', 2, 2);

                return {
                    screen: `${screen.width}x${screen.height}`,
                    timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
                    language: navigator.language,
                    platform: navigator.platform,
                    canvas: canvas.toDataURL(),
                    userAgent: navigator.userAgent.substring(0, 100), // Truncate for security
                };
            }

            // Add device fingerprint to form
            const deviceInfo = detectDeviceFingerprint();
            const fingerprintInput = document.createElement('input');
            fingerprintInput.type = 'hidden';
            fingerprintInput.name = 'device_fingerprint';
            fingerprintInput.value = btoa(JSON.stringify(deviceInfo));
            loginForm.appendChild(fingerprintInput);

            // Custom validation
            loginForm.addEventListener('submit', function (e) {
                e.preventDefault();
                let valid = true;

                const email = document.getElementById('email');
                const password = document.getElementById('password');
                const emailError = document.getElementById('emailError');
                const passwordError = document.getElementById('passwordError');

                emailError.classList.add('hidden');
                passwordError.classList.add('hidden');

                if (email.value.trim() === '') {
                    emailError.textContent = 'Email is required.';
                    emailError.classList.remove('hidden');
                    valid = false;
                }
                if (password.value.trim() === '') {
                    passwordError.textContent = 'Password is required.';
                    passwordError.classList.remove('hidden');
                    valid = false;
                }

                if (!valid) {
                    loginBox.classList.add('animate-shake');
                    setTimeout(() => loginBox.classList.remove('animate-shake'), 600);
                    return;
                }

                // Tampilkan spinner
                btnText.textContent = 'Logging in...';
                loadingSpinner.classList.remove('hidden');
                submitBtn.disabled = true;

                loginForm.submit();
            });
        </script>
    </body>
</html>
