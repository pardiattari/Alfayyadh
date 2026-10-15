<?php
require_once __DIR__ . '/auth.php';
require_admin_login();
$admin = current_admin();
$adminTitle = isset($adminPageTitle) ? $adminPageTitle : 'Dashboard';
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($adminTitle) ?> - Admin Alfayyadh.ID</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="<?= e(app_url('assets/css/style.css')) ?>" rel="stylesheet">
</head>
<body class="admin-body">
<div class="admin-shell">
    <aside class="admin-sidebar">
        <a class="brand-mark" href="<?= e(app_url('admin/dashboard.php')) ?>">
            <img src="<?= e(site_logo()) ?>" alt="Alfayyadh.ID" class="brand-logo" style="height: 40px; width: auto;">
        </a>
        <nav class="admin-menu">
            <a class="<?= admin_active('dashboard.php') ?>" href="<?= e(app_url('admin/dashboard.php')) ?>"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
            <a class="<?= admin_active('beranda.php') ?>" href="<?= e(app_url('admin/beranda.php')) ?>"><i class="fa-solid fa-house"></i> Beranda</a>
            <a class="<?= admin_active('products.php') ?>" href="<?= e(app_url('admin/products.php')) ?>"><i class="fa-solid fa-box"></i> Produk</a>
            <a class="<?= admin_active('categories.php') ?>" href="<?= e(app_url('admin/categories.php')) ?>"><i class="fa-solid fa-layer-group"></i> Kategori</a>
            <a class="<?= admin_active('marketplaces.php') ?>" href="<?= e(app_url('admin/marketplaces.php')) ?>"><i class="fa-solid fa-store"></i> Marketplace</a>
            <a class="<?= admin_active('banners.php') ?>" href="<?= e(app_url('admin/banners.php')) ?>"><i class="fa-solid fa-image"></i> Banner</a>
            <a class="<?= admin_active('articles.php') ?>" href="<?= e(app_url('admin/articles.php')) ?>"><i class="fa-solid fa-newspaper"></i> Artikel</a>
            <a class="<?= admin_active('statistics.php') ?>" href="<?= e(app_url('admin/statistics.php')) ?>"><i class="fa-solid fa-chart-pie"></i> Statistik</a>
            <a class="<?= admin_active('settings.php') ?>" href="<?= e(app_url('admin/settings.php')) ?>"><i class="fa-solid fa-gear"></i> Pengaturan</a>
            <a href="<?= e(app_url()) ?>" target="_blank"><i class="fa-solid fa-up-right-from-square"></i> Lihat Website</a>
            <a href="<?= e(app_url('admin/logout.php')) ?>"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </nav>
    </aside>
    <main class="admin-main">
        <div class="admin-topbar">
            <div>
                <span class="section-kicker">Admin Panel</span>
                <h1 class="h3 fw-black mb-0"><?= e($adminTitle) ?></h1>
            </div>
            <div class="text-end">
                <strong><?= e($admin['nama']) ?></strong>
                <div class="text-muted small"><?= e($admin['role']) ?></div>
            </div>
        </div>
