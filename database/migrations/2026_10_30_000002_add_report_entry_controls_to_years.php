<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tahun_ajaran', function (Blueprint $table) {
            $table->date('batas_pengisian')->nullable();
            $table->timestamp('pengisian_ditutup_pada')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('tahun_ajaran', fn (Blueprint $table) => $table->dropColumn(['batas_pengisian', 'pengisian_ditutup_pada']));
    }
};
