<?php
namespace App\Models;

use App\Core\Model;

class Election extends Model
{
    protected string $table = 'elections';

    public function findWithStats(int $id): ?array
    {
        $sql = "SELECT e.*, u.full_name as creator_name,
                (SELECT COUNT(*) FROM candidates WHERE election_id = e.id) as candidate_count,
                (SELECT COUNT(*) FROM voters WHERE election_id = e.id) as voter_count,
                (SELECT COUNT(*) FROM voters WHERE election_id = e.id AND has_voted = 1) as voted_count
                FROM {$this->table} e
                LEFT JOIN users u ON e.created_by = u.id
                WHERE e.id = :id LIMIT 1";
        $result = $this->query($sql, ['id' => $id]);
        return $result[0] ?? null;
    }

    public function getAllWithStats(string $orderBy = 'created_at DESC'): array
    {
        $sql = "SELECT e.*, u.full_name as creator_name,
                (SELECT COUNT(*) FROM candidates WHERE election_id = e.id) as candidate_count,
                (SELECT COUNT(*) FROM voters WHERE election_id = e.id) as voter_count,
                (SELECT COUNT(*) FROM voters WHERE election_id = e.id AND has_voted = 1) as voted_count
                FROM {$this->table} e
                LEFT JOIN users u ON e.created_by = u.id
                ORDER BY {$orderBy}";
        return $this->query($sql);
    }

    public function getVisible(): array
    {
        $sql = "SELECT e.*,
                (SELECT COUNT(*) FROM candidates WHERE election_id = e.id) as candidate_count,
                (SELECT COUNT(*) FROM voters WHERE election_id = e.id) as voter_count,
                (SELECT COUNT(*) FROM voters WHERE election_id = e.id AND has_voted = 1) as voted_count
                FROM {$this->table} e
                WHERE e.is_visible = 1
                ORDER BY e.start_time DESC";
        return $this->query($sql);
    }

    public function toggleField(int $id, string $field): bool
    {
        if (!in_array($field, ['is_visible', 'show_result'])) {
            return false;
        }
        $sql = "UPDATE {$this->table} SET {$field} = NOT {$field} WHERE id = :id";
        return $this->execute($sql, ['id' => $id]);
    }
}
