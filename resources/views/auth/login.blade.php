<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin RIIB</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            color-scheme: light;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
            color: #0f172a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .card {
            width: min(420px, 100%);
            background: #ffffff;
            border-radius: 24px;
            padding: 40px 36px;
            box-shadow:
                0 40px 80px rgba(15, 23, 42, 0.25),
                0 20px 40px rgba(15, 23, 42, 0.15);
        }

        .logo {
            display: block;
            width: 240px;
            max-width: 100%;
            margin: 0 auto 20px;
        }

        h1 {
            margin: 0 0 12px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 600;
            color: #0f172a;
        }

        p.subtitle {
            margin: 0 0 32px;
            text-align: center;
            color: #475569;
            font-size: 0.95rem;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #0f172a;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            border: 1px solid #cbd5f5;
            border-radius: 14px;
            padding: 12px 14px;
            font-size: 0.95rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #2563eb;
            outline: none;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        }

        .password-wrapper {
            position: relative;
        }

        .password-wrapper input {
            padding-right: 48px;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border: none;
            background: transparent;
            cursor: pointer;
            border-radius: 50%;
            transition: background-color 0.2s ease;
        }

        .toggle-password:hover {
            background-color: rgba(37, 99, 235, 0.08);
        }

        .toggle-password:focus-visible {
            outline: 2px solid #2563eb;
            outline-offset: 2px;
        }

        .toggle-password img {
            width: 22px;
            height: 22px;
            display: block;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
            font-size: 0.9rem;
        }

        .remember {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #0f172a;
        }

        .remember input {
            accent-color: #ef4444;
            width: 18px;
            height: 18px;
        }

        .forgot-link {
            color: #ef4444;
            text-decoration: none;
            font-weight: 600;
        }

        .forgot-link:hover,
        .forgot-link:focus {
            text-decoration: underline;
        }

        button[type="submit"] {
            width: 100%;
            border: none;
            border-radius: 16px;
            padding: 14px;
            font-size: 1rem;
            font-weight: 600;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: #ffffff;
            cursor: pointer;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        button[type="submit"]:hover,
        button[type="submit"]:focus {
            transform: translateY(-1px);
            box-shadow: 0 18px 30px rgba(239, 68, 68, 0.25);
        }

        .error {
            margin-top: 6px;
            display: block;
            font-size: 0.82rem;
            color: #dc2626;
        }

        .alert {
            padding: 12px 14px;
            border-radius: 12px;
            background-color: rgba(220, 38, 38, 0.08);
            color: #b91c1c;
            font-size: 0.9rem;
            margin-bottom: 24px;
        }

        .alert.success {
            background-color: #d1fae5;
            color: #0f5132;
        }

        @media (max-width: 480px) {
            .card {
                padding: 28px 24px;
                border-radius: 20px;
            }

            .logo {
                width: 80px;
                margin-bottom: 16px;
            }
        }
    </style>
</head>

<body>
    <main class="card" aria-labelledby="login-heading">
        <img
            src="{{ asset('images/TelU Sby-1.png') }}"
            alt="Telkom University Surabaya"
            class="logo"
        >
        <h1 id="login-heading">Portal Admin RIIB</h1>
        <p class="subtitle">Masuk dengan kredensial admin untuk mengelola data dosen RIIB.</p>

        @if ($errors->any())
            <div class="alert" role="alert">
                Username atau password tidak sesuai. Silakan coba lagi.
            </div>
        @endif

        @if (session('status'))
            <div class="alert success" role="status">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.attempt') }}" novalidate>
            @csrf
            <div class="form-group">
                <label for="username">Username</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    value="{{ old('username', '') }}"
                    autocomplete="username"
                    required
                    autofocus
                >
                @error('username')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-wrapper">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        autocomplete="current-password"
                        required
                    >
                    <button
                        type="button"
                        class="toggle-password"
                        aria-label="Tampilkan password"
                        aria-pressed="false"
                        data-visible-icon="{{ asset('images/icons/eye-open.svg') }}"
                        data-hidden-icon="{{ asset('images/icons/eye-closed.svg') }}"
                    >
                        <img src="{{ asset('images/icons/eye-closed.svg') }}" alt="" aria-hidden="true">
                    </button>
                </div>
                @error('password')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="options">
                <label class="remember">
                    <input
                        type="checkbox"
                        name="remember"
                        value="1"
                        {{ old('remember') ? 'checked' : '' }}
                    >
                    Ingat saya
                </label>
                <a
                    class="forgot-link"
                    href="{{ route('password.request') }}"
                >
                    Lupa Password?
                </a>
            </div>

            <button type="submit">Masuk</button>
        </form>
    </main>

    <script>
        (function () {
            const passwordInput = document.getElementById('password');
            const toggleButton = document.querySelector('.toggle-password');

            if (!passwordInput || !toggleButton) {
                return;
            }

            const icon = toggleButton.querySelector('img');
            const visibleIcon = toggleButton.dataset.visibleIcon;
            const hiddenIcon = toggleButton.dataset.hiddenIcon;
            let isVisible = false;

            function updateState() {
                passwordInput.type = isVisible ? 'text' : 'password';
                toggleButton.setAttribute('aria-pressed', String(isVisible));
                toggleButton.setAttribute('aria-label', isVisible ? 'Sembunyikan password' : 'Tampilkan password');
                if (icon && visibleIcon && hiddenIcon) {
                    icon.src = isVisible ? visibleIcon : hiddenIcon;
                }
            }

            toggleButton.addEventListener('click', function () {
                isVisible = !isVisible;
                updateState();
            });

            toggleButton.addEventListener('keydown', function (event) {
                if (event.key === ' ' || event.key === 'Enter') {
                    event.preventDefault();
                    toggleButton.click();
                }
            });

            updateState();
        })();
    </script>
</body>

</html>
