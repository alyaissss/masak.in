<?php

namespace App\Http\Controllers\Chef;

use App\Http\Controllers\Controller;
use App\Models\Diskusi;
use App\Models\Kelas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

// FR-51..FR-54: chef melihat pertanyaan learner dan membalas diskusi per kelas.
class DiskusiController extends Controller
{
    public function index(): View
    {
        $kelasIds = Auth::user()->kelasDiajar()->pluck('id');

        $pertanyaan = Diskusi::with(['kelas', 'user', 'balasan.user'])
            ->whereIn('kelas_id', $kelasIds)
            ->whereNull('parent_id')
            ->orderBy('created_at')
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        return view('chef.diskusi', compact('pertanyaan'));
    }

    public function balas(Request $request, Kelas $kelas): RedirectResponse
    {
        abort_unless($kelas->chef_id === Auth::id(), 403, 'Anda hanya dapat membalas diskusi kelas Anda sendiri.');

        $data = $request->validate([
            'parent_id' => ['required', Rule::exists('diskusi', 'id')->where('kelas_id', $kelas->id)],
            'pesan' => 'required|string|max:2000',
        ], [
            'pesan.required' => 'Balasan tidak boleh kosong.',
        ]);

        Diskusi::create([
            'kelas_id' => $kelas->id,
            'user_id' => Auth::id(),
            'parent_id' => $data['parent_id'],
            'pesan' => $data['pesan'],
        ]);

        $redirect = $request->input('kembali_ke') === 'kelas'
            ? redirect()->route('chef.kelas.show', ['kelas' => $kelas, 'tab' => 'diskusi'])
            : back();

        return $redirect->with('sukses', 'Balasan berhasil dikirim.');
    }
}
