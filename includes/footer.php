    </main>
    <footer class="site-footer">
        <div class="container">
            <div class="footer-main">
                <div class="footer-about">
                    <a class="footer-brand" href="<?= e(app_url()) ?>">
                        <i class="fa-solid fa-fire-flame-curved"></i> Alfayyadh.ID
                    </a>
                    <p>Portal produk pilihan dari Shopee, TikTok Shop, dan Tokopedia. Klik produk untuk langsung menuju toko online terkait.</p>
                </div>
                <div class="footer-navigation">
                    <h3>Navigasi</h3>
                    <nav class="footer-nav" aria-label="Navigasi footer">
                        <a class="<?= $currentPage === 'index.php' ? 'active' : '' ?>" href="<?= e(app_url()) ?>">Beranda</a>
                        <a class="<?= in_array('Shopee', $activeMarketplaces, true) ? 'active' : '' ?>" href="<?= e(app_url('kategori.php?marketplace[]=Shopee')) ?>">Shopee</a>
                        <a class="<?= in_array('TikTok Shop', $activeMarketplaces, true) ? 'active' : '' ?>" href="<?= e(app_url('kategori.php?marketplace[]=TikTok%20Shop')) ?>">TikTok</a>
                        <a class="<?= in_array('Tokopedia', $activeMarketplaces, true) ? 'active' : '' ?>" href="<?= e(app_url('kategori.php?marketplace[]=Tokopedia')) ?>">Tokopedia</a>
                        <a href="<?= e(app_url('admin/login.php')) ?>"><i class="fa-regular fa-user" aria-hidden="true"></i> Login</a>
                    </nav>
                </div>
            </div>
            <div class="footer-bottom">
                <span>&copy; <?= date('Y') ?> Alfayyadh.ID. Semua hak dilindungi.</span>
                <span>Affiliate disclosure: beberapa link dapat menghasilkan komisi.</span>
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= e(app_url('assets/js/main.js')) ?>"></script>
</body>
</html>
