<?php
namespace App\Models;

use App\Core\Model;

class Candidate extends Model
{
    protected string $table = 'candidates';

    public function getByElection(int $electionId): array
    {
        return $this->where(['election_id' => $electionId], 'display_order ASC, id ASC');
    }

    public function deleteByElection(int $electionId): bool
    {
        return $this->execute("DELETE FROM {$this->table} WHERE election_id = :eid", ['eid' => $electionId]);
    }

    public function upsert(array $data): int
    {
        $sql = "INSERT INTO {$this->table} (election_id, full_name, class_name, student_id, gpa, conduct_score, bio, display_order)
                VALUES (:election_id, :full_name, :class_name, :student_id, :gpa, :conduct_score, :bio, :display_order)
                ON DUPLICATE KEY UPDATE
                    full_name = VALUES(full_name),
                    class_name = VALUES(class_name),
                    gpa = VALUES(gpa),
                    conduct_score = VALUES(conduct_score),
                    bio = VALUES(bio),
                    display_order = VALUES(display_order)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        return $stmt->rowCount(); // 1=inserted, 2=updated
    }

    public function getWithVoteCount(int $electionId): array
    {
        $sql = "SELECT c.*, COUNT(vd.id) AS vote_count,
                       MAX(v.submitted_at) AS last_vote_at
                FROM {$this->table} c
                LEFT JOIN vote_details vd ON c.id = vd.candidate_id
                LEFT JOIN votes v ON vd.vote_id = v.id
                WHERE c.election_id = :eid
                GROUP BY c.id
                ORDER BY vote_count DESC, last_vote_at ASC, c.display_order ASC";
        return $this->query($sql, ['eid' => $electionId]);
    }
}
