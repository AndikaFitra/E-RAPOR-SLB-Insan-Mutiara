# Data demo E-Rapor

Seeder utama (`DatabaseSeeder`) sekarang memuat `DemoScenarioSeeder` setelah data sekolah, akun, kelas, siswa, dan rapor dasar dibuat. Jalankan pada **database demo**:

```bash
php artisan migrate:fresh --seed
```

Perintah di atas menghapus isi database yang dituju. Jika database dasar sudah ada dan hanya ingin menambahkan skenario, jalankan:

```bash
php artisan db:seed --class=DemoScenarioSeeder
```

Seeder skenario aman dijalankan ulang: siswa dikenali dari NISN demo dan rapor dari pasangan siswa–periode. PDF rapor yang sudah final tidak dibuat ulang.
Skenario selalu dibuat pada **tahun ajaran yang sedang aktif**. Jika periode aktif diganti, jalankan lagi perintah `db:seed --class=DemoScenarioSeeder` agar contoh rapor muncul pada periode baru. Penugasan wali kelas dan mata pelajaran yang sudah ada pada periode aktif dipertahankan; yang belum ada disalin dari data contoh 2026/2027 Ganjil.

## Akun untuk masuk

Semua akun demo berikut memakai password `password` setelah seeder utama dijalankan.

| Peran | Username | Yang bisa didemokan |
| --- | --- | --- |
| Admin | `admin` | Data siswa/guru, kelas, penugasan, mata pelajaran, ekstrakurikuler, akun, tahun ajaran, sekolah, arsip |
| Kepala Sekolah | `kepsek` | Antrean persetujuan, rapor final, leger, arsip |
| Wali Kelas Tunarungu | `rinapratiwi` | Nilai, evaluasi, empat status rapor, distribusi, arsip |
| Wali Kelas Tunagrahita | `ahmadfauzi` | Alur serupa untuk kelas Tunagrahita A |
| Wali Kelas Autis | `sitinurhaliza` | Alur serupa untuk kelas Autis A |
| Akun nonaktif | `nonaktif` | Pesan penolakan login akun nonaktif |

## Skenario yang tersedia

Setiap wali kelas mempunyai siswa bernama **Demo** dengan status `Draft`, `Dikembalikan`, `Diajukan`, dan `Disetujui`. Draft sengaja belum lengkap agar pengisian dan validasi kelengkapan dapat dicoba. Rapor yang dikembalikan memiliki catatan revisi; rapor diajukan memiliki nilai dan narasi lengkap; rapor disetujui memiliki dokumen PDF final dan snapshot.

Di Admin ada **Demo Siswa Belum Ditugaskan**, **Demo Siswa Nonaktif**, dan **Demo Guru Tanpa Penugasan**. Data tersebut dapat dipakai untuk menunjukkan filter, penugasan, serta contoh guru yang boleh dihapus. Guru yang masih terhubung ke akun atau penugasan tetap ditolak oleh aturan aplikasi.

Distribusi rapor final mempunyai contoh `Terkirim`, `Gagal`, dan `Pending` pada tiga kelas. **Demo Gina Tunarungu** juga memiliki rapor final semester 2025/2026 Genap untuk menampilkan arsip lintas tahun. Data siswa dan nomor kontak berawalan “Demo” bersifat contoh; jangan gunakan untuk pengiriman WhatsApp sungguhan.
