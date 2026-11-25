@extends('layouts.app')

@section('judul', 'Pembayaran Kelas')

@section('konten')
    <div class="baris-atas">
        <a href="{{ route('kelas.show', $kelas) }}" class="btn btn-ghost btn-sm" data-testid="kembali-detail-button">
            <x-ikon nama="panah" style="transform: rotate(180deg);" /> Kembali ke detail kelas
        </a>
    </div>

    <div class="grid-2-1">
        <div class="kartu">
            <div class="kartu-kepala">
                <h3>Instruksi &amp; Unggah Bukti Transfer</h3>
                <span class="badge badge-menunggu" data-testid="badge-status-menunggu">Menunggu Verifikasi</span>
            </div>
            <form method="POST" action="{{ route('kelas.daftar.simpan', $kelas) }}" enctype="multipart/form-data"
                  class="kartu-isi form-grid" data-testid="form-pembayaran">
                @csrf

                <div class="field">
                    <label class="wajib">Metode Pembayaran</label>
                    <select name="metode" class="select" required data-testid="pembayaran-metode-select">
                        <option value="">— Pilih metode —</option>
                        @foreach ($metode as $nilai => $label)
                            <option value="{{ $nilai }}" @selected(old('metode') === $nilai)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('metode') <span class="galat">{{ $message }}</span> @enderror
                </div>

                <div class="field">
                    <label for="no_referensi" class="wajib">Nama Lengkap Pengirim</label>
                    <input id="no_referensi" name="no_referensi" type="text" class="input" value="{{ old('no_referensi') }}" required placeholder="cth: TRF-20260214-001" data-testid="input-ref-number">
                    @error('no_referensi') <span class="galat">{{ $message }}</span> @enderror
                    <p class="bantuan">Cantumkan nama lengkap peserta yang mendaftar kelas.</p>
                </div>

                <div class="field">
                    <label class="wajib">Bukti Transfer</label>
                    <label class="file-drop" data-testid="pembayaran-bukti-drop">
                        <x-ikon nama="berkas" />
                        <span class="utama">Unggah bukti transfer</span>
                        <span class="redup">JPG / PNG / PDF · maksimal 2 MB</span>
                        <input type="file" name="bukti" accept=".jpg,.jpeg,.png,.pdf" required>
                    </label>
                    @error('bukti') <span class="galat">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="btn btn-utama btn-penuh" data-testid="kirim-pembayaran-button">
                    <x-ikon nama="berkas" /> Kirim Bukti Pembayaran
                </button>
                <p class="bantuan teks-tengah">Tombol &ldquo;Masuk Kelas&rdquo; akan terbuka setelah pembayaran dikonfirmasi berhasil oleh admin (FR-42).</p>
            </form>
        </div>

        <div style="display: flex; flex-direction: column; gap: 24px;">
            <div class="kartu">
                <div class="kartu-kepala"><h3>Ringkasan Kelas</h3></div>
                <div class="kartu-isi">
                    <h2 style="font-size: 1.2rem;">{{ $kelas->nama_kelas }}</h2>
                    <p class="redup kecil" style="margin-top: 4px;">Chef {{ $kelas->chef->nama }}</p>
                    <div class="rincian-bayar jarak-atas" style="margin-top: 16px;"><span>Biaya kelas</span><strong>{{ $kelas->hargaRp() }}</strong></div>
                    <div class="rincian-bayar" style="border-bottom: 0;"><span>Total dibayar</span><span class="total">{{ $kelas->hargaRp() }}</span></div>
                </div>
            </div>

            <div class="kartu">
                <div class="kartu-kepala"><h3>Rekening Tujuan</h3></div>
                <div class="kartu-isi daftar-baris" style="gap: 10px;">
                    <div class="kartu-rek">
                        <span><span class="bank">Bank BCA</span><br><span class="redup kecil">a.n. PT MASAK Akademi Kuliner</span></span>
                        <span class="nomor">5420819374</span>
                    </div>
                    <div class="kartu-rek">
                        <span><span class="bank">Bank BRI</span><br><span class="redup kecil">a.n. PT MASAK Akademi Kuliner</span></span>
                        <span class="nomor">034101048291503</span>
                    </div>
                    <div class="kartu-rek">
                        <span><span class="bank">Bank Mandiri</span><br><span class="redup kecil">a.n. PT MASAK Akademi Kuliner</span></span>
                        <span class="nomor">1400092837465</span>
                    </div>
                    <p class="bantuan">Tunggu hingga proses pembayaran berhasil dikonfirmasi oleh admin.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
