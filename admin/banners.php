<?php
require_once __DIR__ . '/includes/auth.php';
require_admin_login();

$adminPageTitle = 'Banner';
$editId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if (isset($_GET['delete'])) {
    execute_query('DELETE FROM banners WHERE id = ?', [(int) $_GET['delete']]);
    admin_redirect('banners.php?deleted=1');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $judul = trim(isset($_POST['judul']) ? $_POST['judul'] : '');
    $subtitle = trim(isset($_POST['subtitle']) ? $_POST['subtitle'] : '');
    $gambar = trim(isset($_POST['gambar']) ? $_POST['gambar'] : '');
    $buttonText = trim(isset($_POST['button_text']) ? $_POST['button_text'] : 'Lihat Promo');
    $buttonLink = trim(isset($_POST['button_link']) ? $_POST['button_link'] : app_url('kategori.php'));
    $status = isset($_POST['status']) && $_POST['status'] === 'draft' ? 'draft' : 'aktif';

    if ($id > 0) {
        execute_query('UPDATE banners SET judul = ?, subtitle = ?, gambar = ?, button_text = ?, button_link = ?, status = ? WHERE id = ?', [$judul, $subtitle, $gambar, $buttonText, $buttonLink, $status, $id]);
    } else {
        execute_query('INSERT INTO banners (judul, subtitle, gambar, button_text, button_link, status) VALUES (?, ?, ?, ?, ?, ?)', [$judul, $subtitle, $gambar, $buttonText, $buttonLink, $status]);
    }
    admin_redirect('banners.php?saved=1');
}

$edit = $editId > 0 ? fetch_one('SELECT * FROM banners WHERE id = ?', [$editId]) : null;
$banners = fetch_all('SELECT * FROM banners ORDER BY id DESC');

require_once __DIR__ . '/includes/header.php';
?>

<?php if (isset($_GET['saved'])) : ?><div class="alert alert-success">Banner berhasil disimpan.</div><?php endif; ?>
<?php if (isset($_GET['deleted'])) : ?><div class="alert alert-success">Banner berhasil dihapus.</div><?php endif; ?>

<div class="row g-4">
    <div class="col-lg-5">
        <form class="admin-card" method="post">
            <input type="hidden" name="id" value="<?= e($edit ? $edit['id'] : 0) ?>">
            <h2 class="h5 fw-bold mb-3"><?= $edit ? 'Edit Banner' : 'Tambah Banner' ?></h2>
            <div class="mb-3">
                <label class="form-label fw-bold" for="judul">Judul</label>
                <input id="judul" class="form-control" name="judul" value="<?= e($edit ? $edit['judul'] : '') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold" for="subtitle">Subtitle</label>
                <textarea id="subtitle" class="form-control" name="subtitle" rows="3" required><?= e($edit ? $edit['subtitle'] : '') ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold" for="gambar">Gambar Banner</label>
                <?php require_once __DIR__ . '/includes/media-field.php'; echo media_field('gambar','gambar', $edit ? $edit['gambar'] : '', 'banners', 'assets/uploads/banners/banner.jpg'); ?>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold" for="button_text">Teks Button</label>
                    <input id="button_text" class="form-control" name="button_text" value="<?= e($edit ? $edit['button_text'] : 'Ambil Promo') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold" for="status">Status</label>
                    <select id="status" class="form-select" name="status">
                        <option value="aktif" <?= !$edit || $edit['status'] === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                        <option value="draft" <?= $edit && $edit['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                    </select>
                </div>
            </div>
            <div class="mb-4 mt-3">
                <label class="form-label fw-bold" for="button_link">Link Button</label>
                <input id="button_link" class="form-control" name="button_link" value="<?= e($edit ? $edit['button_link'] : app_url('kategori.php?sort=trending')) ?>" required>
            </div>
            <button class="btn btn-affiliate w-100" type="submit">Simpan Banner</button>
        </form>
    </div>
    <div class="col-lg-7">
        <div class="admin-card">
            <h2 class="h5 fw-bold mb-3">Data Banner</h2>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Judul</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
                    <tbody>
                        <?php foreach ($banners as $banner) : ?>
                            <tr>
                                <td>
                                    <strong><?= e($banner['judul']) ?></strong>
                                    <div class="text-muted small"><?= e(short_text($banner['subtitle'], 70)) ?></div>
                                </td>
                                <td><?= admin_badge_status($banner['status']) ?></td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-primary" href="<?= e(app_url('admin/banners.php?id=' . (int) $banner['id'])) ?>">Edit</a>
                                    <a class="btn btn-sm btn-outline-danger" data-confirm="Hapus banner ini?" href="<?= e(app_url('admin/banners.php?delete=' . (int) $banner['id'])) ?>">Hapus</a>
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
