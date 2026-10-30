<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLES = ['penugasan_siswa_kelas', 'kelas_mata_pelajaran', 'ekstrakurikuler_siswa', 'prestasi_siswa'];

    public function up(): void
    {
        foreach (self::TABLES as $table) Schema::table($table, fn (Blueprint $blueprint) => $blueprint->softDeletes());
    }

    public function down(): void
    {
        foreach (self::TABLES as $table) Schema::table($table, fn (Blueprint $blueprint) => $blueprint->dropSoftDeletes());
    }
};
