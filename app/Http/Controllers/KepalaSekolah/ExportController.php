<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\{Kelas, MataPelajaran, Sekolah, TahunAjaran};
use App\Services\LegerService;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
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
            $lastColumn = Coordinate::stringFromColumnIndex(count($headers));
            $sheet->mergeCells("A1:{$lastColumn}1");
            $sheet->mergeCells("A2:{$lastColumn}2");
            $sheet->mergeCells("A3:{$lastColumn}3");
            $sheet->setCellValue('A1', strtoupper(Sekolah::instance()->nama));
            $sheet->setCellValue('A2', 'LEGER / REKAP NILAI · '.$result['periode']->label);
            $sheet->setCellValue('A3', 'Ketuntasan: '.(config('rapor.nilai_tuntas') === null ? 'belum ditetapkan oleh sekolah' : 'nilai minimum '.config('rapor.nilai_tuntas')).' · '.$result['rows']->count().' siswa');
            $sheet->getStyle("A1:{$lastColumn}1")->getFont()->setBold(true)->setSize(15)->getColor()->setRGB('33385F');
            $sheet->getStyle("A2:{$lastColumn}2")->getFont()->setBold(true)->setSize(11);
            $sheet->getStyle("A3:{$lastColumn}3")->getFont()->setSize(9)->getColor()->setRGB('666666');
            $sheet->getRowDimension(1)->setRowHeight(30);
            $sheet->getRowDimension(2)->setRowHeight(24);
            $sheet->getRowDimension(3)->setRowHeight(21);
            foreach ($headers as $col => $header) {
                $sheet->setCellValueExplicit([$col + 1, 4], $header, DataType::TYPE_STRING);
                $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($col + 1))->setWidth(match ($col) { 0 => 28, 1 => 16, 2 => 22, 3 => 17, 4 => 23, default => 20 });
            }
            $sheet->getStyle("A4:{$lastColumn}4")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '555783']],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            ]);
            $sheet->getRowDimension(4)->setRowHeight(32);
            foreach ($result['rows'] as $index => $row) {
                $data = $row['data'];
                $values = [$data['siswa']['nama_lengkap'], $data['siswa']['nisn'], $data['kelas'], $data['tingkat'].' / '.$data['fase'], $data['tahun'].' '.$data['semester']];
                foreach ($result['subjects'] as $subject) {
                    $values[] = collect($data['nilai'])->firstWhere('id', $subject['id'])['nilai'] ?? null;
                }
                $values[] = $row['rata'] !== null ? round($row['rata'], 2) : null;
                $values[] = $row['status'];
                foreach ($values as $col => $value) {
                    $sheet->setCellValueExplicit([$col + 1, $index + 5], $value ?? '', is_int($value) || is_float($value) ? DataType::TYPE_NUMERIC : DataType::TYPE_STRING);
                    if ($col >= 5 && (is_int($value) || is_float($value))) {
                        $format = (float) $value === floor((float) $value) ? '0' : '0.00';
                        $cell = Coordinate::stringFromColumnIndex($col + 1).($index + 5);
                        $sheet->getStyle($cell)->getNumberFormat()->setFormatCode($format);
                    }
                }
                if ($index % 2 === 1) $sheet->getStyle('A'.($index + 5).':'.$lastColumn.($index + 5))->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F7F6F2');
            }
            $lastRow = $result['rows']->count() + 4;
            $sheet->getStyle("A4:{$lastColumn}{$lastRow}")->getBorders()->getBottom()->setBorderStyle(Border::BORDER_HAIR)->getColor()->setRGB('D8DAE4');
            $sheet->getStyle("A5:{$lastColumn}{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("B5:B{$lastRow}")->getNumberFormat()->setFormatCode('@');
            $sheet->freezePane('F5');
            $sheet->setAutoFilter("A4:{$lastColumn}{$lastRow}");
            $sheet->getPageSetup()->setOrientation('landscape')->setFitToWidth(1)->setFitToHeight(0);
            $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 4);
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
