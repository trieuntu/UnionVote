<?php
namespace App\Models;

use App\Core\Model;

class EmailToken extends Model
{
    protected string $table = 'email_tokens';

    public function findValidToken(string $tokenHash, int $electionId): ?array
    {
        $sql = "SELECT * FROM {$this->table}
                WHERE token_hash = :hash AND election_id = :eid AND is_used = 0 AND expires_at > NOW()
                LIMIT 1";
        $result = $this->query($sql, ['hash' => $tokenHash, 'eid' => $electionId]);
        return $result[0] ?? null;
    }

    public function invalidateOldTokens(int $electionId, int $voterId): bool
    {
        return $this->execute(
            "UPDATE {$this->table} SET is_used = 1 WHERE election_id = :eid AND voter_id = :vid AND is_used = 0",
            ['eid' => $electionId, 'vid' => $voterId]
        );
    }

    public function countRecentTokens(int $electionId, int $voterId): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table}
                WHERE election_id = :eid AND voter_id = :vid AND created_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)";
        $stmt = $this->getDb()->prepare($sql);
        $stmt->execute(['eid' => $electionId, 'vid' => $voterId]);
        return (int)$stmt->fetchColumn();
    }

    public function markUsed(int $id): bool
    {
        return $this->update($id, ['is_used' => 1]);
    }
}
