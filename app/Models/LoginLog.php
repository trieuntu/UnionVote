<?php
namespace App\Models;

use App\Core\Model;

class LoginLog extends Model
{
    protected string $table = 'login_logs';

    public function log(string $username, string $status, ?int $userId = null, ?string $failureReason = null): int
    {
        return $this->create([
            'user_id' => $userId,
            'username' => $username,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
            'user_agent' => mb_substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500),
            'status' => $status,
            'failure_reason' => $failureReason,
        ]);
    }

    public function getRecent(int $limit = 50, int $offset = 0): array
    {
        $sql = "SELECT l.*, u.full_name, u.role
                FROM {$this->table} l
                LEFT JOIN users u ON l.user_id = u.id
                ORDER BY l.created_at DESC
                LIMIT :lim OFFSET :off";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':lim', $limit, \PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countAll(): int
    {
        return (int)$this->db->query("SELECT COUNT(*) FROM {$this->table}")->fetchColumn();
    }
}
