<?php
namespace App\Models;

use App\Core\Model;

class Setting extends Model
{
    protected string $table = 'settings';

    public function get(string $key, string $default = ''): string
    {
        $row = $this->findWhere(['setting_key' => $key]);
        return $row ? ($row['setting_value'] ?? $default) : $default;
    }

    public function set(string $key, string $value): void
    {
        $existing = $this->findWhere(['setting_key' => $key]);
        if ($existing) {
            $this->update($existing['id'], ['setting_value' => $value]);
        } else {
            $this->create(['setting_key' => $key, 'setting_value' => $value]);
        }
    }

    public function getMultiple(array $keys): array
    {
        if (empty($keys)) return [];
        $placeholders = implode(',', array_fill(0, count($keys), '?'));
        $sql = "SELECT setting_key, setting_value FROM {$this->table} WHERE setting_key IN ({$placeholders})";
        $stmt = $this->getDb()->prepare($sql);
        $stmt->execute(array_values($keys));
        $results = $stmt->fetchAll();
        $settings = [];
        foreach ($results as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    }
}
