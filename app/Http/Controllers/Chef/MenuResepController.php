<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\MenuResep;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

// FR-24..FR-27: chef menambah/mengedit/menghapus menu & resep pada kelasnya.
class MenuResepController extends Controller
{
    public function store(Request $request, Kelas $kelas): RedirectResponse
    {
        $this->pastikanPemilik($kelas);

        $data = $this->validasi($request);

        if ($request->hasFile('foto')) {
            $data['foto'] = $this->simpanFoto($request);
        }

        $kelas->menuResep()->create($data);

        return back()->with('sukses', 'Menu & resep berhasil ditambahkan.');
    }

    public function update(Request $request, Kelas $kelas, MenuResep $menu): RedirectResponse
    {
        $this->pastikanPemilik($kelas);
        abort_unless($menu->kelas_id === $kelas->id, 404);

        $data = $this->validasi($request);

        if ($request->hasFile('foto')) {
            $fotoLama = $menu->foto;
            $data['foto'] = $this->simpanFoto($request);
            $menu->update($data);
            if ($fotoLama && str_starts_with($fotoLama, 'menu/')) {
                Storage::disk('public')->delete($fotoLama);
            }
        } else {
            unset($data['foto']); // pertahankan foto lama bila tidak mengunggah baru
            $menu->update($data);
        }

        return back()->with('sukses', 'Menu & resep berhasil diperbarui.');
    }

    public function destroy(Kelas $kelas, MenuResep $menu): RedirectResponse
    {
        $this->pastikanPemilik($kelas);
        abort_unless($menu->kelas_id === $kelas->id, 404);

        if ($menu->foto) {
            Storage::disk('public')->delete($menu->foto);
        }

        $menu->delete();

        return back()->with('sukses', 'Menu & resep berhasil dihapus.');
    }

    private function pastikanPemilik(Kelas $kelas): void
    {
        abort_unless($kelas->chef_id === Auth::id(), 403, 'Anda hanya dapat mengelola kelas yang Anda ampu sendiri.');
    }

    private function simpanFoto(Request $request): string
    {
        $path = $request->file('foto')->store('menu', 'public');
        if ($path === false) {
            throw new \RuntimeException('Foto hidangan gagal disimpan.');
        }

        return $path;
    }

    private function validasi(Request $request): array
    {
        return $request->validate([
            'nama_menu' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:2000',
            'resep' => 'nullable|string|max:10000',
            // NFR-05 / NFR-11: unggahan divalidasi, maksimal 2 MB.
            'foto' => 'nullable|image|max:2048',
        ], [
            'nama_menu.required' => 'Nama menu wajib diisi.',
            'foto.image' => 'Foto harus berupa berkas gambar (JPG, PNG, WEBP).',
            'foto.max' => 'Ukuran foto maksimal 2 MB.',
        ]);
    }
}
