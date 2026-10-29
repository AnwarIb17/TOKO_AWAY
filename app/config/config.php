<?php
declare(strict_types=1);

/* ------------------------------------------------------------------
 |  KONFIGURASI APLIKASI
 |  Ubah bagian database bila password MySQL Anda berbeda.
 * ------------------------------------------------------------------ */

define('APP_NAME', 'AWAY FOTOCOPY');
define('APP_TAGLINE', 'Fotokopi & Alat Tulis');

define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'away_fotocopy');
define('DB_USER', 'root');
define('DB_PASS', '');

define('APP_TIMEZONE', 'Asia/Jakarta');

/* Path absolut aplikasi */
define('ROOT_PATH', dirname(__DIR__, 2));
define('APP_PATH', ROOT_PATH . '/app');
define('VIEW_PATH', ROOT_PATH . '/views');
define('STORAGE_PATH', ROOT_PATH . '/storage');

/* Base URL relatif, mis. "/TOKO" */
$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/index.php');
define('BASE_PATH', rtrim(dirname($scriptName), '/'));

date_default_timezone_set(APP_TIMEZONE);

/* Tampilkan error saat pengembangan. Set false untuk produksi. */
define('APP_DEBUG', true);

if (APP_DEBUG) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(E_ALL);
}
