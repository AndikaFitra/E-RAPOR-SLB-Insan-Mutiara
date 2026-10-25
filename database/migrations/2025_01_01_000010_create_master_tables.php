<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Informasi sekolah (singleton — hanya 1 baris)
        Schema::create('sekolah', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('npsn', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->string('kota', 100)->nullable();
            $table->string('provinsi', 100)->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->string('telepon', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('kepala_sekolah', 100)->nullable();
            $table->string('nip_kepala', 30)->nullable();
            $table->string('logo')->nullable(); // path ke file logo
            $table->timestamps();
        });

        // Tahun ajaran & semester
        Schema::create('tahun_ajaran', function (Blueprint $table) {
            $table->id();
            $table->string('tahun', 10); // mis.: 2026/2027
            $table->enum('semester', ['Ganjil', 'Genap']);
            $table->boolean('is_aktif')->default(false);
            $table->timestamps();

            $table->unique(['tahun', 'semester']);
        });

        // Data guru
        Schema::create('guru', function (Blueprint $table) {
            $table->id();
            $table->string('nip', 30)->nullable()->unique();
            $table->string('nama_lengkap', 100);
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('jabatan', 100)->nullable();
            $table->string('no_whatsapp', 20)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Data siswa
        Schema::create('siswa', function (Blueprint $table) {
            $table->id();
            $table->string('nisn', 10)->unique();
            $table->string('nama_lengkap', 100);
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->string('kekhususan', 100)->nullable(); // mis.: Tunarungu, Tunagrahita
            $table->string('no_hp', 20)->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Data orang tua / wali siswa
        Schema::create('orang_tua', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->string('nama', 100);
            $table->enum('hubungan', ['Ayah', 'Ibu', 'Wali']);
            $table->string('no_whatsapp', 20)->nullable();
            $table->boolean('is_penerima_utama')->default(false);
            $table->timestamps();
        });

        // Data kelas
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100); // mis.: Tunarungu A
            $table->string('kekhususan', 100)->nullable(); // mis.: Tunarungu
            $table->string('kode', 20)->unique(); // mis.: TU-A
            $table->timestamps();
        });

        // Mata pelajaran
        Schema::create('mata_pelajaran', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150);
            $table->string('kode', 20)->unique();
            $table->enum('kelompok', [
                'Umum',
                'Seni dan Vokasi',
                'Program Kekhususan',
                'Muatan Lokal',
                'Keterampilan Pilihan',
            ])->default('Umum');
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });

        // Ekstrakurikuler
        Schema::create('ekstrakurikuler', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ekstrakurikuler');
        Schema::dropIfExists('mata_pelajaran');
        Schema::dropIfExists('kelas');
        Schema::dropIfExists('orang_tua');
        Schema::dropIfExists('siswa');
        Schema::dropIfExists('guru');
        Schema::dropIfExists('tahun_ajaran');
        Schema::dropIfExists('sekolah');
    }
};
