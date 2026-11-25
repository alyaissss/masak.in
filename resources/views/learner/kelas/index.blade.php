@extends('layouts.app')

@section('judul', 'Katalog Kelas Kuliner')
@section('sub-judul')
    <p class="sub-judul">Pilih masterclass sesuai selera dan jadwal Anda.</p>
@endsection

@section('konten')
    <form method="GET" action="{{ route('kelas.index') }}" class="baris-atas" data-testid="form-cari-kelas">
        <div style="display: flex; gap: 10px; flex: 1; max-width: 460px;">
            <input type="search" name="q" value="{{ $q }}" class="input" placeholder="Cari kelas, chef, atau menu…" data-testid="input-cari-kelas">
            <button type="submit" class="btn btn-sekunder" data-testid="cari-kelas-button">
                <x-ikon nama="carikan" /> Cari
            </button>
        </div>
        <p class="redup kecil">{{ $kelas->count() }} kelas tersedia</p>
    </form>

    @if ($chefFavorit->isNotEmpty())
        <section class="jarak-bawah" data-testid="chef-favorit">
            <p class="overline" style="margin-bottom: 10px;">Chef Favorit Learner</p>
            <div class="chef-favorit">
                @foreach ($chefFavorit as $i => $c)
                    <div class="item" data-testid="chef-favorit-{{ $c->id }}">
                        <span class="peringkat">{{ $i + 1 }}</span>
                        <span class="avatar"><x-ikon nama="profil" /></span>
                        <div style="min-width: 0;">
                            <p class="nama">Chef {{ $c->nama }}</p>
                            @if ($c->spesialisasi) <p class="redup kecil">{{ $c->spesialisasi }}</p> @endif
                            <x-bintang :nilai="$c->ringkasan->rata" :jumlah="$c->ringkasan->jumlah" />
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    @if ($kelas->isEmpty())
        <div class="kosong" data-testid="learner-empty-katalog">
            <x-ikon nama="menu" />
            <p class="judul">Belum Ada Kelas Tersedia</p>
            <p class="redup">Silakan periksa kembali nanti atau hubungi staf akademi.</p>
        </div>
    @else
        <div class="grid-katalog" data-testid="grid-katalog-kelas">
            @foreach ($kelas as $k)
                @php $status = $statusSaya[$k->id] ?? null; @endphp
                <article class="kartu-kelas" data-testid="class-card-{{ $k->id }}">
                    <div class="foto">
                        @if ($k->foto_url)
                            <img src="{{ $k->foto_url }}" alt="Foto {{ $k->nama_kelas }}">
                        @else
                            <div class="pola">{{ strtoupper(mb_substr($k->nama_kelas, 0, 1)) }}</div>
                        @endif
                        <span class="badge badge-tersedia">Tersedia</span>
                    </div>
                    <div class="badan">
                        <p class="nama">{{ $k->nama_kelas }}</p>
                        <p class="chef"><span class="avatar"><x-ikon nama="profil" /></span> Chef {{ $k->chef->nama }}</p>
                        @php $rc = $ratingChef->get($k->chef_id); @endphp
                        <x-bintang :nilai="$rc?->rata" :jumlah="$rc?->jumlah ?? 0" data-testid="rating-chef-kartu-{{ $k->id }}" />
                        <p class="menu"><x-ikon nama="menu" /> <span><strong>Menu:</strong> {{ $k->menuRingkasan() }}</span></p>
                        <div class="info-sesi">
                            @php $sesi = $k->jadwal->firstWhere(fn ($j) => $j->tanggal->isFuture() || $j->tanggal->isSameDay(today())); @endphp
                            @if ($sesi)
                                <span class="chip-sesi"><x-ikon nama="menu" /> {{ $sesi->menuResep?->nama_menu ?? 'Sesi kelas' }}</span>
                                <span class="chip-sesi"><x-ikon nama="kalender" /> {{ $sesi->tanggal->translatedFormat('d M Y') }}</span>
                                <span class="chip-sesi"><x-ikon nama="jam" /> {{ $sesi->waktu->format('H:i') }} WIB</span>
                            @else
                                <span class="chip-sesi"><x-ikon nama="kalender" /> Jadwal menyusul</span>
                            @endif
                        </div>
                    </div>
                    <div class="kaki">
                        <span class="harga">{{ $k->hargaRp() }}</span>
                        @if ($status === App\Models\Pembayaran::STATUS_BERHASIL)
                            <a href="{{ route('kelas.page', $k) }}" class="btn btn-berhasil btn-sm" data-testid="kelas-buka-ruang-button-{{ $k->id }}">Buka Ruang Belajar</a>
                        @elseif ($status === App\Models\Pembayaran::STATUS_MENUNGGU)
                            <a href="{{ route('learner.pembayaran.index') }}" class="btn btn-sekunder btn-sm" data-testid="kelas-menunggu-button-{{ $k->id }}">Menunggu Verifikasi</a>
                        @elseif ($status === App\Models\Pembayaran::STATUS_GAGAL)
                            <a href="{{ route('kelas.daftar', $k) }}" class="btn btn-aksen btn-sm" data-testid="kelas-daftar-ulang-button-{{ $k->id }}">Daftar Ulang</a>
                        @else
                            <a href="{{ route('kelas.show', $k) }}" class="btn btn-utama btn-sm" data-testid="kelas-pilih-button-{{ $k->id }}">Pilih Kelas</a>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    @endif
@endsection
