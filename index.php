<?php
declare(strict_types=1);

session_start();

require __DIR__ . '/app/config/config.php';
require __DIR__ . '/app/helpers/functions.php';

/* Autoloader sederhana: core, models, controllers */
spl_autoload_register(function (string $class): void {
    foreach (['core', 'models', 'controllers'] as $dir) {
        $file = APP_PATH . '/' . $dir . '/' . $class . '.php';
        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});

/* Cek koneksi database lebih awal agar pesan error ramah */
try {
    Database::pdo();
} catch (Throwable $e) {
    http_response_code(500);
    require VIEW_PATH . '/errors/db.php';
    exit;
}

/* Proteksi CSRF untuk seluruh request POST */
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $token = $_POST['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!hash_equals($_SESSION['_csrf'] ?? '', (string) $token)) {
        if (wants_json()) {
            json_out(['ok' => false, 'message' => 'Sesi keamanan kedaluwarsa. Muat ulang halaman.'], 419);
        }
        flash('error', 'Sesi keamanan kedaluwarsa. Silakan coba lagi.');
        back();
    }
}

/* Guard autentikasi */
$path = current_path();
if (!Auth::check() && $path !== '/login') {
    $_SESSION['intended'] = $path;
    redirect('login');
}
if (Auth::check() && $path === '/login') {
    redirect('dashboard');
}

$router = new Router();
require APP_PATH . '/routes.php';
$router->dispatch();
