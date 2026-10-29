<?php
declare(strict_types=1);

class Product
{
    public static function all(bool $activeOnly = false): array
    {
        $sql = 'SELECT p.*, c.name AS category_name
                FROM products p
                LEFT JOIN categories c ON c.id = p.category_id';
        if ($activeOnly) {
            $sql .= ' WHERE p.is_active = 1';
        }
        $sql .= ' ORDER BY c.sort_order, p.name';
        return Database::pdo()->query($sql)->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT p.*, c.name AS category_name
             FROM products p LEFT JOIN categories c ON c.id = p.category_id
             WHERE p.id = ? LIMIT 1'
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function lock(int $id): ?array
    {
        $stmt = Database::pdo()->prepare('SELECT * FROM products WHERE id = ? LIMIT 1 FOR UPDATE');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function search(string $q = '', ?int $categoryId = null, bool $activeOnly = true): array
    {
        $sql = 'SELECT p.*, c.name AS category_name
                FROM products p LEFT JOIN categories c ON c.id = p.category_id
                WHERE 1 = 1';
        $params = [];

        if ($activeOnly) {
            $sql .= ' AND p.is_active = 1';
        }
        if ($q !== '') {
            $sql .= ' AND (p.name LIKE ? OR p.sku LIKE ? OR p.barcode LIKE ?)';
            $like = '%' . $q . '%';
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }
        if ($categoryId !== null && $categoryId > 0) {
            $sql .= ' AND p.category_id = ?';
            $params[] = $categoryId;
        }
        $sql .= ' ORDER BY p.name ASC';

        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function categories(): array
    {
        return Database::pdo()->query('SELECT * FROM categories ORDER BY sort_order, name')->fetchAll();
    }

    public static function nextSku(): string
    {
        $count = (int) Database::pdo()->query('SELECT COUNT(*) FROM products')->fetchColumn();
        do {
            $count++;
            $sku = 'ATK-' . str_pad((string) $count, 3, '0', STR_PAD_LEFT);
            $stmt = Database::pdo()->prepare('SELECT COUNT(*) FROM products WHERE sku = ?');
            $stmt->execute([$sku]);
        } while ((int) $stmt->fetchColumn() > 0);

        return $sku;
    }

    public static function save(array $data): int
    {
        $pdo = Database::pdo();
        $id = (int) ($data['id'] ?? 0);

        $payload = [
            'category_id' => $data['category_id'] ?: null,
            'sku'         => $data['sku'] !== '' ? $data['sku'] : self::nextSku(),
            'barcode'     => $data['barcode'] !== '' ? $data['barcode'] : null,
            'name'        => $data['name'],
            'unit'        => $data['unit'] ?: 'pcs',
            'cost_price'  => $data['cost_price'],
            'sell_price'  => $data['sell_price'],
            'stock'       => $data['stock'],
            'min_stock'   => $data['min_stock'],
            'is_active'   => $data['is_active'] ? 1 : 0,
        ];

        if ($id > 0) {
            $payload['id'] = $id;
            $stmt = $pdo->prepare(
                'UPDATE products SET category_id = :category_id, sku = :sku, barcode = :barcode,
                 name = :name, unit = :unit, cost_price = :cost_price, sell_price = :sell_price,
                 stock = :stock, min_stock = :min_stock, is_active = :is_active WHERE id = :id'
            );
            $stmt->execute($payload);
            return $id;
        }

        $stmt = $pdo->prepare(
            'INSERT INTO products (category_id, sku, barcode, name, unit, cost_price, sell_price,
             stock, min_stock, is_active)
             VALUES (:category_id, :sku, :barcode, :name, :unit, :cost_price, :sell_price,
             :stock, :min_stock, :is_active)'
        );
        $stmt->execute($payload);
        return (int) $pdo->lastInsertId();
    }

    public static function delete(int $id): void
    {
        $stmt = Database::pdo()->prepare('UPDATE products SET is_active = 0 WHERE id = ?');
        $stmt->execute([$id]);
    }

    /**
     * @param string $type in | out | adjust
     * @param int    $qty  untuk adjust = stok akhir
     */
    public static function adjustStock(int $id, int $qty, string $type, string $note, int $userId): void
    {
        Database::transaction(function (PDO $pdo) use ($id, $qty, $type, $note, $userId) {
            $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ? FOR UPDATE');
            $stmt->execute([$id]);
            $product = $stmt->fetch();
            if (!$product) {
                throw new RuntimeException('Produk tidak ditemukan.');
            }

            $before = (int) $product['stock'];
            $after = match ($type) {
                'in'     => $before + abs($qty),
                'out'    => $before - abs($qty),
                'adjust' => $qty,
                default  => $before,
            };

            if ($after < 0) {
                throw new RuntimeException('Stok tidak boleh minus.');
            }

            $pdo->prepare('UPDATE products SET stock = ? WHERE id = ?')->execute([$after, $id]);
            $pdo->prepare(
                'INSERT INTO stock_movements (product_id, type, qty, stock_before, stock_after, reference, note, user_id)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
            )->execute([
                $id,
                $type === 'adjust' ? 'adjust' : $type,
                $after - $before,
                $before,
                $after,
                'MANUAL',
                $note !== '' ? $note : 'Penyesuaian stok',
                $userId,
            ]);
        });
    }

    public static function lowStock(): array
    {
        return Database::pdo()->query(
            'SELECT * FROM products
             WHERE is_active = 1 AND stock <= min_stock
             ORDER BY (stock - min_stock) ASC, name ASC'
        )->fetchAll();
    }

    public static function totalActive(): int
    {
        return (int) Database::pdo()->query('SELECT COUNT(*) FROM products WHERE is_active = 1')->fetchColumn();
    }

    public static function stockValue(): float
    {
        return (float) Database::pdo()->query(
            'SELECT COALESCE(SUM(stock * cost_price), 0) FROM products WHERE is_active = 1'
        )->fetchColumn();
    }

    public static function movements(int $limit = 50): array
    {
        return Database::pdo()->query(
            'SELECT m.*, p.name AS product_name, u.name AS user_name
             FROM stock_movements m
             JOIN products p ON p.id = m.product_id
             LEFT JOIN users u ON u.id = m.user_id
             ORDER BY m.id DESC LIMIT ' . max(1, $limit)
        )->fetchAll();
    }
}
