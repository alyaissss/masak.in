<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\Pembayaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotaPembayaranTest extends TestCase
{
    use RefreshDatabase;

    public function test_nota_is_shown_to_learner_and_admin_without_replacing_transfer_proof(): void
    {
        $chef = User::create([
            'nama' => 'Chef Uji',
            'email' => 'chef@example.test',
            'password' => 'password',
            'role' => User::ROLE_CHEF,
        ]);
        $learner = User::create([
            'nama' => 'Peserta Uji',
            'email' => 'learner@example.test',
            'password' => 'password',
            'role' => User::ROLE_LEARNER,
        ]);
        $admin = User::create([
            'nama' => 'Admin Uji',
            'email' => 'admin@example.test',
            'password' => 'password',
            'role' => User::ROLE_ADMIN,
        ]);
        $kelas = Kelas::create([
            'chef_id' => $chef->id,
            'nama_kelas' => 'Masakan Indonesia',
            'harga' => 125000,
            'status' => Kelas::STATUS_TERSEDIA,
        ]);
        $pembayaran = Pembayaran::create([
            'learner_id' => $learner->id,
            'kelas_id' => $kelas->id,
            'jumlah' => 125000,
            'metode' => 'bca',
            'no_referensi' => 'Pengirim Uji',
            'bukti' => 'bukti/transfer.jpg',
            'status' => Pembayaran::STATUS_MENUNGGU,
        ]);

        $this->actingAs($learner)
            ->get(route('learner.pembayaran.index'))
            ->assertOk()
            ->assertSee($pembayaran->nomorNota())
            ->assertSee('nota-pembayaran-'.$pembayaran->id)
            ->assertSee('bukan bukti transfer bank')
            ->assertSee('lihat-bukti-saya-button-'.$pembayaran->id)
            ->assertSee('img-bukti-saya-'.$pembayaran->id)
            ->assertSee('data-modal-open="modal-bukti-saya-'.$pembayaran->id.'"', false)
            ->assertDontSee('lihat-nota-saya-button-'.$pembayaran->id)
            ->assertSee('Transfer Bank BCA');

        $this->actingAs($admin)
            ->get(route('admin.pembayaran.index'))
            ->assertOk()
            ->assertSee($pembayaran->nomorNota())
            ->assertSee('nota-pembayaran-'.$pembayaran->id)
            ->assertSee('lihat-bukti-button-'.$pembayaran->id)
            ->assertSee('data-modal-open="modal-bukti-'.$pembayaran->id.'"', false)
            ->assertDontSee('lihat-nota-button-'.$pembayaran->id)
            ->assertSee('img-bukti-'.$pembayaran->id);
    }
}
