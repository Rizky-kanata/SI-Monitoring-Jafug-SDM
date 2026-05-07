<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrasi Akun - Monitoring Kepangkatan SDM</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo telu.png') }}">
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
            width: min(460px, 100%);
            background: #ffffff;
            border-radius: 24px;
            padding: 40px 36px;
            box-shadow:
                0 40px 80px rgba(15, 23, 42, 0.25),
                0 20px 40px rgba(15, 23, 42, 0.15);
        }

        .logo {
            display: block;
            width: 96px;
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
        input[type="email"],
        input[type="password"] {
            width: 100%;
            border: 1px solid #cbd5f5;
            border-radius: 14px;
            padding: 12px 14px;
            font-size: 0.95rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        input:focus {
            border-color: #2563eb;
            outline: none;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        }

        .form-group {
            margin-bottom: 18px;
        }

        .password-wrapper {
            position: relative;
        }

        .password-wrapper input {
            padding-right: 48px;
        }

        button {
            width: 100%;
            border: none;
            border-radius: 16px;
            background: linear-gradient(135deg, #2563eb, #4338ca);
            color: #fff;
            font-weight: 600;
            font-size: 1rem;
            padding: 14px;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            margin-top: 8px;
        }

        button:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 24px rgba(37, 99, 235, 0.25);
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
            margin: 0;
            padding: 0;
            border: none;
            border-radius: 50%;
            background: transparent;
            box-shadow: none;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .toggle-password:hover {
            transform: translateY(-50%);
            background-color: rgba(37, 99, 235, 0.08);
            box-shadow: none;
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

        .alert {
            border-radius: 14px;
            padding: 12px 14px;
            margin-bottom: 16px;
            font-size: 0.92rem;
            color: #b91c1c;
            background: #fee2e2;
        }

        .success {
            color: #0f5132;
            background: #d1fae5;
        }

        .error {
            font-size: 0.85rem;
            color: #dc2626;
            margin-top: 6px;
            display: block;
        }

        .back-link {
            display: inline-flex;
            justify-content: center;
            width: 100%;
            margin-top: 20px;
            font-size: 0.9rem;
            color: #2563eb;
            text-decoration: none;
            font-weight: 500;
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
    <main class="card" aria-labelledby="register-heading">
        <img
            src="{{ asset('images/logo telu.png') }}"
            alt="Telkom University"
            class="logo"
        >

        <h1 id="register-heading">Buat Akun Baru</h1>
        <p class="subtitle">
            Daftar untuk dapetin akses ke Monitoring Kepangkatan SDM.
        </p>

        @if ($errors->any())
            <div class="alert" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('register.store') }}" novalidate>
            @csrf
            <div class="form-group">
                <label for="nickname">Nickname</label>
                <input
                    type="text"
                    id="nickname"
                    name="nickname"
                    value="{{ old('nickname') }}"
                    required
                    autocomplete="name"
                >
                @error('nickname')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="username">Username</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    value="{{ old('username') }}"
                    required
                    autocomplete="username"
                >
                @error('username')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="email"
                >
                @error('email')
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
                        required
                        autocomplete="new-password"
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

            <div class="form-group">
                <label for="password_confirmation">Konfirmasi Password</label>
                <div class="password-wrapper">
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
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
            </div>

            <button type="submit">Daftar Sekarang</button>
        </form>

        <a href="{{ route('login') }}" class="back-link">
            Sudah punya akun? Masuk di sini
        </a>
    </main>

    <script>
        (function () {
            const toggleButtons = document.querySelectorAll('.toggle-password');

            toggleButtons.forEach(function (toggleButton) {
                const wrapper = toggleButton.closest('.password-wrapper');
                const passwordInput = wrapper ? wrapper.querySelector('input') : null;

                if (!passwordInput) {
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
            });
        })();
    </script>
</body>

</html>
