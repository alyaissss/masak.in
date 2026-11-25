@extends('layouts.app')

@section('judul', 'Ruang Belajar')

@section('konten')
    @php($tabDiskusiAktif = request()->query('tab') === 'diskusi')

    <div class="baris-atas">
        <div>
            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                <h2 style="font-size: 1.4rem;">{{ $kelas->nama_kelas }}</h2>
                <span class="badge badge-berhasil" data-testid="badge-kelas-aktif">{{ $pembayaran->statusLabel() }}</span>
            </div>
            <p class="sub-judul">Chef {{ $kelas->chef->nama }} · peserta: Anda dan {{ max($kelas->pesertaBerhasil() - 1, 0) }} lainnya</p>
        </div>
        <a href="{{ route('learner.dashboard') }}" class="btn btn-ghost btn-sm" data-testid="kembali-dasbor-learner">
            <x-ikon nama="panah" style="transform: rotate(180deg);" /> Dashboard
        </a>
    </div>

    <div class="kartu jarak-bawah" data-testid="kartu-rating-kelas">
        <div class="kartu-isi" style="display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; padding: 18px 24px;">
            <div>
                <h3 style="font-size: 1.05rem;">Rating Kelas</h3>
                <p class="redup kecil">
                    @if ($kelas->status === App\Models\Kelas::STATUS_SELESAI)
                        {{ $ratingSaya ? "Anda memberi {$ratingSaya} bintang — ubah kapan saja." : 'Kelas sudah selesai. Bagaimana pengalaman belajar Anda?' }}
                    @else
                        Rating dapat diberikan setelah chef menandai kelas ini selesai.
                    @endif
                </p>
            </div>
            @if ($kelas->status === App\Models\Kelas::STATUS_SELESAI)
                @include('learner.partials.form-rating', ['kelas' => $kelas, 'ratingSaya' => $ratingSaya])
            @endif
        </div>
    </div>

    <div data-tabs>
        <div class="daftar-tab" data-testid="tabs-ruang-belajar">
            <button type="button" class="{{ $tabDiskusiAktif ? '' : 'aktif' }}" data-tab="menu" data-testid="tab-menu-resep">Menu &amp; Resep ({{ $kelas->menuResep->count() }})</button>
            <button type="button" data-tab="jadwal" data-testid="tab-jadwal-sesi">Jadwal &amp; Sesi Live ({{ $kelas->jadwal->count() }})</button>
            <button type="button" class="{{ $tabDiskusiAktif ? 'aktif' : '' }}" data-tab="diskusi" data-testid="tab-diskusi">Forum Diskusi</button>
        </div>

        {{-- Menu & resep --}}
        <section data-tab-panel="menu" @if ($tabDiskusiAktif) hidden @endif>
            <div class="daftar-baris">
                @forelse ($kelas->menuResep as $m)
                    <div class="kartu" style="padding: 20px 24px;" data-testid="ruang-menu-{{ $m->id }}">
                        @if ($m->foto)
                            <img class="foto-menu-detail" src="{{ asset('storage/' . $m->foto) }}" alt="Foto {{ $m->nama_menu }}">
                        @endif
                        <h3>{{ $m->nama_menu }}</h3>
                        @if ($m->deskripsi) <p class="redup" style="margin-top: 4px;">{{ $m->deskripsi }}</p> @endif
                        @if ($m->resep)
                            <p class="overline" style="margin-top: 14px;">Bahan &amp; Langkah</p>
                            <p class="resep-isi">{{ $m->resep }}</p>
                        @endif
                    </div>
                @empty
                    <div class="kosong" data-testid="ruang-empty-menu">
                        <x-ikon nama="menu" />
                        <p class="judul">Resep Segera Diposting Chef</p>
                        <p class="redup">Chef sedang menyiapkan menu dan resep kelas ini.</p>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- Jadwal & sesi live --}}
        <section data-tab-panel="jadwal" hidden>
            <div class="daftar-baris">
                @forelse ($kelas->jadwal as $j)
                    <div class="baris" data-testid="ruang-jadwal-{{ $j->id }}">
                        <span class="ikon-bulat"><x-ikon nama="video" /></span>
                        <div class="isi">
                            <p class="judul">{{ $j->menuResep?->nama_menu ?? 'Menu belum ditentukan' }}</p>
                            <p class="redup">{{ $j->tanggalLengkap() }} · {{ $j->waktu->format('H:i') }} WIB · @if ($j->sudahLewat()) sudah berlalu @else akan datang @endif</p>
                        </div>
                        <div class="kanan">
                            @if ($j->link_sesi)
                                <a href="{{ $j->link_sesi }}" target="_blank" rel="noopener" class="btn btn-utama btn-sm" data-testid="gabung-sesi-button-{{ $j->id }}">
                                    <x-ikon nama="video" /> Gabung Sesi Live
                                </a>
                            @else
                                <span class="badge badge-draft">Link menyusul</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="kosong" data-testid="ruang-empty-jadwal">
                        <x-ikon nama="kalender" />
                        <p class="judul">Belum Ada Jadwal Sesi</p>
                        <p class="redup">Chef akan menetapkan jadwal sesi online segera.</p>
                    </div>
                @endforelse
            </div>
            <p class="bantuan">Sesi online menggunakan link video conference eksternal (Google Meet / Zoom) — gunakan sesuai jadwal kelas.</p>
        </section>

        {{-- Forum diskusi --}}
        <section data-tab-panel="diskusi" @unless ($tabDiskusiAktif) hidden @endunless>
            <div class="grid-2-1">
                <div>
                    @forelse ($pertanyaan as $p)
                        <article class="forum-item" data-testid="ruang-forum-item-{{ $p->id }}">
                            <div class="kepala">
                                <span class="avatar"><x-ikon nama="profil" /></span>
                                <span class="nama">{{ $p->user->nama }} @if ($p->user->isChef()) <span class="tanda-chef">Chef</span> @endif</span>
                                <span class="waktu">{{ $p->created_at->translatedFormat('d M Y, H:i') }}</span>
                            </div>
                            <p class="pesan">{{ $p->pesan }}</p>

                            @if ($p->balasan->isNotEmpty())
                                <div class="forum-balasan">
                                    @foreach ($p->balasan as $b)
                                        <div class="balasan" data-testid="ruang-balasan-{{ $b->id }}">
                                            <p><span class="nama">{{ $b->user->nama }}</span>@if ($b->user->isChef())<span class="tanda-chef">Chef</span>@endif
                                                <span style="font-weight: 400; color: var(--warna-teks-redup); font-size: 0.72rem;"> · {{ $b->created_at->translatedFormat('d M Y, H:i') }}</span></p>
                                            <p class="pesan" style="margin-top: 4px;">{{ $b->pesan }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </article>
                    @empty
                        <div class="kosong" data-testid="ruang-empty-diskusi">
                            <x-ikon nama="obrolan" />
                            <p class="judul">Mulai Diskusinya!</p>
                            <p class="redup">Ajukan pertanyaan pertama Anda tentang resep atau jadwal kelas ini.</p>
                        </div>
                    @endforelse
                </div>

                <div class="kartu" style="position: sticky; top: 84px;">
                    <div class="kartu-kepala"><h3>Tanya Chef</h3></div>
                    <form method="POST" action="{{ route('kelas.tanya', $kelas) }}" class="kartu-isi form-grid" data-testid="form-tanya-chef">
                        @csrf
                        <div class="field">
                            <textarea name="pesan" class="textarea" required placeholder="Tulis pertanyaan Anda tentang menu, bahan, atau jadwal…" data-testid="pertanyaan-textarea"></textarea>
                        </div>
                        <button type="submit" class="btn btn-utama btn-penuh" data-testid="kirim-pertanyaan-button">
                            <x-ikon nama="obrolan" /> Kirim Pertanyaan
                        </button>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
