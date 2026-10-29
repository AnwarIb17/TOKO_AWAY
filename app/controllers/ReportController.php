<?php
declare(strict_types=1);

class ReportController
{
    public function index(): void
    {
        [$from, $to] = $this->range();

        $stats = Sale::rangeStats($from, $to);
        $expenseTotal = Expense::totalBetween($from, $to);
        $series = Sale::dailySeries($from, $to);
        $top = Sale::topProducts($from, $to, 10);
        $categories = Sale::categoryTotals($from, $to);

        view('reports/index', [
            'title'        => 'Laporan',
            'from'         => $from,
            'to'           => $to,
            'stats'        => $stats,
            'expenseTotal' => $expenseTotal,
            'netProfit'    => $stats['gross'] - $expenseTotal,
            'series'       => $series,
            'top'          => $top,
            'categories'   => $categories,
            'maxRevenue'   => max(array_map(fn ($d) => $d['revenue'], $series) ?: [0]),
        ]);
    }

    public function export(): void
    {
        [$from, $to] = $this->range();
        $stats = Sale::rangeStats($from, $to);

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="laporan-away-' . $from . '-sd-' . $to . '.csv"');

        $out = fopen('php://output', 'w');
        fwrite($out, "\xEF\xBB\xBF");

        fputcsv($out, ['Laporan AWAY FOTOCOPY']);
        fputcsv($out, ['Periode', $from, 's/d', $to]);
        fputcsv($out, []);
        fputcsv($out, ['Ringkasan']);
        fputcsv($out, ['Transaksi', $stats['trx']]);
        fputcsv($out, ['Omzet', $stats['revenue']]);
        fputcsv($out, ['HPP', $stats['hpp']]);
        fputcsv($out, ['Laba Kotor', $stats['gross']]);
        fputcsv($out, ['Pengeluaran', Expense::totalBetween($from, $to)]);
        fputcsv($out, ['Laba Bersih', $stats['gross'] - Expense::totalBetween($from, $to)]);
        fputcsv($out, []);
        fputcsv($out, ['Tanggal', 'Transaksi', 'Omzet']);

        foreach (Sale::dailySeries($from, $to) as $day) {
            fputcsv($out, [$day['day'], $day['trx'], $day['revenue']]);
        }

        fputcsv($out, []);
        fputcsv($out, ['Produk / Layanan', 'Qty Terjual', 'Omzet']);
        foreach (Sale::topProducts($from, $to, 50) as $row) {
            fputcsv($out, [$row['name'], $row['qty'], $row['revenue']]);
        }

        fclose($out);
        exit;
    }

    /** @return array{0:string,1:string} */
    private function range(): array
    {
        $from = (string) input('from', date('Y-m-01'));
        $to = (string) input('to', date('Y-m-d'));

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)) {
            $from = date('Y-m-01');
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)) {
            $to = date('Y-m-d');
        }
        if ($from > $to) {
            [$from, $to] = [$to, $from];
        }

        return [$from, $to];
    }
}
