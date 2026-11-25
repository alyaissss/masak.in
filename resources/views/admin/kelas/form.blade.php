@extends('layouts.app')

@section('judul', $kelas->exists ? 'Edit Kelas' : 'Tambah Kelas')

@section('konten')
    <div class="baris-atas">
        <a href="{{ route('admin.kelas.index') }}" class="btn btn-ghost btn-sm" data-testid="kembali-kelas-form-button">
            <x-ikon nama="panah" style="transform: rotate(180deg);" /> Kembali ke daftar kelas
        </a>
    </div>

    <div class="kartu" style="max-width: 720px;">
        <div class="kartu-kepala">
            <h3>{{ $kelas->exists ? 'Ubah data kelas' : 'Kelas baru' }}</h3>
        </div>

        <form method="POST" action="{{ $kelas->exists ? route('admin.kelas.update', $kelas) : route('admin.kelas.store') }}"
              enctype="multipart/form-data" class="kartu-isi form-grid" data-image-crop-form data-testid="form-kelas">
            @csrf
            @if ($kelas->exists) @method('PUT') @endif

            <div class="field">
                <label for="nama_kelas" class="wajib">Nama Kelas</label>
                <input id="nama_kelas" name="nama_kelas" type="text" class="input" value="{{ old('nama_kelas', $kelas->nama_kelas) }}" required data-testid="kelas-nama-input">
                @error('nama_kelas') <span class="galat">{{ $message }}</span> @enderror
            </div>

            <div class="form-grid dua">
                <div class="field">
                    <label for="chef_id" class="wajib">Chef Pengampu</label>
                    <select id="chef_id" name="chef_id" class="select" required data-testid="kelas-chef-select">
                        <option value="">— Pilih chef —</option>
                        @foreach ($daftarChef as $c)
                            <option value="{{ $c->id }}" @selected(old('chef_id', $kelas->chef_id) == $c->id)>{{ $c->nama }}</option>
                        @endforeach
                    </select>
                    @error('chef_id') <span class="galat">{{ $message }}</span> @enderror
                </div>

                <div class="field">
                    <label for="status" class="wajib">Status</label>
                    <select id="status" name="status" class="select" data-testid="kelas-status-select">
                        @foreach ([App\Models\Kelas::STATUS_DRAFT => 'Draft', App\Models\Kelas::STATUS_TERSEDIA => 'Tersedia', App\Models\Kelas::STATUS_SELESAI => 'Selesai'] as $nilai => $label)
                            <option value="{{ $nilai }}" @selected(old('status', $kelas->status ?? App\Models\Kelas::STATUS_DRAFT) === $nilai)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="field">
                <label for="deskripsi">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" class="textarea" placeholder="Deskripsi kelas, yang akan dipelajari learner…" data-testid="kelas-deskripsi-textarea">{{ old('deskripsi', $kelas->deskripsi) }}</textarea>
            </div>

            @if ($kelas->exists)
                <p class="bantuan">Tanggal dan jam sesi diubah oleh chef pengampu melalui jadwal kelas.</p>
            @endif

            <div class="form-grid dua">
                <div class="field">
                    <label for="harga" class="wajib">Harga (Rp)</label>
                    <input id="harga" name="harga" type="number" min="0" step="1000" class="input" value="{{ old('harga', $kelas->exists ? (int) $kelas->harga : 0) }}" required data-testid="kelas-harga-input">
                    @error('harga') <span class="galat">{{ $message }}</span> @enderror
                </div>
                <x-image-crop-input id="admin-kelas-foto" label="Foto Sampul Kelas (opsional)" :current="$kelas->foto_url" testid="kelas-foto-input" />
            </div>
            <p class="bantuan">Foto sampul disimpan untuk kelas ini dan otomatis digunakan di katalog serta detail kelas—tidak perlu diunggah ulang.</p>
            @unless ($kelas->exists)
                <p class="bantuan">Tanggal, jam, dan link Zoom diatur chef pengampu untuk setiap menu pada jadwal kelas.</p>
            @endunless

            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <a href="{{ route('admin.kelas.index') }}" class="btn btn-sekunder" data-testid="batal-kelas-button">Batal</a>
                <button type="submit" class="btn btn-utama" data-testid="simpan-kelas-button">
                    <x-ikon nama="centang" /> {{ $kelas->exists ? 'Simpan Perubahan' : 'Tambah Kelas' }}
                </button>
            </div>
        </form>
    </div>
@endsection
