<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Rapor per siswa per tahun ajaran
        Schema::create('rapor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->cascadeOnDelete();
            $table->foreignId('wali_kelas_id')->constrained('guru')->restrictOnDelete();
            $table->enum('status', ['draft', 'diajukan', 'dikembalikan', 'disetujui'])->default('draft');
            $table->text('catatan_revisi')->nullable(); // catatan kepala sekolah saat kembalikan
            $table->text('catatan_wali_kelas')->nullable(); // catatan dari wali kelas di rapor
            $table->date('tgl_rapor')->nullable(); // tanggal penandatanganan rapor
            $table->timestamp('tgl_pengajuan')->nullable();
            $table->timestamp('tgl_persetujuan')->nullable();
            $table->string('pdf_path')->nullable(); // path ke file PDF yang sudah disetujui
            $table->timestamps();

            $table->unique(['siswa_id', 'tahun_ajaran_id']);
        });

        // Nilai asesmen per siswa per mata pelajaran per tahun ajaran
        Schema::create('nilai_asesmen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajaran')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->cascadeOnDelete();
            $table->unsignedTinyInteger('nilai_akhir')->nullable(); // 0-100
            $table->text('narasi_capaian')->nullable();
            $table->boolean('is_locked')->default(false); // terkunci saat rapor diajukan
            $table->timestamps();

            $table->unique(['siswa_id', 'mata_pelajaran_id', 'tahun_ajaran_id']);
        });

        // Evaluasi non-akademik per siswa per tahun ajaran
        Schema::create('evaluasi_nonakademik', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->cascadeOnDelete();
            // Kehadiran
            $table->unsignedSmallInteger('sakit')->default(0);   // hari sakit
            $table->unsignedSmallInteger('izin')->default(0);    // hari izin
            $table->unsignedSmallInteger('alpa')->default(0);    // hari tanpa keterangan
            // Perkembangan fisik & kesehatan
            $table->text('perkembangan')->nullable();
            $table->boolean('tidak_ada_prestasi')->default(false); // flag "tidak ada prestasi"
            $table->boolean('is_locked')->default(false);
            $table->timestamps();

            $table->unique(['siswa_id', 'tahun_ajaran_id']);
        });

        // Prestasi siswa (0 atau lebih per semester)
        Schema::create('prestasi_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->cascadeOnDelete();
            $table->string('kegiatan', 200);
            $table->string('keterangan', 200)->nullable();
            $table->timestamps();
        });

        // Ekstrakurikuler siswa per tahun ajaran
        Schema::create('ekstrakurikuler_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->foreignId('ekstrakurikuler_id')->constrained('ekstrakurikuler')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->cascadeOnDelete();
            $table->string('keterangan', 200)->nullable(); // predikat atau catatan
            $table->timestamps();

            $table->unique(['siswa_id', 'ekstrakurikuler_id', 'tahun_ajaran_id'], 'ekskul_siswa_tahun_unique');
        });

        // Log distribusi rapor ke orang tua
        Schema::create('distribusi_rapor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rapor_id')->constrained('rapor')->cascadeOnDelete();
            $table->foreignId('orang_tua_id')->constrained('orang_tua')->cascadeOnDelete();
            $table->timestamp('tgl_kirim')->nullable();
            $table->enum('status_kirim', ['pending', 'terkirim', 'gagal'])->default('pending');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // Activity log untuk dashboard
        Schema::create('activity_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('subjek', 200); // deskripsi aktivitas
            $table->string('model_type', 100)->nullable(); // mis.: rapor, siswa
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('status', 50)->nullable(); // mis.: Diajukan, Disetujui, Aktif
            $table->timestamps();

            $table->index(['model_type', 'model_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_log');
        Schema::dropIfExists('distribusi_rapor');
        Schema::dropIfExists('ekstrakurikuler_siswa');
        Schema::dropIfExists('prestasi_siswa');
        Schema::dropIfExists('evaluasi_nonakademik');
        Schema::dropIfExists('nilai_asesmen');
        Schema::dropIfExists('rapor');
    }
};
