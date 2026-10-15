<?php
require_once __DIR__ . '/functions.php';
$currentPage = basename($_SERVER['SCRIPT_NAME']);
$activeMarketplaces = isset($_GET['marketplace']) && is_array($_GET['marketplace'])
    ? $_GET['marketplace']
    : [];
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e(isset($pageTitle) ? $pageTitle . ' - ' . APP_NAME : APP_NAME) ?></title>
    <meta name="description" content="<?= e(isset($pageDescription) ? $pageDescription : 'Portal rekomendasi produk affiliate Shopee, Tokopedia, dan TikTok Shop.') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="<?= e(app_url('assets/css/style.css')) ?>" rel="stylesheet">
</head>
<body>
<header class="site-header">
    <nav class="navbar navbar-expand-lg bg-white">
        <div class="container">
            <a class="navbar-brand brand-mark" href="<?= e(app_url()) ?>">
                <img src="<?= e(site_logo()) ?>" alt="Alfayyadh.ID" class="brand-logo" style="height: 40px; width: auto;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Buka menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-lg-end" id="mainNav">
                <ul class="navbar-nav align-items-lg-center gap-lg-3">
                    <li class="nav-item"><a class="nav-link <?= $currentPage === 'index.php' ? 'active' : '' ?>" href="<?= e(app_url()) ?>">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link <?= in_array('Shopee', $activeMarketplaces, true) ? 'active' : '' ?>" href="<?= e(app_url('kategori.php?marketplace[]=Shopee')) ?>">Shopee</a></li>
                    <li class="nav-item"><a class="nav-link <?= in_array('TikTok Shop', $activeMarketplaces, true) ? 'active' : '' ?>" href="<?= e(app_url('kategori.php?marketplace[]=TikTok%20Shop')) ?>">TikTok</a></li>
                    <li class="nav-item"><a class="nav-link <?= in_array('Tokopedia', $activeMarketplaces, true) ? 'active' : '' ?>" href="<?= e(app_url('kategori.php?marketplace[]=Tokopedia')) ?>">Tokopedia</a></li>
                    <li class="nav-item"><a class="nav-link login-link" href="<?= e(app_url('admin/login.php')) ?>"><i class="fa-regular fa-user" aria-hidden="true"></i> Login</a></li>
                </ul>
            </div>
        </div>
    </nav>
</header>
<main>
