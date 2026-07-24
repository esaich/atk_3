<!DOCTYPE html>
<html>
<head>
    <title>Verifikasi OTP</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #e8f0fe;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-card {
            background: #ffffff;
            border: 1px solid #dadce0;
            border-radius: 8px;
            padding: 2.5rem;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 1px 3px 0 rgba(60,64,67,.3), 0 4px 8px 3px rgba(60,64,67,.15);
        }
        .login-title {
            font-size: 24px;
            font-weight: 500;
            color: #202124;
            text-align: center;
            margin-bottom: 8px;
        }
        .otp-input {
            border-radius: 4px;
            border: 1px solid #dadce0;
            height: 56px;
            font-size: 24px;
            letter-spacing: 8px;
            text-align: center;
        }
        .btn-primary {
            background-color: #1a73e8;
            border-color: #1a73e8;
            border-radius: 24px;
            padding: 10px 24px;
            font-weight: 500;
        }
        .btn-primary:hover {
            background-color: #1565c0;
            border-color: #1565c0;
        }
        @media (max-width: 576px) {
            .login-card { padding: 1.5rem; border: none; box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="login-card">
        <h2 class="login-title">Masukkan Kode OTP</h2>
        <p class="text-center text-muted mb-4">Kode 6 digit telah dikirim ke email Anda, berlaku 10 menit</p>

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
                <input type="text" name="otp" class="form-control otp-input" maxlength="6" inputmode="numeric" pattern="[0-9]*" placeholder="------" required autofocus>
            </div>
            <div class="mb-3 d-grid">
                <button type="submit" class="btn btn-primary">Verifikasi</button>
            </div>
            <p class="text-center mb-0">
                <a href="{{ route('password.request') }}">Kirim ulang kode</a>
            </p>
        </form>
    </div>
</body>
</html>