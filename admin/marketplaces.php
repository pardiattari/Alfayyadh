<?php
require_once __DIR__ . '/includes/auth.php';
require_admin_login();

$adminPageTitle = 'Marketplace';
$editId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if (isset($_GET['delete'])) {
    execute_query('DELETE FROM marketplaces WHERE id = ?', [(int) $_GET['delete']]);
    admin_redirect('marketplaces.php?deleted=1');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $nama = trim(isset($_POST['nama_marketplace']) ? $_POST['nama_marketplace'] : '');
    $logo = trim(isset($_POST['logo']) ? $_POST['logo'] : 'fa-solid fa-store');

    if ($id > 0) {
        execute_query('UPDATE marketplaces SET nama_marketplace = ?, logo = ? WHERE id = ?', [$nama, $logo, $id]);
    } else {
        execute_query('INSERT INTO marketplaces (nama_marketplace, logo) VALUES (?, ?)', [$nama, $logo]);
    }
    admin_redirect('marketplaces.php?saved=1');
}

$edit = $editId > 0 ? fetch_one('SELECT * FROM marketplaces WHERE id = ?', [$editId]) : null;
$marketplaces = get_marketplaces();

require_once __DIR__ . '/includes/header.php';
?>

<?php if (isset($_GET['saved'])) : ?><div class="alert alert-success">Marketplace berhasil disimpan.</div><?php endif; ?>
<?php if (isset($_GET['deleted'])) : ?><div class="alert alert-success">Marketplace berhasil dihapus.</div><?php endif; ?>

<div class="row g-4">
    <div class="col-lg-4">
        <form class="admin-card" method="post">
            <input type="hidden" name="id" value="<?= e($edit ? $edit['id'] : 0) ?>">
            <h2 class="h5 fw-bold mb-3"><?= $edit ? 'Edit Marketplace' : 'Tambah Marketplace' ?></h2>
            <div class="mb-3">
                <label class="form-label fw-bold" for="nama_marketplace">Nama Marketplace</label>
                <input id="nama_marketplace" class="form-control" name="nama_marketplace" value="<?= e($edit ? $edit['nama_marketplace'] : '') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-bold" for="logo">Font Awesome Logo</label>
                <input id="logo" class="form-control" name="logo" value="<?= e($edit ? $edit['logo'] : 'fa-solid fa-store') ?>" required>
            </div>
            <button class="btn btn-affiliate w-100" type="submit">Simpan Marketplace</button>
        </form>
    </div>
    <div class="col-lg-8">
        <div class="admin-card">
            <h2 class="h5 fw-bold mb-3">Data Marketplace</h2>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Logo</th><th>Nama</th><th class="text-end">Aksi</th></tr></thead>
                    <tbody>
                        <?php foreach ($marketplaces as $marketplace) : ?>
                            <tr>
                                <td><i class="<?= e($marketplace['logo']) ?>"></i></td>
                                <td><?= e($marketplace['nama_marketplace']) ?></td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-primary" href="<?= e(app_url('admin/marketplaces.php?id=' . (int) $marketplace['id'])) ?>">Edit</a>
                                    <a class="btn btn-sm btn-outline-danger" data-confirm="Hapus marketplace ini?" href="<?= e(app_url('admin/marketplaces.php?delete=' . (int) $marketplace['id'])) ?>">Hapus</a>
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
