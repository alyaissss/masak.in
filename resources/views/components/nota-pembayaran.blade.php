@props(['pembayaran', 'namaPeserta'])

<article class="nota-transaksi" data-testid="nota-pembayaran-{{ $pembayaran->id }}">
    <header class="nota-merek">
        <span class="nota-logo">Masak<span>.</span>In</span>
        <span class="nota-jenis">NOTA TRANSAKSI PEMBAYARAN</span>
    </header>

    <div class="nota-nomor">
        <span>Nomor nota</span>
        <strong data-testid="nomor-nota-{{ $pembayaran->id }}">{{ $pembayaran->nomorNota() }}</strong>
    </div>

    <dl class="nota-rincian">
        <div>
            <dt>Nama peserta</dt>
            <dd>{{ $namaPeserta }}</dd>
        </div>
        <div>
            <dt>Kelas</dt>
            <dd>{{ $pembayaran->kelas->nama_kelas }}</dd>
        </div>
        <div>
            <dt>Chef</dt>
            <dd>{{ $pembayaran->kelas->chef->nama }}</dd>
        </div>
        <div>
            <dt>Metode pembayaran</dt>
            <dd>{{ $pembayaran->metodeLabel() }}</dd>
        </div>
        <div>
            <dt>Nama pengirim</dt>
            <dd>{{ $pembayaran->no_referensi ?: '—' }}</dd>
        </div>
        <div>
            <dt>Tanggal pengajuan</dt>
            <dd>{{ $pembayaran->created_at?->translatedFormat('d M Y, H:i') ?? '—' }}</dd>
        </div>
        @if ($pembayaran->tanggal_bayar)
            <div>
                <dt>Tanggal diproses</dt>
                <dd>{{ $pembayaran->tanggal_bayar->translatedFormat('d M Y, H:i') }}</dd>
            </div>
        @endif
    </dl>

    <div class="nota-total">
        <span>Total transaksi</span>
        <strong>{{ $pembayaran->jumlahRp() }}</strong>
    </div>

    <div class="nota-status">
        <span>Status pembayaran</span>
        <span class="badge badge-{{ $pembayaran->status }}">{{ $pembayaran->statusLabel() }}</span>
    </div>

    <p class="nota-catatan">
        Nota ini merupakan ringkasan transaksi di MASAK, bukan bukti transfer bank.
        Bukti transfer asli tetap menjadi acuan verifikasi pembayaran.
    </p>

    <div class="nota-aksi no-print">
        <button type="button" class="btn btn-utama btn-sm" data-print-nota>
            Cetak Nota
        </button>
    </div>
</article>
