@extends('layouts.app')

@section('judul', 'Konfirmasi Pembayaran')
@section('sub-judul')
    <p class="sub-judul">
        @if ($jumlahMenunggu > 0)
            {{ $jumlahMenunggu }} pembayaran menunggu verifikasi Anda.
        @else
            Semua pembayaran telah diproses.
        @endif
    </p>
@endsection

@section('konten')
    <div class="baris-atas">
        <div style="display: flex; gap: 8px; flex-wrap: wrap;" data-testid="filter-status-pembayaran">
            @foreach (['menunggu' => 'Menunggu', 'berhasil' => 'Berhasil', 'gagal' => 'Gagal', 'semua' => 'Semua'] as $nilai => $label)
                <a href="{{ route('admin.pembayaran.index', ['status' => $nilai]) }}"
                   class="btn btn-sm {{ $status === $nilai ? 'btn-utama' : 'btn-sekunder' }}"
                   data-testid="filter-pembayaran-{{ $nilai }}">{{ $label }}</a>
            @endforeach
        </div>
    </div>

    @if ($pembayaran->isEmpty())
        <div class="kosong" data-testid="empty-pembayaran-admin">
            <x-ikon nama="centang" />
            <p class="judul">Tidak Ada Pembayaran Pada Filter Ini</p>
            <p class="redup">Bukti transfer yang dikirim learner akan tampil di sini untuk diverifikasi.</p>
        </div>
    @else
        <div class="tabel-wrap">
            <table class="tabel" data-testid="tabel-pembayaran">
                <thead>
                    <tr>
                        <th>Learner</th>
                        <th>Kelas</th>
                        <th>Jumlah</th>
                        <th>Metode</th>
                        <th>Nama Pengirim</th>
                        <th>Bukti</th>
                        <th>Status</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pembayaran as $p)
                        <tr data-testid="row-pembayaran-{{ $p->id }}">
                            <td><strong>{{ $p->learner->nama }}</strong></td>
                            <td>
                                {{ $p->kelas->nama_kelas }}<br>
                                <span class="redup kecil">Chef {{ $p->kelas->chef->nama }}</span>
                            </td>
                            <td>{{ $p->jumlahRp() }}</td>
                            <td class="redup">{{ ucfirst($p->metode) }}</td>
                            <td><code>{{ $p->no_referensi ?? '—' }}</code></td>
                            <td>
                                <button type="button" class="btn btn-sekunder btn-sm" data-modal-open="modal-bukti-{{ $p->id }}" data-testid="lihat-bukti-button-{{ $p->id }}">
                                    <x-ikon nama="tatapan" /> Lihat
                                </button>
                            </td>
                            <td><span class="badge badge-{{ $p->status }}" data-testid="badge-status-pembayaran-{{ $p->id }}">{{ $p->statusLabel() }}</span></td>
                            <td>
                                <div class="aksi">
                                    @if ($p->status === App\Models\Pembayaran::STATUS_MENUNGGU)
                                        <form method="POST" action="{{ route('admin.pembayaran.konfirmasi', $p) }}" data-testid="form-konfirmasi-{{ $p->id }}">
                                            @csrf
                                            <button type="submit" class="btn btn-berhasil btn-sm" data-testid="konfirmasi-button-{{ $p->id }}">
                                                <x-ikon nama="centang" /> Konfirmasi
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.pembayaran.tolak', $p) }}" data-confirm="Tandai pembayaran {{ $p->learner->nama }} sebagai gagal?" data-testid="form-tolak-{{ $p->id }}">
                                            @csrf
                                            <button type="submit" class="btn btn-bahaya btn-sm" data-testid="tolak-button-{{ $p->id }}">
                                                <x-ikon nama="silang" /> Tolak
                                            </button>
                                        </form>
                                    @else
                                        <span class="redup kecil">{{ $p->tanggal_bayar?->translatedFormat('d M Y, H:i') }}</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <x-paginasi :paginator="$pembayaran" />
    @endif

    @foreach ($pembayaran as $p)
        <dialog class="modal" id="modal-bukti-{{ $p->id }}">
            <div class="modal-kepala">
                <h3>Nota & Bukti Transfer — {{ $p->learner->nama }}</h3>
                <button type="button" class="btn btn-ghost btn-sm" data-tutup aria-label="Tutup" data-testid="tutup-bukti-button-{{ $p->id }}">
                    <x-ikon nama="silang" />
                </button>
            </div>
            <div class="modal-isi">
                <x-nota-pembayaran :pembayaran="$p" :nama-peserta="$p->learner->nama" />
                <section class="nota-bukti-asli">
                    <h3>Bukti Transfer Asli</h3>
                    @if ($p->bukti)
                        @if (str_ends_with(strtolower($p->bukti), '.pdf'))
                            <iframe src="{{ asset('storage/' . $p->bukti) }}" title="Bukti transfer" style="height: 420px;"></iframe>
                        @else
                            <img src="{{ asset('storage/' . $p->bukti) }}" alt="Bukti transfer {{ $p->learner->nama }}" data-testid="img-bukti-{{ $p->id }}">
                        @endif
                    @else
                        <p class="redup kecil">Bukti transfer belum diunggah.</p>
                    @endif
                </section>
            </div>
        </dialog>
    @endforeach
@endsection
