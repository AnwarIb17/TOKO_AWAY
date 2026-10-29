<?php
declare(strict_types=1);

class ServiceController
{
    public function index(): void
    {
        view('services/index', [
            'title'      => 'Jasa Cetak',
            'services'   => PrintService::all(false),
            'finishings' => PrintService::finishings(false),
            'categories' => Product::categories(),
        ]);
    }

    public function saveService(): void
    {
        $name = (string) input('name', '');
        if ($name === '') {
            flash('error', 'Nama layanan wajib diisi.');
            redirect('jasa');
        }

        PrintService::save([
            'id'             => request_int('id'),
            'name'           => $name,
            'paper_size'     => strtoupper((string) input('paper_size', 'A4')),
            'color_mode'     => input('color_mode', 'bw') === 'color' ? 'color' : 'bw',
            'price_per_page' => request_float('price_per_page'),
            'is_active'      => isset($_POST['is_active']) ? 1 : 0,
        ]);

        flash('success', 'Layanan cetak berhasil disimpan.');
        redirect('jasa');
    }

    public function deleteService(): void
    {
        PrintService::delete(request_int('id'));
        flash('success', 'Layanan cetak dinonaktifkan.');
        redirect('jasa');
    }

    public function saveFinishing(): void
    {
        $name = (string) input('name', '');
        if ($name === '') {
            flash('error', 'Nama finishing wajib diisi.');
            redirect('jasa');
        }

        PrintService::saveFinishing([
            'id'        => request_int('id'),
            'name'      => $name,
            'price'     => request_float('price'),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ]);

        flash('success', 'Finishing berhasil disimpan.');
        redirect('jasa');
    }

    public function deleteFinishing(): void
    {
        PrintService::deleteFinishing(request_int('id'));
        flash('success', 'Finishing dinonaktifkan.');
        redirect('jasa');
    }
}
