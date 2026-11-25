<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Pembayaran;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_chef' => User::where('role', User::ROLE_CHEF)->count(),
            'total_learner' => User::where('role', User::ROLE_LEARNER)->count(),
            'kelas_tersedia' => Kelas::where('status', Kelas::STATUS_TERSEDIA)->count(),
            'pembayaran_menunggu' => Pembayaran::where('status', Pembayaran::STATUS_MENUNGGU)->count(),
            'pendapatan' => (float) Pembayaran::where('status', Pembayaran::STATUS_BERHASIL)->sum('jumlah'),
        ];

        $pembayaranMenunggu = Pembayaran::with(['learner', 'kelas'])
            ->where('status', Pembayaran::STATUS_MENUNGGU)
            ->latest()
            ->take(5)
            ->get();

        $kelasTerbaru = Kelas::with('chef')->withCount('pembayaran')->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'pembayaranMenunggu', 'kelasTerbaru'));
    }
}
