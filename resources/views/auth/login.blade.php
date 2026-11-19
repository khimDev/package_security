<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Package Security</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
</head>

<body class="auth-body">

    <div class="auth-wrapper">
        <div class="auth-card">

            <!-- Brand Logo & Header -->
            <div class="auth-brand">
                <img src="{{ asset('images/logo.svg') }}" alt="Logo Package Security" class="auth-brand-logo">
                <h2>Package Security</h2>
                <span>Security Management</span>
            </div>

            <div class="auth-header">
                <h3>Masuk Petugas</h3>
                <p>Masukkan username dan password untuk bertugas.</p>
            </div>

            {{-- Alert Error --}}
            @if ($errors->any())
                <div class="alert-danger">
                    ⚠ {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="auth-form">
                @csrf

                <div class="form-group">
                    <label for="username">Username</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="Masukkan username"
                        value="{{ old('username') }}"
                        required
                        autofocus
                        autocomplete="username"
                    >
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Masukkan password"
                        required
                        autocomplete="current-password"
                    >
                </div>

                <button type="submit" class="btn-auth-submit">
                    Masuk Sekarang →
                </button>
            </form>

            <div class="auth-footer">
                <p>Belum memiliki akun? <a href="{{ route('register') }}" class="auth-link">Buat Akun Petugas</a></p>
            </div>

        </div>
    </div>

</body>

</html>
