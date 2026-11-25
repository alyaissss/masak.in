<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Pembayaran;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

// FR-35..FR-38: learner melihat kelas tersedia, detail, dan memilih kelas.
class KelasController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $kelas = Kelas::where('status', Kelas::STATUS_TERSEDIA)
            ->with(['chef', 'menuResep', 'jadwal.menuResep'])
            ->when($q !== '', fn ($query) => $query->where(function ($w) use ($q) {
                $w->where('nama_kelas', 'like', "%{$q}%")
                    ->orWhereHas('chef', fn ($c) => $c->where('nama', 'like', "%{$q}%"))
                    ->orWhereHas('menuResep', fn ($m) => $m->where('nama_menu', 'like', "%{$q}%"));
            }))
            ->orderBy('tanggal_mulai')
            ->orderBy('id')
            ->get();

        $statusSaya = Pembayaran::where('learner_id', Auth::id())->pluck('status', 'kelas_id');

        // Rata-rata bintang per chef + 3 chef favorit (rating tertinggi, lalu terbanyak).
        $ratingChef = Rating::ringkasanChef();
        $chefFavorit = User::whereIn('id', $ratingChef->keys())
            ->get()
            ->map(fn ($c) => tap($c, fn ($c) => $c->ringkasan = $ratingChef[$c->id]))
            ->sortBy([fn ($a, $b) => $b->ringkasan->rata <=> $a->ringkasan->rata, fn ($a, $b) => $b->ringkasan->jumlah <=> $a->ringkasan->jumlah])
            ->take(3)
            ->values();

        return view('learner.kelas.index', compact('kelas', 'q', 'statusSaya', 'ratingChef', 'chefFavorit'));
    }

    public function show(Kelas $kelas): View
    {
        if ($kelas->status !== Kelas::STATUS_TERSEDIA) {
            // Kelas non-tersedia hanya dapat dilihat oleh yang sudah mendaftar.
            $terdaftar = Pembayaran::where('learner_id', Auth::id())->where('kelas_id', $kelas->id)->exists();
            abort_unless($terdaftar, 404, 'Kelas tidak ditemukan atau sudah tidak tersedia.');
        }

        $kelas->load(['chef', 'menuResep', 'jadwal.menuResep']);
        $pembayaran = Pembayaran::where('learner_id', Auth::id())->where('kelas_id', $kelas->id)->first();

        return view('learner.kelas.show', compact('kelas', 'pembayaran'));
    }
}
