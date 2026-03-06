<?php
namespace App\Controllers\Frontend;

use App\Core\Controller;
use App\Models\Election;
use App\Models\Candidate;
use App\Models\Voter;

class ElectionController extends Controller
{
    public function show(string $id): void
    {
        $electionModel = new Election();
        $election = $electionModel->findWithStats((int)$id);

        if (!$election || !$election['is_visible']) {
            http_response_code(404);
            $this->view('errors.404');
            return;
        }

        $candidateModel = new Candidate();
        $candidates = $candidateModel->getByElection((int)$id);

        $voterModel = new Voter();
        $totalVoters = $voterModel->count(['election_id' => (int)$id]);
        $totalVoted = $voterModel->countVoted((int)$id);

        // Get results if show_result is enabled
        $results = [];
        if ($election['show_result']) {
            $results = $candidateModel->getWithVoteCount((int)$id);
        }

        $this->view('frontend.elections.show', [
            'election' => $election,
            'candidates' => $candidates,
            'totalVoters' => $totalVoters,
            'totalVoted' => $totalVoted,
            'results' => $results,
        ]);
    }
}
