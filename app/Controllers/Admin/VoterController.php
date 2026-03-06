<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Validator;
use App\Models\Voter;
use App\Models\Election;
use App\Services\ImportService;
use App\Services\ExportService;

class VoterController extends Controller
{
    public function index(string $id): void
    {
        $election = (new Election())->find((int)$id);
        if (!$election) {
            $this->setFlash('error', 'Không tìm thấy cuộc bình chọn.');
            $this->redirect(\App\Config\App::baseUrl() . '/admin/elections');
            return;
        }

        $voters = (new Voter())->getByElection((int)$id);
        $this->view('admin.elections.voters', [
            'election' => $election,
            'voters' => $voters,
            'user' => Auth::user(),
        ]);
    }

    public function import(string $id): void
    {
        $importService = new ImportService();

        $error = $importService->validateUpload($_FILES['file'] ?? []);
        if ($error) {
            $this->setFlash('error', $error);
            $this->redirect(\App\Config\App::baseUrl() . '/admin/elections/' . $id . '/voters');
            return;
        }

        $filePath = $importService->saveUpload($_FILES['file']);

        try {
            $results = $importService->importVoters($filePath, (int)$id);

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

        $this->redirect(\App\Config\App::baseUrl() . '/admin/elections/' . $id . '/voters');
    }

    public function create(string $id): void
    {
        $election = (new Election())->find((int)$id);
        if (!$election) {
            $this->setFlash('error', 'Không tìm thấy cuộc bình chọn.');
            $this->redirect(\App\Config\App::baseUrl() . '/admin/elections');
            return;
        }

        $this->view('admin.elections.voter_form', [
            'election' => $election,
            'voter' => null,
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

        $email = trim($_POST['email'] ?? '');

        $validator = new Validator();
        if (!$validator->validate(['email' => $email], [
            'email' => 'required|email',
        ])) {
            $this->setFlash('error', implode(' ', array_map(fn($errs) => implode(' ', $errs), $validator->errors())));
            $this->redirect(\App\Config\App::baseUrl() . '/admin/elections/' . $id . '/voters/create');
            return;
        }

        $voterModel = new Voter();
        $existing = $voterModel->findByEmailAndElection($email, (int)$id);
        if ($existing) {
            $this->setFlash('error', 'Email ' . $email . ' đã tồn tại trong cuộc bình chọn này.');
            $this->redirect(\App\Config\App::baseUrl() . '/admin/elections/' . $id . '/voters/create');
            return;
        }

        $voterModel->create([
            'election_id' => (int)$id,
            'email' => $email,
        ]);

        $this->setFlash('success', 'Đã thêm cử tri ' . $email . '.');
        $this->redirect(\App\Config\App::baseUrl() . '/admin/elections/' . $id . '/voters');
    }

    public function edit(string $id): void
    {
        $voterModel = new Voter();
        $voter = $voterModel->find((int)$id);
        if (!$voter) {
            $this->setFlash('error', 'Không tìm thấy cử tri.');
            $this->redirectBack();
            return;
        }

        $election = (new Election())->find($voter['election_id']);

        $this->view('admin.elections.voter_form', [
            'election' => $election,
            'voter' => $voter,
            'user' => Auth::user(),
        ]);
    }

    public function update(string $id): void
    {
        $voterModel = new Voter();
        $voter = $voterModel->find((int)$id);
        if (!$voter) {
            $this->setFlash('error', 'Không tìm thấy cử tri.');
            $this->redirectBack();
            return;
        }

        $email = trim($_POST['email'] ?? '');

        $validator = new Validator();
        if (!$validator->validate(['email' => $email], [
            'email' => 'required|email',
        ])) {
            $this->setFlash('error', implode(' ', array_map(fn($errs) => implode(' ', $errs), $validator->errors())));
            $this->redirect(\App\Config\App::baseUrl() . '/admin/voters/' . $id . '/edit');
            return;
        }

        // Check duplicate (exclude current)
        $existing = $voterModel->findByEmailAndElection($email, $voter['election_id']);
        if ($existing && $existing['id'] != (int)$id) {
            $this->setFlash('error', 'Email ' . $email . ' đã tồn tại trong cuộc bình chọn này.');
            $this->redirect(\App\Config\App::baseUrl() . '/admin/voters/' . $id . '/edit');
            return;
        }

        $voterModel->update((int)$id, ['email' => $email]);

        $this->setFlash('success', 'Đã cập nhật cử tri ' . $email . '.');
        $this->redirect(\App\Config\App::baseUrl() . '/admin/elections/' . $voter['election_id'] . '/voters');
    }

    public function destroy(string $id): void
    {
        $voter = (new Voter())->find((int)$id);
        if ($voter) {
            (new Voter())->delete((int)$id);
            $this->setFlash('success', 'Đã xoá cử tri.');
            $this->redirect(\App\Config\App::baseUrl() . '/admin/elections/' . $voter['election_id'] . '/voters');
            return;
        }
        $this->redirectBack();
    }

    public function destroyAll(string $id): void
    {
        (new Voter())->deleteByElection((int)$id);
        $this->setFlash('success', 'Đã xoá tất cả cử tri.');
        $this->redirect(\App\Config\App::baseUrl() . '/admin/elections/' . $id . '/voters');
    }

    public function downloadTemplate(): void
    {
        (new ExportService())->downloadVoterTemplate();
    }
}
