<?php

namespace Tests\Feature;

use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\MenuResep;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class KelasCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_class_without_a_session_date_or_time(): void
    {
        $admin = User::create([
            'nama' => 'Admin Uji',
            'email' => 'admin@example.test',
            'password' => 'password',
            'role' => User::ROLE_ADMIN,
        ]);
        $chef = User::create([
            'nama' => 'Chef Uji',
            'email' => 'chef@example.test',
            'password' => 'password',
            'role' => User::ROLE_CHEF,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.kelas.create'))
            ->assertOk()
            ->assertDontSee('tanggal_mulai')
            ->assertDontSee('name="waktu"', false);

        $this->actingAs($admin)
            ->post(route('admin.kelas.store'), [
                'nama_kelas' => 'Kelas Admin Uji',
                'chef_id' => $chef->id,
                'status' => Kelas::STATUS_DRAFT,
                'harga' => 100000,
            ])
            ->assertRedirect(route('admin.kelas.index'));

        $kelas = Kelas::where('nama_kelas', 'Kelas Admin Uji')->firstOrFail();

        $this->assertSame(0, $kelas->jadwal()->count());
    }

    public function test_chef_creates_schedules_with_dates_and_times_per_menu(): void
    {
        Storage::fake('public');
        $chef = User::create([
            'nama' => 'Chef Uji',
            'email' => 'chef@example.test',
            'password' => 'password',
            'role' => User::ROLE_CHEF,
        ]);

        $this->actingAs($chef)
            ->get(route('chef.kelas.create'))
            ->assertOk()
            ->assertDontSee('tanggal_mulai')
            ->assertDontSee('name="waktu"', false);

        $response = $this->actingAs($chef)->post(route('chef.kelas.store'), [
            'nama_kelas' => 'Kelas Uji',
            'harga' => 100000,
            'foto' => UploadedFile::fake()->image('sampul.jpg'),
        ]);
        $response->assertRedirect();

        $kelas = Kelas::where('nama_kelas', 'Kelas Uji')->firstOrFail();
        $this->assertSame(0, $kelas->jadwal()->count());
        $this->assertSame(1, Kelas::where('foto', $kelas->foto)->count());
        $this->assertSame(asset('storage/'.$kelas->foto), $kelas->foto_url);
        Storage::disk('public')->assertExists($kelas->foto);

        $menu = MenuResep::create([
            'kelas_id' => $kelas->id,
            'nama_menu' => 'Rendang Uji',
        ]);
        $this->actingAs($chef)->get(route('chef.kelas.show', ['kelas' => $kelas, 'tab' => 'jadwal']))
            ->assertOk()
            ->assertSee('chef-jadwal-menu-select');

        $this->actingAs($chef)->post(route('chef.jadwal.store', $kelas), [
            'menu_resep_id' => $menu->id,
            'tanggal' => '2026-12-10',
            'waktu' => '14:30',
            'link_sesi' => 'https://zoom.us/j/123',
        ])->assertRedirect();

        $jadwal = Jadwal::where('kelas_id', $kelas->id)->firstOrFail();
        $jadwal->refresh();
        $this->assertSame($menu->id, $jadwal->menu_resep_id);
        $this->assertSame('https://zoom.us/j/123', $jadwal->link_sesi);
        $this->assertSame(1, $kelas->jadwal()->count());

        $this->actingAs($chef)->post(route('chef.jadwal.store', $kelas), [
            'menu_resep_id' => $menu->id,
            'tanggal' => '2026-12-11',
            'waktu' => '14:30',
        ])->assertSessionHasErrors('menu_resep_id');

        $this->actingAs($chef)->get(route('chef.kelas.show', ['kelas' => $kelas, 'tab' => 'jadwal']))
            ->assertOk()
            ->assertSee('Rendang Uji');

        $kelas->menuResep()->create(['nama_menu' => 'Sambal Uji']);
        $this->actingAs($chef)
            ->get(route('chef.kelas.index'))
            ->assertOk()
            ->assertSee($kelas->foto_url)
            ->assertSee('2 menu')
            ->assertDontSee('<strong>Menu:</strong> Rendang Uji', false);
    }
}
