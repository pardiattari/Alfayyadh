<?php
require_once __DIR__ . '/../includes/functions.php';

if (current_admin()) {
    header('Location: ' . app_url('admin/dashboard.php'));
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim(isset($_POST['username']) ? $_POST['username'] : '');
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $user = fetch_one('SELECT * FROM users WHERE username = ? LIMIT 1', [$username]);

    $valid = false;
    if ($user) {
        $stored = $user['password'];
        $valid = password_verify($password, $stored) || hash_equals($stored, $password);
    }

    if ($valid) {
        $_SESSION['admin_user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'nama' => $user['nama'],
            'role' => $user['role'],
        ];

        if (hash_equals($user['password'], $password)) {
            execute_query('UPDATE users SET password = ? WHERE id = ?', [password_hash($password, PASSWORD_DEFAULT), $user['id']]);
        }

        header('Location: ' . app_url('admin/dashboard.php'));
        exit;
    }

    $error = 'Username atau password salah.';
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin - Alfayyadh.ID</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="<?= e(app_url('assets/css/style.css')) ?>" rel="stylesheet">
</head>
<body class="login-page">
    <form class="login-card" method="post">
        <a class="brand-mark d-inline-flex align-items-center mb-4" href="<?= e(app_url()) ?>">
            <span class="brand-icon"><i class="fa-solid fa-fire-flame-curved"></i></span>
            Alfayyadh.ID
        </a>
        <h1 class="h4 fw-bold mb-2">Login Admin</h1>
        <p class="text-muted">Kelola produk affiliate, artikel, banner, dan statistik.</p>
        <?php if ($error) : ?>
            <div class="alert alert-danger"><?= e($error) ?></div>
        <?php endif; ?>
        <div class="mb-3">
            <label class="form-label fw-bold" for="username">Username</label>
            <input id="username" class="form-control" type="text" name="username" value="admin" required>
        </div>
        <div class="mb-4">
            <label class="form-label fw-bold" for="password">Password</label>
            <input id="password" class="form-control" type="password" name="password" placeholder="admin123" required>
        </div>
        <button class="btn btn-affiliate w-100" type="submit">Masuk</button>
        <p class="small text-muted mt-3 mb-0">Default: admin / admin123</p>
    </form>
</body>
</html>
