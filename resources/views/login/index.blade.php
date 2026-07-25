<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Login</title>
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

        .forgot-link {
            font-size: 14px;
            color: #146c94;
            text-decoration: none;
        }

        .forgot-link:hover {
            text-decoration: underline;
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
            <i class="bi bi-box-seam"></i>
        </div>

        <h2 class="login-title">CV BERKAH ADITYA JAYA</h2>
        <p class="login-subtitle">Masuk untuk melanjutkan ke ATK App</p>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                @foreach ($errors->all() as $item)
                    <li>{{ $item }}</li>
                @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="/login">
            @csrf
            <div class="mb-3">
                <div class="form-floating">
                    <input type="email" name="email" class="form-control" id="floatingEmail"
                           placeholder="name@example.com" value="{{ old('email') }}"
                           autocomplete="email" required autofocus>
                    <label for="floatingEmail">Email address</label>
                </div>
            </div>

            <div class="mb-3 position-relative">
                <div class="form-floating">
                    <input type="password" name="password" class="form-control" id="floatingPassword"
                           placeholder="Password" autocomplete="current-password" required>
                    <label for="floatingPassword">Password</label>
                </div>
                <i class="bi bi-eye toggle-password" id="togglePassword"></i>
            </div>

            <div class="mb-3 d-grid">
                <button type="submit" class="btn btn-primary">Login</button>
            </div>

            <p class="text-center mb-0">
                <a href="{{ route('password.request') }}" class="forgot-link">Lupa password?</a>
            </p>
        </form>
    </div>

    <script>
        const toggle = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('floatingPassword');

        toggle.addEventListener('click', function () {
            const isHidden = passwordInput.type === 'password';
            passwordInput.type = isHidden ? 'text' : 'password';
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });
    </script>
</body>
</html>