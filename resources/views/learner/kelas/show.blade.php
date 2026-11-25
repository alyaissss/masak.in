@extends('layouts.app')

@section('judul', $kelas->nama_kelas)

@section('konten')
    <div class="baris-atas">
        <a href="{{ route('kelas.index') }}" class="btn btn-ghost btn-sm" data-testid="kembali-katalog-button">
            <x-ikon nama="panah" style="transform: rotate(180deg);" /> Kembali ke katalog
        </a>
    </div>

    <div class="kartu jarak-bawah" style="overflow: hidden;">
        @if ($kelas->foto_url)
            <img class="foto-kelas-detail" src="{{ $kelas->foto_url }}" alt="Foto {{ $kelas->nama_kelas }}" data-testid="detail-foto-kelas">
        @endif
        <div class="kartu-isi" style="display: grid; gap: 12px;">
            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                <h2>{{ $kelas->nama_kelas }}</h2>
                <span class="badge badge-tersedia">Tersedia</span>
            </div>
            <p class="redup">{{ $kelas->deskripsi ?? 'Belum ada deskripsi.' }}</p>

            <div class="rincian-bayar"><span>Chef Pengampu</span><strong>{{ $kelas->chef->nama }}{{ $kelas->chef->spesialisasi ? ' — ' . $kelas->chef->spesialisasi : '' }}</strong></div>
            <div class="rincian-bayar" style="border-bottom: 0;"><span>Biaya Kelas</span><span class="total">{{ $kelas->hargaRp() }}</span></div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; flex-wrap: wrap;">
                @if ($pembayaran?->status === App\Models\Pembayaran::STATUS_BERHASIL)
                    <a href="{{ route('kelas.page', $kelas) }}" class="btn btn-berhasil" data-testid="detail-buka-ruang-button">
                        <x-ikon nama="panah" /> Masuk Kelas
                    </a>
                @elseif ($pembayaran?->status === App\Models\Pembayaran::STATUS_MENUNGGU)
                    <span class="badge badge-menunggu" data-testid="detail-badge-menunggu">Menunggu Verifikasi Admin</span>
                    <a href="{{ route('learner.pembayaran.index') }}" class="btn btn-sekunder" data-testid="detail-lihat-status-button">Lihat Status Pembayaran</a>
                @elseif ($pembayaran?->status === App\Models\Pembayaran::STATUS_GAGAL)
                    <a href="{{ route('kelas.daftar', $kelas) }}" class="btn btn-aksen" data-testid="detail-daftar-ulang-button">Daftar Ulang</a>
                @else
                    <a href="{{ route('kelas.daftar', $kelas) }}" class="btn btn-utama" data-testid="detail-daftar-bayar-button">
                        <x-ikon nama="dompet" /> Daftar &amp; Bayar
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="grid-2">
        <div class="kartu">
            <div class="kartu-kepala"><h3>Menu yang Akan Dipelajari</h3></div>
            <div class="kartu-isi daftar-baris" style="gap: 10px;">
                @forelse ($kelas->menuResep as $m)
                    <div class="baris" data-testid="detail-row-menu-{{ $m->id }}">
                        <span class="ikon-bulat"><x-ikon nama="menu" /></span>
                        <div class="isi">
                            <p class="judul">{{ $m->nama_menu }}</p>
                            <p class="redup">{{ $m->deskripsi ?? '' }}</p>
                        </div>
                    </div>
                @empty
                    <p class="redup kecil">Menu akan diumumkan oleh chef.</p>
                @endforelse
            </div>
        </div>

        <div class="kartu">
            <div class="kartu-kepala"><h3>Jadwal Sesi Online</h3></div>
            <div class="kartu-isi daftar-baris" style="gap: 10px;">
                @forelse ($kelas->jadwal as $j)
                    <div class="baris" data-testid="detail-row-jadwal-{{ $j->id }}">
                        <span class="ikon-bulat"><x-ikon nama="video" /></span>
                        <div class="isi">
                            <p class="judul">{{ $j->menuResep?->nama_menu ?? 'Menu belum ditentukan' }}</p>
                            <p class="redup">{{ $j->tanggalLengkap() }} · {{ $j->waktu->format('H:i') }} WIB</p>
                            <p class="redup">{{ $j->link_sesi ? 'Daring via video conference' : 'Link sesi menyusul' }}</p>
                        </div>
                    </div>
                @empty
                    <p class="redup kecil">Jadwal sesi akan diumumkan oleh chef.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
