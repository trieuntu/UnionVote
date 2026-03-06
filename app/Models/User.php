<?php
namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected string $table = 'users';

    public function findByUsername(string $username): ?array
    {
        return $this->findWhere(['username' => $username]);
    }

    public function findByEmail(string $email): ?array
    {
        return $this->findWhere(['email' => $email]);
    }

    public function updateLastLogin(int $id): void
    {
        $this->execute("UPDATE {$this->table} SET last_login = NOW() WHERE id = :id", ['id' => $id]);
    }
}
