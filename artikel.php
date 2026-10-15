<?php
require_once __DIR__ . '/config/config.php';

$page_title = 'Artikel';
$page_description = 'Baca artikel, tips belanja, review produk, dan panduan memilih produk terbaik di marketplace Indonesia';

$db = getDB();
$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    header('Location: ' . url('blog.php'));
    exit;
}

$stmt = $db->prepare("SELECT * FROM articles WHERE slug = ? AND status = 'published'");
$stmt->execute([$slug]);
$article = $stmt->fetch();

if (!$article) {
    http_response_code(404);
    $page_title = 'Artikel Tidak Ditemukan';
    require_once __DIR__ . '/templates/header.php';
?>
<main class="py-5">
    <div class="container">
        <div class="text-center py-5">
            <i class="fas fa-newspaper fa-4x text-muted mb-3"></i>
            <h1 class="text-navy">Artikel Tidak Ditemukan</h1>
            <p class="text-muted">Artikel yang Anda cari tidak ada atau belum dipublikasikan.</p>
            <a href="<?php echo url('blog.php'); ?>" class="btn btn-primary"><i class="fas fa-arrow-left me-1"></i>Kembali ke Blog</a>
        </div>
    </div>
</main>
<?php
    require_once __DIR__ . '/templates/footer.php';
    exit;
}

// Update view count
$db->prepare("UPDATE articles SET view_count = view_count + 1 WHERE id = ?")->execute([$article['id']]);

// Get related articles
$relatedStmt = $db->prepare("SELECT * FROM articles WHERE status = 'published' AND id != ? ORDER BY RAND() LIMIT 3");
$relatedStmt->execute([$article['id']]);
$relatedArticles = $relatedStmt->fetchAll();

$page_title = sanitize($article['title']);
$page_description = sanitize($article['excerpt']);

require_once __DIR__ . '/templates/header.php';
?>
<main>
    <!-- Breadcrumb -->
    <nav aria-label="Breadcrumb" class="py-3 bg-light border-bottom border-border">
        <div class="container">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?php echo url('index.php'); ?>">Beranda</a></li>
                <li class="breadcrumb-item"><a href="<?php echo url('blog.php'); ?>">Artikel</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo sanitize($article['title']); ?></li>
            </ol>
        </div>
    </nav>

    <!-- Article -->
    <article class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-10">
                    <header class="mb-4">
                        <?php if ($article['category']): ?>
                            <span class="badge bg-primary-soft text-primary mb-2"><?php echo sanitize($article['category']); ?></span>
                        <?php endif; ?>
                        <h1 class="display-5 fw-bold text-navy mb-3"><?php echo sanitize($article['title']); ?></h1>
                        <div class="d-flex flex-wrap align-items-center gap-3 text-muted small">
                            <span><i class="far fa-calendar-alt me-1"></i><?php echo date('d F Y', strtotime($article['published_at'] ?? $article['created_at'])); ?></span>
                            <span><i class="far fa-eye me-1"></i><?php echo number_format($article['view_count'] ?? 0); ?> dilihat</span>
                            <?php if ($article['tags']): ?>
                                <span><i class="fas fa-tags me-1"></i><?php echo sanitize($article['tags']); ?></span>
                            <?php endif; ?>
                        </div>
                    </header>

                    <?php if ($article['featured_image']): ?>
                        <figure class="mb-4">
                            <img src="<?php echo asset($article['featured_image']); ?>" alt="<?php echo sanitize($article['title']); ?>" class="img-fluid rounded shadow-sm w-100">
                        </figure>
                    <?php endif; ?>

                    <div class="article-content lead text-muted mb-4 p-3 bg-light rounded border border-border">
                        <?php echo nl2br(sanitize($article['excerpt'])); ?>
                    </div>

                    <div class="article-body" style="font-size: 1.05rem; line-height: 1.8;">
                        <?php echo $article['content']; ?>
                    </div>

                    <footer class="mt-5 pt-4 border-top border-border">
                        <?php if ($article['tags']): ?>
                            <div class="mb-3">
                                <strong class="text-navy me-2">Tags:</strong>
                                <?php foreach (explode(',', $article['tags']) as $tag): ?>
                                    <a href="<?php echo url('blog.php?tag=' . urlencode(trim($tag))); ?>" class="badge bg-light text-navy text-decoration-none me-1"><?php echo sanitize(trim($tag)); ?></a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn btn-outline-primary" onclick="shareArticle()"><i class="fas fa-share-alt me-1"></i>Bagikan</button>
                            <a href="<?php echo url('blog.php'); ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Kembali ke Blog</a>
                        </div>
                    </footer>
                </div>
            </div>

            <!-- Related Articles -->
            <?php if (!empty($relatedArticles)): ?>
                <section class="mt-5">
                    <h2 class="h4 fw-bold text-navy mb-4">Artikel Terkait</h2>
                    <div class="row g-4">
                        <?php foreach ($relatedArticles as $ra): ?>
                            <div class="col-md-4">
                                <article class="card h-100 border-border hover-shadow transition">
                                    <?php if ($ra['featured_image']): ?>
                                        <img src="<?php echo asset($ra['featured_image']); ?>" alt="" class="card-img-top" style="height: 180px; object-fit: cover;">
                                    <?php endif; ?>
                                    <div class="card-body d-flex flex-column">
                                        <?php if ($ra['category']): ?>
                                            <span class="badge bg-primary-soft text-primary mb-2"><?php echo sanitize($ra['category']); ?></span>
                                        <?php endif; ?>
                                        <h3 class="h6 fw-bold text-navy mb-2"><?php echo sanitize($ra['title']); ?></h3>
                                        <p class="text-muted small flex-grow-1"><?php echo sanitize($ra['excerpt'] ?? ''); ?></p>
                                        <a href="<?php echo url("artikel.php?slug={$ra['slug']}"); ?>" class="btn btn-sm btn-outline-primary mt-3">Baca Selengkapnya</a>
                                    </div>
                                </article>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
        </div>
    </article>
</main>

<script>
function shareArticle() {
    if (navigator.share) {
        navigator.share({
            title: '<?php echo addslashes($article['title']); ?>',
            text: '<?php echo addslashes($article['excerpt']); ?>',
            url: window.location.href
        }).catch(console.error);
    } else {
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('Link artikel disalin ke clipboard!');
        });
    }
}
</script>

<?php require_once __DIR__ . '/templates/footer.php'; ?>
