<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lupa Password - Monitoring Kepangkatan SDM</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo telu.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
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
            justify-content: center;
            align-items: center;
            padding: 24px;
        }

        .card {
            width: min(420px, 100%);
            background: #fff;
            border-radius: 24px;
            padding: 40px 36px;
            box-shadow:
                0 40px 80px rgba(15, 23, 42, 0.25),
                0 20px 40px rgba(15, 23, 42, 0.15);
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
        }

        input[type="text"] {
            width: 100%;
            border: 1px solid #cbd5f5;
            border-radius: 14px;
            padding: 12px 14px;
            font-size: 0.95rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        input[type="text"]:focus {
            border-color: #2563eb;
            outline: none;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        }

        button {
            width: 100%;
            border: none;
            border-radius: 16px;
            background: linear-gradient(135deg, #dc2626, #ea580c);
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
            box-shadow: 0 12px 24px rgba(220, 38, 38, 0.25);
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
    </style>
</head>

<body>
    <main class="card" aria-labelledby="forgot-heading">
        <h1 id="forgot-heading">Lupa Password</h1>
        <p class="subtitle">
            Masukkan username Anda. Kode OTP akan dikirim ke email yang terdaftar pada akun.
        </p>

        @if ($errors->any())
            <div class="alert" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        @if (session('status'))
            <div class="alert success" role="status">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.otp.send') }}" novalidate>
            @csrf
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

            <button type="submit">Kirim OTP via Email</button>
        </form>

        <a href="{{ route('login') }}" class="back-link">
            Kembali ke halaman login
        </a>
    </main>
</body>

</html>
