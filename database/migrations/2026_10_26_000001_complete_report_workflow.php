<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rapor', function (Blueprint $table) {
            $table->json('snapshot')->nullable();
            $table->string('share_token', 64)->nullable()->unique();
            $table->string('keputusan', 100)->nullable();
        });
        Schema::table('siswa', function (Blueprint $table) {
            $table->string('nis', 30)->nullable()->unique();
            $table->string('agama', 50)->nullable();
            $table->text('alamat')->nullable();
            $table->date('tanggal_diterima')->nullable();
        });
        Schema::create('penugasan_ekstrakurikuler', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ekstrakurikuler_id')->constrained('ekstrakurikuler')->restrictOnDelete();
            $table->foreignId('guru_id')->constrained('guru')->restrictOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->restrictOnDelete();
            $table->string('jadwal', 150)->nullable();
            $table->timestamps();
            $table->unique(['ekstrakurikuler_id', 'tahun_ajaran_id'], 'ekskul_periode_unique');
        });
        Schema::table('tahun_ajaran', function (Blueprint $table) {
            $table->date('tgl_mulai')->nullable();
            $table->date('tgl_selesai')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penugasan_ekstrakurikuler');
        Schema::table('rapor', fn (Blueprint $table) => $table->dropColumn(['snapshot', 'share_token', 'keputusan']));
        Schema::table('siswa', fn (Blueprint $table) => $table->dropColumn(['nis', 'agama', 'alamat', 'tanggal_diterima']));
        Schema::table('tahun_ajaran', fn (Blueprint $table) => $table->dropColumn(['tgl_mulai', 'tgl_selesai']));
    }
};
