<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use App\Models\Diskusi;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $userId = Auth::id();

        $kelasSaya = Kelas::where('chef_id', $userId)
            ->withCount(['pembayaran', 'menuResep', 'jadwal'])
            ->latest()
            ->get();

        $stats = [
            'kelas_total' => $kelasSaya->count(),
            'kelas_tersedia' => $kelasSaya->where('status', Kelas::STATUS_TERSEDIA)->count(),
            'peserta' => Pembayaran::whereHas('kelas', fn ($q) => $q->where('chef_id', $userId))
                ->where('status', Pembayaran::STATUS_BERHASIL)->count(),
            'pendapatan' => (float) Pembayaran::whereHas('kelas', fn ($q) => $q->where('chef_id', $userId))
                ->where('status', Pembayaran::STATUS_BERHASIL)->sum('jumlah'),
            'rating' => \App\Models\Rating::ringkasanChef()->get($userId),
        ];

        $jadwalHariIni = Jadwal::whereHas('kelas', fn ($q) => $q->where('chef_id', $userId))
            ->whereDate('tanggal', today())
            ->with('kelas')
            ->orderBy('waktu')
            ->get();

        $pertanyaanTerbaru = Diskusi::whereHas('kelas', fn ($q) => $q->where('chef_id', $userId))
            ->whereNull('parent_id')
            ->with(['kelas', 'user', 'balasan.user'])
            ->latest()
            ->take(4)
            ->get();

        return view('chef.dashboard', compact('stats', 'kelasSaya', 'jadwalHariIni', 'pertanyaanTerbaru'));
    }
}
