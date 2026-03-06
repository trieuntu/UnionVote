<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Election;
use App\Models\Voter;

class DashboardController extends Controller
{
    public function index(): void
    {
        $electionModel = new Election();
        $elections = $electionModel->getAllWithStats();

        $totalElections = count($elections);
        $activeElections = 0;
        $totalVotedToday = 0;

        $voterModel = new Voter();
        foreach ($elections as &$e) {
            $e['computed_status'] = \App\Core\computeStatus($e);
            if ($e['computed_status'] === 'active') $activeElections++;
        }
        unset($e);

        // Count votes today
        $db = $voterModel->getDb();
        $stmt = $db->query("SELECT COUNT(*) FROM voters WHERE has_voted = 1 AND DATE(voted_at) = CURDATE()");
        $totalVotedToday = (int)$stmt->fetchColumn();

        $this->view('admin.dashboard.index', [
            'elections' => array_slice($elections, 0, 10),
            'totalElections' => $totalElections,
            'activeElections' => $activeElections,
            'totalVotedToday' => $totalVotedToday,
            'user' => Auth::user(),
        ]);
    }
}
