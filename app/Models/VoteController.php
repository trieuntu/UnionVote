<?php
namespace App\Controllers\Frontend;

use App\Core\Controller;
use App\Core\Session;
use App\Core\CSRF;
use App\Models\Election;
use App\Models\Candidate;
use App\Models\Voter;
use App\Models\Vote;
use App\Models\VoteDetail;
use App\Services\TokenService;
use App\Services\MailService;
use App\Core\Model;

class VoteController extends Controller
{
    public function requestToken(): void
    {
        $email = trim($_POST['email'] ?? '');
        $electionId = (int)($_POST['election_id'] ?? 0);

        $electionModel = new Election();
        $election = $electionModel->find($electionId);

        if (!$election || !$election['is_visible']) {
            $this->setFlash('error', 'Cuộc bình chọn không tồn tại.');
            $this->redirectBack();
            return;
        }

        // Check if election is active
        $status = \App\Core\computeStatus($election);
        if ($status !== 'active') {
            $this->setFlash('error', 'Cuộc bình chọn không trong thời gian diễn ra.');
            $this->redirectBack();
            return;
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !str_ends_with(strtolower($email), '@ntu.edu.vn')) {
            $this->setFlash('error', 'Email phải có đuôi @ntu.edu.vn.');
            $this->redirectBack();
            return;
        }

        $voterModel = new Voter();
        $voter = $voterModel->findByEmailAndElection($email, $electionId);

        if (!$voter) {
            $this->setFlash('error', 'Email không có trong danh sách cử tri.');
            $this->redirectBack();
            return;
        }

        if ($voter['has_voted']) {
            $this->setFlash('error', 'Bạn đã bỏ phiếu cho cuộc bình chọn này.');
            $this->redirectBack();
            return;
        }

        $tokenService = new TokenService();

        if ($tokenService->isRateLimited($electionId, $voter['id'])) {
            $this->setFlash('error', 'Bạn đã yêu cầu mã quá nhiều lần. Vui lòng thử lại sau 15 phút.');
            $this->redirectBack();
            return;
        }

        $token = $tokenService->generateToken($electionId, $voter['id']);

        // Send email
        $mailService = new MailService();
        $sent = $mailService->sendToken($email, $token, $election['title'], $tokenService->getExpiryMinutes());

        if (!$sent) {
            $this->setFlash('error', 'Không thể gửi email. Vui lòng thử lại sau.');
            $this->redirectBack();
            return;
        }

        Session::set('vote_email', $email);
        Session::set('vote_election_id', $electionId);

        $this->redirect(\App\Config\App::baseUrl() . '/vote/' . $electionId . '/verify');
    }

    public function verifyForm(string $electionId): void
    {
        $electionModel = new Election();
        $election = $electionModel->find((int)$electionId);

        if (!$election) {
            http_response_code(404);
            $this->view('errors.404');
            return;
        }

        $email = Session::get('vote_email', '');

        $this->view('frontend.vote.verify', [
            'election' => $election,
            'maskedEmail' => $email ? \App\Core\maskEmail($email) : '',
        ]);
    }

    public function verifyToken(): void
    {
        $token = trim($_POST['token'] ?? '');
        $electionId = (int)($_POST['election_id'] ?? Session::get('vote_election_id', 0));

        if (empty($token) || $electionId <= 0) {
            $this->setFlash('error', 'Vui lòng nhập mã xác thực.');
            $this->redirectBack();
            return;
        }

        $tokenService = new TokenService();
        $tokenRecord = $tokenService->verifyToken($electionId, $token);

        if (!$tokenRecord) {
            $this->setFlash('error', 'Mã xác thực không hợp lệ hoặc đã hết hạn.');
            $this->redirectBack();
            return;
        }

        Session::set('vote_token_id', $tokenRecord['id']);
        Session::set('vote_token_hash', hash('sha256', $token));
        Session::set('vote_voter_id', $tokenRecord['voter_id']);
        Session::set('vote_election_id', $electionId);

        $this->redirect(\App\Config\App::baseUrl() . '/vote/' . $electionId . '/ballot');
    }

    public function ballot(string $electionId): void
    {
        $tokenId = Session::get('vote_token_id');
        if (!$tokenId || Session::get('vote_election_id') != (int)$electionId) {
            $this->setFlash('error', 'Phiên bỏ phiếu không hợp lệ. Vui lòng xác thực lại.');
            $this->redirect(\App\Config\App::baseUrl() . '/election/' . $electionId);
            return;
        }

        $electionModel = new Election();
        $election = $electionModel->find((int)$electionId);

        if (!$election) {
            http_response_code(404);
            $this->view('errors.404');
            return;
        }

        $candidateModel = new Candidate();
        $candidates = $candidateModel->getByElection((int)$electionId);

        $this->view('frontend.vote.ballot', [
            'election' => $election,
            'candidates' => $candidates,
        ]);
    }

    public function submit(): void
    {
        $electionId = (int)($_POST['election_id'] ?? 0);
        $selectedIds = $_POST['candidates'] ?? [];
        $tokenId = Session::get('vote_token_id');
        $tokenHash = Session::get('vote_token_hash');
        $voterId = Session::get('vote_voter_id');

        if (!$tokenId || !$tokenHash || !$voterId || $electionId <= 0) {
            $this->setFlash('error', 'Phiên bỏ phiếu không hợp lệ.');
            $this->redirect(\App\Config\App::baseUrl());
            return;
        }

        $electionModel = new Election();
        $election = $electionModel->find($electionId);

        if (!$election) {
            $this->setFlash('error', 'Cuộc bình chọn không tồn tại.');
            $this->redirect(\App\Config\App::baseUrl());
            return;
        }

        // Validate number of selections
        $count = count($selectedIds);
        if ($count < (int)$election['min_votes'] || $count > (int)$election['max_votes']) {
            $this->setFlash('error', "Vui lòng chọn từ {$election['min_votes']} đến {$election['max_votes']} ứng cử viên.");
            $this->redirect(\App\Config\App::baseUrl() . '/vote/' . $electionId . '/ballot');
            return;
        }

        // Validate candidate IDs belong to this election
        $candidateModel = new Candidate();
        $validCandidates = $candidateModel->getByElection($electionId);
        $validIds = array_column($validCandidates, 'id');
        foreach ($selectedIds as $cid) {
            if (!in_array((int)$cid, array_map('intval', $validIds))) {
                $this->setFlash('error', 'Dữ liệu không hợp lệ.');
                $this->redirect(\App\Config\App::baseUrl() . '/vote/' . $electionId . '/ballot');
                return;
            }
        }

        // If it's a review (confirm step)
        if (isset($_POST['action']) && $_POST['action'] === 'review') {
            Session::set('vote_selected', array_map('intval', $selectedIds));
            $this->view('frontend.vote.review', [
                'election' => $election,
                'candidates' => array_filter($validCandidates, fn($c) => in_array((int)$c['id'], array_map('intval', $selectedIds))),
            ]);
            return;
        }

        // Final submit
        $selectedIds = Session::get('vote_selected', array_map('intval', $selectedIds));
        if (empty($selectedIds)) {
            $this->setFlash('error', 'Không có ứng cử viên được chọn.');
            $this->redirect(\App\Config\App::baseUrl() . '/vote/' . $electionId . '/ballot');
            return;
        }

        $voterModel = new Voter();
        $voteModel = new Vote();
        $voteDetailModel = new VoteDetail();
        $tokenService = new TokenService();

        $baseModel = new Model();
        $baseModel->beginTransaction();

        try {
            // Re-check voter hasn't voted
            $voter = $voterModel->find($voterId);
            if (!$voter || $voter['has_voted']) {
                $baseModel->rollback();
                $this->setFlash('error', 'Bạn đã bỏ phiếu rồi.');
                $this->redirect(\App\Config\App::baseUrl());
                return;
            }

            // Create vote record (anonymous - only token_hash, no voter_id)
            $ipHash = hash('sha256', ($_SERVER['REMOTE_ADDR'] ?? '') . ($_ENV['APP_ENCRYPTION_KEY'] ?? ''));
            $voteId = $voteModel->create([
                'election_id' => $electionId,
                'token_hash' => $tokenHash,
                'ip_hash' => $ipHash,
                'submitted_at' => date('Y-m-d H:i:s'),
            ]);

            // Create vote details
            foreach ($selectedIds as $candidateId) {
                $voteDetailModel->create([
                    'vote_id' => $voteId,
                    'candidate_id' => (int)$candidateId,
                ]);
            }

            // Mark token as used
            $tokenService->markUsed($tokenId);

            // Mark voter as voted (separate from vote record for anonymity)
            $voterModel->markVoted($voterId);

            $baseModel->commit();

            // Clear vote session data
            Session::remove('vote_email');
            Session::remove('vote_election_id');
            Session::remove('vote_token_id');
            Session::remove('vote_token_hash');
            Session::remove('vote_voter_id');
            Session::remove('vote_selected');

            $this->redirect(\App\Config\App::baseUrl() . '/vote/' . $electionId . '/success');
        } catch (\Exception $e) {
            $baseModel->rollback();
            $this->setFlash('error', 'Có lỗi xảy ra. Vui lòng thử lại.');
            $this->redirect(\App\Config\App::baseUrl() . '/vote/' . $electionId . '/ballot');
        }
    }

    public function success(string $electionId): void
    {
        $electionModel = new Election();
        $election = $electionModel->find((int)$electionId);

        $this->view('frontend.vote.success', [
            'election' => $election,
        ]);
    }
}
