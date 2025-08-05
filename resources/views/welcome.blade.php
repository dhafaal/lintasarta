<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Minimal Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 50%, #f9fafb 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
        }

        /* Subtle background pattern */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background:
                radial-gradient(circle at 25% 25%, rgba(255, 255, 255, 0.5) 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, rgba(243, 244, 246, 0.3) 0%, transparent 50%);
            pointer-events: none;
        }

        .form-container {
            background: linear-gradient(145deg, #ffffff 0%, #f9fafb 100%);
            border-radius: 20px;
            border: 1px solid rgba(243, 244, 246, 0.5);
            box-shadow:
                0 10px 40px rgba(0, 0, 0, 0.03),
                0 4px 20px rgba(0, 0, 0, 0.02),
                inset 0 1px 0 rgba(255, 255, 255, 0.9);
            width: 100%;
            max-width: 400px;
            padding: 3rem;
            position: relative;
            z-index: 1;
            backdrop-filter: blur(10px);
        }

        .form-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .logo-placeholder {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            border-radius: 10px;
            margin: 0 auto 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(229, 231, 235, 0.6);
        }

        .logo-placeholder::after {
            content: '';
            width: 16px;
            height: 16px;
            background: linear-gradient(135deg, #9ca3af 0%, #6b7280 100%);
            border-radius: 4px;
        }

        h1 {
            color: #111827;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }

        .subtitle {
            color: #6b7280;
            font-size: 0.875rem;
            font-weight: 400;
            line-height: 1.4;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        label {
            display: block;
            color: #374151;
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
            transition: color 0.2s ease;
        }

        .input-wrapper {
            position: relative;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: linear-gradient(145deg, #ffffff 0%, #f9fafb 100%);
            color: #111827;
            font-size: 0.875rem;
            font-weight: 400;
            transition: all 0.2s ease;
            outline: none;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #d1d5db;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(243, 244, 246, 0.5);
        }

        input[type="email"]:focus+label,
        input[type="password"]:focus+label {
            color: #111827;
        }

        .password-wrapper {
            position: relative;
        }

        .password-wrapper input[type="password"],
        .password-wrapper input[type="text"] {
            padding-right: 3rem;
        }

        .toggle-password {
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 3rem;
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            border-radius: 0 12px 12px 0;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .toggle-password:hover {
            color: #6b7280;
            background: rgba(243, 244, 246, 0.3);
        }

        .toggle-password:focus {
            outline: 2px solid #d1d5db;
            outline-offset: 2px;
        }

        .toggle-password:focus-visible {
            outline: 2px solid #374151;
            outline-offset: 2px;
        }

        .form-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            font-size: 0.875rem;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .remember-me input[type="checkbox"] {
            width: 16px;
            height: 16px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            background: #ffffff;
            cursor: pointer;
        }

        .remember-me label {
            margin: 0;
            color: #6b7280;
            font-weight: 400;
            cursor: pointer;
        }

        .forgot-password a {
            color: #6b7280;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .forgot-password a:hover {
            color: #374151;
        }

        .login-button {
            width: 100%;
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            color: #374151;
            border: 1px solid #d1d5db;
            padding: 0.875rem 1rem;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
        }

        .login-button:hover {
            background: linear-gradient(135deg, #e5e7eb 0%, #d1d5db 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
        }

        .login-button:active {
            transform: translateY(0);
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 2rem 0;
            color: #9ca3af;
            font-size: 0.875rem;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent 0%, #e5e7eb 50%, transparent 100%);
        }

        .divider span {
            padding: 0 1.5rem;
            background: linear-gradient(145deg, #ffffff 0%, #f9fafb 100%);
            font-weight: 500;
        }

        .alternative-login {
            text-align: center;
            margin-top: 1.5rem;
        }

        .alternative-login a {
            color: #6b7280;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .alternative-login a:hover {
            color: #374151;
        }

        /* Loading state */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .loading .login-button {
            background: linear-gradient(135deg, #e5e7eb 0%, #d1d5db 100%);
        }

        /* Success state */
        @keyframes subtle-pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.02);
            }
        }

        .success {
            animation: subtle-pulse 0.4s ease;
        }

        .success .login-button {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border-color: #bbf7d0;
            color: #166534;
        }

        /* Responsive */
        @media (max-width: 480px) {
            body {
                padding: 1rem;
            }

            .form-container {
                padding: 2rem;
            }

            h1 {
                font-size: 1.375rem;
            }
        }

        /* Subtle animation for container */
        @keyframes gentle-float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-2px);
            }
        }

        .form-container {
            animation: gentle-float 6s ease-in-out infinite;
        }
    </style>
</head>

<body>
    <div class="form-container" id="formContainer">
        <div class="form-header">
            <div class="logo-placeholder"></div>
            <h1>Welcome</h1>
            <p class="subtitle">Please sign in to your account</p>
        </div>

        <form id="loginForm">
            <div class="form-group">
                <label for="email">Email</label>
                <div class="input-wrapper">
                    <input type="email" id="email" name="email" required autocomplete="email"
                        placeholder="your@email.com">
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrapper password-wrapper">
                    <input type="password" id="password" name="password" required autocomplete="current-password"
                        placeholder="Enter your password">
                    <button type="button" class="toggle-password" id="togglePassword">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="form-footer">
                <div class="remember-me">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember me</label>
                </div>
                <div class="forgot-password">
                    <a href="#" onclick="showForgotPassword(); return false;">Forgot password?</a>
                </div>
            </div>

            <button type="submit" class="login-button" id="loginButton">
                Sign In
            </button>
        </form>

        <div class="divider">
            <span>or</span>
        </div>

        <div class="alternative-login">
            <a href="#" onclick="showSignup(); return false;">Don't have an account? Sign up</a>
        </div>
    </div>

    <script>
        // Password visibility toggle
        // Password visibility toggle
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = togglePassword.querySelector('svg');

        togglePassword.setAttribute('aria-label', 'Show password');
        togglePassword.setAttribute('title', 'Show password');

        togglePassword.addEventListener('click', function() {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');

            // Update ARIA and title attributes
            togglePassword.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
            togglePassword.setAttribute('title', isPassword ? 'Hide password' : 'Show password');

            // Update SVG icon
            eyeIcon.innerHTML = isPassword ?
                `<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                    <line x1="1" y1="1" x2="23" y2="23"/>` :
                `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                    <circle cx="12" cy="12" r="3"/>`;
            // Maintain focus on password input
            passwordInput.focus();
        });

        // Form submission
        const loginForm = document.getElementById('loginForm');
        const loginButton = document.getElementById('loginButton');
        const formContainer = document.getElementById('formContainer');

        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Add loading state
            formContainer.classList.add('loading');
            loginButton.innerHTML = `
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation: spin 1s linear infinite; margin-right: 8px;">
                    <path d="M21 12a9 9 0 11-6.219-8.56"/>
                </svg>
                Signing in...
            `;

            // Simulate login process
            setTimeout(() => {
                formContainer.classList.remove('loading');
                formContainer.classList.add('success');
                loginButton.innerHTML = `
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 8px;">
                        <polyline points="20,6 9,17 4,12"/>
                    </svg>
                    Welcome back
                `;

                setTimeout(() => {
                    alert('Login successful! (Demo)');
                    loginButton.innerHTML = 'Sign In';
                    formContainer.classList.remove('success');
                    loginForm.reset();
                }, 2000);
            }, 1500);
        });

        // Demo functions
        function showForgotPassword() {
            alert('Forgot password feature (Demo)');
        }

        function showSignup() {
            alert('Sign up page (Demo)');
        }

        // Add spin animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
        `;
        document.head.appendChild(style);

        // Input focus effects
        const inputs = document.querySelectorAll('input[type="email"], input[type="password"]');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                const label = this.parentElement.parentElement.querySelector('label');
                if (label) label.style.color = '#111827';
            });

            input.addEventListener('blur', function() {
                const label = this.parentElement.parentElement.querySelector('label');
                if (label && !this.value) {
                    label.style.color = '#374151';
                }
            });
        });

        // Subtle hover effects for the container
        formContainer.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-4px)';
            this.style.boxShadow =
                '0 15px 50px rgba(0, 0, 0, 0.05), 0 6px 25px rgba(0, 0, 0, 0.03), inset 0 1px 0 rgba(255, 255, 255, 0.9)';
        });

        formContainer.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0px)';
            this.style.boxShadow =
                '0 10px 40px rgba(0, 0, 0, 0.03), 0 4px 20px rgba(0, 0, 0, 0.02), inset 0 1px 0 rgba(255, 255, 255, 0.9)';
        });
    </script>
</body>

</html>
