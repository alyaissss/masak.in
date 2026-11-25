<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Pembayaran;
use App\Models\Rating;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Learner memberi / mengubah rating bintang 1–5 untuk kelas yang sudah selesai diikutinya.
class RatingController extends Controller
{
    public function store(Request $request, Kelas $kelas): RedirectResponse
    {
        $terdaftar = Pembayaran::where('learner_id', Auth::id())
            ->where('kelas_id', $kelas->id)
            ->where('status', Pembayaran::STATUS_BERHASIL)
            ->exists();
        abort_unless($terdaftar, 403, 'Anda hanya dapat menilai kelas yang Anda ikuti.');

        if ($kelas->status !== Kelas::STATUS_SELESAI) {
            return back()->with('galat', 'Rating dapat diberikan setelah kelas ditandai selesai oleh chef.');
        }

        $data = $request->validate([
            'bintang' => 'required|integer|between:1,5',
        ], [
            'bintang.required' => 'Pilih jumlah bintang terlebih dahulu.',
            'bintang.between' => 'Rating harus antara 1 sampai 5 bintang.',
        ]);

        Rating::updateOrCreate(
            ['kelas_id' => $kelas->id, 'learner_id' => Auth::id()],
            ['bintang' => (int) $data['bintang']],
        );

        return back()->with('sukses', "Terima kasih! Rating {$data['bintang']} bintang untuk kelas \"{$kelas->nama_kelas}\" tersimpan.");
    }
}
