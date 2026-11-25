@extends('layouts.app')

@section('judul', $user->exists ? 'Edit Data Chef' : 'Tambah Data Chef')

@section('konten')
    <div class="baris-atas">
        <a href="{{ route('admin.chef.index') }}" class="btn btn-ghost btn-sm" data-testid="kembali-chef-button">
            <x-ikon nama="panah" style="transform: rotate(180deg);" /> Kembali ke daftar chef
        </a>
    </div>

    <div class="kartu" style="max-width: 640px;">
        <div class="kartu-kepala">
            <h3>{{ $user->exists ? 'Ubah data ' . $user->nama : 'Chef baru' }}</h3>
            <span class="badge badge-netral">Peran: Chef</span>
        </div>

        <form method="POST" action="{{ $user->exists ? route('admin.chef.update', $user) : route('admin.chef.store') }}"
              class="kartu-isi form-grid" data-testid="form-chef">
            @csrf
            @if ($user->exists) @method('PUT') @endif

            <div class="field">
                <label for="nama" class="wajib">Nama Lengkap</label>
                <input id="nama" name="nama" type="text" class="input" value="{{ old('nama', $user->nama) }}" required data-testid="chef-nama-input">
                @error('nama') <span class="galat">{{ $message }}</span> @enderror
            </div>

            <div class="field">
                <label for="email" class="wajib">Email</label>
                <input id="email" name="email" type="email" class="input" value="{{ old('email', $user->email) }}" required data-testid="chef-email-input">
                @error('email') <span class="galat">{{ $message }}</span> @enderror
                <p class="bantuan">Email dipakai untuk masuk ke sistem.</p>
            </div>

            <div class="field">
                <label for="password" class="{{ $user->exists ? '' : 'wajib' }}">Kata Sandi</label>
                <input id="password" name="password" type="password" class="input"
                       @unless ($user->exists) required @endunless data-testid="chef-password-input">
                @error('password') <span class="galat">{{ $message }}</span> @enderror
                <p class="bantuan">
                    {{ $user->exists ? 'Biarkan kosong untuk mempertahankan kata sandi saat ini.' : 'Minimal 6 karakter — disimpan dalam bentuk hash.' }}
                </p>
            </div>

            <div class="form-grid dua">
                <div class="field">
                    <label for="telepon">No. Telepon</label>
                    <input id="telepon" name="telepon" type="tel" class="input" value="{{ old('telepon', $user->telepon) }}" placeholder="08xx xxxx xxxx" data-testid="chef-telepon-input">
                </div>
                <div class="field">
                    <label for="spesialisasi">Spesialisasi</label>
                    <input id="spesialisasi" name="spesialisasi" type="text" class="input" value="{{ old('spesialisasi', $user->spesialisasi) }}" placeholder="cth: Masakan Nusantara" data-testid="chef-spesialisasi-input">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <a href="{{ route('admin.chef.index') }}" class="btn btn-sekunder" data-testid="batal-chef-button">Batal</a>
                <button type="submit" class="btn btn-utama" data-testid="simpan-chef-button">
                    <x-ikon nama="centang" /> {{ $user->exists ? 'Simpan Perubahan' : 'Tambah Chef' }}
                </button>
            </div>
        </form>
    </div>
@endsection
