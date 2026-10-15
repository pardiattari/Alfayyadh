<?php
require_once __DIR__ . '/includes/auth.php';
require_admin_login();

$adminPageTitle = 'Dashboard';
$totalProducts = fetch_one('SELECT COUNT(*) AS total FROM products');
$totalCategories = fetch_one('SELECT COUNT(*) AS total FROM categories');
$totalArticles = fetch_one('SELECT COUNT(*) AS total FROM articles');
$totalClicks = fetch_one('SELECT COUNT(*) AS total FROM clicks');
$commission = (int) setting_value('commission_estimate', '350000');
$topProducts = fetch_all(
    'SELECT p.nama_produk, p.gambar, COUNT(c.id) AS total_clicks
     FROM products p
     LEFT JOIN clicks c ON c.product_id = p.id
     GROUP BY p.id
     ORDER BY total_clicks DESC, p.rating DESC
     LIMIT 5'
);
$chartRows = fetch_all(
    'SELECT DATE(tanggal) AS day_label, COUNT(*) AS total
     FROM clicks
     WHERE tanggal >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
     GROUP BY DATE(tanggal)
     ORDER BY day_label ASC'
);
$chartLabels = [];
$chartData = [];
foreach ($chartRows as $row) {
    $chartLabels[] = date('d M', strtotime($row['day_label']));
    $chartData[] = (int) $row['total'];
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="admin-card">
            <span class="text-muted">Total Produk</span>
            <div class="stat-number"><?= number_format((int) $totalProducts['total'], 0, ',', '.') ?></div>
            <small class="text-success">Produk affiliate aktif dan draft</small>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="admin-card">
            <span class="text-muted">Kategori</span>
            <div class="stat-number"><?= number_format((int) $totalCategories['total'], 0, ',', '.') ?></div>
            <small class="text-muted">Kanal produk</small>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="admin-card">
            <span class="text-muted">Klik Affiliate</span>
            <div class="stat-number"><?= number_format((int) $totalClicks['total'], 0, ',', '.') ?></div>
            <small class="text-success">Tracking dari redirect.php</small>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="admin-card">
            <span class="text-muted">Estimasi Komisi</span>
            <div class="stat-number"><?= rupiah($commission) ?></div>
            <small class="text-success">Bisa diubah dari pengaturan</small>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-8">
        <div class="admin-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h5 fw-bold mb-0">Grafik Klik Affiliate</h2>
                <span class="badge text-bg-light">7 Hari Terakhir</span>
            </div>
            <canvas id="clickChart" height="120" aria-label="Grafik klik affiliate"></canvas>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="admin-card">
            <h2 class="h5 fw-bold mb-3">Produk Teratas</h2>
            <div class="list-group list-group-flush">
                <?php foreach ($topProducts as $item) : ?>
                    <div class="list-group-item px-0 d-flex align-items-center gap-3">
                        <img src="<?= e(product_image($item['gambar'])) ?>" alt="<?= e($item['nama_produk']) ?>" style="width: 52px; height: 52px; object-fit: cover; border-radius: 8px;">
                        <div class="flex-grow-1">
                            <strong><?= e($item['nama_produk']) ?></strong>
                            <div class="text-muted small"><?= (int) $item['total_clicks'] ?> klik</div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-md-6">
        <div class="admin-card">
            <span class="text-muted">Total Artikel</span>
            <div class="stat-number"><?= number_format((int) $totalArticles['total'], 0, ',', '.') ?></div>
            <small class="text-muted">Artikel SEO dan rekomendasi admin</small>
        </div>
    </div>
    <div class="col-md-6">
        <div class="admin-card">
            <span class="text-muted">CTR Demo</span>
            <div class="stat-number text-orange">25.9%</div>
            <small class="text-muted">Contoh metrik untuk kebutuhan dashboard</small>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var canvas = document.getElementById('clickChart');
    if (!canvas) return;
    var labels = <?= json_encode($chartLabels) ?>;
    var values = <?= json_encode($chartData) ?>;
    var ctx = canvas.getContext('2d');
    var width = canvas.width = canvas.offsetWidth * window.devicePixelRatio;
    var height = canvas.height = 260 * window.devicePixelRatio;
    ctx.scale(window.devicePixelRatio, window.devicePixelRatio);
    var viewW = canvas.offsetWidth;
    var viewH = 260;
    var pad = 34;
    var max = Math.max.apply(null, values.concat([1]));
    ctx.clearRect(0, 0, viewW, viewH);
    ctx.strokeStyle = '#e5e7eb';
    ctx.lineWidth = 1;
    for (var i = 0; i < 4; i++) {
        var y = pad + i * ((viewH - pad * 2) / 3);
        ctx.beginPath();
        ctx.moveTo(pad, y);
        ctx.lineTo(viewW - pad, y);
        ctx.stroke();
    }
    ctx.strokeStyle = '#6d28d9';
    ctx.lineWidth = 3;
    ctx.beginPath();
    values.forEach(function (value, index) {
        var x = pad + index * ((viewW - pad * 2) / Math.max(1, values.length - 1));
        var y = viewH - pad - (value / max) * (viewH - pad * 2);
        if (index === 0) ctx.moveTo(x, y); else ctx.lineTo(x, y);
    });
    ctx.stroke();
    ctx.fillStyle = '#ff5722';
    values.forEach(function (value, index) {
        var x = pad + index * ((viewW - pad * 2) / Math.max(1, values.length - 1));
        var y = viewH - pad - (value / max) * (viewH - pad * 2);
        ctx.beginPath();
        ctx.arc(x, y, 4, 0, Math.PI * 2);
        ctx.fill();
        ctx.fillStyle = '#6b7280';
        ctx.font = '12px Arial';
        ctx.fillText(labels[index] || '', x - 14, viewH - 10);
        ctx.fillStyle = '#ff5722';
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
