<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rapor', function (Blueprint $table) {
            $table->timestamp('share_token_expires_at')->nullable();
        });

        // Existing recipient links remain usable for 30 days after deployment.
        DB::table('rapor')->whereNotNull('share_token')->where('status', 'disetujui')
            ->update(['share_token_expires_at' => now()->addDays(30)]);
    }

    public function down(): void
    {
        Schema::table('rapor', function (Blueprint $table) {
            $table->dropColumn('share_token_expires_at');
        });
    }
};
