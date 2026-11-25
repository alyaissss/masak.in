@extends('layouts.app')

@section('judul', $kelas->nama_kelas)

@section('konten')
    @php($tabJadwalAktif = request()->query('tab') === 'jadwal')
    @php($tabDiskusiAktif = request()->query('tab') === 'diskusi')

    <div class="baris-atas">
        <a href="{{ route('chef.kelas.index') }}" class="btn btn-ghost btn-sm" data-testid="chef-kembali-dari-show">
            <x-ikon nama="panah" style="transform: rotate(180deg);" /> Kelas saya
        </a>
        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            @if ($kelas->status === App\Models\Kelas::STATUS_DRAFT)
                <form method="POST" action="{{ route('chef.kelas.set-status', $kelas) }}" data-testid="chef-form-publikasikan">
                    @csrf
                    <input type="hidden" name="status" value="{{ App\Models\Kelas::STATUS_TERSEDIA }}">
                    <button type="submit" class="btn btn-berhasil" data-testid="chef-publikasikan-button">
                        <x-ikon nama="centang" /> Publikasikan Kelas
                    </button>
                </form>
            @elseif ($kelas->status === App\Models\Kelas::STATUS_TERSEDIA)
                <form method="POST" action="{{ route('chef.kelas.set-status', $kelas) }}" data-testid="chef-form-selesaikan">
                    @csrf
                    <input type="hidden" name="status" value="{{ App\Models\Kelas::STATUS_SELESAI }}">
                    <button type="submit" class="btn btn-sekunder" data-testid="chef-tandai-selesai-button">
                        <x-ikon nama="centang" /> Tandai Selesai
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('chef.kelas.set-status', $kelas) }}" data-testid="chef-form-buka-kembali">
                    @csrf
                    <input type="hidden" name="status" value="{{ App\Models\Kelas::STATUS_TERSEDIA }}">
                    <button type="submit" class="btn btn-aksen" data-testid="chef-buka-kembali-button">
                        <x-ikon nama="panah" /> Buka Kembali
                    </button>
                </form>
            @endif
            <a href="{{ route('chef.kelas.edit', $kelas) }}" class="btn btn-sekunder" data-testid="chef-edit-kelas-show-button">
                <x-ikon nama="edit" /> Edit
            </a>
            <form method="POST" action="{{ route('chef.kelas.destroy', $kelas) }}" data-confirm="Hapus kelas ini? Seluruh menu, jadwal, dan pembayarannya ikut terhapus." data-testid="chef-form-hapus-kelas-show">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-bahaya" data-testid="chef-hapus-kelas-show-button">
                    <x-ikon nama="hapus" /> Hapus
                </button>
            </form>
        </div>
    </div>

    <div data-tabs>
        <div class="daftar-tab" data-testid="chef-tabs-kelas">
            <button type="button" class="{{ $tabJadwalAktif || $tabDiskusiAktif ? '' : 'aktif' }}" data-tab="menu" data-testid="chef-tab-menu">Menu &amp; Resep ({{ $kelas->menuResep->count() }})</button>
            <button type="button" class="{{ $tabJadwalAktif ? 'aktif' : '' }}" data-tab="jadwal" data-testid="chef-tab-jadwal">Jadwal &amp; Sesi ({{ $kelas->jadwal->count() }})</button>
            <button type="button" data-tab="peserta" data-testid="chef-tab-peserta">Peserta ({{ $kelas->pembayaran->count() }})</button>
            <button type="button" class="{{ $tabDiskusiAktif ? 'aktif' : '' }}" data-tab="diskusi" data-testid="chef-tab-diskusi">Diskusi ({{ $pertanyaan->count() }})</button>
        </div>

        {{-- ── Menu & Resep ── --}}
        <section data-tab-panel="menu" @if ($tabJadwalAktif || $tabDiskusiAktif) hidden @endif>
            <div class="grid-2-1">
                <div class="daftar-baris">
                    @forelse ($kelas->menuResep as $m)
                        <div class="kartu" style="padding: 18px 20px;" data-testid="chef-row-menu-{{ $m->id }}">
                            <div style="display: flex; gap: 14px; align-items: flex-start;">
                                <span class="ikon-bulat"><x-ikon nama="menu" /></span>
                                <div class="isi" style="flex: 1;">
                                    <p class="judul">{{ $m->nama_menu }}</p>
                                    @if ($m->deskripsi) <p class="redup">{{ $m->deskripsi }}</p> @endif
                                    @if ($m->resep)
                                        <p class="resep-isi">{{ $m->resep }}</p>
                                    @endif
                                    <details class="edit-inline">
                                        <summary data-testid="chef-edit-menu-toggle-{{ $m->id }}">Ubah menu ini</summary>
                                        <form method="POST" action="{{ route('chef.menu.update', [$kelas, $m]) }}" enctype="multipart/form-data" class="form-grid" data-image-crop-form data-testid="chef-form-edit-menu-{{ $m->id }}">
                                            @csrf
                                            @method('PUT')
                                            <div class="field"><label>Nama Menu</label><input name="nama_menu" class="input" value="{{ $m->nama_menu }}" required></div>
                                            <div class="field"><label>Deskripsi</label><textarea name="deskripsi" class="textarea">{{ $m->deskripsi }}</textarea></div>
                                            <div class="field"><label>Bahan &amp; Langkah Resep</label><textarea name="resep" class="textarea">{{ $m->resep }}</textarea></div>
                                            <x-image-crop-input id="edit-menu-foto-{{ $m->id }}" label="Ganti Foto Hidangan (opsional)" :current="$m->foto ? asset('storage/' . $m->foto) : null" />
                                            <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                                <button type="submit" class="btn btn-utama btn-sm">Simpan Menu</button>
                                            </div>
                                        </form>
                                    </details>
                                </div>
                                <div class="kanan">
                                    @if ($m->foto)
                                        <img class="foto-menu" src="{{ asset('storage/' . $m->foto) }}" alt="Foto {{ $m->nama_menu }}">
                                    @endif
                                    <form method="POST" action="{{ route('chef.menu.destroy', [$kelas, $m]) }}" data-confirm="Hapus menu {{ $m->nama_menu }}?" data-testid="chef-form-hapus-menu-{{ $m->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-bahaya btn-sm" data-testid="chef-hapus-menu-button-{{ $m->id }}"><x-ikon nama="hapus" /></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="kosong" data-testid="chef-empty-menu">
                            <x-ikon nama="menu" />
                            <p class="judul">Belum Ada Menu</p>
                            <p class="redup">Tambahkan menu utama dan resepnya melalui formulir di samping.</p>
                        </div>
                    @endforelse
                </div>

                <div class="kartu">
                    <div class="kartu-kepala"><h3>Tambah Menu & Resep</h3></div>
                    <form method="POST" action="{{ route('chef.menu.store', $kelas) }}" enctype="multipart/form-data" class="kartu-isi form-grid" data-image-crop-form data-testid="chef-form-tambah-menu">
                        @csrf
                        <div class="field">
                            <label class="wajib">Nama Menu</label>
                            <input name="nama_menu" class="input" required placeholder="cth: Rendang Wagyu" data-testid="chef-menu-nama-input">
                        </div>
                        <div class="field">
                            <label>Deskripsi</label>
                            <textarea name="deskripsi" class="textarea" placeholder="Deskripsi singkat hidangan…" data-testid="chef-menu-deskripsi-textarea"></textarea>
                        </div>
                        <div class="field">
                            <label>Bahan &amp; Langkah</label>
                            <textarea name="resep" class="textarea" placeholder="Bahan:…&#10;1. …&#10;2. …" data-testid="chef-menu-resep-textarea"></textarea>
                        </div>
                        <x-image-crop-input id="tambah-menu-foto" label="Foto Hidangan (opsional)" testid="chef-menu-foto-drop" />
                        <button type="submit" class="btn btn-utama btn-penuh" data-testid="chef-tambah-menu-button">
                            <x-ikon nama="tambah" /> Tambah Menu
                        </button>
                    </form>
                </div>
            </div>
        </section>

        {{-- ── Jadwal & Sesi ── --}}
        <section data-tab-panel="jadwal" @unless ($tabJadwalAktif) hidden @endunless>
            <div class="grid-2-1">
                <div class="daftar-baris">
                    @forelse ($kelas->jadwal as $j)
                        <div class="baris" data-testid="chef-row-jadwal-{{ $j->id }}">
                            <span class="ikon-bulat"><x-ikon nama="kalender" /></span>
                            <div class="isi">
                                <p class="judul">{{ $j->menuResep?->nama_menu ?? 'Menu belum dipilih' }}</p>
                                <p class="redup">{{ $j->tanggalLengkap() }} · {{ $j->waktu->format('H:i') }} WIB</p>
                                <p class="redup">{{ $j->link_sesi ? 'Sesi online: ' . $j->link_sesi : 'Link video conference belum diisi' }}</p>
                                <details class="edit-inline">
                                    <summary data-testid="chef-edit-jadwal-toggle-{{ $j->id }}">Ubah jadwal ini</summary>
                                    <form method="POST" action="{{ route('chef.jadwal.update', [$kelas, $j]) }}" class="form-grid" data-testid="chef-form-edit-jadwal-{{ $j->id }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="field">
                                            <label class="wajib">Menu sesi</label>
                                            <select name="menu_resep_id" class="select" required>
                                                <option value="">— Pilih menu —</option>
                                                @foreach ($kelas->menuResep as $menu)
                                                    <option value="{{ $menu->id }}" @selected($j->menu_resep_id === $menu->id)>{{ $menu->nama_menu }}</option>
                                                @endforeach
                                            </select>
                                            @error('menu_resep_id') <span class="galat">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="form-grid dua">
                                            <div class="field">
                                                <label>Tanggal</label>
                                                <input type="date" name="tanggal" class="input" value="{{ $j->tanggal->format('Y-m-d') }}" required>
                                                @error('tanggal') <span class="galat">{{ $message }}</span> @enderror
                                            </div>
                                            <div class="field">
                                                <label>Jam Mulai</label>
                                                <input type="time" name="waktu" class="input" value="{{ $j->waktu->format('H:i') }}" required>
                                                @error('waktu') <span class="galat">{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                        <div class="field">
                                            <label>Link Video Conference</label>
                                            <input type="url" name="link_sesi" class="input" value="{{ $j->link_sesi }}" placeholder="https://meet.google.com/…">
                                            @error('link_sesi') <span class="galat">{{ $message }}</span> @enderror
                                        </div>
                                        <div style="display: flex; justify-content: flex-end;">
                                            <button type="submit" class="btn btn-utama btn-sm">Simpan Jadwal</button>
                                        </div>
                                    </form>
                                </details>
                            </div>
                            <div class="kanan">
                                <form method="POST" action="{{ route('chef.jadwal.destroy', [$kelas, $j]) }}" data-confirm="Hapus jadwal sesi ini?" data-testid="chef-form-hapus-jadwal-{{ $j->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-bahaya btn-sm" data-testid="chef-hapus-jadwal-button-{{ $j->id }}"><x-ikon nama="hapus" /></button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="kosong" data-testid="chef-empty-jadwal">
                            <x-ikon nama="kalender" />
                            <p class="judul">Belum Ada Jadwal</p>
                            <p class="redup">Tambahkan menu terlebih dahulu, lalu tetapkan tanggal, jam, dan link video conference untuk setiap sesi.</p>
                        </div>
                    @endforelse
                </div>

                <div class="kartu">
                    <div class="kartu-kepala">
                        <h3>Tambah Jadwal Sesi</h3>
                        <span class="redup kecil">{{ $kelas->jadwal->whereNotNull('menu_resep_id')->count() }} dari {{ $kelas->menuResep->count() }} menu sudah dijadwalkan</span>
                    </div>
                    @if ($menuBelumTerjadwal->isNotEmpty())
                        <form method="POST" action="{{ route('chef.jadwal.store', $kelas) }}" class="kartu-isi form-grid" data-testid="chef-form-tambah-jadwal">
                            @csrf
                            <div class="field">
                                <label class="wajib">Menu sesi</label>
                                <select name="menu_resep_id" class="select" required data-testid="chef-jadwal-menu-select">
                                    <option value="">— Pilih menu —</option>
                                    @foreach ($menuBelumTerjadwal as $menu)
                                        <option value="{{ $menu->id }}" @selected(old('menu_resep_id') == $menu->id)>{{ $menu->nama_menu }}</option>
                                    @endforeach
                                </select>
                                @error('menu_resep_id') <span class="galat">{{ $message }}</span> @enderror
                            </div>
                            <div class="field">
                                <label class="wajib">Tanggal</label>
                                <input type="date" name="tanggal" class="input" value="{{ old('tanggal', $jadwalAwalBelumTerhubung?->tanggal?->format('Y-m-d')) }}" required data-testid="chef-jadwal-tanggal-input">
                                @error('tanggal') <span class="galat">{{ $message }}</span> @enderror
                            </div>
                            <div class="field">
                                <label class="wajib">Jam Mulai</label>
                                <input type="time" name="waktu" class="input" value="{{ old('waktu', $jadwalAwalBelumTerhubung?->waktu?->format('H:i')) }}" required data-testid="chef-jadwal-waktu-input">
                                @error('waktu') <span class="galat">{{ $message }}</span> @enderror
                            </div>
                            <div class="field">
                                <label>Link Video Conference</label>
                                <input type="url" name="link_sesi" class="input" value="{{ old('link_sesi') }}" placeholder="https://meet.google.com/… atau https://zoom.us/…" data-testid="chef-jadwal-link-input">
                                @error('link_sesi') <span class="galat">{{ $message }}</span> @enderror
                                <p class="bantuan">Gunakan link eksternal (Google Meet / Zoom) — sistem tidak menyelenggarakan live streaming sendiri.</p>
                            </div>
                            <button type="submit" class="btn btn-utama btn-penuh" data-testid="chef-tambah-jadwal-button">
                                <x-ikon nama="tambah" /> Tambah Jadwal
                            </button>
                        </form>
                    @else
                        <div class="kartu-isi">
                            <p class="redup">{{ $kelas->menuResep->isEmpty() ? 'Tambahkan menu terlebih dahulu di tab Menu & Resep untuk membuat jadwal sesi.' : 'Semua menu sudah memiliki jadwal sesi.' }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        {{-- ── Peserta ── --}}
        <section data-tab-panel="peserta" hidden>
            <div class="tabel-wrap">
                <table class="tabel" data-testid="chef-tabel-peserta">
                    <thead>
                        <tr><th>Learner</th><th>Jumlah</th><th>No. Referensi</th><th>Status Pembayaran</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($kelas->pembayaran as $p)
                            <tr data-testid="chef-row-peserta-{{ $p->id }}">
                                <td><strong>{{ $p->learner->nama }}</strong><br><span class="redup kecil">{{ $p->learner->email }}</span></td>
                                <td>{{ $p->jumlahRp() }}</td>
                                <td><code>{{ $p->no_referensi ?? '—' }}</code></td>
                                <td><span class="badge badge-{{ $p->status }}">{{ $p->statusLabel() }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4"><div class="kosong" style="border: 0;"><p class="judul">Belum Ada Peserta</p><p class="redup">Peserta muncul setelah mendaftar kelas ini.</p></div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{-- ── Diskusi ── --}}
        <section data-tab-panel="diskusi" @unless ($tabDiskusiAktif) hidden @endunless>
            @include('chef.partials.forum', ['kelas' => $kelas, 'pertanyaan' => $pertanyaan, 'kembaliKeKelas' => true])
        </section>
    </div>
@endsection
