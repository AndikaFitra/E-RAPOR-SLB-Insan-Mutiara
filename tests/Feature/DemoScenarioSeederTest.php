<?php

namespace Tests\Feature;

use App\Models\DistribusiRapor;
use App\Models\KelasMataPelajaran;
use App\Models\PenugasanSiswaKelas;
use App\Models\PenugasanWaliKelas;
use App\Models\Rapor;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use Database\Seeders\DemoScenarioSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DemoScenarioSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_scenarios_cover_all_roles_and_remain_stable_on_rerun(): void
    {
        Storage::fake('local');
        $this->seed();

        foreach (['admin', 'kepala_sekolah', 'wali_kelas'] as $role) {
            $this->assertTrue(User::where('role', $role)->where('is_aktif', true)->exists());
        }
        $activeYear = TahunAjaran::getAktif();
        $this->assertNotNull($activeYear);
        foreach (['Tunarungu', 'Tunagrahita', 'Autis'] as $kekhususan) {
            $reports = Rapor::where('tahun_ajaran_id', $activeYear->id)
                ->whereHas('siswa', fn ($query) => $query->where('kekhususan', $kekhususan)->where('nama_lengkap', 'like', 'Demo %'));
            foreach (['draft', 'dikembalikan', 'diajukan', 'disetujui'] as $status) {
                $this->assertTrue((clone $reports)->where('status', $status)->exists(), "$kekhususan: $status");
            }
        }
        $approved = Rapor::where('status', 'disetujui')->whereHas('siswa',
            fn ($query) => $query->where('nisn', '0999001004'))->get();
        $this->assertCount(2, $approved);
        foreach ($approved as $report) {
            $this->assertNotEmpty($report->snapshot);
            Storage::disk('local')->assertExists($report->pdf_path);
        }
        foreach (['pending', 'terkirim', 'gagal'] as $status) {
            $this->assertTrue(DistribusiRapor::where('status_kirim', $status)
                ->whereHas('rapor.siswa', fn ($query) => $query->where('nama_lengkap', 'like', 'Demo %'))->exists());
        }
        $this->assertTrue(Siswa::where('nisn', '0999001013')->doesntHave('penugasanKelas')->exists());
        $this->assertTrue(Siswa::where('nisn', '0999001014')->where('is_aktif', false)->exists());

        $count = Siswa::where('nama_lengkap', 'like', 'Demo %')->count();
        $this->seed(DemoScenarioSeeder::class);
        $this->assertSame($count, Siswa::where('nama_lengkap', 'like', 'Demo %')->count());
        $this->assertCount(2, Rapor::whereHas('siswa', fn ($query) => $query->where('nisn', '0999001004'))->get());
    }

    public function test_demo_scenarios_follow_the_current_active_period(): void
    {
        Storage::fake('local');
        $this->seed();
        $old = TahunAjaran::getAktif();
        $old->update(['is_aktif' => false]);
        $new = TahunAjaran::create(['tahun' => '2027/2028', 'semester' => 'Genap', 'is_aktif' => true]);

        $this->seed(DemoScenarioSeeder::class);

        $this->assertSame(12, PenugasanSiswaKelas::where('tahun_ajaran_id', $new->id)
            ->whereHas('siswa', fn ($query) => $query->where('nama_lengkap', 'like', 'Demo %'))->count());
        $this->assertSame(3, PenugasanWaliKelas::where('tahun_ajaran_id', $new->id)->count());
        $this->assertGreaterThan(0, KelasMataPelajaran::where('tahun_ajaran_id', $new->id)->count());
        $this->assertSame(3, Rapor::where('tahun_ajaran_id', $new->id)->where('status', 'disetujui')
            ->whereHas('siswa', fn ($query) => $query->where('nama_lengkap', 'like', 'Demo %'))->count());
        $this->assertTrue(Rapor::where('tahun_ajaran_id', $old->id)
            ->whereHas('siswa', fn ($query) => $query->where('nisn', '0999001004'))->exists());
    }
}
