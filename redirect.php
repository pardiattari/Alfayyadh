<?php
require_once __DIR__ . '/includes/functions.php';

$offerId = isset($_GET['offer_id']) ? (int) $_GET['offer_id'] : 0;
$productId = isset($_GET['product_id']) ? (int) $_GET['product_id'] : 0;
$target = null;

if ($offerId > 0) {
    $offer = fetch_one('SELECT product_id, link_affiliate FROM product_offers WHERE id = ?', [$offerId]);
    if ($offer) {
        $productId = (int) $offer['product_id'];
        $target = $offer['link_affiliate'];
    }
}

if (!$target && $productId > 0) {
    $product = fetch_one('SELECT id, link_affiliate FROM products WHERE id = ?', [$productId]);
    if ($product) {
        $target = $product['link_affiliate'];
    }
}

if ($target && filter_var($target, FILTER_VALIDATE_URL)) {
    // Setiap klik affiliate dicatat sebelum pengunjung diarahkan ke marketplace.
    execute_query(
        'INSERT INTO clicks (product_id, tanggal, ip_address) VALUES (?, NOW(), ?)',
        [$productId, isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0']
    );
    header('Location: ' . $target);
    exit;
}

header('Location: ' . app_url('kategori.php'));
exit;
