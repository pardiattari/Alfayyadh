<?php
require_once __DIR__ . '/db.php';

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function rupiah($number)
{
    return 'Rp' . number_format((float) $number, 0, ',', '.');
}

function fetch_all($sql, $params = [])
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function fetch_one($sql, $params = [])
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch();
    return $row ?: null;
}

function execute_query($sql, $params = [])
{
    $stmt = db()->prepare($sql);
    return $stmt->execute($params);
}

function short_text($text, $limit = 120)
{
    $text = trim(strip_tags((string) $text));
    if (strlen($text) <= $limit) {
        return $text;
    }
    return substr($text, 0, $limit - 3) . '...';
}

function rating_stars($rating)
{
    $rating = (float) $rating;
    $html = '';
    for ($i = 1; $i <= 5; $i++) {
        $html .= '<i class="fa-solid fa-star' . ($rating + 0.1 < $i ? ' muted-star' : '') . '"></i>';
    }
    return $html;
}

function marketplace_icon($name)
{
    $name = strtolower((string) $name);
    if (strpos($name, 'shopee') !== false) {
        return 'fa-bag-shopping';
    }
    if (strpos($name, 'tokopedia') !== false) {
        return 'fa-store';
    }
    if (strpos($name, 'tiktok') !== false) {
        return 'fa-music';
    }
    return 'fa-cart-shopping';
}

function marketplace_class($name)
{
    $name = strtolower((string) $name);
    if (strpos($name, 'shopee') !== false) {
        return 'market-shopee';
    }
    if (strpos($name, 'tokopedia') !== false) {
        return 'market-tokopedia';
    }
    if (strpos($name, 'tiktok') !== false) {
        return 'market-tiktok';
    }
    return 'market-default';
}

function get_categories()
{
    return fetch_all('SELECT * FROM categories ORDER BY nama_kategori ASC');
}

function get_marketplaces()
{
    return fetch_all('SELECT * FROM marketplaces ORDER BY nama_marketplace ASC');
}

function get_product_offers($productId)
{
    return fetch_all(
        'SELECT po.*, m.nama_marketplace, m.logo
         FROM product_offers po
         JOIN marketplaces m ON m.id = po.marketplace_id
         WHERE po.product_id = ?
         ORDER BY po.harga ASC',
        [$productId]
    );
}

function best_offer($offers)
{
    if (!$offers) {
        return null;
    }
    usort($offers, function ($a, $b) {
        return (int) $a['harga'] - (int) $b['harga'];
    });
    return $offers[0];
}

function product_image($src)
{
    if ($src) {
        if (preg_match('/^https?:\/\//i', $src) || strpos($src, '/') === 0) {
            return $src;
        }
        return app_url($src);
    }
    return 'https://placehold.co/640x640/f5f7fb/111827?text=Alfayyadh.ID';
}

function site_logo()
{
    $logoPath = 'assets/uploads/logo/ALFAYYADH.png';
    return app_url($logoPath);
}

function setting_value($key, $fallback = '')
{
    $row = fetch_one('SELECT setting_value FROM settings WHERE setting_key = ?', [$key]);
    return $row ? $row['setting_value'] : $fallback;
}

function current_admin()
{
    return isset($_SESSION['admin_user']) ? $_SESSION['admin_user'] : null;
}

function render_product_card($product, $extraClass = '')
{
    // Card produk dipakai ulang di beranda dan halaman kategori.
    $productUrl = app_url('redirect.php?product_id=' . (int) $product['id']);
    $marketClass = marketplace_class($product['marketplace']);
    ob_start();
    ?>
    <article class="product-card <?= e($extraClass) ?>">
        <a class="product-image-link" href="<?= e($productUrl) ?>" target="_blank" rel="nofollow sponsored noopener" aria-label="Lihat <?= e($product['nama_produk']) ?> di <?= e($product['marketplace']) ?>">
            <img src="<?= e(product_image($product['gambar'])) ?>" alt="<?= e($product['nama_produk']) ?>" loading="lazy">
        </a>
        <?php if (!empty($product['badge'])) : ?>
            <span class="product-badge"><?= e($product['badge']) ?></span>
        <?php endif; ?>
        <div class="product-body">
            <h3><a href="<?= e($productUrl) ?>" target="_blank" rel="nofollow sponsored noopener"><?= e($product['nama_produk']) ?></a></h3>
            <div class="rating-line">
                <span class="stars"><?= rating_stars($product['rating']) ?></span>
                <span><?= e($product['rating']) ?></span>
            </div>
            <div class="product-price"><?= rupiah($product['harga']) ?></div>
            <div class="market-row <?= e($marketClass) ?>">
                <i class="fa-solid <?= e(marketplace_icon($product['marketplace'])) ?>"></i>
                <span><?= e($product['marketplace']) ?></span>
            </div>
            <a href="<?= e($productUrl) ?>" class="btn btn-sm btn-affiliate w-100" target="_blank" rel="nofollow sponsored noopener">Lihat Produk</a>
        </div>
    </article>
    <?php
    return ob_get_clean();
}

function admin_badge_status($status)
{
    return $status === 'aktif'
        ? '<span class="badge text-bg-success">Aktif</span>'
        : '<span class="badge text-bg-secondary">Draft</span>';
}

function clean_number($value)
{
    return (int) preg_replace('/[^0-9]/', '', (string) $value);
}
