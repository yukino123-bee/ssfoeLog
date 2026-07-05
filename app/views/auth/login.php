<?php
$title = "Login - " . APP_NAME;
$captcha_secret = $_SESSION['login_captcha_token'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Syne:wght@600;700;800&display=swap" rel="stylesheet">
    <script src="<?php echo asset_url('js/tailwind.js'); ?>"></script>
    <link rel="stylesheet" href="<?php echo asset_url('css/all.min.css'); ?>">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { DEFAULT: '#c62828', dark: '#111', rose: '#e11d48' },
                    },
                    fontFamily: {
                        display: ['Syne', 'Plus Jakarta Sans', 'sans-serif'],
                    },
                },
            },
        };
    </script>
    <style>
        @keyframes loginFadeUp {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes loginSpin {
            to { transform: rotate(360deg); }
        }
        @keyframes loginCheckPop {
            0% { transform: scale(0); opacity: 0; }
            60% { transform: scale(1.15); }
            100% { transform: scale(1); opacity: 1; }
        }
        .animate-fade-up {
            animation: loginFadeUp 0.65s cubic-bezier(0.22, 1, 0.36, 1) forwards;
            opacity: 0;
        }
        .delay-1 { animation-delay: 0.12s; }
        .delay-2 { animation-delay: 0.22s; }
        .delay-3 { animation-delay: 0.32s; }
        .delay-4 { animation-delay: 0.42s; }
        
        .login-panel-red {
            background: linear-gradient(145deg, #881337 0%, #be123c 100%);
            position: relative;
            overflow: hidden;
        }
        .login-panel-red::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none;
        }
        /* Replica reCAPTCHA (Light Theme) */
        .recaptcha-replica {
            width: 100%;
            max-width: 302px;
            min-height: 76px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: #fafafa;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            display: flex;
            align-items: stretch;
            justify-content: space-between;
            padding: 0 10px 0 12px;
            transition: border-color 0.25s ease, box-shadow 0.25s ease;
            margin: 0 auto;
        }
        .recaptcha-replica.is-verified {
            border-color: #d1d5db;
            background: #ffffff;
        }
        .recaptcha-replica.is-verified .recaptcha-box {
            border-color: #22c55e;
            background: #f0fdf4;
        }
        .recaptcha-box.is-disabled {
            cursor: default;
        }
        .recaptcha-left {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
            min-width: 0;
        }
        .recaptcha-box {
            width: 28px;
            height: 28px;
            border: 2px solid #d1d5db;
            border-radius: 4px;
            background: #ffffff;
            cursor: pointer;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: border-color 0.2s, background-color 0.2s;
        }
        .recaptcha-box:hover:not(.is-disabled) {
            border-color: #9ca3af;
        }
        .recaptcha-box:focus-visible {
            outline: 2px solid #e11d48;
            outline-offset: 2px;
        }
        .recaptcha-box.is-loading {
            cursor: wait;
            border-color: #e11d48;
        }
        .recaptcha-spinner {
            width: 20px;
            height: 20px;
            border: 2px solid #e5e7eb;
            border-top-color: #e11d48;
            border-radius: 50%;
            animation: loginSpin 0.7s linear infinite;
        }
        .recaptcha-check {
            font-size: 18px;
            color: #22c55e;
            animation: loginCheckPop 0.45s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }
        .recaptcha-label {
            font-size: 14px;
            color: #4b5563;
            font-weight: 500;
            user-select: none;
        }
        .recaptcha-brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 6px 0 6px 4px;
            border-left: 1px solid #e5e7eb;
            flex-shrink: 0;
            width: 72px;
            text-align: center;
        }
        .recaptcha-logo {
            width: 36px;
            height: 36px;
            margin-bottom: 2px;
        }
        .recaptcha-brand-text {
            font-size: 9px;
            color: #9ca3af;
            line-height: 1.2;
        }
        .recaptcha-brand-text a {
            color: #9ca3af;
            text-decoration: none;
        }
        .recaptcha-brand-text a:hover {
            text-decoration: underline;
            color: #6b7280;
        }
        @media (prefers-reduced-motion: reduce) {
            .animate-fade-up {
                animation: none !important;
                opacity: 1 !important;
                transform: none !important;
            }
            .recaptcha-spinner { animation: none; }
        }
    </style>
</head>
<body class="min-h-screen font-sans antialiased bg-gray-50 text-gray-900 flex items-center justify-center p-4">
    <div class="w-full max-w-[420px] bg-white rounded-[24px] p-8 md:p-10 shadow-[0_8px_30px_rgb(0,0,0,0.04)] relative animate-fade-up border border-gray-200">
        
        <!-- Logo -->
        <div class="flex justify-center mb-8">
            <div class="w-14 h-14 rounded-2xl bg-[#e11d48] flex items-center justify-center shadow-lg shadow-rose-500/20 border border-rose-600/50">
                <i class="fas fa-hand-holding-heart text-2xl text-white"></i>
            </div>
        </div>

        <!-- Title -->
        <h1 class="text-2xl font-bold text-center text-gray-900 mb-8 tracking-tight">Log In to your account</h1>

        <!-- Form -->
        <form method="POST" action="<?php echo base_url('login'); ?>" class="space-y-5" id="login-form">
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <input type="hidden" name="captcha_token" id="captcha_token" value="">

            <!-- Alerts -->
            <?php if (isset($error_message)): ?>
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700" role="alert">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    <?php echo htmlspecialchars($_SESSION['success_message']); unset($_SESSION['success_message']); ?>
                </div>
            <?php endif; ?>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email address</label>
                <input type="email" id="email" name="email" required autocomplete="username"
                       class="block px-4 py-3 w-full text-sm text-gray-900 bg-white rounded-xl border border-gray-300 focus:border-[#e11d48] focus:ring-4 focus:ring-[#e11d48]/10 transition-all outline-none"
                       placeholder="hello@example.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>

            <!-- Password -->
            <div class="mt-5">
                    <label for="password" class="block text-sm font-semibold text-gray-700">Password</label>
                <div class="relative">
                    <input type="password" id="password" name="password" required autocomplete="current-password"
                           class="block px-4 py-3 w-full text-sm text-gray-900 bg-white rounded-xl border border-gray-300 focus:border-[#e11d48] focus:ring-4 focus:ring-[#e11d48]/10 transition-all outline-none pr-10"
                           placeholder="••••••••">
                    <button type="button" id="toggle-password" class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600 transition-colors">
                        <i id="eye-icon" class="fas fa-eye-slash text-sm"></i>
                    </button>
                </div>
            </div>

            <!-- Recaptcha -->
            <div class="pt-2">
                <div id="recaptcha-widget" class="recaptcha-replica" role="group" aria-label="Security verification">
                    <div class="recaptcha-left py-2">
                        <div id="recaptcha-checkbox" class="recaptcha-box" tabindex="0" role="checkbox" aria-checked="false" aria-labelledby="recaptcha-text">
                            <span id="recaptcha-inner" class="flex items-center justify-center w-full h-full"></span>
                        </div>
                        <span id="recaptcha-text" class="recaptcha-label">I'm not a robot</span>
                    </div>
                    <div class="recaptcha-brand">
                        <svg class="recaptcha-logo" viewBox="0 0 64 64" aria-hidden="true">
                            <defs>
                                <linearGradient id="g1" x1="0" y1="0" x2="1" y2="1">
                                    <stop offset="0%" stop-color="#4285f4"/>
                                    <stop offset="100%" stop-color="#34a853"/>
                                </linearGradient>
                            </defs>
                            <circle cx="32" cy="32" r="28" fill="url(#g1)" opacity="0.9"/>
                            <path fill="#fff" d="M32 18c-6 0-11 4-13 10h8c1-1.5 3-2.5 5-2.5 3.5 0 6.5 3 6.5 6.5S35.5 39 32 39c-2 0-4-1-5-2.5h-4l-3 3c3 3 7 5 12 5 8 0 14-6 14-14s-6-14-14-14z"/>
                        </svg>
                        <div class="text-[10px] font-semibold text-gray-500 leading-tight mt-1">reCAPTCHA</div>
                        <div class="recaptcha-brand-text">
                            <span>Privacy</span> · <span>Terms</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Login Button -->
            <button type="submit" id="login-submit" disabled
                    class="w-full mt-4 rounded-xl bg-[#e11d48] hover:bg-[#be123c] py-3.5 text-center text-[15px] font-bold text-white shadow-md shadow-rose-500/20 transition-all active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed">
                Log In
            </button>



            
            <p class="mt-4 text-center text-xs text-gray-400 font-medium">Default Tester: admin@ssfo.local / admin123</p>
        </form>
    </div>

    <script>
    (function () {
        var token = <?php echo json_encode($captcha_secret, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
        var box = document.getElementById('recaptcha-checkbox');
        var inner = document.getElementById('recaptcha-inner');
        var hidden = document.getElementById('captcha_token');
        var submit = document.getElementById('login-submit');
        var wrap = document.getElementById('recaptcha-widget');
        var verified = false;
        var loading = false;

        function setAria(checked) {
            box.setAttribute('aria-checked', checked ? 'true' : 'false');
        }

        function clearInner() {
            inner.innerHTML = '';
        }

        function showSpinner() {
            clearInner();
            var d = document.createElement('div');
            d.className = 'recaptcha-spinner';
            d.setAttribute('aria-hidden', 'true');
            inner.appendChild(d);
        }

        function showCheck() {
            clearInner();
            var i = document.createElement('i');
            i.className = 'fas fa-check recaptcha-check';
            i.setAttribute('aria-hidden', 'true');
            inner.appendChild(i);
        }

        function runVerify() {
            if (loading || verified || !token) return;
            loading = true;
            box.classList.add('is-loading');
            box.classList.add('is-disabled');
            setAria(false);
            showSpinner();
            var delay = 1200 + Math.floor(Math.random() * 800);
            setTimeout(function () {
                loading = false;
                verified = true;
                hidden.value = token;
                box.classList.remove('is-loading');
                box.classList.add('is-disabled');
                showCheck();
                setAria(true);
                wrap.classList.add('is-verified');
                submit.disabled = false;
            }, delay);
        }

        box.addEventListener('click', function (e) {
            e.preventDefault();
            if (verified || loading) return;
            runVerify();
        });
        box.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                if (!verified && !loading) runVerify();
            }
        });

        document.getElementById('login-form').addEventListener('submit', function (e) {
            if (!hidden.value || hidden.value !== token) {
                e.preventDefault();
                alert('Please complete the verification (I\'m not a robot).');
            }
        });

        // Password Visibility Toggle
        var passwordInput = document.getElementById('password');
        var toggleBtn = document.getElementById('toggle-password');
        var eyeIcon = document.getElementById('eye-icon');

        toggleBtn.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Toggle icon classes
            if (type === 'password') {
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        });
    })();
    </script>
</body>
</html>
