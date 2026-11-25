<?php

namespace Database\Seeders;

use App\Models\Diskusi;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\MenuResep;
use App\Models\Pembayaran;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // Data awal (idempoten) sesuai SRS: "data awal diinput oleh Admin".
    public function run(): void
    {
        if (User::query()->exists()) {
            $this->seedRating();
            $this->command?->info('Seed pengguna dilewati — data sudah ada (rating contoh dipastikan ada).');

            return;
        }

        // ── Pengguna ──────────────────────────────────────────────
        $admin = User::create([
            'nama' => 'Rina Kartika',
            'email' => 'admin@masak.id',
            'password' => 'masak123',
            'role' => User::ROLE_ADMIN,
        ]);

        $dewi = User::create([
            'nama' => 'Dewi Anggraini',
            'email' => 'chef.dewi@masak.id',
            'password' => 'chef123',
            'role' => User::ROLE_CHEF,
            'telepon' => '0812 3456 7801',
            'spesialisasi' => 'Masakan Nusantara',
        ]);

        $raka = User::create([
            'nama' => 'Raka Pratama',
            'email' => 'chef.raka@masak.id',
            'password' => 'chef123',
            'role' => User::ROLE_CHEF,
            'telepon' => '0812 3456 7802',
            'spesialisasi' => 'Patiseri & Kue Barat',
        ]);

        $budi = User::create([
            'nama' => 'Budi Santoso',
            'email' => 'learner.budi@masak.id',
            'password' => 'learner123',
            'role' => User::ROLE_LEARNER,
            'telepon' => '0813 2211 3301',
        ]);

        $sari = User::create([
            'nama' => 'Sari Melati',
            'email' => 'learner.sari@masak.id',
            'password' => 'learner123',
            'role' => User::ROLE_LEARNER,
            'telepon' => '0813 2211 3302',
        ]);

        $andra = User::create([
            'nama' => 'Andra Wijaya',
            'email' => 'learner.andra@masak.id',
            'password' => 'learner123',
            'role' => User::ROLE_LEARNER,
            'telepon' => '0813 2211 3303',
        ]);

        // ── Kelas 1: Rendang (tersedia) ───────────────────────────
        $rendang = Kelas::create([
            'chef_id' => $dewi->id,
            'nama_kelas' => 'Rendang Wagyu Modern Masterclass',
            'deskripsi' => 'Teknik memasak rendang wagyu dengan kontrol suhu rendah, rempah yang digiling segar, dan plating modern untuk sajian restoran.',
            'status' => Kelas::STATUS_TERSEDIA,
            'tanggal_mulai' => today()->addDays(2),
            'tanggal_selesai' => today()->addDays(9),
            'harga' => 750000,
            'foto' => 'https://images.unsplash.com/photo-1663530761401-15eefb544889?crop=entropy&cs=srgb&fm=jpg&q=85',
        ]);

        $menuRendang = MenuResep::create([
            'kelas_id' => $rendang->id,
            'nama_menu' => 'Rendang Wagyu Kering Control',
            'deskripsi' => 'Rendang wagyu dengan bumbu dimasak panas rendah selama 6 jam.',
            'resep' => "Bahan:\n- 800 g wagyu short rib\n- 10 buah cabai merah keriting\n- 5 bawang merah, 4 siung bawang putih\n- 2 cm lengkuas, 2 batang serai\n- 500 ml santan kental\n\nLangkah:\n1. Giling rempah halus tanpa air.\n2. Sangrai rempah dengan minyak hingga wangi.\n3. Masukkan daging, tuang santan, masak 6 jam api kecil.\n4. Masak hingga kering berminyak, diamkan sebelum disajikan.",
        ]);
        $menuSambal = MenuResep::create([
            'kelas_id' => $rendang->id,
            'nama_menu' => 'Sambal Matah & Nasi Uduk Komplit',
            'deskripsi' => 'Pendamping klasik rendang dengan nasi gurih wangi serai.',
            'resep' => "Bahan:\n- 2 batang serai, 5 daun jeruk\n- 400 ml santan, 300 g beras\n- Bawang merah, cabai rawit, jeruk limau (sambal matah)\n\nLangkah:\n1. Masak nasi uduk dengan santan & rempah hingga matang.\n2. Iris semua bahan sambal matah, tuang minyak panas.\n3. Sajikan bersama rendang.",
        ]);

        $jadwalRendang1 = Jadwal::create([
            'kelas_id' => $rendang->id,
            'menu_resep_id' => $menuRendang->id,
            'tanggal' => today()->addDays(2),
            'waktu' => '14:00',
            'link_sesi' => 'https://meet.google.com/masak-rendang-sesi1',
        ]);
        Jadwal::create([
            'kelas_id' => $rendang->id,
            'menu_resep_id' => $menuSambal->id,
            'tanggal' => today()->addDays(5),
            'waktu' => '14:00',
            'link_sesi' => 'https://meet.google.com/masak-rendang-sesi2',
        ]);

        Pembayaran::create([
            'learner_id' => $budi->id,
            'kelas_id' => $rendang->id,
            'jumlah' => 750000,
            'metode' => 'bca',
            'no_referensi' => 'TRF-20260201-001',
            'bukti' => null,
            'status' => Pembayaran::STATUS_BERHASIL,
            'tanggal_bayar' => now()->subDays(2),
        ]);
        Pembayaran::create([
            'learner_id' => $andra->id,
            'kelas_id' => $rendang->id,
            'jumlah' => 750000,
            'metode' => 'mandiri',
            'no_referensi' => 'TRF-20260201-002',
            'bukti' => null,
            'status' => Pembayaran::STATUS_BERHASIL,
            'tanggal_bayar' => now()->subDay(),
        ]);

        // ── Kelas 2: Sourdough (tersedia, ada pembayaran menunggu) ─
        $sourdough = Kelas::create([
            'chef_id' => $raka->id,
            'nama_kelas' => 'Artisan Sourdough & Viennoiserie',
            'deskripsi' => 'Fermentasi alami sourdough starter, laminasi mentega croissant, dan teknik proofing dingin.',
            'status' => Kelas::STATUS_TERSEDIA,
            'tanggal_mulai' => today()->addDays(3),
            'tanggal_selesai' => today()->addDays(12),
            'harga' => 950000,
            'foto' => 'https://images.unsplash.com/photo-1630507103234-00d3e621cae2?crop=entropy&cs=srgb&fm=jpg&q=85',
        ]);
        $menuSourdough = MenuResep::create([
            'kelas_id' => $sourdough->id,
            'nama_menu' => 'Sourdough Klasik 78% Hidrasi',
            'deskripsi' => 'Roti sourdough kerak tebal, crumb lembap.',
            'resep' => "Bahan:\n- 500 g tepung tinggi protein\n- 390 g air\n- 100 g levain matang\n- 10 g garam\n\nLangkah:\n1. Autolyse 1 jam.\n2. 4x lipat stretch & fold tiap 30 menit.\n3. Bulk 4 jam, shape, cold proof 12 jam.\n4. Panggang 230°C dengan uap 20 menit.",
        ]);
        Jadwal::create([
            'kelas_id' => $sourdough->id,
            'menu_resep_id' => $menuSourdough->id,
            'tanggal' => today()->addDays(3),
            'waktu' => '09:00',
            'link_sesi' => 'https://zoom.us/j/masak-sourdough-1',
        ]);

        Pembayaran::create([
            'learner_id' => $sari->id,
            'kelas_id' => $sourdough->id,
            'jumlah' => 950000,
            'metode' => 'qris',
            'no_referensi' => 'QRIS-20260213-018',
            'bukti' => null,
            'status' => Pembayaran::STATUS_MENUNGGU,
        ]);

        // ── Kelas 3: Sauce Perancis (draft) ───────────────────────
        $sauce = Kelas::create([
            'chef_id' => $raka->id,
            'nama_kelas' => 'French Classical Sauce Making',
            'deskripsi' => 'Fond cokelat, sauce béarnaise, dan jus reduction untuk sajian bistro klasik.',
            'status' => Kelas::STATUS_DRAFT,
            'tanggal_mulai' => today()->addDays(14),
            'tanggal_selesai' => null,
            'harga' => 600000,
            'foto' => null,
        ]);
        MenuResep::create([
            'kelas_id' => $sauce->id,
            'nama_menu' => 'Sauce Béarnaise & Jus Bordeaux',
            'deskripsi' => 'Dua saus induk untuk steak & panggang.',
            'resep' => "Bahan:\n- 4 kuning telur, 200 g mentega klarifikasi\n- Estragon, shallot, cuka putih\n- 500 ml bordeaux, fond tulang sapi\n\nLangkah:\n1. Reduction estragon-cuka.\n2. Emulsifikasi kuning telur & mentega.\n3. Jus: reduksi bordeaux + fond, monter beurre.",
        ]);

        // ── Kelas 4: Seafood Nusantara (selesai) ──────────────────
        $seafood = Kelas::create([
            'chef_id' => $dewi->id,
            'nama_kelas' => 'Nusantara Heritage Seafood',
            'deskripsi' => 'Ikan bakar bumbu rujak, kepiting saus padang, dan teknik memilih seafood segar pasar.',
            'status' => Kelas::STATUS_SELESAI,
            'tanggal_mulai' => today()->subDays(12),
            'tanggal_selesai' => today()->subDays(5),
            'harga' => 550000,
            'foto' => null,
        ]);
        $menuSeafood = MenuResep::create([
            'kelas_id' => $seafood->id,
            'nama_menu' => 'Ikan Bakar Bumbu Rujak',
            'deskripsi' => 'Ikan kakap bakar dengan sambal rujak manis-pedas.',
            'resep' => "Bahan:\n- 1 ekor kakap merah\n- 8 cabai merah, 5 bawang merah\n- 2 sdm petis, gula aren\n\nLangkah:\n1. Lumuri ikan dengan air asam & garam.\n2. Bakar dua tahap, oles bumbu rujak.\n3. Bakar lagi hingga karamelisasi.",
        ]);
        Jadwal::create([
            'kelas_id' => $seafood->id,
            'menu_resep_id' => $menuSeafood->id,
            'tanggal' => today()->subDays(9),
            'waktu' => '16:00',
            'link_sesi' => 'https://meet.google.com/masak-seafood-1',
        ]);
        Pembayaran::create([
            'learner_id' => $budi->id,
            'kelas_id' => $seafood->id,
            'jumlah' => 550000,
            'metode' => 'bca',
            'no_referensi' => 'TRF-20260120-007',
            'bukti' => null,
            'status' => Pembayaran::STATUS_BERHASIL,
            'tanggal_bayar' => today()->subDays(13),
        ]);

        // ── Forum diskusi ─────────────────────────────────────────
        $tanyaBudi = Diskusi::create([
            'kelas_id' => $rendang->id,
            'user_id' => $budi->id,
            'pesan' => 'Chef, kalau wagyu diganti tetelan sapi biasa, berapa lama waktu memasaknya supaya tetap empuk?',
        ]);
        Diskusi::create([
            'kelas_id' => $rendang->id,
            'user_id' => $dewi->id,
            'parent_id' => $tanyaBudi->id,
            'pesan' => 'Boleh sekali. Tetelan butuh 8–9 jam api kecil karena kolagen lebih tebal. Kunci suhu: jaga di kisaran 85°C.',
        ]);
        Diskusi::create([
            'kelas_id' => $rendang->id,
            'user_id' => $andra->id,
            'pesan' => 'Untuk sambal matah, lebih autentik pakai jeruk limau atau jeruk nipis ya chef?',
        ]);

        $this->seedRating();

        $this->command?->info('Seed MASAK selesai: 1 admin, 2 chef, 3 learner, 4 kelas, pembayaran, diskusi & rating contoh.');
    }

    // Rating contoh: learner dengan pembayaran berhasil pada kelas berstatus selesai.
    private function seedRating(): void
    {
        $kelasSelesai = Kelas::where('status', Kelas::STATUS_SELESAI)->get();

        foreach ($kelasSelesai as $kelas) {
            $peserta = Pembayaran::where('kelas_id', $kelas->id)
                ->where('status', Pembayaran::STATUS_BERHASIL)
                ->pluck('learner_id');

            foreach ($peserta as $learnerId) {
                Rating::firstOrCreate(
                    ['kelas_id' => $kelas->id, 'learner_id' => $learnerId],
                    ['bintang' => 5],
                );
            }
        }
    }
}
