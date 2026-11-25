@extends('layouts.app')

@section('judul', 'Status Pembayaran Saya')
@section('sub-judul')
    <p class="sub-judul">Pantau status pembayaran dan akses ruang belajar Anda.</p>
@endsection

@section('konten')
    @if ($pembayaran->isEmpty())
        <div class="kosong" data-testid="learner-empty-pembayaran">
            <x-ikon nama="dompet" />
            <p class="judul">Belum Ada Pembayaran</p>
            <p class="redup">Pilih kelas dari katalog untuk memulai pendaftaran dan pembayaran.</p>
            <a href="{{ route('kelas.index') }}" class="btn btn-utama btn-sm" style="margin-top: 8px;">Lihat Katalog Kelas</a>
        </div>
    @else
        <div class="tabel-wrap">
            <table class="tabel" data-testid="tabel-pembayaran-saya">
                <thead>
                    <tr>
                        <th>Kelas</th>
                        <th>Jumlah</th>
                        <th>Metode</th>
                        <th>Pengirim</th>
                        <th>Bukti</th>
                        <th>Status</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pembayaran as $p)
                        <tr data-testid="row-pembayaran-saya-{{ $p->id }}">
                            <td>
                                <strong>{{ $p->kelas->nama_kelas }}</strong><br>
                                <span class="redup kecil">Chef {{ $p->kelas->chef->nama }}</span>
                            </td>
                            <td>{{ $p->jumlahRp() }}</td>
                            <td class="redup">{{ ucfirst($p->metode) }}</td>
                            <td><code>{{ $p->no_referensi ?? '—' }}</code></td>
                            <td>
                                <button type="button" class="btn btn-sekunder btn-sm" data-modal-open="modal-bukti-saya-{{ $p->id }}" data-testid="lihat-bukti-saya-button-{{ $p->id }}">
                                    Lihat
                                </button>
                            </td>
                            <td><span class="badge badge-{{ $p->status }}" data-testid="badge-pembayaran-saya-{{ $p->id }}">{{ $p->statusLabel() }}</span></td>
                            <td>
                                <div class="aksi">
                                    @if ($p->status === App\Models\Pembayaran::STATUS_BERHASIL)
                                        <a href="{{ route('kelas.page', $p->kelas) }}" class="btn btn-berhasil btn-sm" data-testid="masuk-kelas-button-{{ $p->id }}">
                                            <x-ikon nama="panah" /> Masuk Kelas
                                        </a>
                                    @elseif ($p->status === App\Models\Pembayaran::STATUS_GAGAL)
                                        <a href="{{ route('kelas.daftar', $p->kelas) }}" class="btn btn-aksen btn-sm" data-testid="daftar-ulang-button-{{ $p->id }}">Daftar Ulang</a>
                                    @else
                                        <span class="redup kecil">1×24 jam kerja</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @foreach ($pembayaran as $p)
        <dialog class="modal" id="modal-bukti-saya-{{ $p->id }}">
            <div class="modal-kepala">
                <h3>Nota & Bukti Transfer — {{ $p->kelas->nama_kelas }}</h3>
                <button type="button" class="btn btn-ghost btn-sm" data-tutup aria-label="Tutup" data-testid="tutup-bukti-saya-button-{{ $p->id }}">
                    <x-ikon nama="silang" />
                </button>
            </div>
            <div class="modal-isi">
                <x-nota-pembayaran :pembayaran="$p" :nama-peserta="$p->learner->nama" />
                <section class="nota-bukti-asli">
                    <h3>Bukti Transfer Pengirim</h3>
                    @if ($p->bukti)
                        <img src="{{ asset('storage/' . $p->bukti) }}" alt="Bukti transfer" data-testid="img-bukti-saya-{{ $p->id }}">
                    @else
                        <p class="redup kecil">Bukti transfer belum diunggah.</p>
                    @endif
                </section>
            </div>
        </dialog>
    @endforeach
@endsection
