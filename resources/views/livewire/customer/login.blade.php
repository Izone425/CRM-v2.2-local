<!-- filepath: /var/www/html/timeteccrm/resources/views/livewire/customer/login.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Portal &middot; Sign In &middot; TimeTec</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
        }

        .login-page {
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }

        .login-bg {
            position: absolute;
            inset: 0;
            background: url('{{ asset('img/bg-login.jpg') }}') no-repeat center center;
            background-size: cover;
        }

        .login-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(245, 247, 252, 0.35) 0%, rgba(217, 234, 247, 0.25) 50%, rgba(245, 247, 252, 0.35) 100%);
        }

        .login-card {
            background: rgba(255, 255, 255, 0.82);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 46, 73, 0.12);
        }

        .login-glow-text {
            text-shadow: 0 0 12px rgba(0, 46, 73, 0.25);
        }

        .portal-banner img {
            filter: drop-shadow(0 12px 28px rgba(14, 74, 140, 0.15));
        }

        .portal-banner-mobile img {
            filter: drop-shadow(0 8px 24px rgba(0, 0, 0, 0.18));
        }

        .portal-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 0.9rem;
            border-radius: 999px;
            background: linear-gradient(135deg, rgba(14, 165, 233, 0.12), rgba(56, 189, 248, 0.12));
            border: 1px solid rgba(14, 165, 233, 0.25);
            color: #0369a1;
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.14em;
            text-transform: uppercase;
        }

        .portal-chip .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0ea5e9, #38bdf8);
            box-shadow: 0 0 8px rgba(14, 165, 233, 0.6);
        }

        .portal-chip-mobile {
            background: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.35);
            color: #ffffff;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        .portal-title {
            background: linear-gradient(135deg, #0ea5e9, #0284c7);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            color: transparent;
        }

        .portal-divider {
            height: 1px;
            background: linear-gradient(to right, transparent, rgba(14, 165, 233, 0.35), transparent);
            margin: 0.75rem auto 1rem;
            width: 60%;
        }

        .portal-eyebrow {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            margin-bottom: 1rem;
            color: #0ea5e9;
            font-size: 0.95rem;
            font-weight: 700;
            letter-spacing: 0.22em;
            text-transform: uppercase;
        }

        .portal-eyebrow .line {
            flex: 1;
            height: 1.5px;
            background: linear-gradient(to right, transparent, rgba(14, 165, 233, 0.45));
        }

        .portal-eyebrow .line.right {
            background: linear-gradient(to left, transparent, rgba(14, 165, 233, 0.45));
        }

        .portal-eyebrow .dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #0ea5e9;
            box-shadow: 0 0 10px rgba(14, 165, 233, 0.75);
        }

        @media (max-width: 640px) {
            .portal-eyebrow {
                font-size: 0.82rem;
                letter-spacing: 0.2em;
                gap: 0.6rem;
            }
        }

        .ttc-input {
            width: 100%;
            padding: 0.7rem 1rem;
            font-size: 0.9rem;
            background: #ffffff;
            border: 1px solid #d8e8ed;
            border-radius: 10px;
            color: #1a3a5c;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            outline: none;
        }

        .ttc-input::placeholder {
            color: #94a3b8;
        }

        .ttc-input:focus {
            border-color: #0ea5e9;
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15);
        }

        .ttc-input.has-error {
            border-color: #ef4444;
        }

        .ttc-label {
            display: block;
            font-size: 0.8rem;
            font-weight: 500;
            color: #1a3a5c;
            margin-bottom: 0.375rem;
        }

        .ttc-btn {
            width: 100%;
            background: linear-gradient(135deg, #0ea5e9, #38bdf8);
            color: white;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 0.8rem 1rem;
            border-radius: 40px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            letter-spacing: 0.3px;
        }

        .ttc-btn:hover {
            background: linear-gradient(135deg, #0284c7, #0ea5e9);
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(14, 165, 233, 0.3);
        }

        .ttc-btn:active {
            transform: translateY(0);
        }

        .ttc-alert {
            border-radius: 10px;
            padding: 0.7rem 0.9rem;
            font-size: 0.825rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .ttc-alert-error {
            background: rgba(254, 226, 226, 0.9);
            color: #991b1b;
            border: 1px solid rgba(252, 165, 165, 0.6);
        }

        .ttc-alert-success {
            background: rgba(220, 252, 231, 0.9);
            color: #166534;
            border: 1px solid rgba(134, 239, 172, 0.6);
        }

        .ttc-error-text {
            color: #dc2626;
            font-size: 0.72rem;
            margin-top: 0.25rem;
        }

        .password-toggle {
            position: absolute;
            top: 50%;
            right: 14px;
            transform: translateY(-50%);
            color: #94a3b8;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .password-toggle:hover {
            color: #0ea5e9;
        }

        .fade-in {
            opacity: 0;
            animation: fadeInUp 0.6s ease forwards;
        }

        .fade-in-delay-1 { animation-delay: 0.1s; }
        .fade-in-delay-2 { animation-delay: 0.2s; }
        .fade-in-delay-3 { animation-delay: 0.3s; }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(12px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 640px) {
            .login-card {
                border-radius: 14px;
            }
        }

        @media (max-height: 600px) {
            .login-page {
                min-height: auto;
            }
        }
    </style>
</head>
<body>
    <div class="login-page flex items-center justify-center">
        <div class="login-bg"></div>
        <div class="login-overlay"></div>

        <div class="relative z-10 flex items-center w-full max-w-6xl mx-auto px-4 sm:px-6 py-8 sm:py-12">
            <!-- Left: welcome headline (desktop only) -->
            <div class="hidden lg:flex flex-col justify-center flex-1 pr-16 fade-in">
                <div class="portal-banner mb-8">
                    <img src="{{ asset('img/customer-portal-banner.svg') }}" alt="TimeTec Customer Portal" style="width: 440px; max-width: 100%; height: auto;">
                </div>
                <h1 class="leading-tight mb-4" style="font-size: 2.5rem;">
                    <span style="color: #000e3d; font-weight: 300;">Hello,</span>
                    <span class="portal-title" style="font-weight: 700;">welcome back.</span>
                </h1>
                <p class="max-w-md leading-relaxed" style="color: rgba(0, 14, 61, 0.7); font-size: 1rem;">
                    Your dedicated <strong style="color: #000e3d;">TimeTec Customer Portal</strong> &mdash; manage tickets, track onboarding progress, and collaborate with your implementer team in one place.
                </p>
            </div>

            <!-- Right: form card -->
            <div class="w-full max-w-md mx-auto lg:ml-auto lg:mx-0 fade-in fade-in-delay-1">
                <!-- Mobile banner (outside card) -->
                <div class="lg:hidden text-center mb-6 portal-banner-mobile">
                    <img src="{{ asset('img/customer-portal-banner.svg') }}" alt="TimeTec Customer Portal" class="m-auto" style="width: 300px; max-width: 90%; height: auto;">
                </div>

                <div class="login-card p-6 sm:p-8 md:p-10 fade-in fade-in-delay-2">
                    <div class="portal-eyebrow">
                        <span class="line"></span>
                        <span class="dot"></span>
                        <span>Customer Portal</span>
                        <span class="dot"></span>
                        <span class="line right"></span>
                    </div>
                    <h2 class="text-2xl font-bold text-center" style="color: #1a3a5c;">Sign In</h2>
                    <p class="text-sm text-center mt-2 mb-6" style="color: #6b8299;">
                        Welcome back! Please enter your credentials to continue.
                    </p>

                    @if (session('success'))
                        <div class="ttc-alert ttc-alert-success" role="alert">
                            <i class="fa-solid fa-circle-check mt-0.5"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="ttc-alert ttc-alert-error" role="alert">
                            <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    @if ($errors->any() && !session('error'))
                        <div class="ttc-alert ttc-alert-error" role="alert">
                            <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                            <span>{{ $errors->first() }}</span>
                        </div>
                    @endif

                    <form class="space-y-5" action="{{ route('customer.login.submit') }}" method="POST">
                        @csrf

                        <div>
                            <label for="email" class="ttc-label">Email address</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                   autocomplete="email"
                                   placeholder="you@company.com"
                                   class="ttc-input @error('email') has-error @enderror">
                            @error('email')
                                <p class="ttc-error-text">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="ttc-label">Password</label>
                            <div class="relative">
                                <input type="password" id="password" name="password" required
                                       autocomplete="current-password"
                                       placeholder="Enter your password"
                                       class="ttc-input @error('password') has-error @enderror" style="padding-right: 2.75rem;">
                                <div id="togglePassword" class="password-toggle" aria-label="Toggle password visibility" role="button" tabindex="0">
                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                </div>
                            </div>
                            @error('password')
                                <p class="ttc-error-text">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="ttc-btn fade-in fade-in-delay-3" style="margin-top: 1.5rem;">
                            Sign In
                        </button>
                    </form>
                </div>

                <div class="text-center mt-5 text-[13px] lg:text-white/85 text-[#1a3a5c]/70">
                    &copy; {{ date('Y') }} TimeTec Computing Sdn Bhd. All Rights Reserved.
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');

            if (togglePassword && passwordInput) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);

                    const icon = this.querySelector('i');
                    icon.classList.toggle('fa-eye');
                    icon.classList.toggle('fa-eye-slash');
                });
            }
        });
    </script>
</body>
</html>
