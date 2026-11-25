<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

// FR-10..FR-13: admin mengelola data Learner (lihat, tambah, edit, hapus).
class LearnerController extends Controller
{
    private array $pesan = [
        'nama.required' => 'Nama wajib diisi.',
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'email.unique' => 'Email sudah terdaftar.',
        'password.required' => 'Kata sandi wajib diisi.',
        'password.min' => 'Kata sandi minimal 6 karakter.',
    ];

    public function index(): View
    {
        $learner = User::where('role', User::ROLE_LEARNER)
            ->withCount(['pembayaran' => fn ($q) => $q->where('status', Pembayaran::STATUS_BERHASIL)])
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        return view('admin.learner.index', compact('learner'));
    }

    public function create(): View
    {
        return view('admin.learner.form', ['user' => new User(['role' => User::ROLE_LEARNER])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'telepon' => 'nullable|string|max:25',
        ], $this->pesan);

        User::create($data + ['role' => User::ROLE_LEARNER]);

        return redirect()->route('admin.learner.index')->with('sukses', 'Data learner berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        abort_unless($user->isLearner(), 404);

        return view('admin.learner.form', ['user' => $user]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->isLearner(), 404);

        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'nullable|string|min:6',
            'telepon' => 'nullable|string|max:25',
        ], $this->pesan);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('admin.learner.index')->with('sukses', 'Data learner berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_unless($user->isLearner(), 404);

        if ($user->pembayaran()->exists()) {
            return back()->with('galat', 'Learner masih memiliki riwayat pembayaran dan tidak dapat dihapus agar data keuangan tetap utuh.');
        }

        $user->delete();

        return redirect()->route('admin.learner.index')->with('sukses', 'Data learner berhasil dihapus.');
    }
}
