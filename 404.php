<?php
// 404 Error Page - Custom 404 for Alfayyadh.ID
$pageTitle = 'Halaman Tidak Ditemukan';
$pageDescription = 'Maaf, halaman yang Anda cari tidak ditemukan.';

require_once __DIR__ . '/includes/header.php';
?>

<section class="error-section">
    <div class="container">
        <div class="error-card">
            <div class="error-code">404</div>
            <h1>Halaman Tidak Ditemukan</h1>
            <p class="lead">Maaf, halaman yang Anda cari tidak tersedia atau telah dipindahkan.</p>
            <div class="error-actions">
                <a href="<?= e(app_url()) ?>" class="btn btn-primary">
                    <i class="fa-solid fa-house"></i> Kembali ke Beranda
                </a>
                <a href="<?= e(app_url('kategori.php')) ?>" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-search"></i> Cari Produk
                </a>
            </div>
            <div class="error-help mt-4">
                <small class="text-muted">
                    Jika Anda mengikuti tautan dari situs lain, tautan tersebut mungkin sudah kadaluarsa.
                    Silakan hubungi kami jika Anda percaya ini adalah kesalahan.
                </small>
            </div>
        </div>
    </div>
</section>

<style>
.error-section { min-height: 60vh; display: flex; align-items: center; justify-content: center; padding: 4rem 1rem; }
.error-card { text-align: center; max-width: 480px; }
.error-code { font-size: 8rem; font-weight: 800; color: var(--color-primary); line-height: 1; opacity: 0.15; margin-bottom: -1.5rem; }
.error-card h1 { font-size: 2rem; margin-bottom: 1rem; }
.error-card .lead { color: var(--color-muted); margin-bottom: 2rem; }
.error-actions { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
