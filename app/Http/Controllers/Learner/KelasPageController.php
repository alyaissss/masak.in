<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\Diskusi;
use App\Models\Kelas;
use App\Models\Pembayaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

// FR-44..FR-55: ruang belajar — hanya untuk pembayaran berstatus "berhasil" (NFR-21).
// Berisi menu & resep, jadwal + link sesi, dan forum diskusi.
class KelasPageController extends Controller
{
    public function show(Kelas $kelas): View|RedirectResponse
    {
        $pembayaran = Pembayaran::where('learner_id', Auth::id())->where('kelas_id', $kelas->id)->first();

        if (! $pembayaran || $pembayaran->status !== Pembayaran::STATUS_BERHASIL) {
            return redirect()->route('learner.pembayaran.index')
                ->with('galat', 'Ruang belajar dapat diakses setelah pembayaran Anda dikonfirmasi berhasil.');
        }

        $kelas->load(['chef', 'menuResep', 'jadwal.menuResep']);
        $pertanyaan = $kelas->diskusi()->whereNull('parent_id')->with(['user', 'balasan.user'])
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();
        $ratingSaya = $kelas->rating()->where('learner_id', Auth::id())->value('bintang');

        return view('learner.ruang-belajar', compact('kelas', 'pembayaran', 'pertanyaan', 'ratingSaya'));
    }

    public function tanya(Request $request, Kelas $kelas): RedirectResponse
    {
        $pembayaran = Pembayaran::where('learner_id', Auth::id())->where('kelas_id', $kelas->id)->first();
        abort_unless($pembayaran && $pembayaran->status === Pembayaran::STATUS_BERHASIL, 403, 'Anda belum terdaftar pada kelas ini.');

        $data = $request->validate([
            'pesan' => 'required|string|max:2000',
        ], [
            'pesan.required' => 'Pertanyaan tidak boleh kosong.',
        ]);

        Diskusi::create([
            'kelas_id' => $kelas->id,
            'user_id' => Auth::id(),
            'pesan' => $data['pesan'],
        ]);

        return redirect()->route('kelas.page', ['kelas' => $kelas, 'tab' => 'diskusi'])
            ->with('sukses', 'Pertanyaan terkirim. Chef akan segera membalas.');
    }
}
