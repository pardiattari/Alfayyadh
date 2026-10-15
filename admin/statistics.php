<?php
require_once __DIR__ . '/includes/auth.php';
require_admin_login();

$adminPageTitle = 'Statistik';
$summary = fetch_one('SELECT COUNT(*) AS total_clicks, COUNT(DISTINCT product_id) AS product_clicked FROM clicks');
$daily = fetch_all(
    'SELECT DATE(tanggal) AS tanggal, COUNT(*) AS total
     FROM clicks
     GROUP BY DATE(tanggal)
     ORDER BY tanggal DESC
     LIMIT 14'
);
$products = fetch_all(
    'SELECT p.nama_produk, p.marketplace, p.harga, COUNT(c.id) AS total_clicks
     FROM products p
     LEFT JOIN clicks c ON c.product_id = p.id
     GROUP BY p.id
     ORDER BY total_clicks DESC, p.nama_produk ASC'
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="admin-card">
            <span class="text-muted">Klik Affiliate</span>
            <div class="stat-number"><?= number_format((int) $summary['total_clicks'], 0, ',', '.') ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="admin-card">
            <span class="text-muted">Produk Diklik</span>
            <div class="stat-number"><?= number_format((int) $summary['product_clicked'], 0, ',', '.') ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="admin-card">
            <span class="text-muted">Estimasi Komisi</span>
            <div class="stat-number"><?= rupiah((int) setting_value('commission_estimate', '350000')) ?></div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="admin-card">
            <h2 class="h5 fw-bold mb-3">Klik Harian</h2>
            <div class="table-responsive">
                <table class="table">
                    <thead><tr><th>Tanggal</th><th>Total Klik</th></tr></thead>
                    <tbody>
                        <?php foreach ($daily as $row) : ?>
                            <tr><td><?= date('d M Y', strtotime($row['tanggal'])) ?></td><td><?= (int) $row['total'] ?></td></tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="admin-card">
            <h2 class="h5 fw-bold mb-3">Performa Produk</h2>
            <div class="table-responsive">
                <table class="table">
                    <thead><tr><th>Produk</th><th>Marketplace</th><th>Harga</th><th>Klik</th></tr></thead>
                    <tbody>
                        <?php foreach ($products as $row) : ?>
                            <tr>
                                <td><?= e($row['nama_produk']) ?></td>
                                <td><?= e($row['marketplace']) ?></td>
                                <td><?= rupiah($row['harga']) ?></td>
                                <td><?= (int) $row['total_clicks'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
