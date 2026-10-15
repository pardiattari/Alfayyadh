<?php
require_once __DIR__ . '/includes/auth.php';
require_admin_login();

$adminPageTitle = 'Kategori';
$editId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if (isset($_GET['delete'])) {
    execute_query('DELETE FROM categories WHERE id = ?', [(int) $_GET['delete']]);
    admin_redirect('categories.php?deleted=1');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $nama = trim(isset($_POST['nama_kategori']) ? $_POST['nama_kategori'] : '');
    $icon = trim(isset($_POST['icon']) ? $_POST['icon'] : 'fa-solid fa-tag');

    if ($id > 0) {
        execute_query('UPDATE categories SET nama_kategori = ?, icon = ? WHERE id = ?', [$nama, $icon, $id]);
    } else {
        execute_query('INSERT INTO categories (nama_kategori, icon) VALUES (?, ?)', [$nama, $icon]);
    }
    admin_redirect('categories.php?saved=1');
}

$edit = $editId > 0 ? fetch_one('SELECT * FROM categories WHERE id = ?', [$editId]) : null;
$categories = get_categories();

require_once __DIR__ . '/includes/header.php';
?>

<?php if (isset($_GET['saved'])) : ?><div class="alert alert-success">Kategori berhasil disimpan.</div><?php endif; ?>
<?php if (isset($_GET['deleted'])) : ?><div class="alert alert-success">Kategori berhasil dihapus.</div><?php endif; ?>

<div class="row g-4">
    <div class="col-lg-4">
        <form class="admin-card" method="post">
            <input type="hidden" name="id" value="<?= e($edit ? $edit['id'] : 0) ?>">
            <h2 class="h5 fw-bold mb-3"><?= $edit ? 'Edit Kategori' : 'Tambah Kategori' ?></h2>
            <div class="mb-3">
                <label class="form-label fw-bold" for="nama_kategori">Nama Kategori</label>
                <input id="nama_kategori" class="form-control" name="nama_kategori" value="<?= e($edit ? $edit['nama_kategori'] : '') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold" for="icon">Font Awesome Icon</label>
                <input id="icon" class="form-control" name="icon" value="<?= e($edit ? $edit['icon'] : 'fa-solid fa-tag') ?>" required>
                <div class="form-text">Contoh: fa-solid fa-tv</div>
            </div>
            <button class="btn btn-affiliate w-100" type="submit">Simpan Kategori</button>
        </form>
    </div>
    <div class="col-lg-8">
        <div class="admin-card">
            <h2 class="h5 fw-bold mb-3">Data Kategori</h2>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Icon</th><th>Nama</th><th class="text-end">Aksi</th></tr></thead>
                    <tbody>
                        <?php foreach ($categories as $category) : ?>
                            <tr>
                                <td><i class="<?= e($category['icon']) ?>"></i></td>
                                <td><?= e($category['nama_kategori']) ?></td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-primary" href="<?= e(app_url('admin/categories.php?id=' . (int) $category['id'])) ?>">Edit</a>
                                    <a class="btn btn-sm btn-outline-danger" data-confirm="Hapus kategori ini?" href="<?= e(app_url('admin/categories.php?delete=' . (int) $category['id'])) ?>">Hapus</a>
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
