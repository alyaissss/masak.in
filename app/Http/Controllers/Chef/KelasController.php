<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

// FR-19..FR-23 + state machine (Tugas 5): chef hanya mengelola kelas miliknya (NFR-18).
class KelasController extends Controller
{
    public function index(): View
    {
        $kelas = Kelas::where('chef_id', Auth::id())
            ->withCount(['pembayaran', 'menuResep', 'jadwal'])
            ->latest()
            ->get();

        return view('chef.kelas.index', compact('kelas'));
    }

    public function create(): View
    {
        return view('chef.kelas.form', ['kelas' => new Kelas]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validasi($request);
        $data['chef_id'] = Auth::id();

        if ($request->hasFile('foto')) {
            $data['foto'] = $this->simpanFoto($request);
        }

        $kelas = Kelas::create($data);

        return redirect()->route('chef.kelas.show', $kelas)
            ->with('sukses', 'Kelas berhasil disimpan. Tambahkan menu, lalu buat jadwal berisi tanggal, jam, dan link Zoom untuk setiap menu.');
    }

    public function show(Kelas $kelas): View
    {
        $this->pastikanPemilik($kelas);

        $kelas->load(['menuResep', 'jadwal.menuResep', 'pembayaran.learner']);
        $menuBelumTerjadwal = $kelas->menuResep->reject(
            fn ($menu) => $kelas->jadwal->contains('menu_resep_id', $menu->id)
        );
        $jadwalAwalBelumTerhubung = $kelas->jadwal->firstWhere('menu_resep_id', null);
        $pertanyaan = $kelas->diskusi()->whereNull('parent_id')->with(['user', 'balasan.user'])
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        return view('chef.kelas.show', [
            'kelas' => $kelas,
            'menuBelumTerjadwal' => $menuBelumTerjadwal,
            'jadwalAwalBelumTerhubung' => $jadwalAwalBelumTerhubung,
            'pertanyaan' => $pertanyaan,
        ]);
    }

    public function edit(Kelas $kelas): View
    {
        $this->pastikanPemilik($kelas);

        return view('chef.kelas.form', ['kelas' => $kelas]);
    }

    public function update(Request $request, Kelas $kelas): RedirectResponse
    {
        $this->pastikanPemilik($kelas);

        $data = $this->validasi($request);
        $fotoLama = $kelas->foto;
        if ($request->hasFile('foto')) {
            $data['foto'] = $this->simpanFoto($request);
        } else {
            unset($data['foto']);
        }

        $kelas->update($data);
        if ($request->hasFile('foto')) {
            $this->hapusFoto($fotoLama);
        }

        return redirect()->route('chef.kelas.show', $kelas)->with('sukses', 'Data kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kelas): RedirectResponse
    {
        $this->pastikanPemilik($kelas);

        $nama = $kelas->nama_kelas;
        $foto = $kelas->foto;
        $kelas->delete();
        $this->hapusFoto($foto);

        return redirect()->route('chef.kelas.index')->with('sukses', "Kelas \"{$nama}\" berhasil dihapus.");
    }

    // Transisi status sesuai state machine: draft→tersedia (publikasikan),
    // tersedia→selesai (tandai selesai), selesai→tersedia (buka kembali).
    public function setStatus(Request $request, Kelas $kelas): RedirectResponse
    {
        $this->pastikanPemilik($kelas);

        $tujuan = (string) $request->input('status');
        if (! $kelas->bolehTransisi($tujuan)) {
            return back()->with('galat', "Perubahan status dari \"{$kelas->statusLabel()}\" tidak diizinkan.");
        }

        $kelas->update(['status' => $tujuan]);

        $pesan = match ($tujuan) {
            Kelas::STATUS_TERSEDIA => $kelas->wasChanged() && $kelas->getOriginal('status') === Kelas::STATUS_DRAFT
                ? 'Kelas dipublikasikan dan kini tampil di katalog learner.'
                : 'Kelas dibuka kembali dan kini tampil di katalog learner.',
            Kelas::STATUS_SELESAI => 'Kelas ditandai selesai. Buka kembali bila ingin mengaktifkannya lagi.',
            default => 'Status kelas diperbarui.',
        };

        return back()->with('sukses', $pesan);
    }

    private function pastikanPemilik(Kelas $kelas): void
    {
        abort_unless($kelas->chef_id === Auth::id(), 403, 'Anda hanya dapat mengelola kelas yang Anda ampu sendiri.');
    }

    private function simpanFoto(Request $request): string
    {
        $path = $request->file('foto')->store('kelas', 'public');
        if ($path === false) {
            throw new \RuntimeException('Foto sampul kelas gagal disimpan.');
        }

        return $path;
    }

    private function hapusFoto(?string $foto): void
    {
        if ($foto && str_starts_with($foto, 'kelas/')) {
            Storage::disk('public')->delete($foto);
        }
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'nama_kelas' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:5000',
            'harga' => 'required|numeric|min:0|max:99999999',
            'foto' => 'nullable|image|max:2048',
        ], [
            'nama_kelas.required' => 'Nama kelas wajib diisi.',
            'harga.required' => 'Harga kelas wajib diisi.',
            'harga.numeric' => 'Harga harus berupa angka.',
            'foto.image' => 'Foto harus berupa berkas gambar (JPG, PNG, atau WEBP).',
            'foto.max' => 'Ukuran foto maksimal 2 MB.',
        ]);
    }
}
