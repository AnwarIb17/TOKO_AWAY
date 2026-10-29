<?php
declare(strict_types=1);

class Sale
{
    /* -----------------------------------------------------------------
     |  TRANSAKSI KASIR
     * ----------------------------------------------------------------- */

    public static function checkout(array $payload, int $userId): array
    {
        return Database::transaction(function (PDO $pdo) use ($payload, $userId) {
            $shift = Shift::current();
            $shiftId = $shift['id'] ?? Shift::open(0, $userId, 'Dibuka otomatis oleh transaksi');
            $invoiceNo = self::nextInvoice($pdo);

            $subtotal = 0.0;
            $lines = [];

            $rawItems = $payload['items'] ?? [];
            if (!is_array($rawItems) || $rawItems === []) {
                throw new RuntimeException('Keranjang masih kosong.');
            }

            foreach ($rawItems as $raw) {
                $type = ($raw['type'] ?? 'product') === 'print' ? 'print' : 'product';

                if ($type === 'product') {
                    $product = Product::lock((int) ($raw['id'] ?? 0));
                    if (!$product || (int) $product['is_active'] !== 1) {
                        throw new RuntimeException('Ada produk yang tidak tersedia.');
                    }

                    $quantity = (int) ($raw['qty'] ?? 0);
                    if ($quantity <= 0) {
                        continue;
                    }
                    if ((int) $product['stock'] < $quantity) {
                        throw new RuntimeException(
                            'Stok "' . $product['name'] . '" tidak cukup. Tersisa ' . (int) $product['stock'] . '.'
                        );
                    }

                    $unitPrice = (float) $product['sell_price'];
                    $lineTotal = $unitPrice * $quantity;
                    $subtotal += $lineTotal;

                    $lines[] = [
                        'product_id' => (int) $product['id'],
                        'item_type'  => 'product',
                        'name'       => $product['name'],
                        'qty'        => $quantity,
                        'unit_price' => $unitPrice,
                        'cost_price' => (float) $product['cost_price'],
                        'subtotal'   => $lineTotal,
                        'meta_json'  => null,
                    ];

                    $stockBefore = (int) $product['stock'];
                    $stockAfter = $stockBefore - $quantity;
                    $pdo->prepare('UPDATE products SET stock = ? WHERE id = ?')
                        ->execute([$stockAfter, $product['id']]);

                    $pdo->prepare(
                        'INSERT INTO stock_movements (product_id, type, qty, stock_before, stock_after, reference, user_id)
                         VALUES (?, ?, ?, ?, ?, ?, ?)'
                    )->execute([
                        $product['id'], 'out', -$quantity, $stockBefore, $stockAfter, $invoiceNo, $userId,
                    ]);
                    continue;
                }

                /* ---- Jasa cetak ---- */
                $service = PrintService::find((int) ($raw['service_id'] ?? 0));
                if (!$service) {
                    throw new RuntimeException('Layanan cetak tidak ditemukan.');
                }

                $pages = max(1, (int) ($raw['pages'] ?? 1));
                $copies = max(1, (int) ($raw['copies'] ?? 1));
                $quantity = $pages * $copies;
                $unitPrice = (float) $service['price_per_page'];
                $lineTotal = $unitPrice * $quantity;
                $subtotal += $lineTotal;

                $lines[] = [
                    'product_id' => null,
                    'item_type'  => 'print',
                    'name'       => $service['name'],
                    'qty'        => $quantity,
                    'unit_price' => $unitPrice,
                    'cost_price' => 0,
                    'subtotal'   => $lineTotal,
                    'meta_json'  => json_encode([
                        'paper_size' => $service['paper_size'],
                        'color_mode' => $service['color_mode'],
                        'pages'      => $pages,
                        'copies'     => $copies,
                    ], JSON_UNESCAPED_UNICODE),
                ];

                foreach ((array) ($raw['finishing_ids'] ?? []) as $finishingId) {
                    $finishing = PrintService::findFinishing((int) $finishingId);
                    if (!$finishing) {
                        continue;
                    }
                    $price = (float) $finishing['price'];
                    $subtotal += $price;
                    $lines[] = [
                        'product_id' => null,
                        'item_type'  => 'print',
                        'name'       => 'Finishing: ' . $finishing['name'],
                        'qty'        => 1,
                        'unit_price' => $price,
                        'cost_price' => 0,
                        'subtotal'   => $price,
                        'meta_json'  => json_encode(['finishing_id' => (int) $finishing['id']], JSON_UNESCAPED_UNICODE),
                    ];
                }
            }

            if ($lines === []) {
                throw new RuntimeException('Tidak ada item valid pada keranjang.');
            }

            $discountType = ($payload['discount_type'] ?? 'nominal') === 'percent' ? 'percent' : 'nominal';
            $discountValue = max(0, (float) ($payload['discount'] ?? 0));
            $discount = $discountType === 'percent'
                ? round($subtotal * min(100, $discountValue) / 100, 0)
                : min($discountValue, $subtotal);

            $taxPercent = (float) setting('tax_percent', '0');
            $tax = round(($subtotal - $discount) * $taxPercent / 100, 0);
            $total = round($subtotal - $discount + $tax, 0);

            $paid = (float) ($payload['paid'] ?? 0);
            if ($paid < $total) {
                throw new RuntimeException('Uang diterima kurang dari total pembayaran.');
            }
            $change = round($paid - $total, 0);

            $pdo->prepare(
                'INSERT INTO sales (invoice_no, shift_id, user_id, customer_name, subtotal, discount_amount,
                 tax_amount, total, paid, change_due, payment_method, status, note)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, "cash", "paid", ?)'
            )->execute([
                $invoiceNo,
                $shiftId,
                $userId,
                ($payload['customer_name'] ?? '') !== '' ? $payload['customer_name'] : 'Umum',
                $subtotal,
                $discount,
                $tax,
                $total,
                $paid,
                $change,
                ($payload['note'] ?? '') !== '' ? $payload['note'] : null,
            ]);

            $saleId = (int) $pdo->lastInsertId();
            $insertItem = $pdo->prepare(
                'INSERT INTO sale_items (sale_id, product_id, item_type, name, qty, unit_price, cost_price, subtotal, meta_json)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            foreach ($lines as $line) {
                $insertItem->execute([
                    $saleId,
                    $line['product_id'],
                    $line['item_type'],
                    $line['name'],
                    $line['qty'],
                    $line['unit_price'],
                    $line['cost_price'],
                    $line['subtotal'],
                    $line['meta_json'],
                ]);
            }

            return [
                'id'         => $saleId,
                'invoice_no' => $invoiceNo,
                'total'      => $total,
                'paid'       => $paid,
                'change_due' => $change,
            ];
        });
    }

    private static function nextInvoice(PDO $pdo): string
    {
        $prefix = setting('invoice_prefix', 'AF') ?: 'AF';
        $datePart = date('Ymd');
        $like = $prefix . '-' . $datePart . '-%';

        $stmt = $pdo->prepare('SELECT invoice_no FROM sales WHERE invoice_no LIKE ? ORDER BY id DESC LIMIT 1');
        $stmt->execute([$like]);
        $last = $stmt->fetchColumn();

        $sequence = $last ? ((int) substr((string) $last, -4)) + 1 : 1;
        return sprintf('%s-%s-%04d', $prefix, $datePart, $sequence);
    }

    /* -----------------------------------------------------------------
     |  QUERY & LAPORAN
     * ----------------------------------------------------------------- */

    public static function find(int $id): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT s.*, u.name AS user_name, sh.opened_at AS shift_opened
             FROM sales s
             LEFT JOIN users u ON u.id = s.user_id
             LEFT JOIN shifts sh ON sh.id = s.shift_id
             WHERE s.id = ? LIMIT 1'
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public static function findByInvoice(string $invoice): ?array
    {
        $stmt = Database::pdo()->prepare(
            'SELECT s.*, u.name AS user_name FROM sales s
             LEFT JOIN users u ON u.id = s.user_id
             WHERE s.invoice_no = ? LIMIT 1'
        );
        $stmt->execute([$invoice]);
        return $stmt->fetch() ?: null;
    }

    public static function items(int $saleId): array
    {
        $stmt = Database::pdo()->prepare('SELECT * FROM sale_items WHERE sale_id = ? ORDER BY id ASC');
        $stmt->execute([$saleId]);
        return $stmt->fetchAll();
    }

    public static function paginate(array $filters, int $page = 1, int $perPage = 15): array
    {
        $where = ['1 = 1'];
        $params = [];

        if (!empty($filters['q'])) {
            $where[] = '(s.invoice_no LIKE ? OR s.customer_name LIKE ?)';
            $params[] = '%' . $filters['q'] . '%';
            $params[] = '%' . $filters['q'] . '%';
        }
        if (!empty($filters['from'])) {
            $where[] = 's.created_at >= ?';
            $params[] = $filters['from'] . ' 00:00:00';
        }
        if (!empty($filters['to'])) {
            $where[] = 's.created_at <= ?';
            $params[] = $filters['to'] . ' 23:59:59';
        }
        if (!empty($filters['status'])) {
            $where[] = 's.status = ?';
            $params[] = $filters['status'];
        }

        $whereSql = implode(' AND ', $where);
        $pdo = Database::pdo();

        $countStmt = $pdo->prepare("SELECT COUNT(*) FROM sales s WHERE $whereSql");
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $perPage = max(1, $perPage);
        $pages = max(1, (int) ceil($total / $perPage));
        $page = min(max(1, $page), $pages);
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT s.*, u.name AS user_name,
                (SELECT COUNT(*) FROM sale_items si WHERE si.sale_id = s.id) AS items_count
                FROM sales s LEFT JOIN users u ON u.id = s.user_id
                WHERE $whereSql
                ORDER BY s.id DESC
                LIMIT $perPage OFFSET $offset";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return [
            'rows'  => $stmt->fetchAll(),
            'total' => $total,
            'pages' => $pages,
            'page'  => $page,
        ];
    }

    public static function void(int $saleId, string $reason, int $userId): void
    {
        Database::transaction(function (PDO $pdo) use ($saleId, $reason, $userId) {
            $stmt = $pdo->prepare("SELECT * FROM sales WHERE id = ? AND status = 'paid' FOR UPDATE");
            $stmt->execute([$saleId]);
            $sale = $stmt->fetch();
            if (!$sale) {
                throw new RuntimeException('Transaksi tidak ditemukan atau sudah dibatalkan.');
            }

            $stmt = $pdo->prepare('SELECT * FROM sale_items WHERE sale_id = ? AND product_id IS NOT NULL');
            $stmt->execute([$saleId]);

            foreach ($stmt->fetchAll() as $item) {
                $productStmt = $pdo->prepare('SELECT stock FROM products WHERE id = ? FOR UPDATE');
                $productStmt->execute([$item['product_id']]);
                $before = (int) $productStmt->fetchColumn();
                $after = $before + (int) $item['qty'];

                $pdo->prepare('UPDATE products SET stock = ? WHERE id = ?')
                    ->execute([$after, $item['product_id']]);
                $pdo->prepare(
                    'INSERT INTO stock_movements (product_id, type, qty, stock_before, stock_after, reference, note, user_id)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
                )->execute([
                    $item['product_id'], 'in', (int) $item['qty'], $before, $after,
                    $sale['invoice_no'], 'Void transaksi', $userId,
                ]);
            }

            $pdo->prepare(
                "UPDATE sales SET status = 'void', void_reason = ?, voided_at = NOW() WHERE id = ?"
            )->execute([$reason !== '' ? $reason : 'Dibatalkan', $saleId]);
        });
    }

    public static function todayStats(): array
    {
        $row = Database::pdo()->query(
            "SELECT COUNT(*) AS trx, COALESCE(SUM(total),0) AS revenue
             FROM sales WHERE status = 'paid' AND DATE(created_at) = CURDATE()"
        )->fetch();

        $items = Database::pdo()->query(
            "SELECT COALESCE(SUM(si.qty),0) FROM sale_items si
             JOIN sales s ON s.id = si.sale_id
             WHERE s.status = 'paid' AND DATE(s.created_at) = CURDATE()"
        )->fetchColumn();

        return [
            'trx'     => (int) $row['trx'],
            'revenue' => (float) $row['revenue'],
            'items'   => (float) $items,
        ];
    }

    public static function rangeStats(string $from, string $to): array
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare(
            "SELECT COUNT(*) AS trx, COALESCE(SUM(total),0) AS revenue,
                    COALESCE(SUM(discount_amount),0) AS discount, COALESCE(SUM(tax_amount),0) AS tax
             FROM sales WHERE status = 'paid' AND DATE(created_at) BETWEEN ? AND ?"
        );
        $stmt->execute([$from, $to]);
        $summary = $stmt->fetch();

        $stmt = $pdo->prepare(
            "SELECT COALESCE(SUM(si.cost_price * si.qty),0) AS hpp
             FROM sale_items si JOIN sales s ON s.id = si.sale_id
             WHERE s.status = 'paid' AND DATE(s.created_at) BETWEEN ? AND ?"
        );
        $stmt->execute([$from, $to]);
        $hpp = (float) $stmt->fetchColumn();

        $revenue = (float) $summary['revenue'];

        return [
            'trx'      => (int) $summary['trx'],
            'revenue'  => $revenue,
            'discount' => (float) $summary['discount'],
            'tax'      => (float) $summary['tax'],
            'hpp'      => $hpp,
            'gross'    => $revenue - $hpp,
        ];
    }

    public static function dailySeries(string $from, string $to): array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT DATE(created_at) AS day, COUNT(*) AS trx, COALESCE(SUM(total),0) AS revenue
             FROM sales WHERE status = 'paid' AND DATE(created_at) BETWEEN ? AND ?
             GROUP BY DATE(created_at) ORDER BY day ASC"
        );
        $stmt->execute([$from, $to]);
        $rows = [];
        foreach ($stmt->fetchAll() as $row) {
            $rows[$row['day']] = [
                'day'     => $row['day'],
                'trx'     => (int) $row['trx'],
                'revenue' => (float) $row['revenue'],
            ];
        }

        $series = [];
        $cursor = strtotime($from);
        $end = strtotime($to);
        while ($cursor <= $end) {
            $key = date('Y-m-d', $cursor);
            $series[] = $rows[$key] ?? ['day' => $key, 'trx' => 0, 'revenue' => 0.0];
            $cursor = strtotime('+1 day', $cursor);
        }
        return $series;
    }

    public static function topProducts(string $from, string $to, int $limit = 10): array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT si.name, SUM(si.qty) AS qty, SUM(si.subtotal) AS revenue
             FROM sale_items si JOIN sales s ON s.id = si.sale_id
             WHERE s.status = 'paid' AND DATE(s.created_at) BETWEEN ? AND ?
             GROUP BY si.name ORDER BY qty DESC LIMIT " . max(1, $limit)
        );
        $stmt->execute([$from, $to]);
        return $stmt->fetchAll();
    }

    public static function categoryTotals(string $from, string $to): array
    {
        $stmt = Database::pdo()->prepare(
            "SELECT COALESCE(c.name, 'Jasa Cetak') AS category_name,
                    SUM(si.subtotal) AS revenue, SUM(si.qty) AS qty
             FROM sale_items si
             JOIN sales s ON s.id = si.sale_id
             LEFT JOIN products p ON p.id = si.product_id
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE s.status = 'paid' AND DATE(s.created_at) BETWEEN ? AND ?
             GROUP BY category_name ORDER BY revenue DESC"
        );
        $stmt->execute([$from, $to]);
        return $stmt->fetchAll();
    }

    public static function recent(int $limit = 6): array
    {
        $limit = max(1, $limit);
        return Database::pdo()->query(
            "SELECT s.*, u.name AS user_name FROM sales s
             LEFT JOIN users u ON u.id = s.user_id
             WHERE s.status = 'paid' ORDER BY s.id DESC LIMIT $limit"
        )->fetchAll();
    }

    public static function countPaid(): int
    {
        return (int) Database::pdo()->query("SELECT COUNT(*) FROM sales WHERE status = 'paid'")->fetchColumn();
    }
}
