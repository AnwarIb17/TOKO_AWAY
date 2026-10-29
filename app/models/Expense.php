<?php
declare(strict_types=1);

class Expense
{
    public static function save(array $data, int $userId): void
    {
        $id = (int) ($data['id'] ?? 0);
        $params = [
            $data['expense_date'],
            $data['category'],
            $data['description'],
            $data['amount'],
            $userId,
        ];

        if ($id > 0) {
            $params[] = $id;
            Database::pdo()->prepare(
                'UPDATE expenses SET expense_date = ?, category = ?, description = ?, amount = ?, user_id = ?
                 WHERE id = ?'
            )->execute($params);
            return;
        }

        Database::pdo()->prepare(
            'INSERT INTO expenses (expense_date, category, description, amount, user_id)
             VALUES (?, ?, ?, ?, ?)'
        )->execute($params);
    }

    public static function delete(int $id): void
    {
        Database::pdo()->prepare('DELETE FROM expenses WHERE id = ?')->execute([$id]);
    }

    public static function between(string $from, string $to): array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT e.*, u.name AS user_name FROM expenses e
             LEFT JOIN users u ON u.id = e.user_id
             WHERE e.expense_date BETWEEN ? AND ?
             ORDER BY e.expense_date DESC, e.id DESC'
        );
        $stmt->execute([$from, $to]);
        return $stmt->fetchAll();
    }

    public static function totalBetween(string $from, string $to): float
    {
        $stmt = Database::pdo()->prepare(
            'SELECT COALESCE(SUM(amount),0) FROM expenses WHERE expense_date BETWEEN ? AND ?'
        );
        $stmt->execute([$from, $to]);
        return (float) $stmt->fetchColumn();
    }

    public static function monthlyTotal(?string $month = null): float
    {
        $month = $month ?: date('Y-m');
        return self::totalBetween($month . '-01', date('Y-m-t', strtotime($month . '-01')));
    }
}
