<?php
declare(strict_types=1);

class PrintService
{
    public static function all(bool $activeOnly = false): array
    {
        $sql = 'SELECT * FROM print_services';
        if ($activeOnly) {
            $sql .= ' WHERE is_active = 1';
        }
        $sql .= ' ORDER BY id';
        return Database::pdo()->query($sql)->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare('SELECT * FROM print_services WHERE id = ? AND is_active = 1');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function save(array $data): void
    {
        $id = (int) ($data['id'] ?? 0);
        $params = [
            $data['name'],
            $data['paper_size'],
            $data['color_mode'],
            $data['price_per_page'],
            $data['is_active'] ? 1 : 0,
        ];

        if ($id > 0) {
            $params[] = $id;
            Database::pdo()->prepare(
                'UPDATE print_services SET name = ?, paper_size = ?, color_mode = ?,
                 price_per_page = ?, is_active = ? WHERE id = ?'
            )->execute($params);
            return;
        }

        Database::pdo()->prepare(
            'INSERT INTO print_services (name, paper_size, color_mode, price_per_page, is_active)
             VALUES (?, ?, ?, ?, ?)'
        )->execute($params);
    }

    public static function delete(int $id): void
    {
        Database::pdo()->prepare('UPDATE print_services SET is_active = 0 WHERE id = ?')->execute([$id]);
    }

    public static function finishings(bool $activeOnly = false): array
    {
        $sql = 'SELECT * FROM finishings';
        if ($activeOnly) {
            $sql .= ' WHERE is_active = 1';
        }
        $sql .= ' ORDER BY name';
        return Database::pdo()->query($sql)->fetchAll();
    }

    public static function findFinishing(int $id): ?array
    {
        $stmt = Database::pdo()->prepare('SELECT * FROM finishings WHERE id = ? AND is_active = 1');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function saveFinishing(array $data): void
    {
        $id = (int) ($data['id'] ?? 0);
        $params = [$data['name'], $data['price'], $data['is_active'] ? 1 : 0];

        if ($id > 0) {
            $params[] = $id;
            Database::pdo()->prepare('UPDATE finishings SET name = ?, price = ?, is_active = ? WHERE id = ?')
                ->execute($params);
            return;
        }

        Database::pdo()->prepare('INSERT INTO finishings (name, price, is_active) VALUES (?, ?, ?)')
            ->execute($params);
    }

    public static function deleteFinishing(int $id): void
    {
        Database::pdo()->prepare('UPDATE finishings SET is_active = 0 WHERE id = ?')->execute([$id]);
    }
}
