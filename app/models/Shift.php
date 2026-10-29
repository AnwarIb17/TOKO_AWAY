<?php
declare(strict_types=1);

class Shift
{
    public static function current(): ?array
    {
        $row = Database::pdo()->query(
            "SELECT * FROM shifts WHERE status = 'open' ORDER BY id DESC LIMIT 1"
        )->fetch();
        return $row ?: null;
    }

    public static function open(float $openingCash, int $userId, string $note = ''): int
    {
        if (self::current() !== null) {
            throw new RuntimeException('Masih ada shift yang terbuka. Tutup shift terlebih dahulu.');
        }
        $stmt = Database::pdo()->prepare(
            "INSERT INTO shifts (user_id, opening_cash, note, status) VALUES (?, ?, ?, 'open')"
        );
        $stmt->execute([$userId, $openingCash, $note !== '' ? $note : null]);
        return (int) Database::pdo()->lastInsertId();
    }

    public static function summary(array $shift): array
    {
        $pdo = Database::pdo();
        $end = $shift['closed_at'] ?? date('Y-m-d H:i:s');

        $stmt = $pdo->prepare(
            "SELECT COALESCE(SUM(total),0) AS total, COUNT(*) AS trx
             FROM sales WHERE shift_id = ? AND status = 'paid'"
        );
        $stmt->execute([$shift['id']]);
        $sales = $stmt->fetch();

        $stmt = $pdo->prepare(
            'SELECT COALESCE(SUM(amount),0) AS total FROM expenses
             WHERE created_at BETWEEN ? AND ?'
        );
        $stmt->execute([$shift['opened_at'], $end]);
        $expenses = (float) $stmt->fetchColumn();

        $salesTotal = (float) $sales['total'];
        $expected = (float) $shift['opening_cash'] + $salesTotal - $expenses;

        return [
            'sales_total'   => $salesTotal,
            'trx'           => (int) $sales['trx'],
            'expense_total' => $expenses,
            'expected_cash' => $expected,
        ];
    }

    public static function close(int $shiftId, float $closingCash, string $note, int $userId): void
    {
        Database::transaction(function (PDO $pdo) use ($shiftId, $closingCash, $note) {
            $stmt = $pdo->prepare("SELECT * FROM shifts WHERE id = ? AND status = 'open' FOR UPDATE");
            $stmt->execute([$shiftId]);
            $shift = $stmt->fetch();
            if (!$shift) {
                throw new RuntimeException('Shift tidak ditemukan atau sudah ditutup.');
            }

            $summary = self::summary($shift);
            $difference = $closingCash - $summary['expected_cash'];

            $pdo->prepare(
                "UPDATE shifts SET closed_at = NOW(), closing_cash = ?, expected_cash = ?,
                 difference = ?, sales_total = ?, expense_total = ?, note = ?, status = 'closed'
                 WHERE id = ?"
            )->execute([
                $closingCash,
                $summary['expected_cash'],
                $difference,
                $summary['sales_total'],
                $summary['expense_total'],
                $note !== '' ? $note : $shift['note'],
                $shiftId,
            ]);
        });
    }

    public static function history(int $limit = 30): array
    {
        return Database::pdo()->query(
            "SELECT s.*, u.name AS user_name FROM shifts s
             LEFT JOIN users u ON u.id = s.user_id
             WHERE s.status = 'closed'
             ORDER BY s.id DESC LIMIT " . max(1, $limit)
        )->fetchAll();
    }
}
