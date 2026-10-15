<?php
require_once __DIR__ . '/includes/functions.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$product = fetch_one(
    'SELECT p.*, c.nama_kategori
     FROM products p
     LEFT JOIN categories c ON c.id = p.kategori_id
     WHERE p.id = ? AND p.status = "aktif"',
    [$id]
);

if (!$product) {
    http_response_code(404);
    $pageTitle = 'Produk Tidak Ditemukan';
    require_once __DIR__ . '/includes/header.php';
    ?>
    <section class="page-hero">
        <div class="container">
            <div class="detail-panel text-center py-5">
                <h1>Produk tidak ditemukan</h1>
                <p class="text-muted">Produk mungkin belum aktif atau sudah dihapus.</p>
                <a class="btn btn-affiliate" href="<?= e(app_url('kategori.php')) ?>">Lihat Produk Lain</a>
            </div>
        </div>
    </section>
    <?php
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = $product['nama_produk'];
$pageDescription = short_text($product['deskripsi'], 150);
$offers = get_product_offers($product['id']);
$bestOffer = best_offer($offers);
$gallery = [
    product_image($product['gambar']),
    'https://placehold.co/640x640/f8fafc/111827?text=Detail+Produk',
    'https://placehold.co/640x640/fff4ed/ff5722?text=Spesifikasi',
    'https://placehold.co/640x640/ecfdf5/00a651?text=Review',
];
$specs = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $product['spesifikasi'])));
$pros = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $product['kelebihan'])));
$cons = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $product['kekurangan'])));

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <div class="breadcrumb-lite mb-3">
            Beranda <i class="fa-solid fa-chevron-right mx-1"></i>
            <?= e($product['nama_kategori']) ?> <i class="fa-solid fa-chevron-right mx-1"></i>
            <?= e($product['nama_produk']) ?>
        </div>
    </div>
</section>

<section class="section-block pt-0">
    <div class="container">
        <div class="detail-panel">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="detail-gallery">
                        <img class="main-product-image" src="<?= e($gallery[0]) ?>" alt="<?= e($product['nama_produk']) ?>" data-main-image>
                        <div class="thumb-row">
                            <?php foreach ($gallery as $image) : ?>
                                <img src="<?= e($image) ?>" alt="Galeri <?= e($product['nama_produk']) ?>" data-gallery-thumb>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <span class="section-kicker"><?= e($product['nama_kategori']) ?></span>
                    <h1 class="mt-2 mb-2"><?= e($product['nama_produk']) ?></h1>
                    <div class="rating-line mb-3">
                        <span class="stars"><?= rating_stars($product['rating']) ?></span>
                        <span><?= e($product['rating']) ?> (<?= number_format((int) $product['review_count'], 0, ',', '.') ?> Review)</span>
                    </div>
                    <p class="text-muted mb-1">Harga mulai:</p>
                    <div class="display-6 fw-black text-orange mb-4">
                        <?= rupiah($bestOffer ? $bestOffer['harga'] : $product['harga']) ?>
                    </div>

                    <h2 class="h5 fw-bold mb-3">Pilih Marketplace</h2>
                    <div class="offer-list">
                        <?php foreach ($offers as $offer) : ?>
                            <div class="offer-item">
                                <div>
                                    <div class="offer-name <?= e(marketplace_class($offer['nama_marketplace'])) ?>">
                                        <i class="fa-solid <?= e(marketplace_icon($offer['nama_marketplace'])) ?>"></i>
                                        <?= e($offer['nama_marketplace']) ?>
                                    </div>
                                    <div class="offer-price"><?= rupiah($offer['harga']) ?></div>
                                </div>
                                <a class="btn btn-affiliate" href="<?= e(app_url('redirect.php?offer_id=' . (int) $offer['id'])) ?>" target="_blank" rel="nofollow sponsored noopener">
                                    Beli di <?= e($offer['nama_marketplace']) ?>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="d-flex flex-wrap gap-3 mt-4 text-muted small">
                        <span><i class="fa-solid fa-shield-halved"></i> Garansi Ori</span>
                        <span><i class="fa-solid fa-truck-fast"></i> Pengiriman Cepat</span>
                        <span><i class="fa-solid fa-rotate-left"></i> Return 7 Hari</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="detail-panel mt-4">
            <ul class="nav nav-tabs" id="productTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="desc-tab" data-bs-toggle="tab" data-bs-target="#desc" type="button" role="tab">Deskripsi</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="spec-tab" data-bs-toggle="tab" data-bs-target="#spec" type="button" role="tab">Spesifikasi</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="review-tab" data-bs-toggle="tab" data-bs-target="#review" type="button" role="tab">Review</button>
                </li>
            </ul>
            <div class="tab-content pt-4">
                <div class="tab-pane fade show active" id="desc" role="tabpanel">
                    <p class="text-muted"><?= nl2br(e($product['deskripsi'])) ?></p>
                    <div class="row g-4 mt-2">
                        <div class="col-md-6">
                            <h3 class="h6 text-success fw-bold">Kelebihan</h3>
                            <ul class="benefit-list good">
                                <?php foreach ($pros as $item) : ?>
                                    <li><i class="fa-solid fa-circle-check"></i><?= e($item) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h3 class="h6 text-danger fw-bold">Kekurangan</h3>
                            <ul class="benefit-list bad">
                                <?php foreach ($cons as $item) : ?>
                                    <li><i class="fa-solid fa-circle-xmark"></i><?= e($item) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="spec" role="tabpanel">
                    <ul class="benefit-list good">
                        <?php foreach ($specs as $item) : ?>
                            <li><i class="fa-solid fa-circle-check"></i><?= e($item) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="tab-pane fade" id="review" role="tabpanel">
                    <p class="text-muted mb-0">Mayoritas pembeli menyukai kualitas produk, harga kompetitif, dan proses pengiriman yang cepat. Rating ringkasan: <strong><?= e($product['rating']) ?>/5</strong>.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
