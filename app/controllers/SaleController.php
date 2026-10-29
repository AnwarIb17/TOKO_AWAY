<?php
declare(strict_types=1);

class SaleController
{
    public function index(): void
    {
        $filters = [
            'q'      => (string) input('q', ''),
            'from'   => (string) input('from', ''),
            'to'     => (string) input('to', ''),
            'status' => (string) input('status', ''),
        ];

        $page = max(1, request_int('page', 1));
        $result = Sale::paginate($filters, $page, 15);

        view('sales/index', [
            'title'   => 'Transaksi',
            'result'  => $result,
            'filters' => $filters,
        ]);
    }

    public function show(string $id): void
    {
        $sale = Sale::find((int) $id);
        if (!$sale) {
            flash('error', 'Transaksi tidak ditemukan.');
            redirect('transaksi');
        }

        view('sales/show', [
            'title' => 'Transaksi ' . $sale['invoice_no'],
            'sale'  => $sale,
            'items' => Sale::items((int) $sale['id']),
        ]);
    }

    public function void(): void
    {
        $id = request_int('id');
        $reason = (string) input('reason', '');

        try {
            Sale::void($id, $reason, Auth::id());
            flash('success', 'Transaksi dibatalkan dan stok telah dikembalikan.');
        } catch (RuntimeException $e) {
            flash('error', $e->getMessage());
        }

        redirect('transaksi/' . $id);
    }
}
