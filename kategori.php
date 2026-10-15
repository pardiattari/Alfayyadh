<?php
require_once __DIR__ . '/includes/functions.php';

$pageTitle = 'Kategori Produk';
$pageDescription = 'Filter produk affiliate berdasarkan kategori, harga, dan rating.';

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$cat = isset($_GET['cat']) ? (int) $_GET['cat'] : 0;
$harga = isset($_GET['harga']) ? $_GET['harga'] : '';
$rating = isset($_GET['rating']) ? (float) $_GET['rating'] : 0;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'terbaru';
$selectedMarkets = isset($_GET['marketplace']) && is_array($_GET['marketplace']) ? $_GET['marketplace'] : [];

$where = ['p.status = "aktif"'];
$params = [];

if ($q !== '') {
    $where[] = '(p.nama_produk LIKE ? OR p.deskripsi LIKE ? OR c.nama_kategori LIKE ?)';
    $params[] = '%' . $q . '%';
    $params[] = '%' . $q . '%';
    $params[] = '%' . $q . '%';
}

if ($cat > 0) {
    $where[] = 'p.kategori_id = ?';
    $params[] = $cat;
}

if ($harga === 'lt100') {
    $where[] = 'p.harga < 100000';
} elseif ($harga === '100to500') {
    $where[] = 'p.harga BETWEEN 100000 AND 500000';
} elseif ($harga === 'gt500') {
    $where[] = 'p.harga > 500000';
}

if ($selectedMarkets) {
    $placeholders = implode(',', array_fill(0, count($selectedMarkets), '?'));
    $where[] = 'p.marketplace IN (' . $placeholders . ')';
    foreach ($selectedMarkets as $market) {
        $params[] = $market;
    }
}

if ($rating > 0) {
    $where[] = 'p.rating >= ?';
    $params[] = $rating;
}

$orderBy = 'p.created_at DESC';
if ($sort === 'harga_terendah') {
    $orderBy = 'p.harga ASC';
} elseif ($sort === 'harga_tertinggi') {
    $orderBy = 'p.harga DESC';
} elseif ($sort === 'rating') {
    $orderBy = 'p.rating DESC';
} elseif ($sort === 'trending') {
    $orderBy = 'p.is_trending DESC, p.rating DESC';
}

$products = fetch_all(
    'SELECT p.*, c.nama_kategori
     FROM products p
     LEFT JOIN categories c ON c.id = p.kategori_id
     WHERE ' . implode(' AND ', $where) . '
     ORDER BY ' . $orderBy,
    $params
);
$categories = get_categories();
$resetUrl = app_url('kategori.php');
if ($selectedMarkets) {
    $resetUrl .= '?' . http_build_query(['marketplace' => $selectedMarkets]);
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="container">
        <div class="breadcrumb-lite mb-3">Beranda <i class="fa-solid fa-chevron-right mx-1"></i> Kategori</div>
        <h1>Elektronik, Fashion, Rumah Tangga dan Lainnya</h1>
        <p class="text-muted">Temukan produk affiliate yang paling sesuai dengan budget dan marketplace favorit Anda.</p>
    </div>
</section>

<section class="section-block pt-2">
    <div class="container">
        <div class="row g-4">
            <aside class="col-lg-3">
                <form class="filter-panel" method="get" action="<?= e(app_url('kategori.php')) ?>">
                    <h2>Filter</h2>
                    <?php foreach ($selectedMarkets as $market) : ?>
                        <input type="hidden" name="marketplace[]" value="<?= e($market) ?>">
                    <?php endforeach; ?>
                    <div class="filter-group">
                        <label class="form-label fw-bold" for="filter-q">Cari</label>
                        <input id="filter-q" class="form-control" type="search" name="q" value="<?= e($q) ?>" placeholder="Nama produk">
                    </div>
                    <div class="filter-group">
                        <label class="form-label fw-bold" for="filter-cat">Kategori</label>
                        <select id="filter-cat" name="cat" class="form-select">
                            <option value="0">Semua Kategori</option>
                            <?php foreach ($categories as $category) : ?>
                                <option value="<?= (int) $category['id'] ?>" <?= $cat === (int) $category['id'] ? 'selected' : '' ?>>
                                    <?= e($category['nama_kategori']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <p class="fw-bold mb-2">Harga</p>
                        <?php
                        $priceOptions = [
                            'lt100' => '< 100 ribu',
                            '100to500' => '100 - 500 ribu',
                            'gt500' => '> 500 ribu',
                        ];
                        foreach ($priceOptions as $value => $label) :
                        ?>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="harga" value="<?= e($value) ?>" id="harga-<?= e($value) ?>" <?= $harga === $value ? 'checked' : '' ?>>
                                <label class="form-check-label" for="harga-<?= e($value) ?>"><?= e($label) ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="filter-group">
                        <p class="fw-bold mb-2">Rating</p>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="rating" value="4" id="rating4" <?= $rating === 4.0 ? 'checked' : '' ?>>
                            <label class="form-check-label" for="rating4">4 ke atas</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="rating" value="5" id="rating5" <?= $rating === 5.0 ? 'checked' : '' ?>>
                            <label class="form-check-label" for="rating5">5</label>
                        </div>
                    </div>
                    <div class="filter-group">
                        <label class="form-label fw-bold" for="sort">Urutkan</label>
                        <select id="sort" name="sort" class="form-select">
                            <option value="terbaru" <?= $sort === 'terbaru' ? 'selected' : '' ?>>Terbaru</option>
                            <option value="trending" <?= $sort === 'trending' ? 'selected' : '' ?>>Trending</option>
                            <option value="rating" <?= $sort === 'rating' ? 'selected' : '' ?>>Rating</option>
                            <option value="harga_terendah" <?= $sort === 'harga_terendah' ? 'selected' : '' ?>>Harga Terendah</option>
                            <option value="harga_tertinggi" <?= $sort === 'harga_tertinggi' ? 'selected' : '' ?>>Harga Tertinggi</option>
                        </select>
                    </div>
                    <button class="btn btn-affiliate w-100" type="submit">Terapkan Filter</button>
                    <a class="btn btn-outline-secondary w-100 mt-2" href="<?= e($resetUrl) ?>">Reset</a>
                </form>
            </aside>
            <div class="col-lg-9">
                <div class="section-heading">
                    <div>
                        <span class="section-kicker">Hasil Produk</span>
                        <h2><?= count($products) ?> produk ditemukan</h2>
                    </div>
                </div>
                <?php if (!$products) : ?>
                    <div class="detail-panel text-center py-5">
                        <h2 class="h4">Belum ada produk</h2>
                        <p class="text-muted">Coba ubah filter atau kata kunci pencarian.</p>
                    </div>
                <?php else : ?>
                    <div class="product-grid">
                        <?php foreach ($products as $product) : ?>
                            <?= render_product_card($product) ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
