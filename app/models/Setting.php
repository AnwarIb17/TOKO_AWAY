<?php
declare(strict_types=1);

class Setting
{
    public static function all(): array
    {
        $rows = Database::pdo()->query('SELECT setting_key, setting_value FROM settings')->fetchAll();
        $out = [];
        foreach ($rows as $row) {
            $out[$row['setting_key']] = $row['setting_value'];
        }
        return $out;
    }

    public static function saveMany(array $data): void
    {
        $stmt = Database::pdo()->prepare(
            'INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)'
        );
        foreach ($data as $key => $value) {
            $stmt->execute([$key, (string) $value]);
        }
    }
}
