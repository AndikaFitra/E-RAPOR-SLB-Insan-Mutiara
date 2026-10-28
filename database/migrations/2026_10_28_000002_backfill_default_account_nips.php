<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            'admin@slbinsanmutiara.sch.id' => '198702122010011001',
            'nonaktif@slbinsanmutiara.sch.id' => '199909092020012001',
        ] as $email => $nip) {
            if (!DB::table('users')->where('nip', $nip)->exists()) {
                DB::table('users')->where('email', $email)->whereNull('nip')->update(['nip' => $nip]);
            }
        }
    }

    public function down(): void
    {
        // NIP akun tidak dihapus saat rollback agar akses akun tetap terjaga.
    }
};
