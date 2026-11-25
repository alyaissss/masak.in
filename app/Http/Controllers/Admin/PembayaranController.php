<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

// FR-39..FR-43: verifikasi manual pembayaran (tanpa payment gateway).
// Access ke kelas hanya dibuka setelah status "berhasil" (NFR-21).
class PembayaranController extends Controller
{
    public function index(Request $request): View
    {
        $status = (string) $request->query('status', Pembayaran::STATUS_MENUNGGU);
        if (! in_array($status, ['semua', Pembayaran::STATUS_MENUNGGU, Pembayaran::STATUS_BERHASIL, Pembayaran::STATUS_GAGAL], true)) {
            $status = Pembayaran::STATUS_MENUNGGU;
        }

        $pembayaran = Pembayaran::with(['learner', 'kelas.chef'])
            ->when($status !== 'semua', fn ($q) => $q->where('status', $status))
            ->orderByRaw("CASE WHEN status = 'menunggu' THEN 0 ELSE 1 END")
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $jumlahMenunggu = Pembayaran::where('status', Pembayaran::STATUS_MENUNGGU)->count();

        return view('admin.pembayaran.index', compact('pembayaran', 'status', 'jumlahMenunggu'));
    }

    public function konfirmasi(Request $request, Pembayaran $pembayaran): RedirectResponse
    {
        if ($pembayaran->status !== Pembayaran::STATUS_MENUNGGU) {
            return back()->with('galat', 'Pembayaran ini sudah diproses sebelumnya.');
        }

        $pembayaran->update([
            'status' => Pembayaran::STATUS_BERHASIL,
            'tanggal_bayar' => now(),
        ]);

        return back()->with('sukses', "Pembayaran {$pembayaran->learner->nama} untuk kelas \"{$pembayaran->kelas->nama_kelas}\" dikonfirmasi berhasil.");
    }

    public function tolak(Request $request, Pembayaran $pembayaran): RedirectResponse
    {
        if ($pembayaran->status !== Pembayaran::STATUS_MENUNGGU) {
            return back()->with('galat', 'Pembayaran ini sudah diproses sebelumnya.');
        }

        $pembayaran->update([
            'status' => Pembayaran::STATUS_GAGAL,
            'tanggal_bayar' => now(),
        ]);

        return back()->with('sukses', "Pembayaran {$pembayaran->learner->nama} ditandai gagal. Learner dapat mendaftar ulang.");
    }
}
