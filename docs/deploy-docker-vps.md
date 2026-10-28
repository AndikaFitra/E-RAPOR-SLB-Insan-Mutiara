# Menjalankan E-Rapor dengan Docker Compose

Konfigurasi ini memakai tiga container: PHP-FPM (Laravel), Caddy, dan MySQL 8.4. Caddy mengelola sertifikat HTTPS otomatis untuk `insanmutiara.my.id`. Port database tidak dibuka ke internet. Data MySQL dan berkas Laravel (termasuk PDF rapor) disimpan pada volume Docker terpisah dari image.

## Persiapan

- VPS Ubuntu 24.04 dengan Docker Engine dan plugin Docker Compose.
- A record `insanmutiara.my.id` mengarah ke IP publik VPS; port 80 dan 443 terbuka agar Caddy dapat memperoleh serta memperbarui sertifikat.
- Pada VPS 2 GB, aktifkan swap dan pantau memori saat build pertama. Untuk CI/CD, build image di GitHub Actions agar VPS tidak menanggung proses build.

## Konfigurasi pertama

```bash
cp .env.docker.example .env.docker
openssl rand -base64 32
openssl rand -hex 24
openssl rand -hex 24
```

Isi `.env.docker` dengan editor. Tambahkan `base64:` di depan hasil perintah pertama untuk `APP_KEY`. Gunakan dua hasil berikutnya sebagai `DB_PASSWORD` dan `DB_ROOT_PASSWORD`. Jangan commit atau membagikan file ini. Set `APP_URL=https://insanmutiara.my.id` dan `SESSION_SECURE_COOKIE=true` sebelum menjalankan Caddy. Bersihkan cache konfigurasi Laravel setelah mengubah nilai itu.

```bash
docker compose --env-file .env.docker config --quiet
docker compose --env-file .env.docker up -d --build
docker compose --env-file .env.docker exec app php artisan migrate --force
docker compose --env-file .env.docker exec app php artisan optimize
docker compose --env-file .env.docker ps
```

Untuk data demo sintetis, isi `SEED_ACCOUNT_PASSWORD` dengan password kuat minimal 12 karakter di `.env.docker`, simpan nilainya secara pribadi, lalu jalankan `php artisan optimize:clear` dan `php artisan optimize` agar konfigurasi baru terbaca. Seeder production akan menolak berjalan jika password itu belum diisi. Setelah itu jalankan `docker compose --env-file .env.docker exec app php artisan db:seed --force`. Akun bawaan memakai password yang sama dari konfigurasi ini; ubah password tiap akun melalui aplikasi sebelum dipakai oleh pengguna sebenarnya. Jangan memasukkan data siswa sungguhan ke lingkungan demo.

## Pembaruan aplikasi

Sebelum pembaruan yang mengubah skema database, buat cadangan database dan volume berkas. Setelah kode/image baru tersedia:

```bash
docker compose --env-file .env.docker up -d --build
docker compose --env-file .env.docker exec app php artisan migrate --force
docker compose --env-file .env.docker exec app php artisan optimize
```

Jangan menjalankan `docker compose down -v` karena opsi `-v` menghapus volume data. Sertifikat Caddy disimpan pada volume `caddy_data` agar tetap tersedia saat container diperbarui. Workflow CI/CD disiapkan terpisah.

Jika VPS sempat menjalankan versi Compose lama dengan MariaDB, volume `db_data` lama tidak dipakai oleh MySQL. Volume itu sengaja tidak dihapus agar data lama bisa diperiksa atau dicadangkan sebelum dibersihkan secara terpisah.
