<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk · MASAK</title>
    <link rel="icon" href="{{ asset('img/logo.jpeg') }}" type="image/jpeg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<main class="halaman-login">
    <section class="login-hero" style="--hero-img: url('https://images.unsplash.com/photo-1577219491135-ce391730fb2c?crop=entropy&cs=srgb&fm=jpg&q=85')">
        <div class="merek">
            <img class="logo" src="{{ asset('img/logo.jpeg') }}" alt="Logo MASAK">
            <span>
                <span style="font-family: var(--font-judul); font-weight: 700; font-size: 1.5rem; display:block; line-height:1.1;">MASAK<span style="color: var(--warna-aksen);">.</span></span>
                <span style="font-size: 0.8rem; color: var(--warna-sekunder);">Manajemen Aplikasi Sistem Akademi Kuliner</span>
            </span>
        </div>

        <blockquote>
            &ldquo;Belajar memasak bukan tentang resep — ia tentang berkobar bersama.&rdquo;
            <footer>— Akademi Kuliner MASAK</footer>
        </blockquote>

        <p style="font-size: 0.85rem; color: var(--warna-sekunder-muda); max-width: 420px;">
            Kelas memasak daring bersama chef profesional: resep lengkap, jadwal sesi live,
            dan forum diskusi — semuanya dalam satu ruang belajar.
        </p>
    </section>

    <section class="login-panel">
        <div class="login-kotak">
            <p class="overline">Akademi Kuliner MASAK</p>
            <h1 class="judul" style="margin-top: 6px;">Masuk Akun</h1>
            <p class="redup" style="margin-top: 6px;">Gunakan email dan kata sandi terdaftar Anda.</p>

            <div class="toast-wrap" aria-live="polite">
                @if (session('sukses'))
                    <div class="toast toast-sukses" data-autohide data-testid="toast-sukses"><x-ikon nama="centang" /> {{ session('sukses') }}</div>
                @endif
            </div>

            <form id="form-login" method="POST" action="{{ route('login.attempt') }}" class="form-grid" style="margin-top: 28px;" data-testid="login-form">
                @csrf

                <div class="field">
                    <label for="email" class="wajib">Email</label>
                    <input id="email" name="email" type="email" class="input" value="{{ old('email') }}" required autofocus
                           placeholder="nama@contoh.com" data-testid="login-email-input">
                    @error('email') <span class="galat" data-testid="login-error">{{ $message }}</span> @enderror
                </div>

                <div class="field">
                    <label for="password" class="wajib">Kata Sandi</label>
                    <input id="password" name="password" type="password" class="input" required
                           placeholder="••••••••" data-testid="login-password-input">
                </div>

                <label class="cek">
                    <input type="checkbox" name="ingat" value="1" data-testid="login-remember-checkbox">
                    Ingat saya di perangkat ini
                </label>

                <button type="submit" class="btn btn-utama btn-penuh" data-testid="login-submit-button">
                    Masuk <x-ikon nama="panah" />
                </button>
            </form>
        </div>
    </section>
</main>

<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
