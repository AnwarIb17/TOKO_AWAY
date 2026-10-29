<?php
declare(strict_types=1);

class FinanceController
{
    public function index(): void
    {
        $month = (string) input('bulan', date('Y-m'));
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            $month = date('Y-m');
        }

        $from = $month . '-01';
        $to = date('Y-m-t', strtotime($from));

        $stats = Sale::rangeStats($from, $to);
        $expenses = Expense::between($from, $to);
        $expenseTotal = array_sum(array_map(fn ($e) => (float) $e['amount'], $expenses));
        $shift = Shift::current();

        view('finance/index', [
            'title'        => 'Keuangan',
            'month'        => $month,
            'from'         => $from,
            'to'           => $to,
            'stats'        => $stats,
            'expenses'     => $expenses,
            'expenseTotal' => $expenseTotal,
            'netProfit'    => $stats['gross'] - $expenseTotal,
            'shift'        => $shift,
            'shiftSummary' => $shift ? Shift::summary($shift) : null,
        ]);
    }

    public function saveExpense(): void
    {
        $description = (string) input('description', '');
        $amount = request_float('amount');
        $date = (string) input('expense_date', date('Y-m-d'));

        if ($description === '' || $amount <= 0) {
            flash('error', 'Keterangan dan nominal pengeluaran wajib diisi.');
            redirect('keuangan?bulan=' . substr($date, 0, 7));
        }

        Expense::save([
            'id'           => request_int('id'),
            'expense_date' => $date,
            'category'     => (string) input('category', 'Operasional'),
            'description'  => $description,
            'amount'       => $amount,
        ], Auth::id());

        flash('success', 'Pengeluaran berhasil dicatat.');
        redirect('keuangan?bulan=' . substr($date, 0, 7));
    }

    public function deleteExpense(): void
    {
        $id = request_int('id');
        $month = (string) input('bulan', date('Y-m'));
        Expense::delete($id);
        flash('success', 'Catatan pengeluaran dihapus.');
        redirect('keuangan?bulan=' . $month);
    }
}
