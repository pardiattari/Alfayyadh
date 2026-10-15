<?php
require_once __DIR__ . '/includes/functions.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$article = $id > 0 ? fetch_one('SELECT * FROM articles WHERE id = ?', [$id]) : null;
$pageTitle = $article ? $article['judul'] : 'Artikel SEO';
$pageDescription = $article ? short_text($article['isi'], 150) : 'Kumpulan artikel SEO dan rekomendasi produk affiliate.';

require_once __DIR__ . '/includes/header.php';
?>

<?php if ($article) : ?>
    <section class="page-hero">
        <div class="container">
            <div class="breadcrumb-lite mb-3">Beranda <i class="fa-solid fa-chevron-right mx-1"></i> Artikel</div>
            <h1><?= e($article['judul']) ?></h1>
            <p class="text-muted"><?= date('d M Y', strtotime($article['tanggal'])) ?> oleh <?= e($article['penulis']) ?></p>
        </div>
    </section>

    <section class="section-block pt-2">
        <div class="container">
            <article class="detail-panel">
                <img class="w-100 rounded-3 mb-4" style="max-height: 420px; object-fit: cover;" src="<?= e(product_image($article['gambar'])) ?>" alt="<?= e($article['judul']) ?>">
                <div class="blog-content">
                    <?php foreach (preg_split('/\r\n|\r|\n/', trim($article['isi'])) as $paragraph) : ?>
                        <?php if (trim($paragraph) !== '') : ?>
                            <p><?= e($paragraph) ?></p>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </article>
        </div>
    </section>
<?php else : ?>
    <?php $articles = fetch_all('SELECT * FROM articles ORDER BY tanggal DESC'); ?>
    <section class="page-hero">
        <div class="container">
            <div class="breadcrumb-lite mb-3">Beranda <i class="fa-solid fa-chevron-right mx-1"></i> Artikel</div>
            <h1>Artikel SEO dan Panduan Belanja</h1>
            <p class="text-muted">Konten rekomendasi untuk membantu pembaca memilih produk yang tepat.</p>
        </div>
    </section>

    <section class="section-block pt-2">
        <div class="container">
            <div class="article-grid">
                <?php foreach ($articles as $item) : ?>
                    <article class="article-card">
                        <img src="<?= e(product_image($item['gambar'])) ?>" alt="<?= e($item['judul']) ?>" loading="lazy">
                        <div>
                            <span><?= date('d M Y', strtotime($item['tanggal'])) ?> oleh <?= e($item['penulis']) ?></span>
                            <h3><?= e($item['judul']) ?></h3>
                            <p><?= e(short_text($item['isi'], 140)) ?></p>
                            <a href="<?= e(app_url('blog.php?id=' . (int) $item['id'])) ?>">Baca Artikel <i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
