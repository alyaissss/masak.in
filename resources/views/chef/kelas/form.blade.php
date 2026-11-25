@extends('layouts.app')

@section('judul', $kelas->exists ? 'Edit Kelas' : 'Buat Kelas Baru')

@section('konten')
    <div class="baris-atas">
        <a href="{{ $kelas->exists ? route('chef.kelas.show', $kelas) : route('chef.kelas.index') }}" class="btn btn-ghost btn-sm" data-testid="chef-kembali-kelas-button">
            <x-ikon nama="panah" style="transform: rotate(180deg);" /> Kembali
        </a>
    </div>

    <div class="kartu" style="max-width: 720px;">
        <div class="kartu-kepala">
            <h3>{{ $kelas->exists ? 'Ubah data kelas' : 'Kelas memasak baru' }}</h3>
            @if ($kelas->exists)
                <span class="badge badge-{{ $kelas->status }}">{{ $kelas->statusLabel() }}</span>
            @endif
        </div>

        <form method="POST" action="{{ $kelas->exists ? route('chef.kelas.update', $kelas) : route('chef.kelas.store') }}"
              enctype="multipart/form-data" class="kartu-isi form-grid" data-image-crop-form data-testid="chef-form-kelas">
            @csrf
            @if ($kelas->exists) @method('PUT') @endif

            <div class="field">
                <label for="nama_kelas" class="wajib">Nama Kelas</label>
                <input id="nama_kelas" name="nama_kelas" type="text" class="input" value="{{ old('nama_kelas', $kelas->nama_kelas) }}" required placeholder="cth: Rendang Wagyu Modern Masterclass" data-testid="chef-kelas-nama-input">
                @error('nama_kelas') <span class="galat">{{ $message }}</span> @enderror
            </div>

            <div class="field">
                <label for="deskripsi">Deskripsi Kelas</label>
                <textarea id="deskripsi" name="deskripsi" class="textarea" placeholder="Ceritakan kelas Anda: teknik yang dipelajari, durasi, tingkat kesulitan…" data-testid="chef-kelas-deskripsi-textarea">{{ old('deskripsi', $kelas->deskripsi) }}</textarea>
            </div>

            @if ($kelas->exists)
                <p class="bantuan">Tanggal dan jam sesi diubah dari tab <strong>Jadwal &amp; Sesi</strong> pada detail kelas.</p>
            @endif

            <div class="form-grid dua">
                <div class="field">
                    <label for="harga" class="wajib">Harga (Rp)</label>
                    <input id="harga" name="harga" type="number" min="0" step="1000" class="input" value="{{ old('harga', $kelas->exists ? (int) $kelas->harga : 0) }}" required data-testid="chef-kelas-harga-input">
                    @error('harga') <span class="galat">{{ $message }}</span> @enderror
                </div>
                <x-image-crop-input id="chef-kelas-foto" label="Foto Sampul Kelas (opsional)" :current="$kelas->foto_url" testid="chef-kelas-foto-input" />
            </div>
            <p class="bantuan">Foto sampul disimpan untuk kelas ini dan otomatis digunakan di katalog serta detail kelas—tidak perlu diunggah ulang.</p>

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <a href="{{ $kelas->exists ? route('chef.kelas.show', $kelas) : route('chef.kelas.index') }}" class="btn btn-sekunder" data-testid="chef-batal-kelas-button">Batal</a>
                <button type="submit" class="btn btn-utama" data-testid="chef-simpan-kelas-button">
                    <x-ikon nama="centang" /> {{ $kelas->exists ? 'Simpan Perubahan' : 'Simpan Kelas' }}
                </button>
            </div>
            @unless ($kelas->exists)
                <p class="bantuan">Kelas baru tersimpan berstatus <strong>Draft</strong>. Tambahkan menu terlebih dahulu, lalu isi tanggal, jam, dan link Zoom di jadwal untuk setiap menu.</p>
            @endunless
        </form>
    </div>
@endsection
