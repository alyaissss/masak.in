<?php

use App\Http\Controllers\Admin\ChefController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\KelasController as AdminKelas;
use App\Http\Controllers\Admin\LearnerController;
use App\Http\Controllers\Admin\PembayaranController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Chef\DashboardController as ChefDashboard;
use App\Http\Controllers\Chef\DiskusiController as ChefDiskusi;
use App\Http\Controllers\Chef\JadwalController;
use App\Http\Controllers\Chef\KelasController as ChefKelas;
use App\Http\Controllers\Chef\MenuResepController;
use App\Http\Controllers\Learner\DashboardController as LearnerDashboard;
use App\Http\Controllers\Learner\KelasController as LearnerKelas;
use App\Http\Controllers\Learner\KelasPageController;
use App\Http\Controllers\Learner\PembayaranController as LearnerPembayaran;
use Illuminate\Support\Facades\Route;
Route::get('/', fn () => redirect()->route('login'));

// ── Autentikasi (FR-01..FR-05) ────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.attempt');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ── Admin (FR-06..FR-18, FR-39..FR-43) ───────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [AdminDashboard::class, 'index'])->name('dashboard');
    Route::get('/dashboard', fn () => redirect()->route('admin.dashboard'));

    // Kelola Chef
    Route::get('/chef', [ChefController::class, 'index'])->name('chef.index');
    Route::get('/chef/create', [ChefController::class, 'create'])->name('chef.create');
    Route::post('/chef', [ChefController::class, 'store'])->name('chef.store');
    Route::get('/chef/{user}/edit', [ChefController::class, 'edit'])->name('chef.edit');
    Route::put('/chef/{user}', [ChefController::class, 'update'])->name('chef.update');
    Route::delete('/chef/{user}', [ChefController::class, 'destroy'])->name('chef.destroy');

    // Kelola Learner
    Route::get('/learner', [LearnerController::class, 'index'])->name('learner.index');
    Route::get('/learner/create', [LearnerController::class, 'create'])->name('learner.create');
    Route::post('/learner', [LearnerController::class, 'store'])->name('learner.store');
    Route::get('/learner/{user}/edit', [LearnerController::class, 'edit'])->name('learner.edit');
    Route::put('/learner/{user}', [LearnerController::class, 'update'])->name('learner.update');
    Route::delete('/learner/{user}', [LearnerController::class, 'destroy'])->name('learner.destroy');

    // Kelola Kelas
    Route::get('/kelas', [AdminKelas::class, 'index'])->name('kelas.index');
    Route::get('/kelas/create', [AdminKelas::class, 'create'])->name('kelas.create');
    Route::post('/kelas', [AdminKelas::class, 'store'])->name('kelas.store');
    Route::get('/kelas/{kelas}', [AdminKelas::class, 'show'])->name('kelas.show');
    Route::get('/kelas/{kelas}/edit', [AdminKelas::class, 'edit'])->name('kelas.edit');
    Route::put('/kelas/{kelas}', [AdminKelas::class, 'update'])->name('kelas.update');
    Route::delete('/kelas/{kelas}', [AdminKelas::class, 'destroy'])->name('kelas.destroy');

    // Konfirmasi pembayaran
    Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
    Route::post('/pembayaran/{pembayaran}/konfirmasi', [PembayaranController::class, 'konfirmasi'])->name('pembayaran.konfirmasi');
    Route::post('/pembayaran/{pembayaran}/tolak', [PembayaranController::class, 'tolak'])->name('pembayaran.tolak');
});

// ── Chef (FR-19..FR-34, FR-51..FR-55) ────────────────────────────────────────
Route::prefix('chef')->name('chef.')->middleware(['auth', 'role:chef'])->group(function () {
    Route::get('/', [ChefDashboard::class, 'index'])->name('dashboard');
    Route::get('/dashboard', fn () => redirect()->route('chef.dashboard'));

    Route::get('/kelas', [ChefKelas::class, 'index'])->name('kelas.index');
    Route::get('/kelas/create', [ChefKelas::class, 'create'])->name('kelas.create');
    Route::post('/kelas', [ChefKelas::class, 'store'])->name('kelas.store');
    Route::get('/kelas/{kelas}', [ChefKelas::class, 'show'])->name('kelas.show');
    Route::get('/kelas/{kelas}/edit', [ChefKelas::class, 'edit'])->name('kelas.edit');
    Route::put('/kelas/{kelas}', [ChefKelas::class, 'update'])->name('kelas.update');
    Route::delete('/kelas/{kelas}', [ChefKelas::class, 'destroy'])->name('kelas.destroy');
    Route::post('/kelas/{kelas}/status', [ChefKelas::class, 'setStatus'])->name('kelas.set-status');

    Route::post('/kelas/{kelas}/menu', [MenuResepController::class, 'store'])->name('menu.store');
    Route::put('/kelas/{kelas}/menu/{menu}', [MenuResepController::class, 'update'])->name('menu.update');
    Route::delete('/kelas/{kelas}/menu/{menu}', [MenuResepController::class, 'destroy'])->name('menu.destroy');

    Route::post('/kelas/{kelas}/jadwal', [JadwalController::class, 'store'])->name('jadwal.store');
    Route::put('/kelas/{kelas}/jadwal/{jadwal}', [JadwalController::class, 'update'])->name('jadwal.update');
    Route::delete('/kelas/{kelas}/jadwal/{jadwal}', [JadwalController::class, 'destroy'])->name('jadwal.destroy');

    Route::get('/diskusi', [ChefDiskusi::class, 'index'])->name('diskusi.index');
    Route::post('/kelas/{kelas}/diskusi', [ChefDiskusi::class, 'balas'])->name('diskusi.balas');
});

// ── Learner (FR-35..FR-55) ───────────────────────────────────────────────────
Route::prefix('learner')->name('learner.')->middleware(['auth', 'role:learner'])->group(function () {
    Route::get('/', [LearnerDashboard::class, 'index'])->name('dashboard');
    Route::get('/dashboard', fn () => redirect()->route('learner.dashboard'));
    Route::get('/pembayaran', [LearnerPembayaran::class, 'index'])->name('pembayaran.index');
});

// Katalog & alur kelas learner (prefix bebas /kelas agar mudah dibagikan).
Route::middleware(['auth', 'role:learner'])->group(function () {
    Route::get('/kelas', [LearnerKelas::class, 'index'])->name('kelas.index');
    Route::get('/kelas/{kelas}', [LearnerKelas::class, 'show'])->name('kelas.show');
    Route::get('/kelas/{kelas}/daftar', [LearnerPembayaran::class, 'create'])->name('kelas.daftar');
    Route::post('/kelas/{kelas}/daftar', [LearnerPembayaran::class, 'store'])->name('kelas.daftar.simpan');

    Route::get('/kelas-saya/{kelas}', [KelasPageController::class, 'show'])->name('kelas.page');
    Route::post('/kelas-saya/{kelas}/diskusi', [KelasPageController::class, 'tanya'])->name('kelas.tanya');
    Route::post('/kelas-saya/{kelas}/rating', [\App\Http\Controllers\Learner\RatingController::class, 'store'])->name('kelas.rating');
});
