<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login to Dashboard</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <script src="https://cdn.tailwindcss.com"></script>
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

<body class="min-h-screen bg-radial from-sky-300 via-sky-100 to-white flex items-center justify-center overflow-hidden relative">

  <div id="loginBox" class="bg-white/90 backdrop-blur-lg border border-sky-200 shadow-2xl rounded-2xl p-8 w-full max-w-md">
    <!-- Logo -->
    <div class="flex justify-center mb-6">
      <img src="/Logo-Lintasarta-new.webp" alt="Logo" class="h-20">
    </div>

    <!-- Error message dari server -->
    @if ($errors->any())
      <div id="serverError" class="mb-4 p-3 rounded-lg bg-red-100 text-red-600 text-sm animate-shake">
        {{ $errors->first() }}
      </div>
    @endif

    <form id="loginForm" method="POST" action="{{ route('login') }}" class="space-y-6" novalidate>
      @csrf

      <!-- Email -->
      <div>
        <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email Address</label>
        <input type="email" name="email" id="email" value="{{ old('email') }}"
               placeholder="Enter your email"
               class="w-full px-4 py-2 border border-sky-300 rounded-lg bg-sky-50/50 focus:outline-none focus:ring-2 focus:ring-sky-400 hover:shadow-md transition duration-300">
        <p id="emailError" class="hidden text-sm text-red-600 mt-1"></p>
      </div>

      <!-- Password -->
      <div>
        <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
        <div class="relative">
          <input type="password" name="password" id="password" placeholder="Enter your password"
                 class="w-full px-4 pr-12 py-2 border border-sky-300 rounded-lg bg-sky-50/50 focus:outline-none focus:ring-2 focus:ring-sky-400 hover:shadow-md transition duration-300">
          <button type="button" id="togglePassword"
                  class="absolute inset-y-0 right-2 flex items-center justify-center text-gray-500 hover:text-sky-600 transition w-12 h-full">
            <!-- Eye open -->
            <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                 class="lucide lucide-eye transition-all duration-300 ease-in-out opacity-100 scale-100">
              <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
              <circle cx="12" cy="12" r="3" />
            </svg>
            <!-- Eye closed -->
            <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                 class="lucide lucide-eye-off transition-all duration-300 ease-in-out opacity-0 scale-90 hidden">
              <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20C5 20 1 12 1 12
                       a21.86 21.86 0 0 1 5.17-6.88M9.9 4.24
                       A10.94 10.94 0 0 1 12 4c7 0 11 8 11 8
                       a21.86 21.86 0 0 1-2.88 4.27M12 12
                       a3 3 0 0 1-3-3m6 0a3 3 0 0 1-3 3z" />
              <line x1="1" y1="1" x2="23" y2="23" />
            </svg>
          </button>
        </div>
        <p id="passwordError" class="hidden text-sm text-red-600 mt-1"></p>
      </div>

      <!-- Remember Me + Forgot Password -->
      <div class="flex items-center justify-between text-sm">
        <label class="flex items-center gap-2 text-gray-700">
          <input type="checkbox" name="remember" class="rounded border-sky-300 text-sky-500 focus:ring-sky-400"
                 {{ old('remember') ? 'checked' : '' }}>
          Remember me
        </label>
        <a href="{{ route('password.request') }}" class="text-sky-500 hover:underline font-medium transition">
          Forgot your password?
        </a>
      </div>

      <!-- Submit -->
      <button id="submitBtn" type="submit"
              class="w-full bg-[#1E90FF] text-white py-2 rounded-xl font-semibold text-lg shadow-md hover:shadow-lg hover:bg-[#1E90FF]/90 transition duration-300 flex justify-center items-center gap-2">
        <span id="btnText">Login</span>
        <svg id="loadingSpinner" class="hidden animate-spin h-5 w-5 text-white"
             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
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
        emailError.textContent = "Email is required.";
        emailError.classList.remove('hidden');
        valid = false;
      }
      if (password.value.trim() === '') {
        passwordError.textContent = "Password is required.";
        passwordError.classList.remove('hidden');
        valid = false;
      }

      if (!valid) {
        loginBox.classList.add('animate-shake');
        setTimeout(() => loginBox.classList.remove('animate-shake'), 600);
        return;
      }

      // Tampilkan spinner
      btnText.textContent = "Logging in...";
      loadingSpinner.classList.remove('hidden');
      submitBtn.disabled = true;

      loginForm.submit();
    });
  </script>
</body>
</html>