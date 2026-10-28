# Menjalankan E-Rapor dengan Docker Compose

Konfigurasi ini memakai tiga container: PHP-FPM (Laravel), Nginx, dan MySQL 8.4. Port database tidak dibuka ke internet. Data MySQL dan berkas Laravel (termasuk PDF rapor) disimpan pada volume Docker terpisah dari image.

## Persiapan

- VPS Ubuntu 24.04 dengan Docker Engine dan plugin Docker Compose.
- Port 80 terbuka. Sebelum memakai data siswa sungguhan, pasang domain dan HTTPS.
- Pada VPS 2 GB, aktifkan swap dan pantau memori saat build pertama. Untuk CI/CD, build image di GitHub Actions agar VPS tidak menanggung proses build.

## Konfigurasi pertama

```bash
cp .env.docker.example .env.docker
openssl rand -base64 32
openssl rand -hex 24
openssl rand -hex 24
```

Isi `.env.docker` dengan editor. Tambahkan `base64:` di depan hasil perintah pertama untuk `APP_KEY`. Gunakan dua hasil berikutnya sebagai `DB_PASSWORD` dan `DB_ROOT_PASSWORD`. Jangan commit atau membagikan file ini. Sesuaikan `APP_URL` dengan alamat server; ganti menjadi `https://nama-domain` setelah HTTPS aktif.

```bash
docker compose --env-file .env.docker config --quiet
docker compose --env-file .env.docker up -d --build
docker compose --env-file .env.docker exec app php artisan migrate --force
docker compose --env-file .env.docker exec app php artisan optimize
docker compose --env-file .env.docker ps
```

Seeder berisi akun contoh dengan password bawaan `password`. Jangan jalankan seeder pada situs yang terbuka untuk umum sebelum akses dibatasi dan password akun diganti. Bila hanya menampilkan demo dengan data sintetis, jalankan `docker compose --env-file .env.docker exec app php artisan db:seed --force` setelah migrasi, lalu segera ganti password akun contoh.

## Pembaruan aplikasi

Sebelum pembaruan yang mengubah skema database, buat cadangan database dan volume berkas. Setelah kode/image baru tersedia:

```bash
docker compose --env-file .env.docker up -d --build
docker compose --env-file .env.docker exec app php artisan migrate --force
docker compose --env-file .env.docker exec app php artisan optimize
```

Jangan menjalankan `docker compose down -v` karena opsi `-v` menghapus volume data. Konfigurasi ini belum memasang HTTPS atau workflow CI/CD; keduanya disiapkan sebelum aplikasi digunakan dengan data sekolah sungguhan.
