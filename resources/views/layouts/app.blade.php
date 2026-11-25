<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('judul', 'Dashboard') · MASAK</title>
    <link rel="icon" href="{{ asset('img/logo.jpeg') }}" type="image/jpeg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
@php
    $user = auth()->user();
    $hariIni = now()->translatedFormat('l, d F Y');

    $menu = match (true) {
        $user->isAdmin() => [
            ['url' => route('admin.dashboard'), 'label' => 'Dashboard', 'ikon' => 'dasbor', 'is' => ['admin', 'admin/dashboard']],
            ['url' => route('admin.chef.index'), 'label' => 'Data Chef', 'ikon' => 'chef', 'is' => ['admin/chef*']],
            ['url' => route('admin.learner.index'), 'label' => 'Data Learner', 'ikon' => 'pengguna', 'is' => ['admin/learner*']],
            ['url' => route('admin.kelas.index'), 'label' => 'Data Kelas', 'ikon' => 'kelas', 'is' => ['admin/kelas*']],
            ['url' => route('admin.pembayaran.index'), 'label' => 'Pembayaran', 'ikon' => 'dompet', 'is' => ['admin/pembayaran*']],
        ],
        $user->isChef() => [
            ['url' => route('chef.dashboard'), 'label' => 'Dashboard', 'ikon' => 'dasbor', 'is' => ['chef', 'chef/dashboard']],
            ['url' => route('chef.kelas.index'), 'label' => 'Kelas Saya', 'ikon' => 'kelas', 'is' => ['chef/kelas*']],
            ['url' => route('chef.diskusi.index'), 'label' => 'Forum Diskusi', 'ikon' => 'obrolan', 'is' => ['chef/diskusi*']],
        ],
        default => [
            ['url' => route('learner.dashboard'), 'label' => 'Dashboard', 'ikon' => 'dasbor', 'is' => ['learner', 'learner/dashboard']],
            ['url' => route('kelas.index'), 'label' => 'Katalog Kelas', 'ikon' => 'menu', 'is' => ['kelas', 'kelas/*']],
            ['url' => route('learner.pembayaran.index'), 'label' => 'Pembayaran Saya', 'ikon' => 'dompet', 'is' => ['learner/pembayaran*']],
        ],
    };

    $peranLabel = match ($user->role) {
        App\Models\User::ROLE_ADMIN => 'Admin',
        App\Models\User::ROLE_CHEF => 'Chef',
        default => 'Learner',
    };
@endphp

<div class="app-shell">
    <aside class="sidebar" data-testid="sidebar">
        <div class="sidebar-merek">
            <img class="logo" src="{{ asset('img/logo.jpeg') }}" alt="Logo MASAK">
            <span>
                <span class="nama">Masak<span class="titik">.</span>In</span>
            </span>
        </div>
        <span class="sidebar-peran">{{ $peranLabel }}</span>

        <nav class="sidebar-menu" data-testid="sidebar-menu">
            @foreach ($menu as $item)
                <a href="{{ $item['url'] }}" @if (request()->is(...$item['is'])) class="aktif" @endif data-testid="nav-{{ \Illuminate\Support\Str::slug($item['label']) }}">
                    <x-ikon nama="{{ $item['ikon'] }}" />
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="sidebar-kaki">
            <div class="pengguna">
                <span class="avatar"><x-ikon nama="profil" /></span>
                <span>
                    <span class="nama">{{ $user->nama }}</span><br>
                    <span class="email">{{ $user->email }}</span>
                </span>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="keluar" data-testid="logout-button">
                    <x-ikon nama="keluar" />
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <button class="sidebar-overlay" data-drawer aria-label="Tutup menu"></button>

    <div class="konten-utama">
        <header class="atas">
            <button class="btn btn-sekunder btn-sm tombol-drawer" data-drawer aria-label="Buka menu" data-testid="open-drawer-button">
                <x-ikon nama="dasbor" />
            </button>
            <div class="judul-wrap">
                <h1 class="judul-halaman">@yield('judul')</h1>
                @yield('sub-judul')
            </div>
            <div class="kanan">
                <span class="chip-tanggal"><x-ikon nama="kalender" /> {{ $hariIni }}</span>
                <span class="avatar" title="{{ $user->nama }}"><x-ikon nama="profil" /></span>
            </div>
        </header>

        <main class="konten">
            <div class="toast-wrap" aria-live="polite">
                @if (session('sukses'))
                    <div class="toast toast-sukses" data-autohide data-testid="toast-sukses">
                        <x-ikon nama="centang" /> {{ session('sukses') }}
                    </div>
                @endif
                @if (session('galat'))
                    <div class="toast toast-galat" data-autohide data-testid="toast-galat">
                        <x-ikon nama="tanda-seru" /> {{ session('galat') }}
                    </div>
                @endif
            </div>

            @yield('konten')
        </main>
    </div>
</div>

<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
