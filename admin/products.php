<?php
require_once __DIR__ . '/includes/auth.php';
require_admin_login();

$adminPageTitle = 'Produk';
$categories = get_categories();
$marketplaces = get_marketplaces();
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if (isset($_GET['delete'])) {
    execute_query('DELETE FROM products WHERE id = ?', [(int) $_GET['delete']]);
    admin_redirect('products.php?deleted=1');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $nama = trim(isset($_POST['nama_produk']) ? $_POST['nama_produk'] : '');
    $kategoriId = isset($_POST['kategori_id']) ? (int) $_POST['kategori_id'] : null;
    $deskripsi = trim(isset($_POST['deskripsi']) ? $_POST['deskripsi'] : '');
    $harga = clean_number(isset($_POST['harga']) ? $_POST['harga'] : 0);
    $gambar = trim(isset($_POST['gambar']) ? $_POST['gambar'] : '');
    $rating = isset($_POST['rating']) ? (float) $_POST['rating'] : 0;
    $reviewCount = isset($_POST['review_count']) ? (int) $_POST['review_count'] : 0;
    $marketplace = trim(isset($_POST['marketplace']) ? $_POST['marketplace'] : 'Shopee');
    $linkAffiliate = trim(isset($_POST['link_affiliate']) ? $_POST['link_affiliate'] : '');
    $status = isset($_POST['status']) && $_POST['status'] === 'draft' ? 'draft' : 'aktif';
    $isTrending = isset($_POST['is_trending']) ? 1 : 0;
    $isViral = isset($_POST['is_viral']) ? 1 : 0;
    $badge = trim(isset($_POST['badge']) ? $_POST['badge'] : '');
    $spesifikasi = trim(isset($_POST['spesifikasi']) ? $_POST['spesifikasi'] : '');
    $kelebihan = trim(isset($_POST['kelebihan']) ? $_POST['kelebihan'] : '');
    $kekurangan = trim(isset($_POST['kekurangan']) ? $_POST['kekurangan'] : '');

    if ($productId > 0) {
        execute_query(
            'UPDATE products SET nama_produk = ?, kategori_id = ?, deskripsi = ?, harga = ?, gambar = ?, rating = ?, review_count = ?, marketplace = ?, link_affiliate = ?, status = ?, is_trending = ?, is_viral = ?, badge = ?, spesifikasi = ?, kelebihan = ?, kekurangan = ? WHERE id = ?',
            [$nama, $kategoriId, $deskripsi, $harga, $gambar, $rating, $reviewCount, $marketplace, $linkAffiliate, $status, $isTrending, $isViral, $badge, $spesifikasi, $kelebihan, $kekurangan, $productId]
        );
    } else {
        execute_query(
            'INSERT INTO products (nama_produk, kategori_id, deskripsi, harga, gambar, rating, review_count, marketplace, link_affiliate, status, is_trending, is_viral, badge, spesifikasi, kelebihan, kekurangan, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())',
            [$nama, $kategoriId, $deskripsi, $harga, $gambar, $rating, $reviewCount, $marketplace, $linkAffiliate, $status, $isTrending, $isViral, $badge, $spesifikasi, $kelebihan, $kekurangan]
        );
        $productId = (int) db()->lastInsertId();
    }

    $offerPrices = isset($_POST['offer_price']) && is_array($_POST['offer_price']) ? $_POST['offer_price'] : [];
    $offerLinks = isset($_POST['offer_link']) && is_array($_POST['offer_link']) ? $_POST['offer_link'] : [];

    // Offer marketplace disimpan terpisah agar satu produk bisa dibandingkan di tiga kanal.
    foreach ($marketplaces as $marketplaceRow) {
        $marketId = (int) $marketplaceRow['id'];
        $offerPrice = clean_number(isset($offerPrices[$marketId]) ? $offerPrices[$marketId] : 0);
        $offerLink = trim(isset($offerLinks[$marketId]) ? $offerLinks[$marketId] : '');
        $existing = fetch_one('SELECT id FROM product_offers WHERE product_id = ? AND marketplace_id = ?', [$productId, $marketId]);

        if ($offerPrice > 0 && $offerLink !== '') {
            if ($existing) {
                execute_query('UPDATE product_offers SET harga = ?, link_affiliate = ? WHERE id = ?', [$offerPrice, $offerLink, $existing['id']]);
            } else {
                execute_query('INSERT INTO product_offers (product_id, marketplace_id, harga, link_affiliate) VALUES (?, ?, ?, ?)', [$productId, $marketId, $offerPrice, $offerLink]);
            }
        } elseif ($existing) {
            execute_query('DELETE FROM product_offers WHERE id = ?', [$existing['id']]);
        }
    }

    admin_redirect('products.php?saved=1');
}

$product = null;
$offersByMarket = [];
if (($action === 'edit' || $action === 'add') && $id > 0) {
    $product = fetch_one('SELECT * FROM products WHERE id = ?', [$id]);
    foreach (get_product_offers($id) as $offer) {
        $offersByMarket[(int) $offer['marketplace_id']] = $offer;
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<?php if (isset($_GET['saved'])) : ?>
    <div class="alert alert-success">Produk berhasil disimpan.</div>
<?php endif; ?>
<?php if (isset($_GET['deleted'])) : ?>
    <div class="alert alert-success">Produk berhasil dihapus.</div>
<?php endif; ?>

<?php if ($action === 'add' || $action === 'edit') : ?>
    <?php
    $field = function ($key, $default = '') use ($product) {
        return $product && isset($product[$key]) ? $product[$key] : $default;
    };
    ?>
    <form class="admin-card" method="post">
        <input type="hidden" name="id" value="<?= e($field('id', 0)) ?>">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h5 fw-bold mb-0"><?= $product ? 'Edit Produk' : 'Tambah Produk' ?></h2>
            <a class="btn btn-outline-secondary" href="<?= e(app_url('admin/products.php')) ?>">Kembali</a>
        </div>
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="mb-3">
                    <label class="form-label fw-bold" for="nama_produk">Nama Produk</label>
                    <input id="nama_produk" class="form-control" name="nama_produk" value="<?= e($field('nama_produk')) ?>" required>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold" for="kategori_id">Kategori</label>
                        <select id="kategori_id" class="form-select" name="kategori_id" required>
                            <?php foreach ($categories as $category) : ?>
                                <option value="<?= (int) $category['id'] ?>" <?= (int) $field('kategori_id') === (int) $category['id'] ? 'selected' : '' ?>>
                                    <?= e($category['nama_kategori']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold" for="gambar">Gambar Produk</label>
                        <?php require_once __DIR__ . '/includes/media-field.php'; echo media_field('gambar','gambar',$field('gambar'),'products','assets/uploads/products/foto.jpg'); ?>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold" for="harga">Harga Utama</label>
                        <input id="harga" class="form-control" name="harga" value="<?= e($field('harga')) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold" for="rating">Rating</label>
                        <input id="rating" class="form-control" type="number" step="0.1" min="0" max="5" name="rating" value="<?= e($field('rating', '4.8')) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold" for="review_count">Jumlah Review</label>
                        <input id="review_count" class="form-control" type="number" name="review_count" value="<?= e($field('review_count', '0')) ?>">
                    </div>
                </div>
                <div class="mb-3 mt-3">
                    <label class="form-label fw-bold" for="deskripsi">Deskripsi</label>
                    <textarea id="deskripsi" class="form-control" name="deskripsi" rows="5" required><?= e($field('deskripsi')) ?></textarea>
                </div>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold" for="spesifikasi">Spesifikasi</label>
                        <textarea id="spesifikasi" class="form-control" name="spesifikasi" rows="5" placeholder="Satu poin per baris"><?= e($field('spesifikasi')) ?></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold" for="kelebihan">Kelebihan</label>
                        <textarea id="kelebihan" class="form-control" name="kelebihan" rows="5" placeholder="Satu poin per baris"><?= e($field('kelebihan')) ?></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold" for="kekurangan">Kekurangan</label>
                        <textarea id="kekurangan" class="form-control" name="kekurangan" rows="5" placeholder="Satu poin per baris"><?= e($field('kekurangan')) ?></textarea>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold" for="marketplace">Marketplace Utama</label>
                        <select id="marketplace" class="form-select" name="marketplace">
                            <?php foreach ($marketplaces as $marketplaceRow) : ?>
                                <option value="<?= e($marketplaceRow['nama_marketplace']) ?>" <?= $field('marketplace', 'Shopee') === $marketplaceRow['nama_marketplace'] ? 'selected' : '' ?>>
                                    <?= e($marketplaceRow['nama_marketplace']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold" for="status">Status</label>
                        <select id="status" class="form-select" name="status">
                            <option value="aktif" <?= $field('status', 'aktif') === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                            <option value="draft" <?= $field('status') === 'draft' ? 'selected' : '' ?>>Draft</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3 mt-3">
                    <label class="form-label fw-bold" for="link_affiliate">Link Affiliate Utama</label>
                    <input id="link_affiliate" class="form-control" name="link_affiliate" value="<?= e($field('link_affiliate')) ?>" placeholder="https://..." required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold" for="badge">Badge</label>
                    <input id="badge" class="form-control" name="badge" value="<?= e($field('badge')) ?>" placeholder="Trending, Viral, Best Deal">
                </div>
                <div class="d-flex gap-4 mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_trending" id="is_trending" <?= (int) $field('is_trending') === 1 ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_trending">Trending</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_viral" id="is_viral" <?= (int) $field('is_viral') === 1 ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_viral">Viral</label>
                    </div>
                </div>

                <h3 class="h6 fw-bold">Harga dan Link Marketplace</h3>
                <p class="text-muted small">Isi minimal satu marketplace agar halaman detail dan perbandingan harga aktif.</p>
                <?php foreach ($marketplaces as $marketplaceRow) : ?>
                    <?php $offer = isset($offersByMarket[(int) $marketplaceRow['id']]) ? $offersByMarket[(int) $marketplaceRow['id']] : null; ?>
                    <div class="border rounded-3 p-3 mb-3">
                        <div class="fw-bold mb-2"><i class="<?= e($marketplaceRow['logo']) ?>"></i> <?= e($marketplaceRow['nama_marketplace']) ?></div>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <input class="form-control" name="offer_price[<?= (int) $marketplaceRow['id'] ?>]" value="<?= e($offer ? $offer['harga'] : '') ?>" placeholder="Harga">
                            </div>
                            <div class="col-md-8">
                                <input class="form-control" name="offer_link[<?= (int) $marketplaceRow['id'] ?>]" value="<?= e($offer ? $offer['link_affiliate'] : '') ?>" placeholder="Link affiliate">
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <button class="btn btn-affiliate w-100" type="submit">Simpan Produk</button>
            </div>
        </div>
    </form>
<?php else : ?>
    <?php
    $products = fetch_all(
        'SELECT p.*, c.nama_kategori
         FROM products p
         LEFT JOIN categories c ON c.id = p.kategori_id
         ORDER BY p.created_at DESC'
    );
    ?>
    <div class="admin-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h5 fw-bold mb-0">Data Produk</h2>
            <a class="btn btn-affiliate" href="<?= e(app_url('admin/products.php?action=add')) ?>"><i class="fa-solid fa-plus"></i> Tambah Produk</a>
        </div>
        <div class="table-responsive">
            <table class="table align-middle admin-table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Kategori</th>
                        <th>Marketplace</th>
                        <th>Harga</th>
                        <th>Rating</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $item) : ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="<?= e(product_image($item['gambar'])) ?>" alt="<?= e($item['nama_produk']) ?>">
                                    <div>
                                        <strong><?= e($item['nama_produk']) ?></strong>
                                        <div class="text-muted small"><?= e($item['badge']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><?= e($item['nama_kategori']) ?></td>
                            <td><?= e($item['marketplace']) ?></td>
                            <td><?= rupiah($item['harga']) ?></td>
                            <td><?= e($item['rating']) ?></td>
                            <td><?= admin_badge_status($item['status']) ?></td>
                            <td class="text-end">
                                <a class="btn btn-sm btn-outline-primary" href="<?= e(app_url('admin/products.php?action=edit&id=' . (int) $item['id'])) ?>">Edit</a>
                                <a class="btn btn-sm btn-outline-danger" data-confirm="Hapus produk ini?" href="<?= e(app_url('admin/products.php?delete=' . (int) $item['id'])) ?>">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
