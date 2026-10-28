<?php

namespace Tests\Feature;

use App\Models\{DistribusiRapor, KelasMataPelajaran, PenugasanSiswaKelas, PenugasanWaliKelas, Rapor, Siswa, TahunAjaran, User};
use Database\Seeders\NaturalSchoolDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NaturalSchoolDataSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_each_period_has_natural_student_records_and_all_report_states(): void
    {
        Storage::fake('local');
        $this->seed();

        foreach (TahunAjaran::all() as $period) {
            $this->assertSame(10, PenugasanSiswaKelas::where('tahun_ajaran_id', $period->id)
                ->whereHas('siswa', fn ($query) => $query->whereIn('nisn', $this->sampleNisns()))->count());
            $this->assertSame(2, PenugasanWaliKelas::where('tahun_ajaran_id', $period->id)->count());
            $this->assertGreaterThan(0, KelasMataPelajaran::where('tahun_ajaran_id', $period->id)->count());
            foreach (['Tunarungu', 'Tunagrahita'] as $specialty) {
                foreach (['draft', 'dikembalikan', 'diajukan', 'disetujui'] as $status) {
                    $this->assertTrue(Rapor::where('tahun_ajaran_id', $period->id)->where('status', $status)
                        ->whereHas('siswa', fn ($query) => $query->where('kekhususan', $specialty)
                            ->whereIn('nisn', $this->sampleNisns()))->exists(), "$period->label: $specialty / $status");
                }
            }
        }
        $this->assertFalse(Siswa::where('nama_lengkap', 'like', 'Demo %')->exists());
        $this->assertTrue(Siswa::where('nisn', '0160003013')->doesntHave('penugasanKelas')->exists());
        $this->assertTrue(Siswa::where('nisn', '0150003014')->where('is_aktif', false)->exists());
        foreach (['pending', 'terkirim', 'gagal'] as $status) {
            $this->assertTrue(DistribusiRapor::where('status_kirim', $status)->exists());
        }
        foreach (Rapor::where('status', 'disetujui')->whereHas('siswa',
            fn ($query) => $query->whereIn('nisn', $this->sampleNisns()))->get() as $report) {
            $this->assertNotEmpty($report->snapshot);
            Storage::disk('local')->assertExists($report->pdf_path);
        }
    }

    public function test_seeder_is_repeatable_and_fills_a_new_period(): void
    {
        Storage::fake('local');
        $this->seed();
        $count = Siswa::count();
        $this->seed(NaturalSchoolDataSeeder::class);
        $this->assertSame($count, Siswa::count());

        TahunAjaran::query()->update(['is_aktif' => false]);
        $new = TahunAjaran::create(['tahun' => '2027/2028', 'semester' => 'Genap', 'is_aktif' => true]);
        $this->seed(NaturalSchoolDataSeeder::class);
        $this->assertSame(10, Rapor::where('tahun_ajaran_id', $new->id)
            ->whereHas('siswa', fn ($query) => $query->whereIn('nisn', $this->sampleNisns()))->count());
        $this->assertSame($count, Siswa::count());
    }

    public function test_all_seeded_accounts_sign_in_with_nip_only(): void
    {
        Storage::fake('local');
        config()->set('rapor.seed_account_password', 'RahasiaDemoSekolah2026!');
        $this->seed();
        $this->assertSame(4, User::count());
        $this->assertSame(2, User::where('role', 'wali_kelas')->count());
        foreach (['admin', 'kepala_sekolah', 'wali_kelas'] as $role) {
            $user = User::where('role', $role)->where('is_aktif', true)->firstOrFail();
            $this->assertMatchesRegularExpression('/^\d{18}$/', $user->nip);
            $this->post('/login', ['nip' => $user->nip, 'password' => 'RahasiaDemoSekolah2026!'])
                ->assertRedirect(route($user->getDashboardRoute()));
            $this->post('/logout')->assertRedirect(route('login'));
        }
        $waliKedua = User::where('nip', '199107242015021002')->firstOrFail();
        $this->post('/login', ['nip' => $waliKedua->nip, 'password' => 'RahasiaDemoSekolah2026!'])
            ->assertRedirect(route($waliKedua->getDashboardRoute()));
    }

    private function sampleNisns(): array
    {
        return ['0160003001', '0150003002', '0140003003', '0130003004',
            '0160003005', '0150003006', '0140003007', '0130003008',
            '0120003015', '0120003016'];
    }
}
