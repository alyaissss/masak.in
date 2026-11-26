# MASAK (Masak.In) — Manajemen Aplikasi Sistem Akademi Kuliner

## Tim Pengembang (Kelompok 5)

Proyek ini dikembangkan untuk memenuhi tugas mata kuliah Rekayasa Perangkat Lunak Universitas Indraprasta PGRI:
* Cantik Alya Isnaeni (202343501574)
* Marsya Nur Afni (202343501545)
* Shabita Devanya (202343501571)

Aplikasi berbasis web untuk pengelolaan dan pelaksanaan kelas memasak daring secara terpusat. Platform ini menghubungkan **Admin**, **Chef**, dan **Learner** dalam satu ekosistem pembelajaran—mulai dari manajemen kelas, penyusunan menu dan resep, penjadwalan sesi *video conference*, konfirmasi pembayaran, hingga forum diskusi interaktif.

---

## Fitur Utama

* **Autentikasi & Role-Based Access Control (RBAC):** Sistem login terintegrasi yang mengarahkan pengguna ke *dashboard* sesuai hak akses (**Admin**, **Chef**, dan **Learner**).
* **Manajemen Pengguna & Kelas (Admin):** Pengelolaan data master Chef, Learner, daftar kelas, serta verifikasi bukti transfer pembayaran peserta.
* **Pengelolaan Kelas, Menu & Resep (Chef):** Pembuatan kelas memasak (dengan alur status *Draft*, *Tersedia*, dan *Selesai*), penambahan daftar menu, resep lengkap, serta pengaturan jadwal dan tautan sesi *video conference*.
* **Katalog Kelas & Pembayaran (Learner):** Eksplorasi kelas yang tersedia, pemesanan kelas, pengunggahan bukti pembayaran, serta akses ruang belajar setelah pembayaran dikonfirmasi.
* **Ruang Belajar & Forum Diskusi:** Akses materi menu, resep, jadwal kelas, link *video conference*, forum tanya-jawab antara Chef dan Learner, serta fitur ulasan/rating kelas.

---

## Teknologi yang Digunakan

* **Backend:** PHP 8.2+, Laravel 11 (Arsitektur MVC)
* **Frontend:** HTML5, Tailwind CSS, JavaScript
* **Database:** MySQL (mendukung SQLite untuk pengujian cepat)
* **Server & Tools:** Composer, Artisan CLI

---

## Prasyarat Sistem

Pastikan perangkat Anda sudah terpasang:
* PHP >= 8.2
* Composer
* MySQL / MariaDB

---

## Panduan Instalasi & Menjalankan Aplikasi

1. **Clone repositori ini:**
   ```bash
   git clone https://github.com/alyaissss/masak.in.git
   cd <nama-folder-project
