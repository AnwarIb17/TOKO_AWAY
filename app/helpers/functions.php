<?php
declare(strict_types=1);

/* ------------------------------------------------------------------
 |  HELPER GLOBAL
 * ------------------------------------------------------------------ */

function e(mixed $value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $path = ''): string
{
    return BASE_PATH . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

function back(): never
{
    $ref = $_SERVER['HTTP_REFERER'] ?? url('dashboard');
    header('Location: ' . $ref);
    exit;
}

function current_path(): string
{
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    if (BASE_PATH !== '' && str_starts_with($uri, BASE_PATH)) {
        $uri = substr($uri, strlen(BASE_PATH));
    }
    $uri = '/' . trim($uri, '/');
    return $uri;
}

function money(float|int|string|null $amount, bool $withPrefix = true): string
{
    $number = number_format((float) $amount, 0, ',', '.');
    return $withPrefix ? 'Rp ' . $number : $number;
}

function qty(float|int|string|null $value): string
{
    $number = (float) $value;
    return rtrim(rtrim(number_format($number, 2, ',', '.'), '0'), ',');
}

function date_id(?string $date, bool $withTime = false): string
{
    if (!$date) {
        return '-';
    }
    $months = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    $ts = strtotime($date);
    $text = date('j', $ts) . ' ' . $months[(int) date('n', $ts)] . ' ' . date('Y', $ts);
    return $withTime ? $text . ' · ' . date('H:i', $ts) : $text;
}

function day_id(?string $date): string
{
    if (!$date) {
        return '-';
    }
    $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    return $days[(int) date('w', strtotime($date))];
}

function flash(string $type, string $message): void
{
    $_SESSION['_flash'][] = ['type' => $type, 'message' => $message];
}

function flashes(): array
{
    $items = $_SESSION['_flash'] ?? [];
    unset($_SESSION['_flash']);
    return $items;
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(csrf_token()) . '">';
}

function setting(string $key, ?string $default = null): ?string
{
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        foreach (Database::pdo()->query('SELECT setting_key, setting_value FROM settings') as $row) {
            $cache[$row['setting_key']] = $row['setting_value'];
        }
    }
    return $cache[$key] ?? $default;
}

function view(string $view, array $data = [], ?string $layout = 'layouts/app'): void
{
    $viewFile = VIEW_PATH . '/' . $view . '.php';
    if (!is_file($viewFile)) {
        throw new RuntimeException('View tidak ditemukan: ' . $view);
    }

    extract($data, EXTR_SKIP);
    $pageScripts = $pageScripts ?? '';

    ob_start();
    require $viewFile;
    $content = (string) ob_get_clean();

    if ($layout === null) {
        echo $content;
        return;
    }

    require VIEW_PATH . '/' . $layout . '.php';
}

function json_out(array $payload, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function wants_json(): bool
{
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    $requested = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
    return str_contains($accept, 'application/json') || strtolower($requested) === 'xmlhttprequest';
}

function input(string $key, mixed $default = null): mixed
{
    $value = $_POST[$key] ?? $_GET[$key] ?? $default;
    return is_string($value) ? trim($value) : $value;
}

function request_int(string $key, int $default = 0): int
{
    return (int) ($_POST[$key] ?? $_GET[$key] ?? $default);
}

function request_float(string $key, float $default = 0): float
{
    $raw = $_POST[$key] ?? $_GET[$key] ?? $default;
    if (is_string($raw)) {
        $raw = trim($raw);
        if ($raw === '') {
            return $default;
        }
        if (str_contains($raw, ',')) {
            // Format Indonesia: 1.500,50
            $raw = str_replace('.', '', $raw);
            $raw = str_replace(',', '.', $raw);
        } elseif (preg_match('/^\d{1,3}(\.\d{3})+$/', $raw)) {
            // Hanya pemisah ribuan: 1.500
            $raw = str_replace('.', '', $raw);
        }
    }
    return (float) $raw;
}

function post_json(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === false || $raw === '') {
        return [];
    }
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
    return trim($text, '-') ?: 'item-' . time();
}

function percent(float $part, float $whole): float
{
    return $whole > 0 ? round(($part / $whole) * 100, 1) : 0.0;
}
