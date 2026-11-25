<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Kelas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

// FR-29..FR-32: chef mengelola tanggal, jam, dan link video conference per kelas.
class JadwalController extends Controller
{
    public function store(Request $request, Kelas $kelas): RedirectResponse
    {
        $this->pastikanPemilik($kelas);

        $data = $this->validasi($request, $kelas);

        $jadwalAwal = $kelas->jadwal()->whereNull('menu_resep_id')->orderBy('id')->first();
        if ($jadwalAwal) {
            $jadwalAwal->update($data);
        } else {
            $kelas->jadwal()->create($data);
        }

        return back()->with('sukses', 'Jadwal sesi berhasil ditambahkan.');
    }

    public function update(Request $request, Kelas $kelas, Jadwal $jadwal): RedirectResponse
    {
        $this->pastikanPemilik($kelas);
        abort_unless($jadwal->kelas_id === $kelas->id, 404);

        $jadwal->update($this->validasi($request, $kelas, $jadwal));

        return back()->with('sukses', 'Jadwal sesi berhasil diperbarui.');
    }

    public function destroy(Kelas $kelas, Jadwal $jadwal): RedirectResponse
    {
        $this->pastikanPemilik($kelas);
        abort_unless($jadwal->kelas_id === $kelas->id, 404);

        $jadwal->delete();

        return back()->with('sukses', 'Jadwal sesi berhasil dihapus.');
    }

    private function pastikanPemilik(Kelas $kelas): void
    {
        abort_unless($kelas->chef_id === Auth::id(), 403, 'Anda hanya dapat mengelola kelas yang Anda ampu sendiri.');
    }

    private function validasi(Request $request, Kelas $kelas, ?Jadwal $jadwal = null): array
    {
        $menuUnik = Rule::unique('jadwal', 'menu_resep_id');
        if ($jadwal) {
            $menuUnik->ignore($jadwal->id);
        }

        return $request->validate([
            'menu_resep_id' => [
                'required',
                Rule::exists('menu_resep', 'id')->where('kelas_id', $kelas->id),
                $menuUnik,
            ],
            'tanggal' => 'required|date',
            'waktu' => 'required|date_format:H:i',
            'link_sesi' => 'nullable|url|max:500',
        ], [
            'menu_resep_id.required' => 'Menu untuk sesi ini wajib dipilih.',
            'menu_resep_id.exists' => 'Menu tidak ditemukan pada kelas ini.',
            'menu_resep_id.unique' => 'Menu ini sudah memiliki jadwal sesi.',
            'tanggal.required' => 'Tanggal sesi wajib diisi.',
            'waktu.required' => 'Jam mulai wajib diisi.',
            'waktu.date_format' => 'Format jam tidak valid (contoh: 14:00).',
            'link_sesi.url' => 'Link sesi harus berupa URL yang valid.',
        ]);
    }
}
