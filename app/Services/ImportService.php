<?php
namespace App\Services;

use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportService
{
    public function importCandidates(string $filePath, int $electionId): array
    {
        $rows = $this->readFile($filePath);
        $results = ['success' => [], 'updated' => [], 'errors' => []];

        $candidate = new \App\Models\Candidate();
        $order = 0;

        foreach ($rows as $index => $row) {
            $rowNum = $index + 2; // +2 because index 0 = row 2 (row 1 is header)
            $order++;

            $fullName = trim($row[0] ?? '');
            $className = trim($row[1] ?? '');
            $studentId = trim($row[2] ?? '');
            $gpa = trim($row[3] ?? '');
            $conductScore = trim($row[4] ?? '');
            $bio = trim($row[5] ?? '');

            // Validate
            $errors = [];
            if (empty($fullName)) $errors[] = 'Họ và tên trống';
            if (empty($className)) $errors[] = 'Lớp trống';
            if (empty($studentId)) $errors[] = 'MSSV trống';
            if ($gpa !== '' && (!is_numeric($gpa) || $gpa < 0 || $gpa > 10)) {
                $errors[] = 'Điểm TB không hợp lệ (0-10)';
            }
            if ($conductScore !== '' && (!is_numeric($conductScore) || $conductScore < 0 || $conductScore > 100)) {
                $errors[] = 'Điểm rèn luyện không hợp lệ (0-100)';
            }

            if (!empty($errors)) {
                $results['errors'][] = ['row' => $rowNum, 'errors' => $errors];
                continue;
            }

            try {
                $rowCount = $candidate->upsert([
                    'election_id' => $electionId,
                    'full_name' => $fullName,
                    'class_name' => $className,
                    'student_id' => (string)$studentId,
                    'gpa' => $gpa !== '' ? (float)$gpa : null,
                    'conduct_score' => $conductScore !== '' ? (float)$conductScore : null,
                    'bio' => $bio !== '' ? $bio : null,
                    'display_order' => $order,
                ]);
                if ($rowCount === 1) {
                    $results['success'][] = $rowNum;
                } else {
                    $results['updated'][] = $rowNum;
                }
            } catch (\PDOException $e) {
                $results['errors'][] = ['row' => $rowNum, 'errors' => ['Lỗi lưu dữ liệu']];
            }
        }

        return $results;
    }

    public function importVoters(string $filePath, int $electionId): array
    {
        $rows = $this->readFile($filePath);
        $results = ['success' => [], 'updated' => [], 'errors' => []];

        $voter = new \App\Models\Voter();

        foreach ($rows as $index => $row) {
            $rowNum = $index + 2;
            $email = strtolower(trim($row[0] ?? ''));

            $errors = [];
            if (empty($email)) {
                $errors[] = 'Email trống';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Email không hợp lệ';
            } elseif (!str_ends_with($email, '@ntu.edu.vn')) {
                $errors[] = 'Email phải có đuôi @ntu.edu.vn';
            }

            if (!empty($errors)) {
                $results['errors'][] = ['row' => $rowNum, 'errors' => $errors];
                continue;
            }

            try {
                $rowCount = $voter->upsert([
                    'election_id' => $electionId,
                    'email' => $email,
                ]);
                if ($rowCount === 1) {
                    $results['success'][] = $rowNum;
                } else {
                    $results['updated'][] = $rowNum;
                }
            } catch (\PDOException $e) {
                $results['errors'][] = ['row' => $rowNum, 'errors' => ['Lỗi lưu dữ liệu']];
            }
        }

        return $results;
    }

    private function readFile(string $filePath): array
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if ($extension === 'csv') {
            return $this->readCsv($filePath);
        }

        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = [];

        foreach ($sheet->getRowIterator(2) as $row) { // Start from row 2 (skip header)
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            $rowData = [];
            foreach ($cellIterator as $cell) {
                $rowData[] = $cell->getValue();
            }
            // Skip empty rows
            if (array_filter($rowData, fn($v) => $v !== null && $v !== '')) {
                $rows[] = $rowData;
            }
        }

        return $rows;
    }

    private function readCsv(string $filePath): array
    {
        $rows = [];
        $handle = fopen($filePath, 'r');
        if ($handle === false) return [];

        // Detect BOM and skip
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $isFirst = true;
        while (($data = fgetcsv($handle)) !== false) {
            if ($isFirst) {
                $isFirst = false;
                continue; // Skip header
            }
            if (array_filter($data, fn($v) => $v !== null && $v !== '')) {
                $rows[] = $data;
            }
        }
        fclose($handle);

        return $rows;
    }

    public function validateUpload(array $file): ?string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return 'Lỗi upload file.';
        }

        $maxSize = (int)($_ENV['UPLOAD_MAX_SIZE'] ?? 5242880);
        if ($file['size'] > $maxSize) {
            return 'File quá lớn (tối đa ' . round($maxSize / 1024 / 1024, 1) . 'MB).';
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = explode(',', $_ENV['UPLOAD_ALLOWED_EXTENSIONS'] ?? 'xlsx,xls,csv');
        if (!in_array($extension, $allowed)) {
            return 'Định dạng file không hợp lệ. Chỉ chấp nhận: ' . implode(', ', $allowed);
        }

        return null;
    }

    public function saveUpload(array $file): string
    {
        $uploadDir = dirname(__DIR__, 2) . '/public/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = bin2hex(random_bytes(16)) . '.' . $extension;
        $filepath = $uploadDir . $filename;
        move_uploaded_file($file['tmp_name'], $filepath);
        return $filepath;
    }
}
