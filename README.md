# 🏫 E-Rapor SLB Insan Mutiara Pekanbaru

> **Sistem Informasi Buku Laporan Hasil Belajar Siswa Berbasis Kurikulum Merdeka Sekolah Luar Biasa (SLB)**

[![Laravel Version](https://img.shields.io/badge/Laravel-v11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D%208.2-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![Vite](https://img.shields.io/badge/Vite-v6.x-646CFF?style=for-the-badge&logo=vite&logoColor=white)](https://vitejs.dev)
[![WhatsApp Integration](https://img.shields.io/badge/WhatsApp-wa.me_API-25D366?style=for-the-badge&logo=whatsapp&logoColor=white)](https://whatsapp.com)
[![License](https://img.shields.io/badge/License-MIT-blue?style=for-the-badge)](LICENSE)

---

## 📖 Tentang Aplikasi

**E-Rapor SLB Insan Mutiara** adalah platform evaluasi dan pelaporan hasil belajar digital yang dirancang khusus untuk memenuhi kebutuhan satuan pendidikan khusus (SLB). Mengacu pada format resmi pindaian rapor fisik dan kurikulum inklusi, sistem ini memfasilitasi pencatatan capaian akademik, evaluasi perkembangan adaptif, program kekhususan, hingga pelaporan langsung ke orang tua secara transparan dan akuntabel.

Sistem ini diterapkan untuk **SLB Insan Mutiara Pekanbaru** yang beralamat di Jalan Cendana No. 33A / Adi Sucipto, Kota Pekanbaru, Riau, di bawah pimpinan Kepala Sekolah **Novia Parenri, S.Si, M.Si** (NIP. 198005122005012003).

### Kelompok Lunora

| Anggota | NIM |
| --- | --- |
| Andika Fitra Darmawan | 2303135265 |
| TIO ANDRIAN | 2303110119 |
| Irfan Sinaga | 2303113019 |
| M. Taqiyuddin Dzaky | 2303126079 |
| Fitri Nur Dayani | 2303110134 |

---

## ✨ Fitur Unggulan (Key Features)

### 1. 🎯 Pembagian Khusus 3 Ketunaan & Program Kebutuhan Khusus
Sistem disesuaikan dengan 3 kategori ketunaan di SLB Insan Mutiara:
* **Tunarungu**: Program Kebutuhan Khusus **PKPBI** (*Pengembangan Komunikasi, Persepsi Bunyi, dan Irama*).
* **Tunagrahita**: Program Kebutuhan Khusus **Bina Diri** (*Kemandirian & Aktivitas Hidup Sehari-hari*).
* **Autis**: Program Kebutuhan Khusus **PKISP** (*Pengembangan Komunikasi, Interaksi Sosial, dan Perilaku*).

### 2. 🛡️ Keamanan Penghapusan 2 Langkah (2-Step Verification Delete)
Mencegah terhapusnya data penting pendidikan secara tidak sengaja:
* Semua aksi `DELETE` (Data Guru, Siswa, Kelas, Mata Pelajaran, Orang Tua, dan Ekstrakurikuler) menggunakan komponen modal interaktif [`confirm-delete.blade.php`](resources/views/components/modal/confirm-delete.blade.php).
* Tombol **"Hapus Permanen"** terkunci secara default hingga pengguna secara sadar mencentang kotak persetujuan konfirmasi risiko.

### 3. 📲 Distribusi Leger & Rapor via WhatsApp (`wa.me`)
* Terintegrasi dengan [`WhatsAppService`](app/Services/WhatsAppService.php) untuk normalisasi nomor telepon Indonesia (`08...` / `+62...` $\rightarrow$ `628...`).
* Mengirimkan ringkasan capaian belajar dan leger nilai langsung ke nomor WhatsApp orang tua/wali murid dengan satu klik melalui tautan resmi `https://wa.me/{nomor}?text={pesan}`.
* Pencatatan riwayat distribusi otomatis ke tabel `distribusi_rapor`.

### 4. 📄 Format Rapor Otentik 1:1 Sesuai Dokumen Resmi
* Halaman cetak dan pratinjau rapor disusun dengan presisi menyerupai buku rapor fisik resmi SLB Insan Mutiara.
* Mencakup tabel capaian pembelajaran, narasi kompetensi, catatan kehadiran (Sakit, Izin, Alpa), evaluasi fisik & kesehatan, catatan wali kelas (**Rina Pratiwi, S.Pd.**), serta tanda tangan kepala sekolah (**Novia Parenri, S.Si, M.Si**).

### 5. 👥 Hak Akses Bertingkat (Role-Based Access Control)
* **Administrator**: Mengelola master data (Guru, Siswa, Kelas, Mapel, Ortu, Ekskul, Tahun Ajaran, Profil Sekolah, & Manajemen Akun).
* **Kepala Sekolah**: Verifikasi & persetujuan rapor (Setujui / Kembalikan dengan catatan revisi), monitoring statistik pengajuan, dan cetak leger nilai.
* **Wali Kelas**: Pengisian nilai asesmen naratif, input evaluasi non-akademik & kehadiran, pengajuan rapor ke kepala sekolah, serta distribusi ke orang tua.

### 6. 🎨 Antarmuka Identik 1:1 Berbasis Reusable Components
* Desain visual modern menggunakan Tailwind CSS dengan token warna dan tipografi konsisten.
* Komponen modular yang dapat dipakai ulang: `Sidebar`, `Topbar`, `StatCard`, `BannerHero`, `ActionItem`, `ActivityTable`, `ConfirmDeleteModal`, `UnsavedChangesModal`, dan `AlertBanner`.

---

## 🗄️ Struktur Basis Data & Seeder Terpisah

Untuk menjamin modularitas dan kemudahan pemeliharaan, seeder basis data dipisahkan menjadi **20 file seeder mandiri** yang masing-masing merepresentasikan 1 tabel:

| No | File Seeder | Nama Tabel | Deskripsi Data |
|:--:|:---|:---|:---|
| 1 | `SekolahSeeder.php` | `sekolah` | Profil identitas resmi SLB Insan Mutiara & Kepala Sekolah |
| 2 | `TahunAjaranSeeder.php` | `tahun_ajaran` | Periode akademik aktif (2026/2027 Ganjil) & arsip periode |
| 3 | `GuruSeeder.php` | `guru` | Data pendidik dan tenaga kependidikan lengkap dengan NIP & WA |
| 4 | `UserSeeder.php` | `users` | Akun autentikasi multi-role (Admin, Kepala Sekolah, Wali Kelas) |
| 5 | `KelasSeeder.php` | `kelas` | Kelas berdasarkan 3 ketunaan (Tunarungu, Tunagrahita, Autis) |
| 6 | `MataPelajaranSeeder.php` | `mata_pelajaran` | Kurikulum umum, vokasi, muatan lokal, dan program kekhususan |
| 7 | `EkstrakurikulerSeeder.php` | `ekstrakurikuler` | Kegiatan ekskul (Pramuka, Pantomim, Bocce, Melukis) |
| 8 | `SiswaSeeder.php` | `siswa` | Data peserta didik berdasarkan ketunaan & data pindaian asli |
| 9 | `OrangTuaSeeder.php` | `orang_tua` | Data kontak wali/orang tua dan nomor penerima WhatsApp |
| 10 | `PenugasanWaliKelasSeeder.php` | `penugasan_wali_kelas` | Penugasan guru sebagai wali kelas per tahun ajaran |
| 11 | `PenugasanSiswaKelasSeeder.php` | `penugasan_siswa_kelas` | Penempatan rombel siswa beserta tingkat kelas & fase (D/F) |
| 12 | `PenugasanPembimbingSeeder.php` | `penugasan_pembimbing` | Penugasan guru pembimbing khusus untuk siswa tertentu |
| 13 | `KelasMataPelajaranSeeder.php` | `kelas_mata_pelajaran` | Pemetaan mata pelajaran relevan untuk masing-masing kelas |
| 14 | `RaporSeeder.php` | `rapor` | Rekam status rapor (Draft, Diajukan, Dikembalikan, Disetujui) |
| 15 | `NilaiAsesmenSeeder.php` | `nilai_asesmen` | Nilai angka (0-100) dan narasi capaian kompetensi siswa |
| 16 | `EvaluasiNonakademikSeeder.php` | `evaluasi_nonakademik` | Rekapitulasi absensi dan catatan perkembangan fisik/motorik |
| 17 | `PrestasiSiswaSeeder.php` | `prestasi_siswa` | Catatan prestasi perlombaan tingkat kota/provinsi |
| 18 | `EkstrakurikulerSiswaSeeder.php` | `ekstrakurikuler_siswa` | Nilai predikat dan capaian kegiatan ekstrakurikuler siswa |
| 19 | `DistribusiRaporSeeder.php` | `distribusi_rapor` | Log pengiriman rapor via WhatsApp ke orang tua |
| 20 | `ActivityLogSeeder.php` | `activity_log` | Jejak audit riwayat aktivitas sistem |

Semua seeder di atas dikoordinasikan secara otomatis melalui [`DatabaseSeeder.php`](database/seeders/DatabaseSeeder.php).

---

## 💻 Kebutuhan Sistem (Prerequisites)

* **PHP**: Versi `>= 8.2` (dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`)
* **Composer**: Versi `>= 2.2`
* **Node.js**: Versi `>= 18.x` & **NPM**
* **MySQL / MariaDB**: Versi `>= 8.0` / `>= 10.4`
* **Web Server**: Apache / Nginx / Built-in PHP CLI

---

## 🚀 Panduan Instalasi (Step-by-Step)

### 1. Clone Repository
```bash
git clone https://github.com/AndikaFitra/E-RAPOR-SLB-Insan-Mutiara.git
cd e-rapor-slb-insan-mutiara
```

### 2. Instal Dependensi PHP & Node
```bash
# Instal dependensi backend (Laravel)
composer install

# Instal dependensi frontend (Tailwind & Vite)
npm install
```

### 3. Konfigurasi Lingkungan (`.env`)
Salin file konfigurasi template `.env.example` ke `.env`:
```bash
cp .env.example .env
```
Buka file `.env` dan sesuaikan koneksi database MySQL:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=erapor_slb
DB_USERNAME=root
DB_PASSWORD=
```
Generate kunci enkripsi aplikasi:
```bash
php artisan key:generate
```

### 4. Migrasi & Eksekusi Seeder Basis Data
Jalankan migrasi seluruh tabel beserta pemisahan seeder:
```bash
# Migrasi segar dan isi seluruh 20 tabel secara berurutan
php artisan migrate:fresh --seed
```

> **Tips:** Anda juga dapat menjalankan seeder individual per tabel:
> ```bash
> php artisan db:seed --class=SekolahSeeder
> php artisan db:seed --class=GuruSeeder
> php artisan db:seed --class=SiswaSeeder
> php artisan db:seed --class=RaporSeeder
> ```

### 5. Kompilasi Aset Frontend
```bash
# Untuk keperluan development (hot-reload)
npm run dev

# ATAU kompilasi build produksi
npm run build
```

### 6. Jalankan Server Lokal
```bash
php artisan serve
```
Akses aplikasi melalui browser di: **`http://127.0.0.1:8000`**

---

## 🔑 Akun Bawaan (Default Credentials)

Semua akun demo menggunakan password default yang sama: **`password`**

| Role | Nama Pengguna | NIP Login | Hak Akses Utama |
|:---|:---|:---|:---|
| **Administrator** | Sari Wulandari, S.Pd. | `198702122010011001` | Akses penuh master data, manajemen akun & konfigurasi sekolah |
| **Kepala Sekolah** | Novia Parenri, S.Si, M.Si | `198005122005012003` | Verifikasi persetujuan rapor, catatan revisi, dan cetak leger |
| **Wali Kelas** | Rina Pratiwi, S.Pd. | `198803152011012014` | Input asesmen Tunarungu, evaluasi bina komunikasi, kirim WA |
| **Wali Kelas** | Ahmad Fauzi, S.Pd. | `199107242015021002` | Pengelolaan rapor siswa kelas Tunagrahita & Bina Diri |

---

## 📁 Struktur Direktori Utama

```
e-rapor-slb-insan-mutiara/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/             # Controller Admin (Siswa, Guru, Kelas, Mapel, dll.)
│   │   │   ├── Auth/              # Autentikasi multi-role & proteksi status aktif
│   │   │   ├── KepalaSekolah/     # Controller Persetujuan, Dashboard, Leger
│   │   │   └── WaliKelas/         # Controller Nilai, Evaluasi, Rapor, Distribusi
│   │   └── Middleware/            # EnsureRole middleware
│   ├── Models/                    # Eloquent Model (Siswa, Guru, Rapor, Nilai, dll.)
│   └── Services/
│       └── WhatsAppService.php    # Service pembuatan tautan wa.me & format pesan formal
├── database/
│   ├── migrations/                # Skema tabel database (Users, Master, Penugasan, Penilaian)
│   └── seeders/                   # 20 Seeder terpisah per tabel + DatabaseSeeder master
├── resources/
│   ├── css/                       # Gaya Tailwind kustom
│   └── views/
│       ├── admin/                 # Halaman kelola Admin
│       ├── auth/                  # Halaman Login identik
│       ├── components/            # Reusable UI (Sidebar, Topbar, Modal Confirm Delete, dll.)
│       ├── kepala_sekolah/        # Halaman Persetujuan & Leger Kepala Sekolah
│       └── wali_kelas/            # Halaman Penilaian, Rapor Otentik & Distribusi WA
├── routes/
│   └── web.php                    # Definisi rute aplikasi dengan proteksi middleware peran
└── scratch/
    └── test_all_features_suite.php # Skrip pengujian otomatis fitur end-to-end
```

---

## 🧪 Pengujian Otomatis (Automated Testing)

Aplikasi dilengkapi dengan suite pengujian komprehensif untuk memastikan seluruh alur kerja berjalan sempurna:

```bash
# Jalankan suite pengujian fitur lengkap (CRUD, WA.me, 2-Step Delete, Persetujuan)
php scratch/test_all_features_suite.php
```

Output pengujian yang terverifikasi:
```text
=== SUITE TEST SELURUH FITUR & WA.ME INTEGRATION ===
 [PASS] formatPhoneNumber 0812... -> 62812...
 [PASS] formatPhoneNumber +62 813... -> 62813...
 [PASS] Pesan WhatsApp memuat Nama, PKPBI, dan Tunarungu
 [PASS] wa.me link format sesuai standar WhatsApp
 [PASS] Leger Kepala Sekolah memuat 11 data siswa dari DB
 [PASS] Data Leger memuat wa_url untuk setiap siswa
 [PASS] Distribusi Wali Kelas memuat 11 data distribusi
 [PASS] Data Distribusi memuat wa_url untuk orang tua
 [PASS] markSent berhasil redirect dengan status terkirim
 [PASS] Approve Rapor berhasil mengubah status ke disetujui & timestamp persetujuan
 [PASS] Reject Rapor berhasil mengubah status ke dikembalikan & menyimpan catatan revisi
 [PASS] Admin Guru Store berhasil membuat Guru baru di database
 [PASS] Admin Guru Update berhasil memperbarui nama & spesialisasi Autis
 [PASS] Admin Guru Destroy berhasil soft delete guru
 [PASS] Admin Kelas Store berhasil membuat kelas dengan ketunaan Autis
 [PASS] Admin Kelas Update berhasil memperbarui kelas
 [PASS] Admin Kelas Destroy berhasil menghapus kelas
 [PASS] Admin Mapel Store berhasil
 [PASS] Admin Mapel Destroy berhasil
 [PASS] Admin Orang Tua Store berhasil
 [PASS] Admin Orang Tua Destroy berhasil
 [PASS] Admin Sekolah Update berhasil memperbarui Novia Parenri, S.Si, M.Si
 [PASS] Wali Kelas submit rapor berhasil mengubah status ke Diajukan

HASIL PENGUJIAN: 23 LULUS, 0 GAGAL
SEMUA PENGUJIAN LULUS DENGAN SEMPURNA!
```

---

## 🤝 Pengembang & Lisensi

* **Pengembang**: Kelompok Lunora (anggota tercantum di atas).
* **Kontak proyek**: [Andika Fitra Darmawan](https://github.com/AndikaFitra) · `andika.namikaze02@gmail.com`
* **Institusi**: SLB Insan Mutiara Pekanbaru
* **Lisensi**: Proyek ini dilisensikan di bawah lisensi terbuka [MIT](LICENSE).
