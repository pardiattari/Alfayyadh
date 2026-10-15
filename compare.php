<?php
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Perbandingan Harga';
$pageDescription = 'Bandingkan harga produk affiliate dari Shopee, Tokopedia, dan TikTok Shop.';

$allProducts = fetch_all('SELECT id, nama_produk, gambar, harga FROM products WHERE status = "aktif" ORDER BY nama_produk ASC');
$selectedId = isset($_GET['id']) ? (int) $_GET['id'] : (isset($allProducts[0]) ? (int) $allProducts[0]['id'] : 0);
$product = fetch_one('SELECT * FROM products WHERE id = ? AND status = "aktif"', [$selectedId]);
$offers = $product ? get_product_offers($product['id']) : [];
$bestOffer = best_offer($offers);

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <div class="breadcrumb-lite mb-3">Beranda <i class="fa-solid fa-chevron-right mx-1"></i> Perbandingan Harga</div>
        <h1>Bandingkan Harga Marketplace</h1>
        <p class="text-muted">Lihat pilihan harga untuk produk yang sama sebelum klik link affiliate.</p>
    </div>
</section>

<section class="section-block pt-2">
    <div class="container">
        <div class="detail-panel mb-4">
            <form class="row g-3 align-items-end" action="<?= e(app_url('compare.php')) ?>" method="get">
                <div class="col-lg-9">
                    <label class="form-label fw-bold" for="product-select">Produk</label>
                    <select id="product-select" name="id" class="form-select">
                        <?php foreach ($allProducts as $item) : ?>
                            <option value="<?= (int) $item['id'] ?>" <?= $selectedId === (int) $item['id'] ? 'selected' : '' ?>>
                                <?= e($item['nama_produk']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-lg-3">
                    <button class="btn btn-affiliate w-100" type="submit">Bandingkan</button>
                </div>
            </form>
        </div>

        <?php if ($product) : ?>
            <div class="row g-4 align-items-stretch">
                <div class="col-lg-4">
                    <div class="compare-card h-100">
                        <img class="main-product-image mb-3" src="<?= e(product_image($product['gambar'])) ?>" alt="<?= e($product['nama_produk']) ?>">
                        <span class="section-kicker">Produk</span>
                        <h2 class="h4 fw-bold mt-2"><?= e($product['nama_produk']) ?></h2>
                        <p class="text-muted"><?= e(short_text($product['deskripsi'], 130)) ?></p>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="compare-grid">
                        <?php foreach ($offers as $offer) : ?>
                            <?php $isCheapest = $bestOffer && (int) $bestOffer['id'] === (int) $offer['id']; ?>
                            <article class="compare-card <?= $isCheapest ? 'cheapest' : '' ?>">
                                <?php if ($isCheapest) : ?>
                                    <span class="cheapest-label mb-3">Harga Termurah</span>
                                <?php endif; ?>
                                <div class="offer-name <?= e(marketplace_class($offer['nama_marketplace'])) ?> mb-2">
                                    <i class="fa-solid <?= e(marketplace_icon($offer['nama_marketplace'])) ?>"></i>
                                    <?= e($offer['nama_marketplace']) ?>
                                </div>
                                <div class="display-6 fw-black text-orange mb-3"><?= rupiah($offer['harga']) ?></div>
                                <p class="text-muted small">Harga dapat berubah mengikuti marketplace. Klik untuk cek detail terbaru.</p>
                                <a class="btn btn-affiliate w-100" href="<?= e(app_url('redirect.php?offer_id=' . (int) $offer['id'])) ?>" target="_blank" rel="nofollow sponsored noopener">
                                    Cek di <?= e($offer['nama_marketplace']) ?>
                                </a>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
