<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Candidate;
use App\Models\Election;
use App\Services\ImportService;
use App\Services\ExportService;

class CandidateController extends Controller
{
    public function index(string $id): void
    {
        $election = (new Election())->find((int)$id);
        if (!$election) {
            $this->setFlash('error', 'Không tìm thấy cuộc bình chọn.');
            $this->redirect(\App\Config\App::baseUrl() . '/admin/elections');
            return;
        }

        $candidates = (new Candidate())->getByElection((int)$id);
        $this->view('admin.elections.candidates', [
            'election' => $election,
            'candidates' => $candidates,
            'user' => Auth::user(),
        ]);
    }

    public function import(string $id): void
    {
        $importService = new ImportService();

        $error = $importService->validateUpload($_FILES['file'] ?? []);
        if ($error) {
            $this->setFlash('error', $error);
            $this->redirect(\App\Config\App::baseUrl() . '/admin/elections/' . $id . '/candidates');
            return;
        }

        $filePath = $importService->saveUpload($_FILES['file']);

        try {
            $results = $importService->importCandidates($filePath, (int)$id);

            $msg = 'Import: ' . count($results['success']) . ' thêm mới';
            if (!empty($results['updated'])) {
                $msg .= ', ' . count($results['updated']) . ' cập nhật';
            }
            $msg .= '.';
            if (!empty($results['errors'])) {
                $msg .= ' Lỗi: ' . count($results['errors']) . ' dòng.';
                foreach (array_slice($results['errors'], 0, 5) as $err) {
                    $msg .= ' Dòng ' . $err['row'] . ': ' . implode(', ', $err['errors']) . '.';
                }
            }

            $type = empty($results['errors']) ? 'success' : (empty($results['success']) && empty($results['updated']) ? 'error' : 'warning');
            $this->setFlash($type, $msg);
        } finally {
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $this->redirect(\App\Config\App::baseUrl() . '/admin/elections/' . $id . '/candidates');
    }

    public function destroy(string $id): void
    {
        $candidate = (new Candidate())->find((int)$id);
        if ($candidate) {
            (new Candidate())->delete((int)$id);
            $this->setFlash('success', 'Đã xoá ứng cử viên.');
            $this->redirect(\App\Config\App::baseUrl() . '/admin/elections/' . $candidate['election_id'] . '/candidates');
            return;
        }
        $this->redirectBack();
    }

    public function destroyAll(string $id): void
    {
        (new Candidate())->deleteByElection((int)$id);
        $this->setFlash('success', 'Đã xoá tất cả ứng cử viên.');
        $this->redirect(\App\Config\App::baseUrl() . '/admin/elections/' . $id . '/candidates');
    }

    public function downloadTemplate(): void
    {
        (new ExportService())->downloadCandidateTemplate();
    }
}
