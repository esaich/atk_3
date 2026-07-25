<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Reset Password</title>
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

        .form-floating .form-control {
            border-radius: 6px;
            border: 1px solid #d7dade;
            height: 56px;
            transition: border-color .2s ease-in-out, box-shadow .2s ease-in-out;
        }

        .form-floating .form-control:focus {
            border-color: #146c94;
            box-shadow: 0 0 0 3px rgba(20, 108, 148, .15);
        }

        .form-floating > label {
            color: #80868b;
        }

        .form-floating > .form-control:focus ~ label,
        .form-floating > .form-control:not(:placeholder-shown) ~ label {
            color: #146c94;
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

        .toggle-password {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #80868b;
            z-index: 5;
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
            <i class="bi bi-shield-lock"></i>
        </div>

        <h2 class="login-title">Buat Password Baru</h2>
        <p class="login-subtitle">Minimal 8 karakter</p>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                @foreach ($errors->all() as $item)
                    <li>{{ $item }}</li>
                @endforeach
                </ul>
            </div>
        @endif

        {{-- Tidak perlu hidden input token/email — flow ini session-based
             (otp_email & otp_verified), bukan token URL bawaan Laravel. --}}
        <form method="POST" action="{{ route('password.reset') }}">
            @csrf

            <div class="mb-3 position-relative">
                <div class="form-floating">
                    <input type="password" name="password" class="form-control" id="floatingPassword"
                           placeholder="Password baru" autocomplete="new-password" required minlength="8">
                    <label for="floatingPassword">Password baru</label>
                </div>
                <i class="bi bi-eye toggle-password" data-target="floatingPassword"></i>
            </div>

            <div class="mb-3 position-relative">
                <div class="form-floating">
                    <input type="password" name="password_confirmation" class="form-control" id="floatingPasswordConfirm"
                           placeholder="Konfirmasi password" autocomplete="new-password" required minlength="8">
                    <label for="floatingPasswordConfirm">Konfirmasi password</label>
                </div>
                <i class="bi bi-eye toggle-password" data-target="floatingPasswordConfirm"></i>
            </div>

            <div class="mb-3 d-grid">
                <button type="submit" class="btn btn-primary">Simpan Password</button>
            </div>
        </form>
    </div>

    <script>
        document.querySelectorAll('.toggle-password').forEach(function (icon) {
            icon.addEventListener('click', function () {
                const input = document.getElementById(this.dataset.target);
                const isHidden = input.type === 'password';
                input.type = isHidden ? 'text' : 'password';
                this.classList.toggle('bi-eye');
                this.classList.toggle('bi-eye-slash');
            });
        });
    </script>
</body>
</html>