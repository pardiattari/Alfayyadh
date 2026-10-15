<?php
require_once __DIR__ . '/includes/auth.php';
require_admin_login();

$adminPageTitle = 'Artikel';
$editId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if (isset($_GET['delete'])) {
    execute_query('DELETE FROM articles WHERE id = ?', [(int) $_GET['delete']]);
    admin_redirect('articles.php?deleted=1');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $judul = trim(isset($_POST['judul']) ? $_POST['judul'] : '');
    $gambar = trim(isset($_POST['gambar']) ? $_POST['gambar'] : '');
    $isi = trim(isset($_POST['isi']) ? $_POST['isi'] : '');
    $tanggal = trim(isset($_POST['tanggal']) ? $_POST['tanggal'] : date('Y-m-d'));
    $penulis = trim(isset($_POST['penulis']) ? $_POST['penulis'] : 'Admin Alfayyadh');
    $isRekomendasi = isset($_POST['is_rekomendasi']) ? 1 : 0;

    if ($isRekomendasi) {
        execute_query('UPDATE articles SET is_rekomendasi = 0');
    }

    if ($id > 0) {
        execute_query('UPDATE articles SET judul = ?, gambar = ?, isi = ?, tanggal = ?, penulis = ?, is_rekomendasi = ? WHERE id = ?', [$judul, $gambar, $isi, $tanggal, $penulis, $isRekomendasi, $id]);
    } else {
        execute_query('INSERT INTO articles (judul, gambar, isi, tanggal, penulis, is_rekomendasi) VALUES (?, ?, ?, ?, ?, ?)', [$judul, $gambar, $isi, $tanggal, $penulis, $isRekomendasi]);
    }
    admin_redirect('articles.php?saved=1');
}

$edit = $editId > 0 ? fetch_one('SELECT * FROM articles WHERE id = ?', [$editId]) : null;
$articles = fetch_all('SELECT * FROM articles ORDER BY tanggal DESC');

require_once __DIR__ . '/includes/header.php';
?>

<?php if (isset($_GET['saved'])) : ?><div class="alert alert-success">Artikel berhasil disimpan.</div><?php endif; ?>
<?php if (isset($_GET['deleted'])) : ?><div class="alert alert-success">Artikel berhasil dihapus.</div><?php endif; ?>

<div class="row g-4">
    <div class="col-xl-5">
        <form class="admin-card" method="post">
            <input type="hidden" name="id" value="<?= e($edit ? $edit['id'] : 0) ?>">
            <h2 class="h5 fw-bold mb-3"><?= $edit ? 'Edit Artikel' : 'Tambah Artikel' ?></h2>
            <div class="mb-3">
                <label class="form-label fw-bold" for="judul">Judul</label>
                <input id="judul" class="form-control" name="judul" value="<?= e($edit ? $edit['judul'] : '') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold" for="gambar">Thumbnail Artikel</label>
                <?php require_once __DIR__ . '/includes/media-field.php'; echo media_field('gambar','gambar', $edit ? $edit['gambar'] : '', 'articles', 'assets/uploads/articles/thumb.jpg'); ?>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold" for="tanggal">Tanggal</label>
                    <input id="tanggal" class="form-control" type="date" name="tanggal" value="<?= e($edit ? $edit['tanggal'] : date('Y-m-d')) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold" for="penulis">Penulis</label>
                    <input id="penulis" class="form-control" name="penulis" value="<?= e($edit ? $edit['penulis'] : 'Admin Alfayyadh') ?>" required>
                </div>
            </div>
            <div class="mb-3 mt-3">
                <label class="form-label fw-bold" for="isi">Isi Artikel</label>
                <textarea id="isi" class="form-control" name="isi" rows="8" required><?= e($edit ? $edit['isi'] : '') ?></textarea>
            </div>
            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" name="is_rekomendasi" id="is_rekomendasi" <?= $edit && (int) $edit['is_rekomendasi'] === 1 ? 'checked' : '' ?>>
                <label class="form-check-label" for="is_rekomendasi">Jadikan Rekomendasi Admin</label>
            </div>
            <button class="btn btn-affiliate w-100" type="submit">Simpan Artikel</button>
        </form>
    </div>
    <div class="col-xl-7">
        <div class="admin-card">
            <h2 class="h5 fw-bold mb-3">Data Artikel</h2>
            <div class="table-responsive">
                <table class="table align-middle admin-table">
                    <thead><tr><th>Artikel</th><th>Tanggal</th><th>Rekomendasi</th><th class="text-end">Aksi</th></tr></thead>
                    <tbody>
                        <?php foreach ($articles as $article) : ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="<?= e(product_image($article['gambar'])) ?>" alt="<?= e($article['judul']) ?>">
                                        <strong><?= e($article['judul']) ?></strong>
                                    </div>
                                </td>
                                <td><?= date('d M Y', strtotime($article['tanggal'])) ?></td>
                                <td><?= (int) $article['is_rekomendasi'] === 1 ? '<span class="badge text-bg-primary">Ya</span>' : '<span class="badge text-bg-light">Tidak</span>' ?></td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-primary" href="<?= e(app_url('admin/articles.php?id=' . (int) $article['id'])) ?>">Edit</a>
                                    <a class="btn btn-sm btn-outline-danger" data-confirm="Hapus artikel ini?" href="<?= e(app_url('admin/articles.php?delete=' . (int) $article['id'])) ?>">Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
