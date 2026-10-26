<?php

namespace Tests\Feature;

use App\Models\{Ekstrakurikuler, EkstrakurikulerSiswa, EvaluasiNonakademik, Guru, Kelas, KelasMataPelajaran, MataPelajaran, NilaiAsesmen, OrangTua, PenugasanSiswaKelas, PenugasanWaliKelas, Rapor, Siswa, TahunAjaran, User};
use App\Services\{LegerService, RaporService};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReportWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private User $wali;
    private User $kepala;
    private User $admin;
    private TahunAjaran $year;
    private Kelas $kelas;
    private Siswa $student;
    private Siswa $outsider;
    private MataPelajaran $subject;
    private Rapor $report;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        $this->year = TahunAjaran::create(['tahun'=>'2026/2027','semester'=>'Ganjil','is_aktif'=>true]);
        $guru = Guru::create(['nama_lengkap'=>'Guru Penguji','jenis_kelamin'=>'P']);
        $this->wali = User::factory()->create(['role'=>'wali_kelas','guru_id'=>$guru->id,'is_aktif'=>true]);
        $this->kepala = User::factory()->create(['role'=>'kepala_sekolah','is_aktif'=>true]);
        $this->admin = User::factory()->create(['role'=>'admin','is_aktif'=>true]);
        $this->kelas = Kelas::create(['nama'=>'Kelas A','kode'=>'A','kekhususan'=>'Tunarungu']);
        $other = Kelas::create(['nama'=>'Kelas B','kode'=>'B','kekhususan'=>'Tunarungu']);
        PenugasanWaliKelas::create(['guru_id'=>$guru->id,'kelas_id'=>$this->kelas->id,'tahun_ajaran_id'=>$this->year->id]);
        $this->student = Siswa::create(['nama_lengkap'=>'Siswa Pengujian','nisn'=>'1234567890','jenis_kelamin'=>'L','kekhususan'=>'Tunarungu']);
        $this->outsider = Siswa::create(['nama_lengkap'=>'Siswa Kelas Lain','nisn'=>'1234567891','jenis_kelamin'=>'L','kekhususan'=>'Tunarungu']);
        foreach ([[$this->student,$this->kelas],[$this->outsider,$other]] as [$s,$k]) {
            PenugasanSiswaKelas::create(['siswa_id'=>$s->id,'kelas_id'=>$k->id,'tahun_ajaran_id'=>$this->year->id,'tingkat'=>'VIII','fase'=>'D']);
        }
        $this->subject = MataPelajaran::create(['nama'=>'Matematika','kode'=>'MAT','kelompok'=>'Umum','is_aktif'=>true]);
        KelasMataPelajaran::create(['kelas_id'=>$this->kelas->id,'mata_pelajaran_id'=>$this->subject->id,'tahun_ajaran_id'=>$this->year->id]);
        NilaiAsesmen::create(['siswa_id'=>$this->student->id,'tahun_ajaran_id'=>$this->year->id,'mata_pelajaran_id'=>$this->subject->id,'nilai_akhir'=>37,'narasi_capaian'=>'Narasi unik pengujian dari database.']);
        EvaluasiNonakademik::create(['siswa_id'=>$this->student->id,'tahun_ajaran_id'=>$this->year->id,'sakit'=>2,'izin'=>1,'alpa'=>0,'perkembangan'=>'Perkembangan individual tersimpan.','tidak_ada_prestasi'=>true]);
        $this->report = Rapor::create(['siswa_id'=>$this->student->id,'tahun_ajaran_id'=>$this->year->id,'wali_kelas_id'=>$guru->id,'catatan_wali_kelas'=>'Catatan individual pengujian.','tgl_rapor'=>'2026-12-19','status'=>'draft']);
        OrangTua::create(['siswa_id'=>$this->student->id,'nama'=>'Wali Pengujian','hubungan'=>'Ibu','no_whatsapp'=>'081234567890','is_penerima_utama'=>true]);
    }

    private function scorePayload(?int $value = 41): array
    {
        return ['kelas_id'=>$this->kelas->id,'mapel_id'=>$this->subject->id,'nilai'=>[['siswa_id'=>$this->student->id,'nilai_akhir'=>$value,'narasi_capaian'=>$value === null ? null : 'Narasi hasil pembaruan.']]];
    }

    public function test_wali_only_sees_assigned_students(): void
    {
        $this->actingAs($this->wali)->get(route('wali.rapor.index'))->assertOk()->assertSee('Siswa Pengujian')->assertDontSee('Siswa Kelas Lain');
        $this->get(route('wali.rapor.show',$this->outsider->id))->assertForbidden();
        $payload=$this->scorePayload();$payload['nilai'][0]['siswa_id']=$this->outsider->id;
        $this->post(route('wali.asesmen.store'),$payload)->assertForbidden();
    }

    public function test_draft_can_be_partial_but_cannot_be_submitted(): void
    {
        $this->actingAs($this->wali)->post(route('wali.asesmen.store'),$this->scorePayload(null))->assertRedirect()->assertSessionHasNoErrors();
        $this->assertDatabaseHas('nilai_asesmen',['siswa_id'=>$this->student->id,'nilai_akhir'=>null]);
        $this->post(route('wali.rapor.submit',$this->student->id))->assertSessionHasErrors('kelengkapan');
        $this->assertSame('draft',$this->report->fresh()->status);
    }

    public function test_complete_submission_locks_and_return_unlocks(): void
    {
        $this->actingAs($this->wali)->post(route('wali.rapor.submit',$this->student->id))->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame('diajukan',$this->report->fresh()->status);
        $this->post(route('wali.asesmen.store'),$this->scorePayload())->assertStatus(422);
        $this->actingAs($this->kepala)->post(route('kepala.persetujuan.reject',$this->report->id),['catatan_revisi'=>'Perbaiki narasi individual.'])->assertRedirect();
        $this->actingAs($this->wali)->post(route('wali.asesmen.store'),$this->scorePayload())->assertRedirect()->assertSessionHasNoErrors();
        $this->assertDatabaseHas('nilai_asesmen',['siswa_id'=>$this->student->id,'nilai_akhir'=>41,'is_locked'=>false]);
    }

    public function test_approval_requires_submission_and_creates_immutable_pdf(): void
    {
        $this->actingAs($this->kepala)->post(route('kepala.persetujuan.approve',$this->report->id))->assertStatus(422);
        $this->actingAs($this->wali)->post(route('wali.rapor.submit',$this->student->id))->assertRedirect();
        $this->actingAs($this->kepala)->post(route('kepala.persetujuan.approve',$this->report->id))->assertRedirect()->assertSessionHasNoErrors();
        $report=$this->report->fresh();
        $this->assertSame(37,$report->snapshot['nilai'][0]['nilai']);
        Storage::disk('local')->assertExists($report->pdf_path);
        $this->assertStringStartsWith('%PDF',Storage::disk('local')->get($report->pdf_path));
        $this->subject->update(['nama'=>'Nama baru sesudah final']);
        $this->assertSame('Matematika',app(RaporService::class)->data($this->student,$this->year,$report)['nilai'][0]['nama']);
        auth()->logout();
        $this->get(route('reports.shared',$report->share_token))->assertOk()->assertHeader('content-type','application/pdf');
        $this->get(route('reports.shared',str_repeat('x',64)))->assertNotFound();
        $this->actingAs($this->wali)->post(route('wali.rapor.submit',$this->student->id))->assertStatus(422);
    }

    public function test_print_review_and_leger_use_stored_values(): void
    {
        $this->actingAs($this->wali)->get(route('wali.rapor.cetak',$this->student->id))->assertOk()->assertSee('Narasi unik pengujian dari database.')->assertSee('37');
        $this->actingAs($this->kepala)->get(route('reports.show',$this->report->id))->assertOk()->assertSee('Narasi unik pengujian dari database.');
        $response=$this->get(route('kepala.leger.index'));
        $response->assertOk()->assertSee('37,00')->assertSee('Belum ditetapkan');
    }

    public function test_historical_report_keeps_its_period(): void
    {
        $this->year->update(['is_aktif'=>false]);
        TahunAjaran::create(['tahun'=>'2026/2027','semester'=>'Genap','is_aktif'=>true]);
        $this->actingAs($this->wali)->get(route('reports.cetak',$this->report->id))->assertOk()->assertSee('Ganjil')->assertSee('Narasi unik pengujian');
        $this->get(route('wali.arsip.index',['tahun_ajaran_id'=>$this->year->id]))->assertOk()->assertSee('Siswa Pengujian');
        $this->get(route('wali.rapor.cetak',['id'=>$this->student->id,'tahun_ajaran_id'=>$this->year->id]))->assertOk()->assertSee('Ganjil');
    }

    public function test_exports_contain_actual_values(): void
    {
        $this->actingAs($this->kepala)->get(route('kepala.leger.export',['format'=>'pdf']))->assertOk()->assertHeader('content-type','application/pdf');
        $response=$this->get(route('kepala.leger.export',['format'=>'xlsx']));
        $response->assertOk()->assertHeader('content-type','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->assertStringStartsWith('PK',$response->getContent());
        $path = tempnam(sys_get_temp_dir(), 'leger-test-');
        try {
            file_put_contents($path, $response->getContent());
            $book = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
            $sheet = $book->getActiveSheet();
            $this->assertSame('1234567890', $sheet->getCell('B3')->getValue());
            $this->assertEquals(37, $sheet->getCell('F3')->getValue());
            $this->assertEquals(37, $sheet->getCell('G3')->getValue());
            $this->assertSame('Belum ditetapkan', $sheet->getCell('H3')->getValue());
            $book->disconnectWorksheets();
        } finally {
            unlink($path);
        }
        $this->get(route('kepala.leger.export',['format'=>'pdf','minimum'=>90]))->assertRedirect()->assertSessionHas('error');
    }

    public function test_evaluation_saves_achievements_and_extracurricular_grades(): void
    {
        $activity=Ekstrakurikuler::create(['nama'=>'Pramuka']);
        EkstrakurikulerSiswa::create(['siswa_id'=>$this->student->id,'ekstrakurikuler_id'=>$activity->id,'tahun_ajaran_id'=>$this->year->id]);
        $this->actingAs($this->wali)->post(route('wali.nonakademik.store'),[
            'siswa_id'=>$this->student->id,'sakit'=>0,'izin'=>0,'alpa'=>0,'perkembangan'=>'Sehat dan berkembang.',
            'catatan_wali'=>'Catatan terbaru.','tgl_rapor'=>'2026-12-20','prestasi'=>[['kegiatan'=>'Lomba seni','keterangan'=>'Peserta']],
            'ekskul'=>[$activity->id=>'Aktif dan disiplin.'],
        ])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertDatabaseHas('prestasi_siswa',['kegiatan'=>'Lomba seni','siswa_id'=>$this->student->id]);
        $this->assertDatabaseHas('ekstrakurikuler_siswa',['keterangan'=>'Aktif dan disiplin.']);
        $this->get(route('wali.nonakademik.index',['siswa_id'=>$this->student->id]))->assertOk()->assertSee('Lomba seni');
    }

    public function test_admin_cannot_delete_assigned_teacher(): void
    {
        $this->actingAs($this->admin)->delete(route('admin.guru.destroy',$this->wali->guru_id))->assertRedirect()->assertSessionHas('error');
        $this->assertNotSoftDeleted('guru',['id'=>$this->wali->guru_id]);
    }

    public function test_distribution_requires_final_report_and_keeps_each_attempt(): void
    {
        $this->actingAs($this->wali)->post(route('wali.distribusi.mark-sent',$this->report->id),['status_kirim'=>'terkirim'])->assertStatus(422);
        $this->post(route('wali.rapor.submit',$this->student->id));
        $this->actingAs($this->kepala)->post(route('kepala.persetujuan.approve',$this->report->id))->assertRedirect();
        $this->actingAs($this->wali)->post(route('wali.distribusi.mark-sent',$this->report->id),['status_kirim'=>'gagal'])->assertRedirect();
        $this->post(route('wali.distribusi.mark-sent',$this->report->id),['status_kirim'=>'terkirim'])->assertRedirect();
        $this->assertDatabaseCount('distribusi_rapor',2);
        $this->get(route('wali.distribusi.index'))->assertOk()->assertSee('/rapor-penerima/',false);
    }

    public function test_relevant_pages_render_for_each_role(): void
    {
        foreach ([
            [$this->wali,['wali.dashboard','wali.asesmen.index','wali.nonakademik.index','wali.rapor.index','wali.arsip.index','wali.distribusi.index']],
            [$this->kepala,['kepala.dashboard','kepala.persetujuan.index','kepala.leger.index','kepala.arsip.index']],
            [$this->admin,['admin.dashboard','admin.penugasan.index','admin.ekskul.penugasan','admin.tahun-ajaran.index','admin.sekolah.index','admin.akun.index','admin.arsip.index','admin.siswa.create']],
        ] as [$user,$routes]) {
            $this->actingAs($user);
            foreach ($routes as $route) $this->get(route($route))->assertOk();
        }
        $this->actingAs($this->wali)->get(route('wali.asesmen.index',['kelas_id'=>$this->kelas->id,'mapel_id'=>$this->subject->id]))->assertOk();
        $this->actingAs($this->admin)->get(route('admin.akun.edit',$this->wali->id))->assertOk();
    }

    private function assignmentPayload(): array
    {
        return ['kelas_id'=>$this->kelas->id, 'guru_id'=>$this->wali->guru_id,
            'siswa'=>[$this->student->id, $this->outsider->id],
            'tingkat'=>[$this->student->id=>'VIII', $this->outsider->id=>'IX'],
            'fase'=>[$this->student->id=>'D', $this->outsider->id=>'D'], 'mapel'=>[$this->subject->id]];
    }

    public function test_assignment_requires_confirmation_and_preserves_individual_grades(): void
    {
        $payload = $this->assignmentPayload();
        $this->actingAs($this->admin)->post(route('admin.penugasan.store'), $payload)->assertSessionHasErrors('konfirmasi_pindah');
        $this->assertDatabaseMissing('penugasan_siswa_kelas', ['siswa_id'=>$this->outsider->id, 'kelas_id'=>$this->kelas->id]);
        $payload['konfirmasi_pindah'] = '1';
        $this->post(route('admin.penugasan.store'), $payload)->assertSessionHasNoErrors();
        foreach ([[$this->student,'VIII'],[$this->outsider,'IX']] as [$student,$grade]) {
            $this->assertDatabaseHas('penugasan_siswa_kelas', ['siswa_id'=>$student->id,'kelas_id'=>$this->kelas->id,'tingkat'=>$grade,'fase'=>'D']);
        }
    }

    public function test_teacher_conflicts_and_locked_assignments_are_rejected(): void
    {
        $payload = $this->assignmentPayload();
        $payload['kelas_id'] = $this->outsider->penugasanAktif()->kelas_id;
        $this->actingAs($this->admin)->post(route('admin.penugasan.store'), $payload)->assertSessionHasErrors('guru_id');
        $this->actingAs($this->wali)->post(route('wali.rapor.submit', $this->student->id))->assertSessionHasNoErrors();
        $this->actingAs($this->admin)->post(route('admin.penugasan.store'), $this->assignmentPayload())->assertSessionHasErrors('siswa');
        $this->assertDatabaseHas('penugasan_siswa_kelas', ['siswa_id'=>$this->student->id,'tingkat'=>'VIII']);
    }

    public function test_maximum_filter_can_be_used_without_minimum(): void
    {
        $this->actingAs($this->kepala)->get(route('kepala.leger.index', ['maksimum'=>40]))
            ->assertOk()->assertSee('Siswa Pengujian')->assertDontSee('Siswa Kelas Lain');
        $this->get(route('kepala.leger.index', ['minimum'=>50,'maksimum'=>40]))->assertSessionHasErrors('maksimum');
    }

    public function test_deactivating_student_keeps_report_history(): void
    {
        $this->actingAs($this->admin)->get(route('admin.siswa.index'))->assertOk()->assertSee('Riwayat siswa dan rapor tetap tersimpan.');
        $this->delete(route('admin.siswa.destroy', $this->student->id))->assertRedirect();
        $this->assertDatabaseHas('siswa', ['id'=>$this->student->id,'is_aktif'=>false,'deleted_at'=>null]);
        $this->get(route('admin.siswa.show', $this->student->id))->assertOk();
        $this->get(route('reports.show', $this->report->id))->assertOk()->assertSee('Narasi unik pengujian');
    }

    public function test_legacy_return_only_allows_active_reports_without_final_snapshot(): void
    {
        $this->report->update(['status'=>'disetujui']);
        $this->actingAs($this->kepala)->post(route('kepala.persetujuan.return-legacy',$this->report->id), ['catatan_revisi'=>'Lengkapi data rapor lama.'])->assertSessionHasNoErrors();
        $this->assertSame('dikembalikan',$this->report->fresh()->status);
        $this->actingAs($this->wali)->post(route('wali.rapor.submit',$this->student->id))->assertSessionHasNoErrors();
        $this->actingAs($this->kepala)->post(route('kepala.persetujuan.approve',$this->report->id))->assertSessionHasNoErrors();
        $this->post(route('kepala.persetujuan.return-legacy',$this->report->id), ['catatan_revisi'=>'Tidak boleh mengubah final.'])->assertStatus(422);
        $this->assertSame('disetujui',$this->report->fresh()->status);
        $this->report->update(['snapshot'=>null]);
        $this->year->update(['is_aktif'=>false]);
        $this->post(route('kepala.persetujuan.return-legacy',$this->report->id), ['catatan_revisi'=>'Periode harus aktif dahulu.'])->assertStatus(422);
    }

    public function test_shared_back_navigation_uses_role_and_page_context(): void
    {
        foreach ([
            [$this->admin, 'admin.dashboard', 'admin.arsip.index'],
            [$this->wali, 'wali.dashboard', 'wali.arsip.index'],
            [$this->kepala, 'kepala.dashboard', 'kepala.arsip.index'],
        ] as [$user, $dashboard, $archive]) {
            $this->actingAs($user)->get(route($dashboard))->assertOk()->assertDontSee('aria-label="Kembali ke', false);
            $this->get(route($archive))->assertOk()->assertSee('aria-label="Kembali ke Beranda"', false)
                ->assertSee('href="'.route($dashboard).'"', false);
            $this->get(route('reports.show', ['id'=>$this->report->id, 'from'=>'arsip', 'tahun_ajaran_id'=>$this->year->id]))
                ->assertOk()->assertSee('aria-label="Kembali ke Arsip"', false)
                ->assertSee(route($archive, ['tahun_ajaran_id'=>$this->year->id]), false);
        }
        $this->actingAs($this->admin)->get(route('admin.siswa.edit', $this->student->id))
            ->assertOk()->assertSee('aria-label="Kembali ke Data siswa"', false);
        $this->actingAs($this->wali)->get(route('wali.rapor.show', $this->student->id))
            ->assertOk()->assertSee('aria-label="Kembali ke Rapor saya"', false);
        $this->actingAs($this->kepala)->get(route('reports.show', $this->report->id))
            ->assertOk()->assertSee('aria-label="Kembali ke Persetujuan rapor"', false);
    }
}
