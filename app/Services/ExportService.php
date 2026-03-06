<?php
namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExportService
{
    public function downloadCandidateTemplate(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Danh sách tiến cử');

        // Headers
        $headers = ['Họ và tên', 'Lớp', 'MSSV', 'Điểm TB tích luỹ', 'Điểm rèn luyện tích luỹ', 'Tóm tắt thông tin cá nhân'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }

        // Sample data
        $sheet->setCellValue('A2', 'Nguyễn Văn A');
        $sheet->setCellValue('B2', '62CNTT1');
        $sheet->setCellValue('C2', '62130001');
        $sheet->setCellValue('D2', 8.25);
        $sheet->setCellValue('E2', 85.5);
        $sheet->setCellValue('F2', 'UV BCH Đoàn khoa khoá trước');

        // Style header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E40AF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);

        // Auto width
        foreach (range('A', 'F') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        $this->downloadXlsx($spreadsheet, 'mau_danh_sach_tien_cu.xlsx');
    }

    public function downloadVoterTemplate(): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Danh sách cử tri');

        $sheet->setCellValue('A1', 'Email');
        $sheet->setCellValue('A2', '62130001@ntu.edu.vn');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E40AF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $sheet->getStyle('A1')->applyFromArray($headerStyle);
        $sheet->getColumnDimension('A')->setAutoSize(true);

        $this->downloadXlsx($spreadsheet, 'mau_danh_sach_cu_tri.xlsx');
    }

    public function exportResults(array $election, array $candidates, int $totalVoted, int $totalVoters): void
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Kết quả bình chọn');

        // Title
        $sheet->setCellValue('A1', 'KẾT QUẢ BÌNH CHỌN');
        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A2', $election['title']);
        $sheet->mergeCells('A2:F2');
        $sheet->setCellValue('A3', 'Tổng phiếu: ' . $totalVoted . '/' . $totalVoters);
        $sheet->mergeCells('A3:F3');

        // Headers row 5
        $headers = ['STT', 'Họ và tên', 'Lớp', 'MSSV', 'Số phiếu', 'Tỉ lệ (%)'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '5', $header);
            $col++;
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E40AF']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $sheet->getStyle('A5:F5')->applyFromArray($headerStyle);

        // Data
        $row = 6;
        $stt = 1;
        foreach ($candidates as $c) {
            $percentage = $totalVoted > 0 ? round(($c['vote_count'] / $totalVoted) * 100, 1) : 0;
            $sheet->setCellValue('A' . $row, $stt);
            $sheet->setCellValue('B' . $row, $c['full_name']);
            $sheet->setCellValue('C' . $row, $c['class_name']);
            $sheet->setCellValue('D' . $row, $c['student_id']);
            $sheet->setCellValue('E' . $row, $c['vote_count']);
            $sheet->setCellValue('F' . $row, $percentage . '%');
            $stt++;
            $row++;
        }

        foreach (range('A', 'F') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        $filename = 'ket_qua_' . preg_replace('/[^a-zA-Z0-9]/', '_', $election['title']) . '.xlsx';
        $this->downloadXlsx($spreadsheet, $filename);
    }

    private function downloadXlsx(Spreadsheet $spreadsheet, string $filename): void
    {
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
