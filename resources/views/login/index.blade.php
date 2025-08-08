<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');
        
        /* Menggunakan font Roboto untuk tampilan Google */
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #e8f0fe; /* Warna latar belakang yang lebih terang */
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-card {
            background: #ffffff;
            border: 1px solid #dadce0;
            border-radius: 8px; /* Sudut lebih membulat */
            padding: 2.5rem;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 1px 3px 0 rgba(60,64,67,.3), 0 4px 8px 3px rgba(60,64,67,.15); /* Bayangan yang lebih halus */
        }
        
        .login-logo {
            display: block;
            margin: 0 auto 24px auto;
            max-width: 150px;
            height: auto;
        }

        .login-title {
            font-size: 24px;
            font-weight: 500;
            color: #202124;
            text-align: center;
            margin-bottom: 24px;
        }

        /* Gaya input field agar terlihat seperti Google */
        .form-floating .form-control {
            border-radius: 4px;
            border: 1px solid #dadce0;
            transition: border-color 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            height: 56px; /* Tinggi input yang lebih proporsional */
        }

        /* Border biru yang lebih menonjol saat fokus */
        .form-floating .form-control:focus {
            border-color: #1a73e8;
            box-shadow: 0 1px 1px 0 rgba(26,115,232,.8);
        }

        /* Gaya label yang mengambang */
        .form-floating > label {
            color: #80868b;
            padding: 1rem 0.75rem;
            transition: transform 0.2s ease-in-out, font-size 0.2s ease-in-out, color 0.2s ease-in-out; /* Transisi yang lebih halus */
        }
        
        .form-floating > .form-control:focus ~ label,
        .form-floating > .form-control:not(:placeholder-shown) ~ label {
            transform: scale(0.75) translateY(-1rem) translateX(0.15rem); /* Mengubah posisi label ke atas */
            font-size: 1rem; /* Mengurangi ukuran font saat label mengambang */
            color: #1a73e8; /* Mengubah warna label saat mengambang atau fokus */
        }

        .btn-primary {
            background-color: #1a73e8;
            border-color: #1a73e8;
            border-radius: 24px; /* Sudut lebih membulat */
            padding: 10px 24px;
            font-weight: 500;
            letter-spacing: .25px;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #1565c0;
            border-color: #1565c0;
        }

        .alert ul {
            margin-bottom: 0;
            padding-left: 1.5rem;
        }

        /* Responsive */
        @media (max-width: 576px) {
            .login-card {
                padding: 1.5rem;
                border: none;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="login-card">

        <!-- Logo -->
        <img src="{{ asset('assets/img/Kerawang.png') }}" alt="Logo" class="login-logo" />

        <h2 class="login-title">Sign in</h2>
        <p class="text-center text-muted mb-4">To Continue to ATK App</p>

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
                    <input type="email" name="email" class="form-control" id="floatingEmail" placeholder="name@example.com" value="{{ old('email') }}">
                    <label for="floatingEmail">Email address</label>
                </div>
            </div>
            <div class="mb-3">
                <div class="form-floating">
                    <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Password">
                    <label for="floatingPassword">Password</label>
                </div>
            </div>
            <div class="mb-3 d-grid">
                <button type="submit" class="btn btn-primary">Login</button>
            </div>
        </form>
    </div>
</body>
</html>
