@extends('layouts.app')

@section('judul', 'Kelola Data Chef')
@section('sub-judul')
    <p class="sub-judul">Tambah, ubah, dan hapus data chef pengajar akademi.</p>
@endsection

@section('konten')
    <div class="baris-atas">
        <p class="redup kecil">{{ $chef->total() }} chef terdaftar</p>
        <a href="{{ route('admin.chef.create') }}" class="btn btn-utama" data-testid="tambah-chef-button">
            <x-ikon nama="tambah" /> Tambah Chef
        </a>
    </div>

    @if ($chef->isEmpty())
        <div class="kosong" data-testid="empty-chef">
            <x-ikon nama="chef" />
            <p class="judul">Belum Ada Data Chef</p>
            <p class="redup">Tambahkan chef pertama agar dapat mengampu kelas kuliner.</p>
        </div>
    @else
        <div class="tabel-wrap">
            <table class="tabel" data-testid="tabel-chef">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Kontak</th>
                        <th>Spesialisasi</th>
                        <th>Kelas</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($chef as $c)
                        <tr data-testid="row-chef-{{ $c->id }}">
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <span class="avatar"><x-ikon nama="profil" /></span>
                                    <span>
                                        <strong>{{ $c->nama }}</strong><br>
                                        <span class="redup kecil">{{ $c->email }}</span>
                                    </span>
                                </div>
                            </td>
                            <td class="redup">{{ $c->telepon ?? '—' }}</td>
                            <td>{{ $c->spesialisasi ?? '—' }}</td>
                            <td><span class="badge badge-netral">{{ $c->kelas_diajar_count }} kelas</span></td>
                            <td>
                                <div class="aksi">
                                    <a href="{{ route('admin.chef.edit', $c) }}" class="btn btn-sekunder btn-sm" data-testid="edit-chef-button-{{ $c->id }}">
                                        <x-ikon nama="edit" /> Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.chef.destroy', $c) }}" data-confirm="Hapus chef {{ $c->nama }}?" data-testid="form-hapus-chef-{{ $c->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-bahaya btn-sm" data-testid="hapus-chef-button-{{ $c->id }}">
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

        <x-paginasi :paginator="$chef" />
    @endif
@endsection
