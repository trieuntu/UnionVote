<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Election;
use App\Models\Candidate;
use App\Models\Voter;
use App\Services\ExportService;

class ResultController extends Controller
{
    public function index(string $id): void
    {
        $election = (new Election())->findWithStats((int)$id);
        if (!$election) {
            $this->setFlash('error', 'Không tìm thấy cuộc bình chọn.');
            $this->redirect(\App\Config\App::baseUrl() . '/admin/elections');
            return;
        }

        $candidates = (new Candidate())->getWithVoteCount((int)$id);
        $totalVoted = (new Voter())->countVoted((int)$id);
        $totalVoters = (new Voter())->count(['election_id' => (int)$id]);

        $this->view('admin.results.index', [
            'election' => $election,
            'candidates' => $candidates,
            'totalVoted' => $totalVoted,
            'totalVoters' => $totalVoters,
            'user' => Auth::user(),
        ]);
    }

    public function export(string $id): void
    {
        $election = (new Election())->find((int)$id);
        if (!$election) {
            $this->redirectBack();
            return;
        }

        $candidates = (new Candidate())->getWithVoteCount((int)$id);
        $totalVoted = (new Voter())->countVoted((int)$id);
        $totalVoters = (new Voter())->count(['election_id' => (int)$id]);

        (new ExportService())->exportResults($election, $candidates, $totalVoted, $totalVoters);
    }
}
