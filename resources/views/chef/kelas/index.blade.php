@extends('layouts.app')

@section('judul', 'Kelas Saya')
@section('sub-judul')
    <p class="sub-judul">Kelola kelas milik Anda: data, menu & resep, jadwal, dan status tayang.</p>
@endsection

@section('konten')
    <div class="baris-atas">
        <p class="redup kecil">{{ $kelas->count() }} kelas Anda</p>
        <a href="{{ route('chef.kelas.create') }}" class="btn btn-utama" data-testid="chef-buat-kelas-button">
            <x-ikon nama="tambah" /> Buat Kelas Baru
        </a>
    </div>

    @if ($kelas->isEmpty())
        <div class="kosong" data-testid="chef-empty-kelas-index">
            <x-ikon nama="kelas" />
            <p class="judul">Anda Belum Membuat Kelas</p>
            <p class="redup">Mulai dengan membuat kelas, lalu tambahkan menu, resep, dan jadwal sesi online.</p>
        </div>
    @else
        <div class="grid-katalog">
            @foreach ($kelas as $k)
                <article class="kartu-kelas" data-testid="chef-kartu-kelas-{{ $k->id }}">
                    <div class="foto">
                        @if ($k->foto_url)
                            <img src="{{ $k->foto_url }}" alt="Foto {{ $k->nama_kelas }}">
                        @else
                            <div class="pola">{{ strtoupper(mb_substr($k->nama_kelas, 0, 1)) }}</div>
                        @endif
                        <span class="badge badge-{{ $k->status }}" data-testid="chef-badge-status-{{ $k->id }}">{{ $k->statusLabel() }}</span>
                    </div>
                    <div class="badan">
                        <p class="nama">{{ $k->nama_kelas }}</p>
                        <p class="menu"><x-ikon nama="menu" /> <span><strong>Menu:</strong> {{ $k->menuRingkasan() }}</span></p>
                        <p class="redup kecil">{{ $k->menu_resep_count }} menu · {{ $k->jadwal_count }} sesi · {{ $k->pembayaran_count }} pendaftar</p>
                    </div>
                    <div class="kaki">
                        <span class="harga">{{ $k->hargaRp() }}</span>
                        <div style="display: flex; gap: 8px;">
                            <a href="{{ route('chef.kelas.show', $k) }}" class="btn btn-utama btn-sm" data-testid="chef-kelola-button-{{ $k->id }}">Kelola</a>
                            <a href="{{ route('chef.kelas.edit', $k) }}" class="btn btn-sekunder btn-sm" data-testid="chef-edit-button-{{ $k->id }}">Edit</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
@endsection
