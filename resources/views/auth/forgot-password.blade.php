<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @keyframes shake {
            10%, 90% { transform: translateX(-2px); }
            20%, 80% { transform: translateX(4px); }
            30%, 50%, 70% { transform: translateX(-6px); }
            40%, 60% { transform: translateX(6px); }
        }

        .animate-shake { animation: shake 0.6s; }
    </style>
</head>
<body class="min-h-screen bg-radial from-sky-200 via-sky-100 to-white flex items-center justify-center overflow-hidden relative">
    <div id="forgotPasswordBox" class="bg-white/90 backdrop-blur-lg border border-sky-200 shadow-2xl rounded-2xl p-8 w-full max-w-md">
        <!-- Logo -->
        <div class="flex justify-center mb-6">
            <img src="/Logo-Lintasarta-new.webp" alt="Logo" class="h-20">
        </div>

        <!-- Status / Alert -->
        @if (session('status'))
            <div class="mb-4 p-3 rounded-lg bg-green-100 text-green-700 text-sm animate-shake">
                {{ session('status') }}
            </div>
        @endif

        <!-- Error -->
        @if ($errors->any())
            <div id="serverError" class="mb-4 p-3 rounded-lg bg-red-100 text-red-600 text-sm animate-shake">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $step = session('step', 'email');
            $email = session('email');
        @endphp

        <!-- Step 1: Masukkan Email -->
        @if ($step === 'email')
            <form id="emailForm" method="POST" action="{{ route('password.send.otp') }}" class="space-y-6" novalidate>
                @csrf
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" id="email" class="w-full px-4 py-2 border border-sky-300 rounded-lg bg-sky-50/50 focus:outline-none focus:ring-2 focus:ring-sky-400 hover:shadow-md transition duration-300" required>
                    <p id="emailError" class="hidden text-sm text-red-600 mt-1"></p>
                </div>
                <button id="submitEmailBtn" type="submit" class="w-full bg-[#1E90FF] text-white py-2 rounded-xl font-semibold text-lg shadow-md hover:shadow-lg hover:bg-[#1E90FF]/90 transition duration-300 flex justify-center items-center gap-2">
                    <span id="btnTextEmail">Kirim OTP</span>
                    <svg id="loadingSpinnerEmail" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                </button>
            </form>
        @endif

        <!-- Step 2: Masukkan OTP -->
        @if ($step === 'otp')
            <form id="otpForm" method="POST" action="{{ route('password.verify.otp') }}" class="space-y-6" novalidate>
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                <div>
                    <label for="otp" class="block text-sm font-semibold text-gray-700 mb-1">Kode OTP</label>
                    <input type="number" name="otp" id="otp" class="w-full px-4 py-2 border border-sky-300 rounded-lg bg-sky-50/50 focus:outline-none focus:ring-2 focus:ring-sky-400 hover:shadow-md transition duration-300" required>
                    <p id="otpError" class="hidden text-sm text-red-600 mt-1"></p>
                </div>
                <button id="submitOtpBtn" type="submit" class="w-full bg-[#1E90FF] text-white py-2 rounded-xl font-semibold text-lg shadow-md hover:shadow-lg hover:bg-[#1E90FF]/90 transition duration-300 flex justify-center items-center gap-2">
                    <span id="btnTextOtp">Verifikasi OTP</span>
                    <svg id="loadingSpinnerOtp" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                </button>
            </form>
        @endif

        <!-- Step 3: Reset Password -->
        @if ($step === 'reset')
            <form id="resetForm" method="POST" action="{{ route('password.reset') }}" class="space-y-6" novalidate>
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Password Baru</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" class="w-full px-4 py-2 border border-sky-300 rounded-lg bg-sky-50/50 focus:outline-none focus:ring-2 focus:ring-sky-400 hover:shadow-md transition duration-300" required>
                        <button type="button" id="togglePassword" class="absolute inset-y-0 right-2 flex items-center justify-center text-gray-500 hover:text-sky-600 transition w-12 h-full">
                            <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye transition-all duration-300 ease-in-out opacity-100 scale-100">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                            <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye-off transition-all duration-300 ease-in-out opacity-0 scale-90 hidden">
                                <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20C5 20 1 12 1 12 a21.86 21.86 0 0 1 5.17-6.88M9.9 4.24 A10.94 10.94 0 0 1 12 4c7 0 11 8 11 8 a21.86 21.86 0 0 1-2.88 4.27M12 12 a3 3 0 0 1-3-3m6 0a3 3 0 0 1-3 3z" />
                                <line x1="1" y1="1" x2="23" y2="23" />
                            </svg>
                        </button>
                    </div>
                    <p id="passwordError" class="hidden text-sm text-red-600 mt-1"></p>
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1">Konfirmasi Password</label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="password_confirmation" class="w-full px-4 py-2 border border-sky-300 rounded-lg bg-sky-50/50 focus:outline-none focus:ring-2 focus:ring-sky-400 hover:shadow-md transition duration-300" required>
                        <button type="button" id="toggleConfirmPassword" class="absolute inset-y-0 right-2 flex items-center justify-center text-gray-500 hover:text-sky-600 transition w-12 h-full">
                            <svg id="eyeOpenConfirm" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye transition-all duration-300 ease-in-out opacity-100 scale-100">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                            <svg id="eyeClosedConfirm" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye-off transition-all duration-300 ease-in-out opacity-0 scale-90 hidden">
                                <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20C5 20 1 12 1 12 a21.86 21.86 0 0 1 5.17-6.88M9.9 4.24 A10.94 10.94 0 0 1 12 4c7 0 11 8 11 8 a21.86 21.86 0 0 1-2.88 4.27M12 12 a3 3 0 0 1-3-3m6 0a3 3 0 0 1-3 3z" />
                                <line x1="1" y1="1" x2="23" y2="23" />
                            </svg>
                        </button>
                    </div>
                    <p id="passwordConfirmError" class="hidden text-sm text-red-600 mt-1"></p>
                </div>
                <button id="submitResetBtn" type="submit" class="w-full bg-[#1E90FF] text-white py-2 rounded-xl font-semibold text-lg shadow-md hover:shadow-lg hover:bg-[#1E90FF]/90 transition duration-300 flex justify-center items-center gap-2">
                    <span id="btnTextReset">Reset Password</span>
                    <svg id="loadingSpinnerReset" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                </button>
            </form>
        @endif
    </div>

    <script>
        // Email Form Validation
        const emailForm = document.getElementById('emailForm');
        if (emailForm) {
            const forgotPasswordBox = document.getElementById('forgotPasswordBox');
            const submitEmailBtn = document.getElementById('submitEmailBtn');
            const btnTextEmail = document.getElementById('btnTextEmail');
            const loadingSpinnerEmail = document.getElementById('loadingSpinnerEmail');

            emailForm.addEventListener('submit', function (e) {
                e.preventDefault();
                let valid = true;

                const email = document.getElementById('email');
                const emailError = document.getElementById('emailError');

                emailError.classList.add('hidden');

                if (email.value.trim() === '') {
                    emailError.textContent = "Email is required.";
                    emailError.classList.remove('hidden');
                    valid = false;
                }

                if (!valid) {
                    forgotPasswordBox.classList.add('animate-shake');
                    setTimeout(() => forgotPasswordBox.classList.remove('animate-shake'), 600);
                    return;
                }

                btnTextEmail.textContent = "Sending OTP...";
                loadingSpinnerEmail.classList.remove('hidden');
                submitEmailBtn.disabled = true;

                emailForm.submit();
            });
        }

        // OTP Form Validation
        const otpForm = document.getElementById('otpForm');
        if (otpForm) {
            const forgotPasswordBox = document.getElementById('forgotPasswordBox');
            const submitOtpBtn = document.getElementById('submitOtpBtn');
            const btnTextOtp = document.getElementById('btnTextOtp');
            const loadingSpinnerOtp = document.getElementById('loadingSpinnerOtp');

            otpForm.addEventListener('submit', function (e) {
                e.preventDefault();
                let valid = true;

                const otp = document.getElementById('otp');
                const otpError = document.getElementById('otpError');

                otpError.classList.add('hidden');

                if (otp.value.trim() === '') {
                    otpError.textContent = "Kode OTP is required.";
                    otpError.classList.remove('hidden');
                    valid = false;
                }

                if (!valid) {
                    forgotPasswordBox.classList.add('animate-shake');
                    setTimeout(() => forgotPasswordBox.classList.remove('animate-shake'), 600);
                    return;
                }

                btnTextOtp.textContent = "Verifying OTP...";
                loadingSpinnerOtp.classList.remove('hidden');
                submitOtpBtn.disabled = true;

                otpForm.submit();
            });
        }

        // Reset Password Form Validation
        const resetForm = document.getElementById('resetForm');
        if (resetForm) {
            const forgotPasswordBox = document.getElementById('forgotPasswordBox');
            const submitResetBtn = document.getElementById('submitResetBtn');
            const btnTextReset = document.getElementById('btnTextReset');
            const loadingSpinnerReset = document.getElementById('loadingSpinnerReset');

            // Toggle show/hide password
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

            // Toggle show/hide confirm password
            const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
            const confirmPasswordInput = document.getElementById('password_confirmation');
            const eyeOpenConfirm = document.getElementById('eyeOpenConfirm');
            const eyeClosedConfirm = document.getElementById('eyeClosedConfirm');

            toggleConfirmPassword.addEventListener('click', () => {
                const isConfirmPassword = confirmPasswordInput.type === 'password';
                confirmPasswordInput.type = isConfirmPassword ? 'text' : 'password';

                if (isConfirmPassword) {
                    eyeOpenConfirm.classList.add('opacity-0', 'scale-90', 'hidden');
                    eyeClosedConfirm.classList.remove('hidden');
                    setTimeout(() => eyeClosedConfirm.classList.remove('opacity-0', 'scale-90'), 10);
                } else {
                    eyeClosedConfirm.classList.add('opacity-0', 'scale-90', 'hidden');
                    eyeOpenConfirm.classList.remove('hidden');
                    setTimeout(() => eyeOpenConfirm.classList.remove('opacity-0', 'scale-90'), 10);
                }
            });

            resetForm.addEventListener('submit', function (e) {
                e.preventDefault();
                let valid = true;

                const password = document.getElementById('password');
                const passwordConfirm = document.getElementById('password_confirmation');
                const passwordError = document.getElementById('passwordError');
                const passwordConfirmError = document.getElementById('passwordConfirmError');

                passwordError.classList.add('hidden');
                passwordConfirmError.classList.add('hidden');

                if (password.value.trim() === '') {
                    passwordError.textContent = "Password is required.";
                    passwordError.classList.remove('hidden');
                    valid = false;
                }

                if (passwordConfirm.value.trim() === '') {
                    passwordConfirmError.textContent = "Konfirmasi Password is required.";
                    passwordConfirmError.classList.remove('hidden');
                    valid = false;
                }

                if (password.value !== passwordConfirm.value) {
                    passwordConfirmError.textContent = "Password and confirmation must match.";
                    passwordConfirmError.classList.remove('hidden');
                    valid = false;
                }

                if (!valid) {
                    forgotPasswordBox.classList.add('animate-shake');
                    setTimeout(() => forgotPasswordBox.classList.remove('animate-shake'), 600);
                    return;
                }

                btnTextReset.textContent = "Resetting Password...";
                loadingSpinnerReset.classList.remove('hidden');
                submitResetBtn.disabled = true;

                resetForm.submit();
            });
        }
    </script>
</body>
</html>