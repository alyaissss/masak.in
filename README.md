# MASAK — Manajemen Aplikasi Sistem Akademi Kuliner

Website dashboard interaktif berbasis **PHP Laravel 11** (arsitektur MVC, MySQL/SQLite)
untuk mengelola kelas memasak daring: tiga peran (**Admin, Chef, Learner**), menu & resep,
jadwal + link sesi online, pembayaran transfer dengan konfirmasi manual admin, dan forum
diskusi. UI Bahasa Indonesia, responsif di segala device (desktop, tablet, ponsel).

Dibangun sesuai dokumen: **SRS (Tugas 2), Use Case Diagram (Tugas 3), State Machine
Diagram (Tugas 5), dan Class Diagram**.

## Palet Warna

| Warna | Hex | Penggunaan |
|---|---|---|
| Warm White | `#FAF7F2` | Background |
| Burgundy | `#7A2E32` | Primary |
| Espresso | `#2E2421` | Text (dan sidebar) |
| Beige | `#D9C3A5` | Secondary (border, nav) |
| Dusty Rose | `#C98F88` | Accent (badge, focus ring) |

## Cara Menjalankan

Kebutuhan: **PHP ≥ 8.2** (ekstensi mbstring, xml, sqlite3/mysql, curl, zip) dan **Composer**.

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed     # membuat tabel + data contoh
php artisan storage:link       # agar bukti transfer & foto menu tampil
php artisan serve              # http://localhost:8000
```

## Menjalankan Tes

Tes otomatis menggunakan database MySQL khusus `masak_test` agar tidak menghapus
data aplikasi. Buat database tersebut satu kali sebelum
menjalankan tes:

```sql
CREATE DATABASE masak_test;
```

Kemudian jalankan `php artisan test`.

> Bawaan `.env.example` memakai **MySQL** (sesuai SRS). Untuk demo cepat tanpa MySQL,
> ganti `DB_CONNECTION=sqlite` lalu `touch database/database.sqlite`. Hapus
> `DB_HOST`…`DB_PASSWORD`.

## Akun Demo (hasil seed)

| Peran | Email | Kata Sandi |
|---|---|---|
| Admin | `admin@masak.id` | `masak123` |
| Chef  | `chef.dewi@masak.id` | `chef123` |
| Chef  | `chef.raka@masak.id` | `chef123` |
| Learner | `learner.budi@masak.id` | `learner123` |
| Learner | `learner.sari@masak.id` | `learner123` |
| Learner | `learner.andra@masak.id` | `learner123` |

Data contoh: 4 kelas (Rendang Wagyu Masterclass — tersedia, Artisan Sourdough — tersedia,
French Classical Sauce — draft, Nusantara Heritage Seafood — selesai), menu & resep,
jadwal sesi, pembayaran (2 berhasil, 1 menunggu verifikasi), dan diskusi.

## Peta Fitur ↔ Kebutuhan SRS

| FR | Fitur | Implementasi |
|---|---|---|
| FR-01–05 | Login/validasi/pesan galat/redirect per role/logout | `AuthController`, `resources/views/auth/login.blade.php` |
| FR-06–13 | Kelola data Chef & Learner | `Admin/ChefController`, `Admin/LearnerController` |
| FR-14–18 | Kelola data kelas + assign chef | `Admin/KelasController` |
| FR-19–23 | Chef membuat/mengedit/menghapus kelas | `Chef/KelasController` |
| FR-24–28 | Kelola menu & resep; learner melihatnya | `Chef/MenuResepController`, `Learner/KelasPageController` |
| FR-29–34 | Jadwal, jam, link video conference | `Chef/JadwalController` |
| FR-35–38 | Katalog kelas tersedia + detail + pilih kelas | `Learner/KelasController` |
| FR-39–43 | Pembayaran manual (bukti transfer ≤ 2 MB) → status → akses | `Learner/PembayaranController`, `Admin/PembayaranController` |
| FR-44–50 | Tombol "Masuk Kelas" + ruang belajar (menu, resep, jadwal, link, forum) | `Learner/KelasPageController` |
| FR-51–55 | Forum diskusi per kelas + balasan chef | `Chef/DiskusiController`, `Learner/KelasPageController` |
| Tambahan | Rating bintang 1–5 untuk kelas selesai; rata-rata per chef & "Chef Favorit" di katalog | `Learner/RatingController`, `Models/Rating.php` |

**State machine kelas (Tugas 5):** `draft → tersedia` (publikasikan) ·
`tersedia → selesai` (tandai selesai) · `selesai → tersedia` (buka kembali) ·
terhapus (final). Transisi tidak valid ditolak server-side (`Kelas::TRANSISI_VALID`).

## Keamanan & Non-Fungsional

- **NFR-06** password ter-hash (`'password' => 'hashed'` cast — bcrypt).
- **NFR-07–09** RBAC middleware `role:admin|chef|learner`; chef hanya mengelola kelasnya
  (NFR-18); learner hanya mengakses kelas yang pembayarannya *berhasil* (NFR-21).
- **NFR-05/11** validasi unggahan (bukti: JPG/PNG/PDF maks 2 MB; foto menu: gambar maks 2 MB).
- **NFR-12–16** UI konsisten, modular (MVC), portabel, dan responsif (sidebar→drawer mobile).
- **NFR-19–20** semua halaman mengharuskan login kecuali `/login`.

## Struktur

```
app/Http/Controllers/{AuthController, Admin/, Chef/, Learner/}
app/Http/Middleware/EnsureRole.php       # guard peran
app/Models/{User, Kelas, MenuResep, Jadwal, Pembayaran, Diskusi}.php
database/migrations/                     # skema sesuai class diagram
database/seeders/DatabaseSeeder.php      # data contoh idempoten
resources/views/{layouts, auth, admin, chef, learner, components}
public/{css/app.css, js/app.js, img/}    # design system tanpa build step
```

Tidak ada dependensi npm — aset CSS/JS statis di `public/`, sehingga aplikasi jalan
dengan `php artisan serve` saja.
