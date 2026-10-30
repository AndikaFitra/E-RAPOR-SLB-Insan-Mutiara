<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tahun_ajaran', fn (Blueprint $table) => $table->timestamp('ditutup_pada')->nullable());
        Schema::table('siswa', fn (Blueprint $table) => $table->unsignedBigInteger('lulus_pada_tahun_ajaran_id')->nullable());
    }

    public function down(): void
    {
        Schema::table('siswa', fn (Blueprint $table) => $table->dropColumn('lulus_pada_tahun_ajaran_id'));
        Schema::table('tahun_ajaran', fn (Blueprint $table) => $table->dropColumn('ditutup_pada'));
    }
};
