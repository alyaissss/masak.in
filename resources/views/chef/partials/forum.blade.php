{{-- Feed forum diskusi dengan formulir balasan chef — dipakai chef/kelas/show dan chef/diskusi --}}
@if ($pertanyaan->isEmpty())
    <div class="kosong" data-testid="chef-empty-diskusi">
        <x-ikon nama="obrolan" />
        <p class="judul">Belum Ada Pertanyaan di Forum</p>
        <p class="redup">Pertanyaan dari peserta kelas akan muncul di sini.</p>
    </div>
@else
    <div>
        @foreach ($pertanyaan as $p)
            <article class="forum-item" data-testid="chef-forum-item-{{ $p->id }}">
                <div class="kepala">
                    <span class="avatar"><x-ikon nama="profil" /></span>
                    <span class="nama">{{ $p->user->nama }}</span>
                    <span class="waktu">{{ $p->created_at->translatedFormat('d M Y, H:i') }}</span>
                    @isset($p->kelas) <span class="badge badge-netral">{{ $p->kelas->nama_kelas }}</span> @endisset
                    <span class="badge badge-{{ $p->balasan->isEmpty() ? 'menunggu' : 'berhasil' }}" data-testid="chef-badge-diskusi-{{ $p->id }}">
                        {{ $p->balasan->isEmpty() ? 'Belum dibalas' : 'Sudah dibalas' }}
                    </span>
                </div>
                <p class="pesan">{{ $p->pesan }}</p>

                @if ($p->balasan->isNotEmpty())
                    <div class="forum-balasan">
                        @foreach ($p->balasan as $b)
                            <div class="balasan" data-testid="chef-balasan-{{ $b->id }}">
                                <p><span class="nama">{{ $b->user->nama }}</span>@if ($b->user->isChef())<span class="tanda-chef">Chef</span>@endif
                                    <span class="waktu" style="font-weight: 400; color: var(--warna-teks-redup); font-size: 0.72rem;"> · {{ $b->created_at->translatedFormat('d M Y, H:i') }}</span></p>
                                <p class="pesan" style="margin-top: 4px;">{{ $b->pesan }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('chef.diskusi.balas', $p->kelas_id) }}" class="form-grid" style="margin-top: 14px;" data-testid="chef-form-balas-{{ $p->id }}">
                    @csrf
                    <input type="hidden" name="parent_id" value="{{ $p->id }}">
                    @if (!empty($kembaliKeKelas))
                        <input type="hidden" name="kembali_ke" value="kelas">
                    @endif
                    <div class="field">
                        <textarea name="pesan" class="textarea" required placeholder="Tulis balasan untuk {{ $p->user->nama }}…" data-testid="chef-balasan-textarea-{{ $p->id }}"></textarea>
                    </div>
                    <div style="display: flex; justify-content: flex-end;">
                        <button type="submit" class="btn btn-utama btn-sm" data-testid="chef-kirim-balasan-button-{{ $p->id }}">
                            <x-ikon nama="balas" /> Kirim Balasan
                        </button>
                    </div>
                </form>
            </article>
        @endforeach
    </div>

    @if ($pertanyaan instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
        {{ $pertanyaan->links('components.paginasi') }}
    @endif
@endif
