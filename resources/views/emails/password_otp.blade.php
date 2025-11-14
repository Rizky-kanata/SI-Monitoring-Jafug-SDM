<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>{{ $appName }} - Kode OTP Reset Password</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            color: #0f172a;
            background: #f8fafc;
            padding: 32px;
        }

        .card {
            max-width: 480px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 24px;
            padding: 32px;
            border: 1px solid #e2e8f0;
        }

        .otp {
            font-size: 2rem;
            letter-spacing: 0.4rem;
            font-weight: 700;
            text-align: center;
            color: #2563eb;
            margin: 24px 0;
        }

        p {
            line-height: 1.5;
            margin: 0 0 12px;
        }

        .footer {
            margin-top: 32px;
            font-size: 0.85rem;
            color: #64748b;
        }
    </style>
</head>

<body>
    <div class="card">
        <p>Halo {{ $recipientName ?? 'Administrator' }},</p>

        <p>Berikut kode OTP untuk melakukan reset password di {{ $appName }}:</p>

        <div class="otp" aria-label="Kode OTP">{{ $code }}</div>

        <p>Kode ini berlaku selama {{ $expiresInMinutes }} menit sejak email ini dikirim. Jangan bagikan kode kepada siapa pun.</p>

        <p>Apabila Anda tidak meminta reset password, abaikan email ini.</p>

        <p class="footer">
            Salam,<br>
            Tim {{ $appName }}
        </p>
    </div>
</body>

</html>
