<?php
declare(strict_types=1);

class SettingController
{
    public function index(): void
    {
        view('settings/index', [
            'title'    => 'Pengaturan',
            'settings' => Setting::all(),
            'user'     => Auth::user(),
        ]);
    }

    public function save(): void
    {
        $width = request_int('receipt_width', 58);
        $tax = request_float('tax_percent');

        Setting::saveMany([
            'store_name'     => (string) input('store_name', APP_NAME),
            'store_tagline'  => (string) input('store_tagline', ''),
            'store_address'  => (string) input('store_address', ''),
            'store_phone'    => (string) input('store_phone', ''),
            'receipt_footer' => (string) input('receipt_footer', ''),
            'receipt_width'  => in_array($width, [58, 80], true) ? (string) $width : '58',
            'tax_percent'    => (string) max(0, min(100, $tax)),
            'invoice_prefix' => strtoupper(substr(preg_replace('/[^A-Za-z]/', '', (string) input('invoice_prefix', 'AF')) ?: 'AF', 0, 6)),
        ]);

        flash('success', 'Pengaturan toko berhasil disimpan.');
        redirect('pengaturan');
    }

    public function saveProfile(): void
    {
        $name = (string) input('name', '');
        $username = (string) input('username', '');

        if ($name === '' || $username === '') {
            flash('error', 'Nama dan username wajib diisi.');
            redirect('pengaturan');
        }

        $stmt = Database::pdo()->prepare('SELECT id FROM users WHERE username = ? AND id <> ? LIMIT 1');
        $stmt->execute([$username, Auth::id()]);
        if ($stmt->fetch()) {
            flash('error', 'Username sudah digunakan.');
            redirect('pengaturan');
        }

        Auth::updateProfile(Auth::id(), $name, $username);
        $_SESSION['user_name'] = $name;
        flash('success', 'Profil berhasil diperbarui.');
        redirect('pengaturan');
    }

    public function savePassword(): void
    {
        $current = (string) ($_POST['current_password'] ?? '');
        $new = (string) ($_POST['new_password'] ?? '');
        $confirm = (string) ($_POST['confirm_password'] ?? '');

        $user = Auth::user();
        if (!$user || !password_verify($current, $user['password_hash'])) {
            flash('error', 'Password saat ini salah.');
            redirect('pengaturan');
        }
        if (strlen($new) < 6) {
            flash('error', 'Password baru minimal 6 karakter.');
            redirect('pengaturan');
        }
        if ($new !== $confirm) {
            flash('error', 'Konfirmasi password tidak cocok.');
            redirect('pengaturan');
        }

        Auth::updatePassword(Auth::id(), $new);
        flash('success', 'Password berhasil diganti.');
        redirect('pengaturan');
    }
}
