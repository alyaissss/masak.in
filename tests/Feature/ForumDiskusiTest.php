<?php

namespace Tests\Feature;

use App\Models\Diskusi;
use App\Models\Kelas;
use App\Models\Pembayaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ForumDiskusiTest extends TestCase
{
    use RefreshDatabase;

    public function test_chef_forum_is_chronological_and_reply_returns_to_discussion_tab(): void
    {
        $chef = $this->buatPengguna('Chef Uji', 'chef@example.test', User::ROLE_CHEF);
        $learner = $this->buatPengguna('Learner Uji', 'learner@example.test', User::ROLE_LEARNER);
        $kelas = $this->buatKelas($chef);
        $lama = $this->buatPertanyaan($kelas, $learner, 'Pertanyaan lama');
        $baru = $this->buatPertanyaan($kelas, $learner, 'Pertanyaan baru');
        $this->aturWaktu($lama, '2026-11-17 10:00:00');
        $this->aturWaktu($baru, '2026-11-17 11:00:00');

        $this->actingAs($chef)
            ->get(route('chef.kelas.show', ['kelas' => $kelas, 'tab' => 'diskusi']))
            ->assertOk()
            ->assertSeeInOrder(['Pertanyaan lama', 'Pertanyaan baru'])
            ->assertSee('<button type="button" class="aktif" data-tab="diskusi"', false);

        $this->actingAs($chef)
            ->post(route('chef.diskusi.balas', $kelas), [
                'parent_id' => $lama->id,
                'pesan' => 'Balasan chef',
                'kembali_ke' => 'kelas',
            ])
            ->assertRedirect(route('chef.kelas.show', ['kelas' => $kelas, 'tab' => 'diskusi']));
    }

    public function test_learner_question_returns_to_discussion_tab(): void
    {
        $chef = $this->buatPengguna('Chef Uji', 'chef-learner@example.test', User::ROLE_CHEF);
        $learner = $this->buatPengguna('Learner Uji', 'learner-forum@example.test', User::ROLE_LEARNER);
        $kelas = $this->buatKelas($chef);
        Pembayaran::create([
            'learner_id' => $learner->id,
            'kelas_id' => $kelas->id,
            'jumlah' => 100000,
            'status' => Pembayaran::STATUS_BERHASIL,
        ]);

        $this->actingAs($learner)
            ->post(route('kelas.tanya', $kelas), ['pesan' => 'Pertanyaan dari learner'])
            ->assertRedirect(route('kelas.page', ['kelas' => $kelas, 'tab' => 'diskusi']));

        $this->actingAs($learner)
            ->get(route('kelas.page', ['kelas' => $kelas, 'tab' => 'diskusi']))
            ->assertOk()
            ->assertSee('Pertanyaan dari learner')
            ->assertSee('<button type="button" class="aktif" data-tab="diskusi"', false);
    }

    private function buatPengguna(string $nama, string $email, string $role): User
    {
        return User::create([
            'nama' => $nama,
            'email' => $email,
            'password' => 'password',
            'role' => $role,
        ]);
    }

    private function buatKelas(User $chef): Kelas
    {
        return Kelas::create([
            'chef_id' => $chef->id,
            'nama_kelas' => 'Kelas Uji',
            'status' => Kelas::STATUS_DRAFT,
            'harga' => 100000,
        ]);
    }

    private function buatPertanyaan(Kelas $kelas, User $learner, string $pesan): Diskusi
    {
        return Diskusi::create([
            'kelas_id' => $kelas->id,
            'user_id' => $learner->id,
            'pesan' => $pesan,
        ]);
    }

    private function aturWaktu(Diskusi $diskusi, string $waktu): void
    {
        DB::table('diskusi')->where('id', $diskusi->id)->update(['created_at' => $waktu]);
    }
}
