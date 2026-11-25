<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Pembayaran;
use App\Models\Rating;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $userId = Auth::id();

        $pembayaranSaya = Pembayaran::with('kelas.chef', 'kelas.menuResep', 'kelas.jadwal.menuResep')
            ->where('learner_id', $userId)
            ->latest()
            ->get();

        $kelasAktif = $pembayaranSaya->where('status', Pembayaran::STATUS_BERHASIL);

        $stats = [
            'kelas_aktif' => $kelasAktif->count(),
            'menunggu_verifikasi' => $pembayaranSaya->where('status', Pembayaran::STATUS_MENUNGGU)->count(),
            'total_bayar' => (float) $kelasAktif->sum('jumlah'),
        ];

        // Jadwal sesi mendatang dari kelas yang diikuti (status berhasil).
        $jadwalMendatang = $kelasAktif
            ->flatMap(fn ($p) => $p->kelas->jadwal)
            ->filter(fn ($j) => $j->tanggal->isSameDay(today()) || $j->tanggal->isFuture())
            ->sortBy([['tanggal', 'asc'], ['waktu', 'asc']])
            ->take(5);

        $rekomendasi = Kelas::where('status', Kelas::STATUS_TERSEDIA)
            ->whereNotIn('id', $pembayaranSaya->pluck('kelas_id'))
            ->with('chef', 'menuResep')
            ->latest()
            ->take(3)
            ->get();

        return view('learner.dashboard', [
            'ratingSaya' => Rating::where('learner_id', $userId)->pluck('bintang', 'kelas_id'),
            'stats' => $stats,
            'kelasAktif' => $kelasAktif,
            'pembayaranSaya' => $pembayaranSaya,
            'jadwalMendatang' => $jadwalMendatang,
            'rekomendasi' => $rekomendasi,
        ]);
    }
}
