<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

// FR-06..FR-09: admin mengelola data Chef (lihat, tambah, edit, hapus).
class ChefController extends Controller
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
        $chef = User::where('role', User::ROLE_CHEF)
            ->withCount('kelasDiajar')
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        return view('admin.chef.index', compact('chef'));
    }

    public function create(): View
    {
        return view('admin.chef.form', ['user' => new User(['role' => User::ROLE_CHEF])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'telepon' => 'nullable|string|max:25',
            'spesialisasi' => 'nullable|string|max:255',
        ], $this->pesan);

        User::create($data + ['role' => User::ROLE_CHEF]); // password ter-hash otomatis (cast 'hashed')

        return redirect()->route('admin.chef.index')->with('sukses', 'Data chef berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        abort_unless($user->isChef(), 404);

        return view('admin.chef.form', ['user' => $user]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->isChef(), 404);

        $data = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'nullable|string|min:6',
            'telepon' => 'nullable|string|max:25',
            'spesialisasi' => 'nullable|string|max:255',
        ], $this->pesan);

        if (empty($data['password'])) {
            unset($data['password']); // biarkan kata sandi lama bila kosong
        }

        $user->update($data);

        return redirect()->route('admin.chef.index')->with('sukses', 'Data chef berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_unless($user->isChef(), 404);

        if ($user->kelasDiajar()->exists()) {
            return back()->with('galat', 'Chef masih memiliki kelas yang diampu. Pindahkan atau hapus kelasnya terlebih dahulu.');
        }

        $user->delete();

        return redirect()->route('admin.chef.index')->with('sukses', 'Data chef berhasil dihapus.');
    }
}
