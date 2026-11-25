@extends('layouts.app')

@section('judul', $kelas->nama_kelas)

@section('konten')
    <div class="baris-atas">
        <a href="{{ route('admin.kelas.index') }}" class="btn btn-ghost btn-sm" data-testid="kembali-kelas-button">
            <x-ikon nama="panah" style="transform: rotate(180deg);" /> Kembali ke daftar kelas
        </a>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('admin.kelas.edit', $kelas) }}" class="btn btn-sekunder" data-testid="edit-kelas-detail-button">
                <x-ikon nama="edit" /> Edit Kelas
            </a>
        </div>
    </div>

    <div class="kartu jarak-bawah">
        <div class="kartu-isi" style="display: grid; gap: 14px;">
            <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                <h2 style="font-size: 1.5rem;">{{ $kelas->nama_kelas }}</h2>
                <span class="badge badge-{{ $kelas->status }}" data-testid="badge-status-kelas-detail">{{ $kelas->statusLabel() }}</span>
            </div>
            <p class="redup">{{ $kelas->deskripsi ?? 'Belum ada deskripsi.' }}</p>
            <div class="grid-stat" style="margin-top: 6px;">
                <div class="kartu-stat"><p class="label">Chef Pengampu</p><p class="angka" style="font-size: 1.1rem;">{{ $kelas->chef->nama }}</p></div>
                <div class="kartu-stat beige"><p class="label">Harga</p><p class="angka harga" style="font-size: 1.1rem;">{{ $kelas->hargaRp() }}</p></div>
                <div class="kartu-stat aksen"><p class="label">Peserta (Berhasil)</p><p class="angka" style="font-size: 1.1rem;">{{ $kelas->pesertaBerhasil() }}</p></div>
                <div class="kartu-stat hijau"><p class="label">Menu & Sesi</p><p class="angka" style="font-size: 1.1rem;">{{ $kelas->menuResep->count() }} menu · {{ $kelas->jadwal->count() }} sesi</p></div>
            </div>
        </div>
    </div>

    <div class="grid-2">
        <div class="kartu">
            <div class="kartu-kepala"><h3>Menu & Resep</h3></div>
            <div class="kartu-isi daftar-baris">
                @forelse ($kelas->menuResep as $m)
                    <div class="baris" data-testid="row-menu-admin-{{ $m->id }}">
                        <span class="ikon-bulat"><x-ikon nama="menu" /></span>
                        <div class="isi">
                            <p class="judul">{{ $m->nama_menu }}</p>
                            <p class="redup">{{ \Illuminate\Support\Str::limit($m->deskripsi ?? '—', 90) }}</p>
                        </div>
                    </div>
                @empty
                    <p class="redup kecil">Belum ada menu pada kelas ini.</p>
                @endforelse
            </div>
        </div>

        <div class="kartu">
            <div class="kartu-kepala"><h3>Jadwal Sesi Online</h3></div>
            <div class="kartu-isi daftar-baris">
                @forelse ($kelas->jadwal as $j)
                    <div class="baris" data-testid="row-jadwal-admin-{{ $j->id }}">
                        <span class="ikon-bulat"><x-ikon nama="kalender" /></span>
                        <div class="isi">
                            <p class="judul">{{ $j->menuResep?->nama_menu ?? 'Menu belum dipilih' }}</p>
                            <p class="redup">{{ $j->tanggalLengkap() }} · {{ $j->waktu->format('H:i') }} WIB</p>
                            <p class="redup">{{ $j->link_sesi ? 'Link sesi sudah disiapkan' : 'Link sesi belum diisi chef' }}</p>
                        </div>
                    </div>
                @empty
                    <p class="redup kecil">Belum ada jadwal sesi.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="kartu jarak-atas">
        <div class="kartu-kepala"><h3>Peserta & Pembayaran</h3></div>
        <div class="kartu-isi" style="padding: 0;">
            @if ($kelas->pembayaran->isEmpty())
                <div class="kosong" style="border: 0;" data-testid="empty-peserta">
                    <x-ikon nama="pengguna" />
                    <p class="judul">Belum Ada Peserta</p>
                    <p class="redup">Peserta akan muncul setelah mendaftar kelas ini.</p>
                </div>
            @else
                <div class="tabel-wrap" style="border: 0; box-shadow: none;">
                    <table class="tabel" data-testid="tabel-peserta">
                        <thead>
                            <tr><th>Learner</th><th>Jumlah</th><th>No. Referensi</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            @foreach ($kelas->pembayaran as $p)
                                <tr data-testid="row-peserta-{{ $p->id }}">
                                    <td><strong>{{ $p->learner->nama }}</strong></td>
                                    <td>{{ $p->jumlahRp() }}</td>
                                    <td class="redup">{{ $p->no_referensi ?? '—' }}</td>
                                    <td><span class="badge badge-{{ $p->status }}">{{ $p->statusLabel() }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
