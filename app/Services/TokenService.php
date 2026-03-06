<?php
namespace App\Services;

use App\Models\EmailToken;
use App\Models\Setting;

class TokenService
{
    private EmailToken $emailToken;
    private int $expiryMinutes;

    public function __construct()
    {
        $this->emailToken = new EmailToken();
        $setting = new Setting();
        $this->expiryMinutes = (int)$setting->get('token_expiry_minutes', '15');
    }

    public function generateToken(int $electionId, int $voterId): string
    {
        // Invalidate old tokens
        $this->emailToken->invalidateOldTokens($electionId, $voterId);

        // Generate 6-digit token
        $token = str_pad((string)random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        $hash = hash('sha256', $token);

        $expiresAt = (new \DateTime())->modify("+{$this->expiryMinutes} minutes")->format('Y-m-d H:i:s');

        $this->emailToken->create([
            'election_id' => $electionId,
            'voter_id' => $voterId,
            'token_hash' => $hash,
            'expires_at' => $expiresAt,
        ]);

        return $token;
    }

    public function verifyToken(int $electionId, string $token): ?array
    {
        $hash = hash('sha256', $token);
        return $this->emailToken->findValidToken($hash, $electionId);
    }

    public function isRateLimited(int $electionId, int $voterId): bool
    {
        $count = $this->emailToken->countRecentTokens($electionId, $voterId);
        return $count >= 5;
    }

    public function markUsed(int $tokenId): bool
    {
        return $this->emailToken->markUsed($tokenId);
    }

    public function getExpiryMinutes(): int
    {
        return $this->expiryMinutes;
    }

    public function getTokenHash(string $token): string
    {
        return hash('sha256', $token);
    }
}
