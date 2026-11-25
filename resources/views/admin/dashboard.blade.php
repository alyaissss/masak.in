@extends('layouts.app')

@section('judul', 'Dashboard Admin')
@section('sub-judul')
    <p class="sub-judul">Ringkasan operasional akademi kuliner hari ini.</p>
@endsection

@section('konten')
    @php
        $rupiah = 'Rp ' . number_format($stats['pendapatan'], 0, ',', '.');
    @endphp

    <div class="grid-stat dashboard-stat-grid" data-testid="admin-stat-grid">
        <div class="kartu-stat" data-testid="stat-total-chef">
            <p class="label">Total Chef</p>
            <p class="angka">{{ $stats['total_chef'] }}</p>
        </div>
        <div class="kartu-stat beige" data-testid="stat-total-learner">
            <p class="label">Total Learner</p>
            <p class="angka">{{ $stats['total_learner'] }}</p>
        </div>
        <div class="kartu-stat aksen" data-testid="stat-kelas-tersedia">
            <p class="label">Kelas Tersedia</p>
            <p class="angka">{{ $stats['kelas_tersedia'] }}</p>
        </div>
        <div class="kartu-stat" data-testid="stat-pembayaran-menunggu">
            <p class="label">Pembayaran Menunggu</p>
            <p class="angka">{{ $stats['pembayaran_menunggu'] }}</p>
        </div>
        <div class="kartu-stat hijau" data-testid="stat-pendapatan">
            <p class="label">Pendapatan Terkonfirmasi</p>
            <p class="angka">{{ $rupiah }}</p>
        </div>
    </div>

    <div class="grid-2-1 jarak-atas">
        <div class="kartu">
            <div class="kartu-kepala">
                <div>
                    <h3>Pembayaran Menunggu Verifikasi</h3>
                    <p class="redup kecil">Bukti transfer yang perlu dikonfirmasi admin.</p>
                </div>
                <a href="{{ route('admin.pembayaran.index') }}" class="btn btn-utama btn-sm" data-testid="link-kelola-pembayaran">Kelola Pembayaran</a>
            </div>
            <div class="kartu-isi" style="padding: 0;">
                @if ($pembayaranMenunggu->isEmpty())
                    <div class="kosong" style="border: 0;" data-testid="empty-pembayaran">
                        <x-ikon nama="centang" />
                        <p class="judul">Semua Pembayaran Telah Dikonfirmasi</p>
                        <p class="redup">Tidak ada bukti transfer yang menunggu verifikasi saat ini.</p>
                    </div>
                @else
                    <div class="tabel-wrap" style="border: 0; box-shadow: none;">
                        <table class="tabel" data-testid="tabel-pembayaran-menunggu">
                            <thead>
                                <tr>
                                    <th>Learner</th>
                                    <th>Kelas</th>
                                    <th>Jumlah</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pembayaranMenunggu as $p)
                                    <tr data-testid="row-pembayaran-menunggu-{{ $p->id }}">
                                        <td><strong>{{ $p->learner->nama }}</strong></td>
                                        <td>{{ $p->kelas->nama_kelas }}</td>
                                        <td>{{ $p->jumlahRp() }}</td>
                                        <td class="redup">{{ $p->created_at->translatedFormat('d M Y, H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div class="kartu">
            <div class="kartu-kepala">
                <h3>Kelas Terbaru</h3>
                <a href="{{ route('admin.kelas.index') }}" class="btn btn-ghost btn-sm" data-testid="link-kelola-kelas">Lihat semua</a>
            </div>
            <div class="kartu-isi daftar-baris" style="gap: 10px;">
                @forelse ($kelasTerbaru as $k)
                    <div class="baris" data-testid="row-kelas-terbaru-{{ $k->id }}">
                        <span class="ikon-bulat"><x-ikon nama="kelas" /></span>
                        <div class="isi">
                            <p class="judul">{{ $k->nama_kelas }}</p>
                            <p class="redup">Chef {{ $k->chef->nama }} · {{ $k->pesertaBerhasil() }} peserta</p>
                        </div>
                        <span class="badge badge-{{ $k->status }}">{{ $k->statusLabel() }}</span>
                    </div>
                @empty
                    <div class="kosong" style="border: 0; padding: 24px;">
                        <p class="judul">Belum ada kelas</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
