<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Validator;
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

    public function create(string $id): void
    {
        $election = (new Election())->find((int)$id);
        if (!$election) {
            $this->setFlash('error', 'Không tìm thấy cuộc bình chọn.');
            $this->redirect(\App\Config\App::baseUrl() . '/admin/elections');
            return;
        }

        $this->view('admin.elections.candidate_form', [
            'election' => $election,
            'candidate' => null,
            'user' => Auth::user(),
        ]);
    }

    public function store(string $id): void
    {
        $election = (new Election())->find((int)$id);
        if (!$election) {
            $this->setFlash('error', 'Không tìm thấy cuộc bình chọn.');
            $this->redirect(\App\Config\App::baseUrl() . '/admin/elections');
            return;
        }

        $data = [
            'full_name' => trim($_POST['full_name'] ?? ''),
            'class_name' => trim($_POST['class_name'] ?? ''),
            'student_id' => trim($_POST['student_id'] ?? ''),
            'gpa' => $_POST['gpa'] !== '' ? $_POST['gpa'] : null,
            'conduct_score' => $_POST['conduct_score'] !== '' ? $_POST['conduct_score'] : null,
            'bio' => trim($_POST['bio'] ?? ''),
        ];

        $validator = new Validator();
        if (!$validator->validate($data, [
            'full_name' => 'required|min:2|max:100',
            'class_name' => 'required|max:50',
            'student_id' => 'required|max:20',
        ])) {
            $this->setFlash('error', implode(' ', array_map(fn($errs) => implode(' ', $errs), $validator->errors())));
            $this->redirect(\App\Config\App::baseUrl() . '/admin/elections/' . $id . '/candidates/create');
            return;
        }

        $candidateModel = new Candidate();
        $existing = $candidateModel->findWhere(['election_id' => (int)$id, 'student_id' => $data['student_id']]);
        if ($existing) {
            $this->setFlash('error', 'MSSV ' . $data['student_id'] . ' đã tồn tại trong cuộc bình chọn này.');
            $this->redirect(\App\Config\App::baseUrl() . '/admin/elections/' . $id . '/candidates/create');
            return;
        }

        $candidateModel->create([
            'election_id' => (int)$id,
            'full_name' => $data['full_name'],
            'class_name' => $data['class_name'],
            'student_id' => $data['student_id'],
            'gpa' => $data['gpa'],
            'conduct_score' => $data['conduct_score'],
            'bio' => $data['bio'] ?: null,
            'display_order' => 0,
        ]);

        $this->setFlash('success', 'Đã thêm ứng cử viên ' . $data['full_name'] . '.');
        $this->redirect(\App\Config\App::baseUrl() . '/admin/elections/' . $id . '/candidates');
    }

    public function edit(string $id): void
    {
        $candidateModel = new Candidate();
        $candidate = $candidateModel->find((int)$id);
        if (!$candidate) {
            $this->setFlash('error', 'Không tìm thấy ứng cử viên.');
            $this->redirectBack();
            return;
        }

        $election = (new Election())->find($candidate['election_id']);

        $this->view('admin.elections.candidate_form', [
            'election' => $election,
            'candidate' => $candidate,
            'user' => Auth::user(),
        ]);
    }

    public function update(string $id): void
    {
        $candidateModel = new Candidate();
        $candidate = $candidateModel->find((int)$id);
        if (!$candidate) {
            $this->setFlash('error', 'Không tìm thấy ứng cử viên.');
            $this->redirectBack();
            return;
        }

        $data = [
            'full_name' => trim($_POST['full_name'] ?? ''),
            'class_name' => trim($_POST['class_name'] ?? ''),
            'student_id' => trim($_POST['student_id'] ?? ''),
            'gpa' => $_POST['gpa'] !== '' ? $_POST['gpa'] : null,
            'conduct_score' => $_POST['conduct_score'] !== '' ? $_POST['conduct_score'] : null,
            'bio' => trim($_POST['bio'] ?? ''),
        ];

        $validator = new Validator();
        if (!$validator->validate($data, [
            'full_name' => 'required|min:2|max:100',
            'class_name' => 'required|max:50',
            'student_id' => 'required|max:20',
        ])) {
            $this->setFlash('error', implode(' ', array_map(fn($errs) => implode(' ', $errs), $validator->errors())));
            $this->redirect(\App\Config\App::baseUrl() . '/admin/candidates/' . $id . '/edit');
            return;
        }

        // Check duplicate MSSV (exclude current)
        $existing = $candidateModel->findWhere(['election_id' => $candidate['election_id'], 'student_id' => $data['student_id']]);
        if ($existing && $existing['id'] != (int)$id) {
            $this->setFlash('error', 'MSSV ' . $data['student_id'] . ' đã tồn tại trong cuộc bình chọn này.');
            $this->redirect(\App\Config\App::baseUrl() . '/admin/candidates/' . $id . '/edit');
            return;
        }

        $candidateModel->update((int)$id, [
            'full_name' => $data['full_name'],
            'class_name' => $data['class_name'],
            'student_id' => $data['student_id'],
            'gpa' => $data['gpa'],
            'conduct_score' => $data['conduct_score'],
            'bio' => $data['bio'] ?: null,
        ]);

        $this->setFlash('success', 'Đã cập nhật ứng cử viên ' . $data['full_name'] . '.');
        $this->redirect(\App\Config\App::baseUrl() . '/admin/elections/' . $candidate['election_id'] . '/candidates');
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
