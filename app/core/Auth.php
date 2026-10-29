<?php
declare(strict_types=1);

class Auth
{
    private static ?array $user = null;

    public static function attempt(string $username, string $password): bool
    {
        $stmt = Database::pdo()->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return false;
        }

        self::login($user);
        return true;
    }

    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['login_at'] = time();
        self::$user = $user;
    }

    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public static function id(): int
    {
        return (int) ($_SESSION['user_id'] ?? 0);
    }

    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }
        if (self::$user === null) {
            $stmt = Database::pdo()->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
            $stmt->execute([self::id()]);
            self::$user = $stmt->fetch() ?: null;
        }
        return self::$user;
    }

    public static function name(): string
    {
        return (string) (self::user()['name'] ?? 'Admin');
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        self::$user = null;
    }

    public static function updatePassword(int $userId, string $newPassword): void
    {
        $stmt = Database::pdo()->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
        $stmt->execute([password_hash($newPassword, PASSWORD_BCRYPT), $userId]);
    }

    public static function updateProfile(int $userId, string $name, string $username): void
    {
        $stmt = Database::pdo()->prepare('UPDATE users SET name = ?, username = ? WHERE id = ?');
        $stmt->execute([$name, $username, $userId]);
    }
}
