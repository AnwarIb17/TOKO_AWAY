<?php
declare(strict_types=1);

class DashboardController
{
    public function index(): void
    {
        $today = Sale::todayStats();

        $monthStart = date('Y-m-01');
        $todayDate = date('Y-m-d');
        $month = Sale::rangeStats($monthStart, $todayDate);
        $monthExpense = Expense::totalBetween($monthStart, $todayDate);

        $series = Sale::dailySeries(date('Y-m-d', strtotime('-6 days')), $todayDate);

        $hour = (int) date('H');
        $greeting = $hour < 11 ? 'Selamat pagi' : ($hour < 15 ? 'Selamat siang' : ($hour < 19 ? 'Selamat sore' : 'Selamat malam'));

        view('dashboard/index', [
            'title'        => 'Dasbor',
            'today'        => $today,
            'month'        => $month,
            'monthExpense' => $monthExpense,
            'series'       => $series,
            'lowStock'     => Product::lowStock(),
            'recent'       => Sale::recent(6),
            'totalProducts'=> Product::totalActive(),
            'stockValue'   => Product::stockValue(),
            'greeting'     => $greeting,
            'shift'        => Shift::current(),
        ]);
    }
}
