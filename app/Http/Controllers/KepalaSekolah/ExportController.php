<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\{Kelas, MataPelajaran, TahunAjaran};
use App\Services\LegerService;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportController extends Controller
{
    public function index(Request $request, LegerService $service)
    {
        return view('academic.leger', $service->result($request) + [
            'tahunList' => TahunAjaran::orderByDesc('tahun')->get(), 'kelasList' => Kelas::all(), 'mapelList' => MataPelajaran::all(),
        ]);
    }

    public function export(Request $request, LegerService $service)
    {
        $request->validate(['format' => 'required|in:pdf,xlsx']);
        $result = $service->result($request);
        if ($result['rows']->isEmpty()) {
            return back()->with('error', 'Tidak ada data untuk diekspor.');
        }
        try {
            if ($request->format === 'pdf') {
                $pdf = new Dompdf(new Options(['isRemoteEnabled' => false, 'defaultFont' => 'DejaVu Sans']));
                $pdf->loadHtml(view('reports.leger', $result)->render(), 'UTF-8');
                $pdf->setPaper('A4', 'landscape');
                $pdf->render();
                return response($pdf->output(), 200, ['Content-Type'=>'application/pdf', 'Content-Disposition'=>'attachment; filename="leger.pdf"']);
            }
            $book = new Spreadsheet();
            $sheet = $book->getActiveSheet();
            $sheet->setTitle('Leger');
            $headers = ['Siswa', 'NISN', 'Kelas', 'Tingkat / Fase', 'Periode', ...$result['subjects']->pluck('nama')->all(), 'Rata-rata', 'Status'];
            foreach ($headers as $col => $header) {
                $sheet->setCellValueExplicit([$col + 1, 1], $header, DataType::TYPE_STRING);
            }
            foreach ($result['rows'] as $index => $row) {
                $data = $row['data'];
                $values = [$data['siswa']['nama_lengkap'], $data['siswa']['nisn'], $data['kelas'], $data['tingkat'].' / '.$data['fase'], $data['tahun'].' '.$data['semester']];
                foreach ($result['subjects'] as $subject) {
                    $values[] = collect($data['nilai'])->firstWhere('id', $subject['id'])['nilai'] ?? null;
                }
                $values[] = $row['rata'] !== null ? round($row['rata'], 2) : null;
                $values[] = $row['status'];
                foreach ($values as $col => $value) {
                    $sheet->setCellValueExplicit([$col + 1, $index + 2], $value ?? '', is_int($value) || is_float($value) ? DataType::TYPE_NUMERIC : DataType::TYPE_STRING);
                }
            }
            $sheet->freezePane('F2');
            $sheet->setAutoFilter($sheet->calculateWorksheetDimension());
            // Render before returning the response so errors can be reported in the UI.
            ob_start();
            try {
                (new Xlsx($book))->save('php://output');
                $bytes = ob_get_contents();
            } finally {
                ob_end_clean();
                $book->disconnectWorksheets();
            }
            return response($bytes, 200, ['Content-Type'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'Content-Disposition'=>'attachment; filename="leger.xlsx"']);
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Ekspor belum berhasil. Silakan coba lagi.');
        }
    }
}
