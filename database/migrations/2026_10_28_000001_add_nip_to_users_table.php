<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nip', 30)->nullable()->unique();
        });

        DB::table('users')->whereNotNull('guru_id')->get(['id', 'guru_id'])->each(function ($user) {
            $nip = DB::table('guru')->where('id', $user->guru_id)->value('nip');
            if ($nip && ! DB::table('users')->where('nip', $nip)->exists()) {
                DB::table('users')->where('id', $user->id)->update(['nip' => $nip]);
            }
        });

        foreach (['admin@slbinsanmutiara.sch.id' => '198702122010011001',
            'nonaktif@slbinsanmutiara.sch.id' => '199909092020012001'] as $email => $nip) {
            if (! DB::table('users')->where('nip', $nip)->exists()) {
                DB::table('users')->where('email', $email)->whereNull('nip')->update(['nip' => $nip]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('nip'));
    }
};
