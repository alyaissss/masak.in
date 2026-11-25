@extends('layouts.app')

@section('judul', 'Kelola Data Kelas')
@section('sub-judul')
    <p class="sub-judul">Seluruh kelas akademi beserta chef pengampu dan statusnya.</p>
@endsection

@section('konten')
    <div class="baris-atas">
        <div style="display: flex; gap: 8px; flex-wrap: wrap;" data-testid="filter-status-kelas">
            @foreach (['semua' => 'Semua', App\Models\Kelas::STATUS_DRAFT => 'Draft', App\Models\Kelas::STATUS_TERSEDIA => 'Tersedia', App\Models\Kelas::STATUS_SELESAI => 'Selesai'] as $nilai => $label)
                <a href="{{ route('admin.kelas.index', ['status' => $nilai]) }}"
                   class="btn btn-sm {{ $status === $nilai ? 'btn-utama' : 'btn-sekunder' }}"
                   data-testid="filter-kelas-{{ $nilai }}">{{ $label }}</a>
            @endforeach
        </div>
        <a href="{{ route('admin.kelas.create') }}" class="btn btn-utama" data-testid="tambah-kelas-button">
            <x-ikon nama="tambah" /> Tambah Kelas
        </a>
    </div>

    @if ($kelas->isEmpty())
        <div class="kosong" data-testid="empty-kelas">
            <x-ikon nama="kelas" />
            <p class="judul">Belum Ada Kelas</p>
            <p class="redup">Tambahkan kelas atau minta chef membuat kelas baru.</p>
        </div>
    @else
        <div class="tabel-wrap">
            <table class="tabel" data-testid="tabel-kelas">
                <thead>
                    <tr>
                        <th>Kelas</th>
                        <th>Chef</th>
                        <th>Status</th>
                        <th>Menu</th>
                        <th>Sesi</th>
                        <th>Peserta</th>
                        <th>Harga</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($kelas as $k)
                        <tr data-testid="row-kelas-{{ $k->id }}">
                            <td><strong>{{ $k->nama_kelas }}</strong></td>
                            <td>{{ $k->chef->nama }}</td>
                            <td><span class="badge badge-{{ $k->status }}" data-testid="badge-status-kelas-{{ $k->id }}">{{ $k->statusLabel() }}</span></td>
                            <td>{{ $k->menu_resep_count }}</td>
                            <td>{{ $k->jadwal_count }}</td>
                            <td>{{ $k->pembayaran_count }}</td>
                            <td>{{ $k->hargaRp() }}</td>
                            <td>
                                <div class="aksi">
                                    <a href="{{ route('admin.kelas.show', $k) }}" class="btn btn-ghost btn-sm" data-testid="lihat-kelas-button-{{ $k->id }}">Lihat</a>
                                    <a href="{{ route('admin.kelas.edit', $k) }}" class="btn btn-sekunder btn-sm" data-testid="edit-kelas-button-{{ $k->id }}">
                                        <x-ikon nama="edit" /> Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.kelas.destroy', $k) }}" data-confirm="Hapus kelas \"{{ $k->nama_kelas }}\"? Seluruh menu, jadwal, dan pembayarannya ikut terhapus." data-testid="form-hapus-kelas-{{ $k->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-bahaya btn-sm" data-testid="hapus-kelas-button-{{ $k->id }}">
                                            <x-ikon nama="hapus" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <x-paginasi :paginator="$kelas" />
    @endif
@endsection
