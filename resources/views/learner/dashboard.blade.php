@extends('layouts.app')

@section('judul', 'Dashboard Learner')
@section('sub-judul')
    <p class="sub-judul">Kelas yang Anda ikuti, jadwal mendatang, dan rekomendasi kelas baru.</p>
@endsection

@section('konten')
    <div class="grid-stat dashboard-stat-grid" data-testid="learner-stat-grid">
        <div class="kartu-stat hijau" data-testid="learner-stat-kelas-aktif">
            <p class="label">Kelas Diikuti</p>
            <p class="angka">{{ $stats['kelas_aktif'] }}</p>
        </div>
        <div class="kartu-stat" data-testid="learner-stat-menunggu">
            <p class="label">Menunggu Verifikasi</p>
            <p class="angka">{{ $stats['menunggu_verifikasi'] }}</p>
        </div>
        <div class="kartu-stat beige" data-testid="learner-stat-bayar">
            <p class="label">Total Investasi Belajar</p>
            <p class="angka" style="font-size: 1.3rem;">Rp {{ number_format($stats['total_bayar'], 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="jarak-atas">
        <div class="baris-atas">
            <h2 style="font-size: 1.3rem;">Kelas Saya</h2>
            <a href="{{ route('kelas.index') }}" class="btn btn-utama btn-sm" data-testid="learner-link-katalog">Jelajahi Katalog</a>
        </div>

        @if ($kelasAktif->isEmpty())
            <div class="kosong" data-testid="learner-empty-kelas">
                <x-ikon nama="kelas" />
                <p class="judul">Anda Belum Mengikuti Kelas</p>
                <p class="redup">Pilih kelas dari katalog, lakukan pembayaran, dan masuk ke ruang belajar setelah dikonfirmasi.</p>
                <a href="{{ route('kelas.index') }}" class="btn btn-utama btn-sm" style="margin-top: 8px;">Lihat Kelas Tersedia</a>
            </div>
        @else
            <div class="grid-katalog">
                @foreach ($kelasAktif as $p)
                    @php $k = $p->kelas; @endphp
                    <article class="kartu-kelas" data-testid="learner-kelas-aktif-{{ $k->id }}">
                        <div class="foto">
                            @if ($k->foto_url)
                                <img src="{{ $k->foto_url }}" alt="Foto {{ $k->nama_kelas }}">
                            @else
                                <div class="pola">{{ strtoupper(mb_substr($k->nama_kelas, 0, 1)) }}</div>
                            @endif
                            <span class="badge badge-{{ $k->status }}">{{ $k->statusLabel() }}</span>
                        </div>
                        <div class="badan">
                            <p class="nama">{{ $k->nama_kelas }}</p>
                            <p class="chef"><span class="avatar"><x-ikon nama="profil" /></span> Chef {{ $k->chef->nama }}</p>
                            <p class="menu"><x-ikon nama="menu" /> <span><strong>Menu:</strong> {{ $k->menuRingkasan() }}</span></p>
                            @if ($k->status === App\Models\Kelas::STATUS_SELESAI)
                                @if ($ratingSaya->has($k->id))
                                    <x-bintang :nilai="$ratingSaya[$k->id]" data-testid="rating-saya-{{ $k->id }}" />
                                @else
                                    <a href="{{ route('kelas.page', $k) }}" class="kecil" style="font-weight: 600;" data-testid="beri-rating-link-{{ $k->id }}">★ Beri rating kelas ini</a>
                                @endif
                            @endif
                        </div>
                        <div class="kaki">
                            <span class="harga">{{ $k->hargaRp() }}</span>
                            <a href="{{ route('kelas.page', $k) }}" class="btn btn-utama btn-sm" data-testid="learner-buka-kelas-button-{{ $k->id }}">Masuk Kelas</a>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>

    <div class="grid-2 jarak-atas">
        <div class="kartu">
            <div class="kartu-kepala"><h3>Jadwal Sesi Mendatang</h3></div>
            <div class="kartu-isi daftar-baris" style="gap: 10px;">
                @forelse ($jadwalMendatang as $j)
                    <div class="baris" data-testid="learner-row-jadwal-{{ $j->id }}">
                        <span class="ikon-bulat"><x-ikon nama="jam" /></span>
                        <div class="isi">
                            <p class="judul">{{ $j->menuResep?->nama_menu ?? 'Menu belum ditentukan' }}</p>
                            <p class="redup">{{ $j->kelas->nama_kelas }} · {{ $j->tanggalLengkap() }} · {{ $j->waktu->format('H:i') }} WIB</p>
                        </div>
                        @if ($j->link_sesi)
                            <span class="badge badge-berhasil">Link siap</span>
                        @else
                            <span class="badge badge-draft">Menunggu link</span>
                        @endif
                    </div>
                @empty
                    <p class="redup kecil">Tidak ada sesi terjadwal — nyalakan dapur untuk latihan mandiri.</p>
                @endforelse
            </div>
        </div>

        <div class="kartu">
            <div class="kartu-kepala">
                <h3>Rekomendasi Masterclass</h3>
            </div>
            <div class="kartu-isi daftar-baris" style="gap: 10px;">
                @forelse ($rekomendasi as $k)
                    <div class="baris" data-testid="learner-row-rekomendasi-{{ $k->id }}">
                        <span class="ikon-bulat"><x-ikon nama="menu" /></span>
                        <div class="isi">
                            <p class="judul">{{ $k->nama_kelas }}</p>
                            <p class="redup">Chef {{ $k->chef->nama }} · {{ $k->menuRingkasan() }}</p>
                        </div>
                        <div class="kanan">
                            <span class="harga kecil">{{ $k->hargaRp() }}</span>
                            <a href="{{ route('kelas.show', $k) }}" class="btn btn-sekunder btn-sm" data-testid="learner-lihat-rekomendasi-button-{{ $k->id }}">Lihat</a>
                        </div>
                    </div>
                @empty
                    <p class="redup kecil">Belum ada kelas baru yang tersedia.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
