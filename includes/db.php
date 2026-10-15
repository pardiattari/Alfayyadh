<?php
require_once __DIR__ . '/config.php';

function db()
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    // Koneksi PDO dipakai di semua halaman agar query bisa memakai prepared statement.
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (PDOException $exception) {
        http_response_code(500);
        echo '<!doctype html><html lang="id"><head><meta charset="utf-8">';
        echo '<title>Koneksi database gagal</title>';
        echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">';
        echo '</head><body class="bg-light"><main class="container py-5">';
        echo '<div class="alert alert-danger shadow-sm"><h1 class="h4">Koneksi database gagal</h1>';
        echo '<p>Pastikan database <strong>alfayyadh_db</strong> sudah dibuat dan file SQL sudah di-import melalui phpMyAdmin.</p>';
        echo '<p class="mb-0 small text-muted">Detail: ' . htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8') . '</p></div>';
        echo '</main></body></html>';
        exit;
    }

    return $pdo;
}
