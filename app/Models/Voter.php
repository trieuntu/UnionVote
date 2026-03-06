<?php
namespace App\Models;

use App\Core\Model;

class Voter extends Model
{
    protected string $table = 'voters';

    public function getByElection(int $electionId): array
    {
        return $this->where(['election_id' => $electionId], 'id ASC');
    }

    public function findByEmailAndElection(string $email, int $electionId): ?array
    {
        return $this->findWhere(['email' => $email, 'election_id' => $electionId]);
    }

    public function deleteByElection(int $electionId): bool
    {
        return $this->execute("DELETE FROM {$this->table} WHERE election_id = :eid", ['eid' => $electionId]);
    }

    public function upsert(array $data): int
    {
        $sql = "INSERT INTO {$this->table} (election_id, email)
                VALUES (:election_id, :email)
                ON DUPLICATE KEY UPDATE email = VALUES(email)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        return $stmt->rowCount(); // 1=inserted, 0=unchanged
    }

    public function markVoted(int $voterId): bool
    {
        return $this->execute(
            "UPDATE {$this->table} SET has_voted = 1, voted_at = NOW() WHERE id = :id AND has_voted = 0",
            ['id' => $voterId]
        );
    }

    public function countVoted(int $electionId): int
    {
        return $this->count(['election_id' => $electionId, 'has_voted' => 1]);
    }
}
