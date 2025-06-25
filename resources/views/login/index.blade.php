<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            background-color: #f8f9fa;
        }
        body {
            display: flex;
            justify-content: center; /* center horizontal */
            align-items: center;     /* center vertical */
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 2rem;
            background: white;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .login-logo {
            display: block;
            margin: 0 auto 20px auto;
            max-width: 150px;
            height: auto;
        }
    </style>
</head>
<body>
    <div class="login-card">

        <!-- Logo -->
        <img src="{{ asset('assets/img/Kerawang.png') }}" alt="Logo" class="login-logo" />

        <h2 class="text-center mb-4">Login</h2>

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
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}">
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control">
            </div>
            <div class="mb-3 d-grid">
                <button type="submit" class="btn btn-primary">Login</button>
            </div>
        </form>
    </div>
</body>
</html>
