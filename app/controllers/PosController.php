<?php
declare(strict_types=1);

class PosController
{
    public function index(): void
    {
        view('pos/index', [
            'title'         => 'Kasir',
            'products'      => Product::search('', null, true),
            'categories'    => Product::categories(),
            'services'      => PrintService::all(true),
            'finishings'    => PrintService::finishings(true),
            'taxPercent'    => (float) setting('tax_percent', '0'),
            'shift'         => Shift::current(),
            'printWidth'    => setting('receipt_width', '58'),
        ]);
    }

    public function checkout(): void
    {
        $payload = post_json();
        if ($payload === []) {
            json_out(['ok' => false, 'message' => 'Data transaksi tidak valid.'], 422);
        }

        try {
            $sale = Sale::checkout($payload, Auth::id());
            json_out([
                'ok'          => true,
                'invoice_no'  => $sale['invoice_no'],
                'total'       => $sale['total'],
                'change_due'  => $sale['change_due'],
                'redirect'    => url('struk?invoice=' . urlencode($sale['invoice_no'])),
            ]);
        } catch (RuntimeException $e) {
            json_out(['ok' => false, 'message' => $e->getMessage()], 422);
        } catch (Throwable $e) {
            json_out([
                'ok'      => false,
                'message' => APP_DEBUG ? 'Kesalahan sistem: ' . $e->getMessage() : 'Terjadi kesalahan saat menyimpan transaksi.',
            ], 500);
        }
    }

    public function receipt(): void
    {
        $invoice = (string) ($_GET['invoice'] ?? '');
        $sale = $invoice !== '' ? Sale::findByInvoice($invoice) : null;

        if (!$sale) {
            flash('error', 'Struk tidak ditemukan.');
            redirect('transaksi');
        }

        view('pos/receipt', [
            'title'    => 'Struk ' . $sale['invoice_no'],
            'sale'     => $sale,
            'items'    => Sale::items((int) $sale['id']),
            'width'    => (int) setting('receipt_width', '58'),
            'store'    => Setting::all(),
        ], 'layouts/print');
    }
}
