<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Verifikasi OTP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');

        body {
            font-family: 'Roboto', sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #0f3d5c 0%, #146c94 100%);
            padding: 1rem;
        }

        .login-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 2.5rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, .15);
        }

        .brand-badge {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: #146c94;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem auto;
        }

        .brand-badge i {
            color: #ffffff;
            font-size: 26px;
        }

        .login-title {
            font-size: 22px;
            font-weight: 700;
            color: #1c1c1c;
            text-align: center;
            margin-bottom: 4px;
        }

        .login-subtitle {
            font-size: 14px;
            color: #6c757d;
            text-align: center;
            margin-bottom: 1.75rem;
        }

        .otp-input {
            border-radius: 6px;
            border: 1px solid #d7dade;
            height: 60px;
            font-size: 26px;
            letter-spacing: 10px;
            text-align: center;
            transition: border-color .2s ease-in-out, box-shadow .2s ease-in-out;
        }

        .otp-input:focus {
            border-color: #146c94;
            box-shadow: 0 0 0 3px rgba(20, 108, 148, .15);
        }

        .btn-primary {
            background-color: #146c94;
            border-color: #146c94;
            border-radius: 8px;
            padding: 12px 24px;
            font-weight: 500;
            letter-spacing: .25px;
            transition: background-color .2s ease;
        }

        .btn-primary:hover {
            background-color: #0f3d5c;
            border-color: #0f3d5c;
        }

        .resend-btn {
            background: none;
            border: none;
            color: #146c94;
            font-size: 14px;
            padding: 0;
            text-decoration: none;
        }

        .resend-btn:hover {
            text-decoration: underline;
        }

        .alert ul {
            margin-bottom: 0;
            padding-left: 1.25rem;
            font-size: 14px;
        }

        @media (max-width: 576px) {
            .login-card {
                padding: 1.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-card">

        <div class="brand-badge">
            <i class="bi bi-envelope-check"></i>
        </div>

        <h2 class="login-title">Masukkan Kode OTP</h2>
        <p class="login-subtitle">Kode 6 digit telah dikirim ke email Anda, berlaku 10 menit</p>

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                @foreach ($errors->all() as $item)
                    <li>{{ $item }}</li>
                @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.otp.verify') }}">
            @csrf
            <div class="mb-3">
                <input type="text" name="otp" class="form-control otp-input"
                       maxlength="6" inputmode="numeric" pattern="[0-9]*"
                       placeholder="------" required autofocus autocomplete="one-time-code">
            </div>
            <div class="mb-3 d-grid">
                <button type="submit" class="btn btn-primary">Verifikasi</button>
            </div>
        </form>

        <form method="POST" action="{{ route('password.otp.resend') }}" class="text-center mb-0">
            @csrf
            <span class="text-muted" style="font-size: 14px;">Tidak menerima kode?</span>
            <button type="submit" class="resend-btn">Kirim ulang</button>
        </form>
    </div>
</body>
</html>