<?php

namespace App\Http\Controllers\Learner;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Pembayaran;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

// FR-39..FR-43: learner melakukan pembayaran (tanpa gateway — transfer + bukti, verifikasi manual admin).
class PembayaranController extends Controller
{
    private const METODE = [
        'bca' => 'Transfer Bank BCA',
        'mandiri' => 'Transfer Bank Mandiri',
        'qris' => 'QRIS',
    ];

    public static function daftarMetode(): array
    {
        return self::METODE;
    }

    public function index(): View
    {
        $pembayaran = Pembayaran::with('learner', 'kelas.chef', 'kelas.jadwal')
            ->where('learner_id', Auth::id())
            ->latest()
            ->get();

        return view('learner.pembayaran.index', compact('pembayaran'));
    }

    public function create(Kelas $kelas): View|RedirectResponse
    {
        abort_unless($kelas->status === Kelas::STATUS_TERSEDIA, 404, 'Kelas tidak tersedia.');

        $sudah = Pembayaran::where('learner_id', Auth::id())->where('kelas_id', $kelas->id)->first();
        if ($sudah) {
            return redirect()->route('learner.pembayaran.index')
                ->with('galat', 'Anda sudah mendaftar pada kelas ini. Periksa status pembayaran Anda.');
        }

        return view('learner.pembayaran.form', ['kelas' => $kelas->load('chef', 'jadwal'), 'metode' => self::METODE]);
    }

    public function store(Request $request, Kelas $kelas): RedirectResponse
    {
        abort_unless($kelas->status === Kelas::STATUS_TERSEDIA, 404, 'Kelas tidak tersedia.');

        $sudah = Pembayaran::where('learner_id', Auth::id())->where('kelas_id', $kelas->id)->exists();
        if ($sudah) {
            return redirect()->route('learner.pembayaran.index')->with('galat', 'Anda sudah mendaftar pada kelas ini.');
        }

        $data = $request->validate([
            'metode' => ['required', Rule::in(array_keys(self::METODE))],
            'no_referensi' => 'required|string|max:60',
            // NFR-05 / NFR-11: bukti divalidasi tipe & ukuran (maks 2 MB).
            'bukti' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ], [
            'metode.required' => 'Metode pembayaran wajib dipilih.',
            'no_referensi.required' => 'Nomor referensi / nama pengirim wajib diisi.',
            'bukti.required' => 'Bukti transfer wajib diunggah.',
            'bukti.mimes' => 'Bukti transfer harus berupa berkas JPG, PNG, atau PDF.',
            'bukti.max' => 'Ukuran bukti transfer maksimal 2 MB.',
        ]);

        Pembayaran::create([
            'learner_id' => Auth::id(),
            'kelas_id' => $kelas->id,
            'jumlah' => $kelas->harga,
            'metode' => $data['metode'],
            'no_referensi' => $data['no_referensi'],
            'bukti' => $request->file('bukti')->store('bukti', 'public'),
            'status' => Pembayaran::STATUS_MENUNGGU,
        ]);

        return redirect()->route('learner.pembayaran.index')
            ->with('sukses', 'Pembayaran terkirim. Ruang belajar terbuka setelah admin mengonfirmasi pembayaran Anda.');
    }
}
