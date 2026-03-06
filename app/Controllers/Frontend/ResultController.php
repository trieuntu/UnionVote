<?php
namespace App\Controllers\Frontend;

use App\Core\Controller;
use App\Models\Election;
use App\Models\Candidate;
use App\Models\Voter;

class ResultController extends Controller
{
    public function live(string $electionId): void
    {
        $electionModel = new Election();
        $election = $electionModel->findWithStats((int)$electionId);

        if (!$election || !$election['show_result']) {
            http_response_code(404);
            $this->view('errors.404');
            return;
        }

        $candidateModel = new Candidate();
        $candidates = $candidateModel->getWithVoteCount((int)$electionId);

        $voterModel = new Voter();
        $totalVoters = $voterModel->count(['election_id' => (int)$electionId]);
        $totalVoted = $voterModel->countVoted((int)$electionId);

        $this->view('frontend.results.live', [
            'election' => $election,
            'candidates' => $candidates,
            'totalVoters' => $totalVoters,
            'totalVoted' => $totalVoted,
        ]);
    }

    public function apiResults(string $electionId): void
    {
        $electionModel = new Election();
        $election = $electionModel->find((int)$electionId);

        if (!$election || !$election['show_result']) {
            $this->json(['error' => 'Not found'], 404);
            return;
        }

        $candidateModel = new Candidate();
        $candidates = $candidateModel->getWithVoteCount((int)$electionId);

        $voterModel = new Voter();
        $totalVoters = $voterModel->count(['election_id' => (int)$electionId]);
        $totalVoted = $voterModel->countVoted((int)$electionId);

        $candidateData = [];
        foreach ($candidates as $c) {
            $candidateData[] = [
                'id' => (int)$c['id'],
                'full_name' => $c['full_name'],
                'class_name' => $c['class_name'],
                'student_id' => $c['student_id'],
                'vote_count' => (int)$c['vote_count'],
                'percentage' => $totalVoted > 0 ? round(($c['vote_count'] / $totalVoted) * 100, 1) : 0,
            ];
        }

        $this->json([
            'election_id' => (int)$electionId,
            'title' => $election['title'],
            'total_voters' => $totalVoters,
            'total_voted' => $totalVoted,
            'candidates' => $candidateData,
            'last_updated' => date('c'),
        ]);
    }
}
