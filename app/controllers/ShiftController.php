<?php
declare(strict_types=1);

class ShiftController
{
    public function index(): void
    {
        $shift = Shift::current();

        view('shift/index', [
            'title'   => 'Shift Kasir',
            'shift'   => $shift,
            'summary' => $shift ? Shift::summary($shift) : null,
            'history' => Shift::history(20),
        ]);
    }

    public function open(): void
    {
        $openingCash = request_float('opening_cash');
        if ($openingCash < 0) {
            flash('error', 'Kas awal tidak boleh minus.');
            redirect('shift');
        }

        try {
            Shift::open($openingCash, Auth::id(), (string) input('note', ''));
            flash('success', 'Shift dibuka. Selamat bekerja!');
        } catch (RuntimeException $e) {
            flash('error', $e->getMessage());
        }

        redirect('shift');
    }

    public function close(): void
    {
        $shift = Shift::current();
        if (!$shift) {
            flash('error', 'Tidak ada shift yang sedang terbuka.');
            redirect('shift');
        }

        $closingCash = request_float('closing_cash');
        try {
            Shift::close((int) $shift['id'], $closingCash, (string) input('note', ''), Auth::id());
            flash('success', 'Shift ditutup. Rekonsiliasi kas tersimpan.');
        } catch (RuntimeException $e) {
            flash('error', $e->getMessage());
        }

        redirect('shift');
    }
}
