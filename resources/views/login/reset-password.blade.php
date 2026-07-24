<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
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
        .form-floating .form-control {
            border-radius: 4px;
            border: 1px solid #dadce0;
            height: 56px;
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
        <h2 class="login-title">Buat Password Baru</h2>
        <p class="text-center text-muted mb-4">Minimal 8 karakter</p>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                @foreach ($errors->all() as $item)
                    <li>{{ $item }}</li>
                @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.reset') }}">
            @csrf
            <div class="mb-3">
                <div class="form-floating">
                    <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Password baru" required>
                    <label for="floatingPassword">Password baru</label>
                </div>
            </div>
            <div class="mb-3">
                <div class="form-floating">
                    <input type="password" name="password_confirmation" class="form-control" id="floatingPasswordConfirm" placeholder="Konfirmasi password" required>
                    <label for="floatingPasswordConfirm">Konfirmasi password</label>
                </div>
            </div>
            <div class="mb-3 d-grid">
                <button type="submit" class="btn btn-primary">Simpan Password</button>
            </div>
        </form>
    </div>
</body>
</html>