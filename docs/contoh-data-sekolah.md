# Data contoh sekolah

Seeder utama memuat `NaturalSchoolDataSeeder` setelah data dasar dibuat. Untuk database yang sudah berisi data, jalankan:

```bash
php artisan migrate
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=NaturalSchoolDataSeeder
```

Seeder ini dapat dijalankan ulang. Data siswa contoh lama yang sebelumnya berawalan “Demo” diperbarui berdasarkan NISN lama, sehingga tidak membuat duplikat. Penugasan kelas, mata pelajaran, nilai, evaluasi, rapor, dan distribusi disediakan untuk **setiap tahun ajaran dan semester yang ada di database**. Jika menambah periode, jalankan ulang seeder. Data manual di luar baris contoh tidak dihapus.

## Login

Semua akun bawaan memakai password `password`. Form login hanya menerima NIP.

| Peran | NIP |
| --- | --- |
| Admin | `198702122010011001` |
| Kepala Sekolah | `198005122005012003` |
| Wali Kelas Tunarungu | `198803152011012014` |
| Wali Kelas Tunagrahita | `199107242015021002` |

## Data yang tersedia

Dua Wali Kelas masing-masing mendapat lima siswa di kelas Tunarungu dan Tunagrahita pada setiap periode. Keduanya mempunyai contoh rapor Draft, Dikembalikan, Diajukan, dan Disetujui. Nama siswa dan wali dibuat seperti data sekolah sehari-hari. Tingkat kelas mengikuti tahun ajaran; nilai, narasi, kehadiran, serta catatan wali bervariasi. Rapor final mempunyai PDF dan contoh status pengiriman Terkirim, Gagal, dan Pending. Kelas Autis tetap tersedia sebagai data akademik, tetapi tidak mempunyai akun Wali Kelas bawaan.

Siswa Nabila Fauziah belum ditugaskan ke kelas, Rizky Ramadhan tidak aktif, dan guru Mira Susanti belum memiliki penugasan. Ketiganya berguna untuk memperlihatkan filter dan alur administrasi.

Semua identitas dan nomor kontak di seeder adalah data contoh. Jangan gunakan nomor tersebut untuk pengiriman WhatsApp sungguhan.
