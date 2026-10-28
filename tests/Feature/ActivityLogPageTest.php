<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_and_filter_activity_log(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_aktif' => true]);
        ActivityLog::log('Login ke sistem', null, 'Sukses', $admin);
        ActivityLog::log('Menyimpan evaluasi Alya', null, 'Draft', $admin);

        $this->actingAs($admin)->get(route('admin.log.index'))
            ->assertOk()->assertSee('Log Aktivitas')->assertSee('Login ke sistem')->assertSee('Menyimpan evaluasi Alya');

        $this->actingAs($admin)->get(route('admin.log.index', ['search' => 'evaluasi']))
            ->assertOk()->assertSee('Menyimpan evaluasi Alya')->assertDontSee('Login ke sistem');

        $this->actingAs($admin)->get(route('admin.log.index', ['status' => 'Sukses']))
            ->assertOk()->assertSee('Login ke sistem')->assertDontSee('Menyimpan evaluasi Alya');
    }

    public function test_activity_log_is_admin_only(): void
    {
        foreach (['wali_kelas', 'kepala_sekolah'] as $role) {
            $user = User::factory()->create(['role' => $role, 'is_aktif' => true]);
            $this->actingAs($user)->get(route('admin.log.index'))->assertForbidden();
        }
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('admin.log.index'))->assertRedirect(route('login'));
    }
}
