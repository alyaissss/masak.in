@extends('layouts.app')

@section('judul', $user->exists ? 'Edit Data Learner' : 'Tambah Data Learner')

@section('konten')
    <div class="baris-atas">
        <a href="{{ route('admin.learner.index') }}" class="btn btn-ghost btn-sm" data-testid="kembali-learner-button">
            <x-ikon nama="panah" style="transform: rotate(180deg);" /> Kembali ke daftar learner
        </a>
    </div>

    <div class="kartu" style="max-width: 640px;">
        <div class="kartu-kepala">
            <h3>{{ $user->exists ? 'Ubah data ' . $user->nama : 'Learner baru' }}</h3>
            <span class="badge badge-netral">Peran: Learner</span>
        </div>

        <form method="POST" action="{{ $user->exists ? route('admin.learner.update', $user) : route('admin.learner.store') }}"
              class="kartu-isi form-grid" data-testid="form-learner">
            @csrf
            @if ($user->exists) @method('PUT') @endif

            <div class="field">
                <label for="nama" class="wajib">Nama Lengkap</label>
                <input id="nama" name="nama" type="text" class="input" value="{{ old('nama', $user->nama) }}" required data-testid="learner-nama-input">
                @error('nama') <span class="galat">{{ $message }}</span> @enderror
            </div>

            <div class="field">
                <label for="email" class="wajib">Email</label>
                <input id="email" name="email" type="email" class="input" value="{{ old('email', $user->email) }}" required data-testid="learner-email-input">
                @error('email') <span class="galat">{{ $message }}</span> @enderror
            </div>

            <div class="field">
                <label for="password" class="{{ $user->exists ? '' : 'wajib' }}">Kata Sandi</label>
                <input id="password" name="password" type="password" class="input"
                       @unless ($user->exists) required @endunless data-testid="learner-password-input">
                @error('password') <span class="galat">{{ $message }}</span> @enderror
                <p class="bantuan">{{ $user->exists ? 'Biarkan kosong untuk mempertahankan kata sandi saat ini.' : 'Minimal 6 karakter — disimpan dalam bentuk hash.' }}</p>
            </div>

            <div class="field">
                <label for="telepon">No. Telepon</label>
                <input id="telepon" name="telepon" type="tel" class="input" value="{{ old('telepon', $user->telepon) }}" placeholder="08xx xxxx xxxx" data-testid="learner-telepon-input">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <a href="{{ route('admin.learner.index') }}" class="btn btn-sekunder" data-testid="batal-learner-button">Batal</a>
                <button type="submit" class="btn btn-utama" data-testid="simpan-learner-button">
                    <x-ikon nama="centang" /> {{ $user->exists ? 'Simpan Perubahan' : 'Tambah Learner' }}
                </button>
            </div>
        </form>
    </div>
@endsection
