<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

// FR-14..FR-18: admin mengelola data kelas (lihat, tambah, edit, hapus, assign chef).
class KelasController extends Controller
{
    private array $pesan = [
        'nama_kelas.required' => 'Nama kelas wajib diisi.',
        'chef_id.required' => 'Chef pengajar wajib dipilih.',
        'chef_id.exists' => 'Chef tidak ditemukan.',
        'harga.numeric' => 'Harga harus berupa angka.',
        'harga.min' => 'Harga tidak boleh negatif.',
        'foto.image' => 'Foto harus berupa berkas gambar (JPG, PNG, atau WEBP).',
        'foto.max' => 'Ukuran foto maksimal 2 MB.',
    ];

    public function index(Request $request): View
    {
        $status = (string) $request->query('status', 'semua');
        if (! in_array($status, ['semua', Kelas::STATUS_DRAFT, Kelas::STATUS_TERSEDIA, Kelas::STATUS_SELESAI], true)) {
            $status = 'semua';
        }

        $kelas = Kelas::with('chef')
            ->withCount(['pembayaran', 'menuResep', 'jadwal'])
            ->when($status !== 'semua', fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.kelas.index', compact('kelas', 'status'));
    }

    public function show(Kelas $kelas): View
    {
        $kelas->load(['chef', 'menuResep', 'jadwal.menuResep', 'pembayaran.learner']);

        return view('admin.kelas.show', compact('kelas'));
    }

    public function create(): View
    {
        return view('admin.kelas.form', [
            'kelas' => new Kelas,
            'daftarChef' => $this->daftarChef(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validasi($request);

        if ($request->hasFile('foto')) {
            $data['foto'] = $this->simpanFoto($request);
        }

        Kelas::create($data);

        return redirect()->route('admin.kelas.index')->with('sukses', 'Kelas baru berhasil ditambahkan.');
    }

    public function edit(Kelas $kelas): View
    {
        return view('admin.kelas.form', [
            'kelas' => $kelas,
            'daftarChef' => $this->daftarChef(),
        ]);
    }

    public function update(Request $request, Kelas $kelas): RedirectResponse
    {
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

        return redirect()->route('admin.kelas.index')->with('sukses', 'Data kelas berhasil diperbarui.');
    }

    public function destroy(Kelas $kelas): RedirectResponse
    {
        $nama = $kelas->nama_kelas;
        $foto = $kelas->foto;
        $kelas->delete(); // menu, jadwal, pembayaran, diskusi ikut terhapus (FK cascade)
        $this->hapusFoto($foto);

        return redirect()->route('admin.kelas.index')->with('sukses', "Kelas \"{$nama}\" berhasil dihapus.");
    }

    private function daftarChef()
    {
        return User::where('role', User::ROLE_CHEF)->orderBy('nama')->get(['id', 'nama']);
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
            'chef_id' => ['required', Rule::exists('users', 'id')->where('role', User::ROLE_CHEF)],
            'deskripsi' => 'nullable|string|max:5000',
            'status' => ['required', Rule::in([Kelas::STATUS_DRAFT, Kelas::STATUS_TERSEDIA, Kelas::STATUS_SELESAI])],
            'harga' => 'required|numeric|min:0|max:99999999',
            'foto' => 'nullable|image|max:2048',
        ], $this->pesan);
    }
}
