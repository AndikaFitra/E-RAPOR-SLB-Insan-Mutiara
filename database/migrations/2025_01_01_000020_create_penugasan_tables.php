<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Foreign key dari users ke guru (ditambahkan setelah keduanya dibuat)
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('guru_id', 'fk_users_guru_id')->references('id')->on('guru')->nullOnDelete();
        });

        // Penugasan wali kelas ke kelas per tahun ajaran
        Schema::create('penugasan_wali_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('guru')->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['kelas_id', 'tahun_ajaran_id']); // 1 kelas hanya 1 wali kelas per semester
        });

        // Penugasan siswa ke kelas per tahun ajaran (dengan tingkat & fase individual)
        Schema::create('penugasan_siswa_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->cascadeOnDelete();
            $table->string('tingkat', 10)->nullable(); // mis.: VII, VIII, IX
            $table->string('fase', 5)->nullable();     // mis.: A, B, C, D, E, F
            $table->timestamps();

            $table->unique(['siswa_id', 'tahun_ajaran_id']); // siswa hanya di 1 kelas per semester
        });

        // Penugasan pembimbing khusus per siswa
        Schema::create('penugasan_pembimbing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->foreignId('guru_id')->constrained('guru')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['siswa_id', 'tahun_ajaran_id']);
        });

        // Mata pelajaran per kelas/kekhususan (mapel yang diajarkan di kelas tertentu)
        Schema::create('kelas_mata_pelajaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajaran')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['kelas_id', 'mata_pelajaran_id', 'tahun_ajaran_id'], 'kelas_mapel_tahun_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas_mata_pelajaran');
        Schema::dropIfExists('penugasan_pembimbing');
        Schema::dropIfExists('penugasan_siswa_kelas');
        Schema::dropIfExists('penugasan_wali_kelas');

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['guru_id']);
        });
    }
};
