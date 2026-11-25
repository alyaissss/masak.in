@extends('layouts.app')

@section('judul', 'Dashboard Chef')
@section('sub-judul')
    <p class="sub-judul">Ringkasan kelas yang Anda ampu, jadwal hari ini, dan diskusi terbaru.</p>
@endsection

@section('konten')
    <div class="grid-stat dashboard-stat-grid" data-testid="chef-stat-grid">
        <div class="kartu-stat" data-testid="chef-stat-kelas">
            <p class="label">Total Kelas Saya</p>
            <p class="angka">{{ $stats['kelas_total'] }}</p>
        </div>
        <div class="kartu-stat aksen" data-testid="chef-stat-tersedia">
            <p class="label">Kelas Tersedia</p>
            <p class="angka">{{ $stats['kelas_tersedia'] }}</p>
        </div>
        <div class="kartu-stat hijau" data-testid="chef-stat-peserta">
            <p class="label">Peserta Terdaftar</p>
            <p class="angka">{{ $stats['peserta'] }}</p>
        </div>
        <div class="kartu-stat beige" data-testid="chef-stat-pendapatan">
            <p class="label">Pendapatan Kelas</p>
            <p class="angka" style="font-size: 1.3rem;">Rp {{ number_format($stats['pendapatan'], 0, ',', '.') }}</p>
        </div>
        <div class="kartu-stat aksen" data-testid="chef-stat-rating">
            <p class="label">Rating dari Learner</p>
            <p class="angka" style="font-size: 1.3rem;">{{ $stats['rating'] ? number_format($stats['rating']->rata, 1, ',', '.') . ' ★' : '—' }}</p>
            <p class="redup kecil">{{ $stats['rating'] ? $stats['rating']->jumlah . ' rating' : 'Belum ada rating' }}</p>
        </div>
    </div>

    <div class="grid-2-1 jarak-atas">
        <div class="kartu">
            <div class="kartu-kepala">
                <h3>Kelas Saya</h3>
                <a href="{{ route('chef.kelas.create') }}" class="btn btn-utama btn-sm" data-testid="chef-tambah-kelas-button">
                    <x-ikon nama="tambah" /> Buat Kelas
                </a>
            </div>
            <div class="kartu-isi" style="padding: 0;">
                @if ($kelasSaya->isEmpty())
                    <div class="kosong" style="border: 0;" data-testid="chef-empty-kelas">
                        <x-ikon nama="kelas" />
                        <p class="judul">Belum Ada Kelas</p>
                        <p class="redup">Buat kelas memasak pertama Anda, lengkapi menu, resep, dan jadwalnya.</p>
                        <a href="{{ route('chef.kelas.create') }}" class="btn btn-utama btn-sm" style="margin-top: 8px;">Buat Kelas Baru</a>
                    </div>
                @else
                    <div class="tabel-wrap" style="border: 0; box-shadow: none;">
                        <table class="tabel" data-testid="chef-tabel-kelas">
                            <thead>
                                <tr><th>Kelas</th><th>Status</th><th>Peserta</th><th></th></tr>
                            </thead>
                            <tbody>
                                @foreach ($kelasSaya as $k)
                                    <tr data-testid="chef-row-kelas-{{ $k->id }}">
                                        <td><strong>{{ $k->nama_kelas }}</strong></td>
                                        <td><span class="badge badge-{{ $k->status }}">{{ $k->statusLabel() }}</span></td>
                                        <td>{{ $k->pembayaran_count }}</td>
                                        <td style="text-align: right;">
                                            <a href="{{ route('chef.kelas.show', $k) }}" class="btn btn-sekunder btn-sm" data-testid="chef-kelola-kelas-button-{{ $k->id }}">Kelola</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 24px;">
            <div class="kartu">
                <div class="kartu-kepala"><h3>Jadwal Hari Ini</h3></div>
                <div class="kartu-isi daftar-baris" style="gap: 10px;">
                    @forelse ($jadwalHariIni as $j)
                        <div class="baris" data-testid="chef-row-jadwal-hari-ini-{{ $j->id }}">
                            <span class="ikon-bulat"><x-ikon nama="jam" /></span>
                            <div class="isi">
                                <p class="judul">{{ $j->waktu->format('H:i') }} WIB — {{ $j->kelas->nama_kelas }}</p>
                                <p class="redup">{{ $j->link_sesi ? 'Sesi online sudah bersiap' : 'Link sesi belum diisi' }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="redup kecil">Tidak ada sesi terjadwal hari ini.</p>
                    @endforelse
                </div>
            </div>

            <div class="kartu">
                <div class="kartu-kepala">
                    <h3>Pertanyaan Terbaru</h3>
                    <a href="{{ route('chef.diskusi.index') }}" class="btn btn-ghost btn-sm" data-testid="chef-link-diskusi">Semua diskusi</a>
                </div>
                <div class="kartu-isi daftar-baris" style="gap: 10px;">
                    @forelse ($pertanyaanTerbaru as $p)
                        <div class="baris" data-testid="chef-row-pertanyaan-{{ $p->id }}">
                            <span class="ikon-bulat"><x-ikon nama="obrolan" /></span>
                            <div class="isi">
                                <p class="judul">{{ $p->user->nama }} · {{ $p->kelas->nama_kelas }}</p>
                                <p class="redup">{{ \Illuminate\Support\Str::limit($p->pesan, 80) }}</p>
                            </div>
                            <span class="badge badge-{{ $p->balasan->isEmpty() ? 'menunggu' : 'berhasil' }}">
                                {{ $p->balasan->isEmpty() ? 'Belum dibalas' : 'Sudah dibalas' }}
                            </span>
                        </div>
                    @empty
                        <p class="redup kecil">Belum ada pertanyaan dari peserta.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
