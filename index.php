<?php
$pageTitle = 'Portal Rekomendasi Produk Affiliate';
$pageDescription = 'Temukan produk terbaik dari Shopee, Tokopedia, dan TikTok Shop dalam satu portal rekomendasi.';
require_once __DIR__ . '/includes/header.php';

$trendingProducts = fetch_all(
    'SELECT p.*, c.nama_kategori
     FROM products p
     LEFT JOIN categories c ON c.id = p.kategori_id
     WHERE p.status = "aktif"
     ORDER BY p.is_trending DESC, p.created_at DESC
     LIMIT 5'
);
$marketplaces = get_marketplaces();
$viralProducts = fetch_all(
    'SELECT p.*, c.nama_kategori
     FROM products p
     LEFT JOIN categories c ON c.id = p.kategori_id
     WHERE p.status = "aktif" AND p.is_viral = 1
     ORDER BY p.rating DESC
     LIMIT 3'
);
$articles = fetch_all('SELECT * FROM articles ORDER BY tanggal DESC LIMIT 3');
$recommendation = fetch_one('SELECT * FROM articles WHERE is_rekomendasi = 1 ORDER BY tanggal DESC LIMIT 1');
$promoBanner = fetch_one('SELECT * FROM banners WHERE status = "aktif" ORDER BY id DESC LIMIT 1');

// --- Hero beranda (editable via Admin > Beranda) ---
$heroEyebrow = setting_value('hero_eyebrow', 'Portal Affiliate Marketplace');
$heroTitle = setting_value('hero_title', 'Temukan Produk Terbaik Dari Shopee, Tokopedia & TikTok Shop');
$heroChecksRaw = setting_value('hero_checks', '');
$heroChecks = [];
if ($heroChecksRaw !== '') {
    $dec = json_decode($heroChecksRaw, true);
    if (is_array($dec)) $heroChecks = $dec;
    else $heroChecks = array_values(array_filter(array_map('trim', explode("\n", $heroChecksRaw)), fn($v)=>$v!==''));
}
if (!$heroChecks) $heroChecks = ['Harga Terbaik','Produk Viral','Rekomendasi Pilihan','Promo Terbaru'];
$heroBtnText = setting_value('hero_button_text', 'Mulai Cari Produk');
$heroBtnLink = setting_value('hero_button_link', app_url('kategori.php'));
$heroBgType = setting_value('hero_bg_type', 'gradient');
$heroBgColor = setting_value('hero_bg_color', '#e0f7fa');
$heroGradStart = setting_value('hero_bg_gradient_start', '#e0f7fa');
$heroGradEnd = setting_value('hero_bg_gradient_end', '#b2ebf2');
$heroBgImage = trim(setting_value('hero_bg_image', ''));
$heroOverlay = (int) setting_value('hero_bg_overlay', '0');
$heroOverlay = max(0, min(85, $heroOverlay));
$heroShowVisual = setting_value('hero_show_visual', '1') === '1';
$heroTextColor = setting_value('hero_text_color', '#111827');
if (!preg_match('/^#[0-9a-fA-F]{3,8}$/', $heroTextColor)) $heroTextColor = '#111827';
$heroBgCss = '';
if ($heroBgType === 'image' && $heroBgImage !== '') {
    $imgUrl = $heroBgImage;
    if (!preg_match('/^https?:\/\//i', $imgUrl) && strpos($imgUrl, '/') !== 0) $imgUrl = app_url($imgUrl);
    $imgUrlAttr = htmlspecialchars($imgUrl, ENT_QUOTES, 'UTF-8');
    if ($heroOverlay > 0) {
        $heroBgCss = 'background: linear-gradient(rgba(0,0,0,'.($heroOverlay/100).'), rgba(0,0,0,'.($heroOverlay/100).')), url('.$imgUrlAttr.') center/cover no-repeat;';
    } else {
        $heroBgCss = 'background: url('.$imgUrlAttr.') center/cover no-repeat;';
    }
} elseif ($heroBgType === 'color') {
    $heroBgCss = 'background: '.htmlspecialchars($heroBgColor, ENT_QUOTES, 'UTF-8').';';
} else {
    $heroBgCss = 'background: linear-gradient(110deg, '.htmlspecialchars($heroGradStart, ENT_QUOTES, 'UTF-8').' 0%, '.htmlspecialchars($heroGradEnd, ENT_QUOTES, 'UTF-8').' 100%);';
}
?>
<section class="hero-section">
    <div class="container">
        <div class="hero-panel" style="<?= $heroBgCss ?> color: <?= e($heroTextColor) ?>;">
            <div class="hero-copy" style="color: <?= e($heroTextColor) ?>;">
                <span class="eyebrow" style="color: <?= e($heroTextColor) ?>; opacity:.92;"><i class="fa-solid fa-bolt"></i> <?= e($heroEyebrow) ?></span>
                <h1 style="color: <?= e($heroTextColor) ?>;"><?= nl2br(e($heroTitle)) ?></h1>
                <ul class="hero-checks" style="color: <?= e($heroTextColor) ?>;">
                    <?php foreach ($heroChecks as $c): ?><li><i class="fa-solid fa-check" style="color: <?= e($heroTextColor) ?>; opacity:.85"></i> <?= e($c) ?></li><?php endforeach; ?>
                </ul>
                <a class="btn btn-hero" href="<?= e($heroBtnLink) ?>"><?= e($heroBtnText) ?></a>
            </div>
            <?php if ($heroShowVisual): ?>
            <div class="hero-visual" aria-label="Ilustrasi marketplace Alfayyadh.ID">
                <div class="parcel parcel-a"></div>
                <div class="parcel parcel-b"></div>
                <div class="parcel parcel-c"></div>
                <div class="shop-bag bag-shopee"><i class="fa-solid fa-bag-shopping"></i></div>
                <div class="shop-bag bag-tokopedia"><i class="fa-solid fa-store"></i></div>
                <div class="shop-bag bag-tiktok"><i class="fa-brands fa-tiktok"></i></div>
                <div class="tiny-plant"></div>
            </div>
            <?php else: ?>
            <div class="hero-visual" style="min-height:120px" aria-hidden="true"></div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section-block">
    <div class="container">
        <div class="section-heading">
            <div>
                <span class="section-kicker"><i class="fa-solid fa-fire"></i> Produk Trending Hari Ini</span>
                <h2>Rekomendasi yang Lagi Banyak Dicari</h2>
            </div>
            <a href="<?= e(app_url('kategori.php?sort=trending')) ?>">Lihat Semua <i class="fa-solid fa-chevron-right"></i></a>
        </div>
        <div class="product-grid product-grid-five">
            <?php foreach ($trendingProducts as $product) : ?>
                <?= render_product_card($product) ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section-block section-tight">
    <div class="container">
        <div class="section-heading">
            <div>
                <span class="section-kicker">Pilih Marketplace</span>
                <h2>Belanja dari Toko Online Favorit Anda</h2>
            </div>
        </div>
        <div class="category-strip">
            <?php foreach ($marketplaces as $marketplace) : ?>
                <a class="category-tile <?= e(marketplace_class($marketplace['nama_marketplace'])) ?>" href="<?= e(app_url('kategori.php?' . http_build_query(['marketplace' => [$marketplace['nama_marketplace']]]))) ?>">
                    <span><i class="<?= e($marketplace['logo']) ?>"></i></span>
                    <?= e($marketplace['nama_marketplace']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section-block">
    <div class="container">
        <div class="section-heading">
            <div>
                <span class="section-kicker">Produk Viral</span>
                <h2>Produk Viral Minggu Ini</h2>
            </div>
        </div>
        <div class="viral-grid">
            <?php foreach ($viralProducts as $index => $product) : ?>
                <article class="viral-card">
                    <span class="rank-badge"><?= $index + 1 ?></span>
                    <img src="<?= e(product_image($product['gambar'])) ?>" alt="<?= e($product['nama_produk']) ?>" loading="lazy">
                    <div class="viral-body">
                        <span class="viral-label">Produk Viral</span>
                        <h3><?= e($product['nama_produk']) ?></h3>
                        <p><?= e(short_text($product['deskripsi'], 70)) ?></p>
                        <strong><?= rupiah($product['harga']) ?></strong>
                        <a class="btn btn-sm btn-affiliate w-100" href="<?= e(app_url('produk.php?id=' . (int) $product['id'])) ?>">Lihat Produk</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section-block" id="promo">
    <div class="container">
        <div class="row g-4 align-items-stretch">
            <div class="col-lg-7">
                <div class="promo-panel">
                    <div>
                        <span class="section-kicker text-white">Promo Terbaru</span>
                        <h2><?= e($promoBanner ? $promoBanner['judul'] : 'FLASH SALE Diskon Hingga 70%') ?></h2>
                        <p><?= e($promoBanner ? $promoBanner['subtitle'] : 'Koleksi produk pilihan dengan harga affiliate terbaik hari ini.') ?></p>
                        <div class="timer-row" data-countdown>
                            <span>23</span>
                            <span>10</span>
                            <span>45</span>
                        </div>
                    </div>
                    <a class="btn btn-light" href="<?= e($promoBanner ? $promoBanner['button_link'] : app_url('kategori.php')) ?>">
                        <?= e($promoBanner ? $promoBanner['button_text'] : 'Ambil Promo') ?>
                    </a>
                </div>
            </div>
            <div class="col-lg-5" id="rekomendasi">
                <div class="recommend-panel">
                    <img src="<?= e(product_image($recommendation ? $recommendation['gambar'] : '')) ?>" alt="Rekomendasi Admin">
                    <div>
                        <span class="section-kicker">Pilihan Alfayyadh</span>
                        <h2><?= e($recommendation ? $recommendation['judul'] : '5 Rekomendasi Vacuum Cleaner Terbaik 2026') ?></h2>
                        <p><?= e($recommendation ? short_text($recommendation['isi'], 140) : 'Kami pilihkan produk yang seimbang antara harga, kualitas, dan ulasan pembeli.') ?></p>
                        <a class="btn btn-purple" href="<?= e(app_url('blog.php' . ($recommendation ? '?id=' . (int) $recommendation['id'] : ''))) ?>">Baca Review</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section-block">
    <div class="container">
        <div class="section-heading">
            <div>
                <span class="section-kicker">Artikel SEO</span>
                <h2>Panduan Belanja dan Rekomendasi</h2>
            </div>
            <a href="<?= e(app_url('blog.php')) ?>">Lihat Semua <i class="fa-solid fa-chevron-right"></i></a>
        </div>
        <div class="article-grid">
            <?php foreach ($articles as $article) : ?>
                <article class="article-card">
                    <img src="<?= e(product_image($article['gambar'])) ?>" alt="<?= e($article['judul']) ?>" loading="lazy">
                    <div>
                        <span><?= date('d M Y', strtotime($article['tanggal'])) ?> oleh <?= e($article['penulis']) ?></span>
                        <h3><?= e($article['judul']) ?></h3>
                        <p><?= e(short_text($article['isi'], 120)) ?></p>
                        <a href="<?= e(app_url('blog.php?id=' . (int) $article['id'])) ?>">Baca Artikel <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
