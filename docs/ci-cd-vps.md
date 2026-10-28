# CI/CD GitHub Actions ke VPS

Workflow `.github/workflows/ci-deploy.yml` menjalankan tes PHP dan build aset otomatis pada pull request dan push ke `main`. Deploy production dijalankan manual melalui **Actions → CI dan Deploy → Run workflow** pada branch `main`, sesudah tes lulus. Deploy tidak menjalankan seeder atau menghapus volume.

## Siapkan akses GitHub Actions

1. Buat pasangan kunci SSH khusus deploy di komputer pribadi. Simpan kunci privat di tempat aman dan tambahkan kunci publik ke `~ubuntu/.ssh/authorized_keys` di VPS. Jangan memakai password VPS di workflow.
2. Cocokkan fingerprint host SSH VPS yang diperoleh melalui koneksi SSH tepercaya, lalu simpan baris `known_hosts` untuk VPS. Jangan mengandalkan hasil `ssh-keyscan` tanpa verifikasi fingerprint.
3. Di repository GitHub, buat **Environment** bernama `production`. Tambahkan variables `VPS_HOST` (IP VPS saat ini) dan `VPS_USER` (`ubuntu`), serta secrets `VPS_SSH_PRIVATE_KEY` dan `VPS_SSH_KNOWN_HOSTS`.
4. Pastikan akun `ubuntu` bisa menjalankan `sudo -n docker compose version` di VPS tanpa prompt password. Image Ubuntu VPS saat ini telah memakai `sudo` untuk Docker; verifikasi sebelum deploy otomatis.
5. Merge branch yang berisi workflow dan Docker ke `main`. Jalankan workflow manual dari branch `main` setelah environment siap.

Deploy mengambil `main` dengan `git pull --ff-only`, membangun container satu per satu (`--parallel 1`) pada VPS 2 GB, menjalankan migrasi, mengoptimasi Laravel, lalu mengecek halaman login HTTPS. Build saat ini masih berlangsung di VPS; jika build mulai sering kehabisan memori, pindahkan build image ke GitHub Actions dan gunakan registry image sebelum mengaktifkan deploy otomatis pada setiap merge.

Jika IP VPS berubah setelah upgrade paket, perbarui variable `VPS_HOST`, DNS A record, dan `APP_URL` di `.env.docker` sesuai alamat yang baru. File `.env.docker`, PDF rapor, data MySQL, dan sertifikat HTTPS tetap berada di VPS.
