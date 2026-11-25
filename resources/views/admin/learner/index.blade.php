@extends('layouts.app')

@section('judul', 'Kelola Data Learner')
@section('sub-judul')
    <p class="sub-judul">Tambah, ubah, dan hapus data peserta akademi.</p>
@endsection

@section('konten')
    <div class="baris-atas">
        <p class="redup kecil">{{ $learner->total() }} learner terdaftar</p>
        <a href="{{ route('admin.learner.create') }}" class="btn btn-utama" data-testid="tambah-learner-button">
            <x-ikon nama="tambah" /> Tambah Learner
        </a>
    </div>

    @if ($learner->isEmpty())
        <div class="kosong" data-testid="empty-learner">
            <x-ikon nama="pengguna" />
            <p class="judul">Belum Ada Data Learner</p>
            <p class="redup">Tambahkan learner agar dapat memilih dan mengikuti kelas.</p>
        </div>
    @else
        <div class="tabel-wrap">
            <table class="tabel" data-testid="tabel-learner">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Kontak</th>
                        <th>Kelas Diikuti</th>
                        <th>Bergabung</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($learner as $l)
                        <tr data-testid="row-learner-{{ $l->id }}">
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <span class="avatar"><x-ikon nama="profil" /></span>
                                    <span>
                                        <strong>{{ $l->nama }}</strong><br>
                                        <span class="redup kecil">{{ $l->email }}</span>
                                    </span>
                                </div>
                            </td>
                            <td class="redup">{{ $l->telepon ?? '—' }}</td>
                            <td><span class="badge badge-netral">{{ $l->pembayaran_count }} kelas</span></td>
                            <td class="redup">{{ $l->created_at->translatedFormat('d M Y') }}</td>
                            <td>
                                <div class="aksi">
                                    <a href="{{ route('admin.learner.edit', $l) }}" class="btn btn-sekunder btn-sm" data-testid="edit-learner-button-{{ $l->id }}">
                                        <x-ikon nama="edit" /> Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.learner.destroy', $l) }}" data-confirm="Hapus learner {{ $l->nama }}?" data-testid="form-hapus-learner-{{ $l->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-bahaya btn-sm" data-testid="hapus-learner-button-{{ $l->id }}">
                                            <x-ikon nama="hapus" /> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <x-paginasi :paginator="$learner" />
    @endif
@endsection
