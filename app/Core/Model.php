<?php
namespace App\Core;

use App\Config\Database;
use PDO;

class Model
{
    protected string $table = '';
    protected string $primaryKey = 'id';
    protected PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findAll(string $orderBy = 'id DESC', ?int $limit = null): array
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY {$orderBy}";
        if ($limit) {
            $sql .= " LIMIT {$limit}";
        }
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    public function where(array $conditions, string $orderBy = 'id DESC'): array
    {
        $whereClauses = [];
        $params = [];
        foreach ($conditions as $key => $value) {
            $whereClauses[] = "{$key} = :{$key}";
            $params[$key] = $value;
        }
        $whereStr = implode(' AND ', $whereClauses);
        $sql = "SELECT * FROM {$this->table} WHERE {$whereStr} ORDER BY {$orderBy}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findWhere(array $conditions): ?array
    {
        $whereClauses = [];
        $params = [];
        foreach ($conditions as $key => $value) {
            $whereClauses[] = "{$key} = :{$key}";
            $params[$key] = $value;
        }
        $whereStr = implode(' AND ', $whereClauses);
        $sql = "SELECT * FROM {$this->table} WHERE {$whereStr} LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function create(array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $setClauses = [];
        foreach ($data as $key => $value) {
            $setClauses[] = "{$key} = :{$key}";
        }
        $setStr = implode(', ', $setClauses);
        $data[$this->primaryKey] = $id;
        $sql = "UPDATE {$this->table} SET {$setStr} WHERE {$this->primaryKey} = :{$this->primaryKey}";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function count(array $conditions = []): int
    {
        if (empty($conditions)) {
            $stmt = $this->db->query("SELECT COUNT(*) FROM {$this->table}");
        } else {
            $whereClauses = [];
            $params = [];
            foreach ($conditions as $key => $value) {
                $whereClauses[] = "{$key} = :{$key}";
                $params[$key] = $value;
            }
            $whereStr = implode(' AND ', $whereClauses);
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE {$whereStr}");
            $stmt->execute($params);
        }
        return (int)$stmt->fetchColumn();
    }

    public function beginTransaction(): void
    {
        $this->db->beginTransaction();
    }

    public function commit(): void
    {
        $this->db->commit();
    }

    public function rollback(): void
    {
        $this->db->rollBack();
    }

    public function getDb(): PDO
    {
        return $this->db;
    }

    public function query(string $sql, array $params = []): array
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function execute(string $sql, array $params = []): bool
    {
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
}
