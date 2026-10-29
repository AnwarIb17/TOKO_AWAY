<?php
declare(strict_types=1);

class AuthController
{
    public function showLogin(): void
    {
        view('auth/login', ['title' => 'Masuk'], 'layouts/auth');
    }

    public function login(): void
    {
        $username = (string) input('username', '');
        $password = (string) ($_POST['password'] ?? '');

        if ($username === '' || $password === '') {
            flash('error', 'Username dan password wajib diisi.');
            redirect('login');
        }

        if (!Auth::attempt($username, $password)) {
            flash('error', 'Username atau password salah.');
            redirect('login');
        }

        $intended = $_SESSION['intended'] ?? null;
        unset($_SESSION['intended']);

        flash('success', 'Selamat datang kembali, ' . Auth::name() . '.');
        redirect($intended && $intended !== '/login' ? ltrim((string) $intended, '/') : 'dashboard');
    }

    public function logout(): void
    {
        Auth::logout();
        header('Location: ' . url('login'));
        exit;
    }
}
