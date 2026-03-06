<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\CSRF;
use App\Core\Validator;
use App\Models\Election;

class ElectionController extends Controller
{
    private Election $election;

    public function __construct()
    {
        $this->election = new Election();
    }

    public function index(): void
    {
        $elections = $this->election->getAllWithStats();
        foreach ($elections as &$e) {
            $e['computed_status'] = \App\Core\computeStatus($e);
        }
        unset($e);

        $this->view('admin.elections.index', [
            'elections' => $elections,
            'user' => Auth::user(),
        ]);
    }

    public function create(): void
    {
        $this->view('admin.elections.create', ['user' => Auth::user()]);
    }

    public function store(): void
    {
        $validator = new Validator();
        $valid = $validator->validate($_POST, [
            'title' => 'required|max:255',
            'start_time' => 'required|datetime',
            'end_time' => 'required|datetime',
            'min_votes' => 'required|integer|min:1',
            'max_votes' => 'required|integer|min:1',
        ]);

        if (!$valid) {
            $this->setFlash('error', 'Dữ liệu không hợp lệ: ' . implode(', ', array_map(fn($e) => $e[0], $validator->errors())));
            $this->redirect(\App\Config\App::baseUrl() . '/admin/elections/create');
            return;
        }

        $startTime = str_replace('T', ' ', $_POST['start_time']) . ':00';
        $endTime = str_replace('T', ' ', $_POST['end_time']) . ':00';

        if ($endTime <= $startTime) {
            $this->setFlash('error', 'Thời gian kết thúc phải sau thời gian bắt đầu.');
            $this->redirect(\App\Config\App::baseUrl() . '/admin/elections/create');
            return;
        }

        $minVotes = (int)$_POST['min_votes'];
        $maxVotes = (int)$_POST['max_votes'];
        if ($maxVotes < $minVotes) {
            $this->setFlash('error', 'Số lượng tối đa phải lớn hơn hoặc bằng tối thiểu.');
            $this->redirect(\App\Config\App::baseUrl() . '/admin/elections/create');
            return;
        }

        $id = $this->election->create([
            'title' => trim($_POST['title']),
            'description' => trim($_POST['description'] ?? ''),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'min_votes' => $minVotes,
            'max_votes' => $maxVotes,
            'created_by' => Auth::userId(),
        ]);

        $this->setFlash('success', 'Tạo cuộc bình chọn thành công!');
        $this->redirect(\App\Config\App::baseUrl() . '/admin/elections/' . $id);
    }

    public function show(string $id): void
    {
        $election = $this->election->findWithStats((int)$id);
        if (!$election) {
            $this->setFlash('error', 'Không tìm thấy cuộc bình chọn.');
            $this->redirect(\App\Config\App::baseUrl() . '/admin/elections');
            return;
        }

        $election['computed_status'] = \App\Core\computeStatus($election);

        $candidateModel = new \App\Models\Candidate();
        $voterModel = new \App\Models\Voter();

        $candidates = $candidateModel->getByElection((int)$id);
        $voters = $voterModel->getByElection((int)$id);

        // Get results
        $candidatesWithVotes = $candidateModel->getWithVoteCount((int)$id);

        $this->view('admin.elections.show', [
            'election' => $election,
            'candidates' => $candidates,
            'voters' => $voters,
            'results' => $candidatesWithVotes,
            'user' => Auth::user(),
        ]);
    }

    public function edit(string $id): void
    {
        $election = $this->election->find((int)$id);
        if (!$election) {
            $this->setFlash('error', 'Không tìm thấy cuộc bình chọn.');
            $this->redirect(\App\Config\App::baseUrl() . '/admin/elections');
            return;
        }

        $this->view('admin.elections.edit', [
            'election' => $election,
            'user' => Auth::user(),
        ]);
    }

    public function update(string $id): void
    {
        $election = $this->election->find((int)$id);
        if (!$election) {
            $this->setFlash('error', 'Không tìm thấy cuộc bình chọn.');
            $this->redirect(\App\Config\App::baseUrl() . '/admin/elections');
            return;
        }

        $startTime = str_replace('T', ' ', $_POST['start_time'] ?? '') . ':00';
        $endTime = str_replace('T', ' ', $_POST['end_time'] ?? '') . ':00';

        $minVotes = (int)($_POST['min_votes'] ?? 1);
        $maxVotes = (int)($_POST['max_votes'] ?? 1);

        $this->election->update((int)$id, [
            'title' => trim($_POST['title'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'min_votes' => $minVotes,
            'max_votes' => $maxVotes,
            'status' => $_POST['status'] ?? $election['status'],
        ]);

        $this->setFlash('success', 'Cập nhật thành công!');
        $this->redirect(\App\Config\App::baseUrl() . '/admin/elections/' . $id);
    }

    public function destroy(string $id): void
    {
        $this->election->delete((int)$id);
        $this->setFlash('success', 'Đã xoá cuộc bình chọn.');
        $this->redirect(\App\Config\App::baseUrl() . '/admin/elections');
    }

    public function toggleVisibility(string $id): void
    {
        $this->election->toggleField((int)$id, 'is_visible');
        $this->setFlash('success', 'Đã cập nhật trạng thái hiển thị.');
        $this->redirectBack();
    }

    public function toggleResult(string $id): void
    {
        $this->election->toggleField((int)$id, 'show_result');
        $this->setFlash('success', 'Đã cập nhật trạng thái hiển thị kết quả.');
        $this->redirectBack();
    }
}
