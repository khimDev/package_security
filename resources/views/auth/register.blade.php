<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Akun | Package Security</title>
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
                <h3>Buat Akun Petugas</h3>
                <p>Cukup masukkan username dan password untuk mendaftar.</p>
            </div>

            {{-- Alert Error --}}
            @if ($errors->any())
                <div class="alert-danger">
                    ⚠ {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" class="auth-form">
                @csrf

                <div class="form-group">
                    <label for="username">Username</label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="Contoh: satpam_budi"
                        value="{{ old('username') }}"
                        required
                        autofocus
                        autocomplete="username"
                    >
                    @error('username')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Minimal 4 karakter"
                        required
                        autocomplete="new-password"
                    >
                    @error('password')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Ulangi Password</label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        placeholder="Ketik ulang password di atas"
                        required
                        autocomplete="new-password"
                    >
                </div>

                <button type="submit" class="btn-auth-submit">
                    Daftar Akun Baru →
                </button>
            </form>

            <div class="auth-footer">
                <p>Sudah memiliki akun? <a href="{{ route('login') }}" class="auth-link">Masuk di sini</a></p>
            </div>

        </div>
    </div>

</body>

</html>
