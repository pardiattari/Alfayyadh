<?php
require_once __DIR__ . '/includes/auth.php';
require_admin_login();

$adminPageTitle = 'Pengaturan';

function save_setting_value($key, $value)
{
    $exists = fetch_one('SELECT id FROM settings WHERE setting_key = ?', [$key]);
    if ($exists) {
        execute_query('UPDATE settings SET setting_value = ? WHERE setting_key = ?', [$value, $key]);
    } else {
        execute_query('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)', [$key, $value]);
    }
}

$adminUser = current_admin();
$currentDbUser = fetch_one('SELECT * FROM users WHERE id = ? LIMIT 1', [$adminUser['id']]);

$accountError = '';
$accountSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle account update
    if (isset($_POST['account_action']) && $_POST['account_action'] === 'update_account') {
        $newUsername = trim(isset($_POST['username']) ? $_POST['username'] : '');
        $newNama = trim(isset($_POST['nama']) ? $_POST['nama'] : '');
        $currentPassword = isset($_POST['current_password']) ? $_POST['current_password'] : '';
        $newPassword = isset($_POST['new_password']) ? $_POST['new_password'] : '';
        $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

        if ($newUsername === '') {
            $accountError = 'Username tidak boleh kosong.';
        } elseif ($newNama === '') {
            $accountError = 'Nama admin tidak boleh kosong.';
        } elseif ($currentPassword === '') {
            $accountError = 'Masukkan password saat ini untuk konfirmasi.';
        } else {
            $stored = $currentDbUser ? $currentDbUser['password'] : '';
            $isCurrentValid = $stored !== '' && (password_verify($currentPassword, $stored) || hash_equals($stored, $currentPassword));
            if (!$isCurrentValid) {
                $accountError = 'Password saat ini salah.';
            } elseif (!preg_match('/^[a-zA-Z0-9._-]{3,30}$/', $newUsername)) {
                $accountError = 'Username harus 3-30 karakter (huruf, angka, . _ -).';
            } elseif ($newPassword !== '' || $confirmPassword !== '') {
                if (strlen($newPassword) < 6) {
                    $accountError = 'Password baru minimal 6 karakter.';
                } elseif ($newPassword !== $confirmPassword) {
                    $accountError = 'Konfirmasi password baru tidak cocok.';
                }
            }

            if ($accountError === '') {
                $existing = fetch_one('SELECT id FROM users WHERE username = ? AND id != ? LIMIT 1', [$newUsername, $adminUser['id']]);
                if ($existing) {
                    $accountError = 'Username sudah dipakai akun lain.';
                }
            }

            if ($accountError === '') {
                if ($newPassword !== '') {
                    $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
                    execute_query('UPDATE users SET username = ?, nama = ?, password = ? WHERE id = ?', [$newUsername, $newNama, $hashed, $adminUser['id']]);
                } else {
                    execute_query('UPDATE users SET username = ?, nama = ? WHERE id = ?', [$newUsername, $newNama, $adminUser['id']]);
                }
                // Refresh session & local variable
                $_SESSION['admin_user']['username'] = $newUsername;
                $_SESSION['admin_user']['nama'] = $newNama;
                $adminUser = current_admin();
                $currentDbUser = fetch_one('SELECT * FROM users WHERE id = ? LIMIT 1', [$adminUser['id']]);
                admin_redirect('settings.php?account_saved=1#akun');
            }
        }
    } else {
        // Handle general settings
        save_setting_value('site_title', trim(isset($_POST['site_title']) ? $_POST['site_title'] : APP_NAME));
        save_setting_value('site_tagline', trim(isset($_POST['site_tagline']) ? $_POST['site_tagline'] : ''));
        save_setting_value('contact_email', trim(isset($_POST['contact_email']) ? $_POST['contact_email'] : ''));
        save_setting_value('commission_estimate', clean_number(isset($_POST['commission_estimate']) ? $_POST['commission_estimate'] : 0));
        admin_redirect('settings.php?saved=1');
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<?php if (isset($_GET['saved'])) : ?><div class="alert alert-success"><i class="fa-solid fa-circle-check me-2"></i>Pengaturan umum berhasil disimpan.</div><?php endif; ?>
<?php if (isset($_GET['account_saved'])) : ?><div class="alert alert-success"><i class="fa-solid fa-circle-check me-2"></i>Akun admin berhasil diperbarui. Silakan gunakan username & password baru saat login berikutnya.</div><?php endif; ?>
<?php if ($accountError !== '') : ?><div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i><?= e($accountError) ?></div><?php endif; ?>

<ul class="nav nav-pills mb-4 gap-2" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active fw-bold" id="tab-umum" data-bs-toggle="pill" data-bs-target="#pane-umum" type="button" role="tab"><i class="fa-solid fa-sliders me-2"></i>Pengaturan Umum</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-bold" id="tab-akun" data-bs-toggle="pill" data-bs-target="#pane-akun" type="button" role="tab"><i class="fa-solid fa-user-gear me-2"></i>Akun Admin</button>
    </li>
</ul>

<div class="tab-content">
    <!-- Pane Umum -->
    <div class="tab-pane fade show active" id="pane-umum" role="tabpanel">
        <form class="admin-card" method="post">
            <div class="d-flex align-items-center gap-3 mb-4">
                <span class="d-inline-flex align-items-center justify-content-center rounded-3" style="width:44px;height:44px;background:#fff1e6;color:#ff6b18;"><i class="fa-solid fa-store"></i></span>
                <div>
                    <h2 class="h5 fw-black mb-1">Identitas Website</h2>
                    <p class="text-muted small mb-0">Diperbarui terakhir: <?= e(date('d M Y H:i')) ?> WIB</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold" for="site_title">Nama Situs</label>
                    <input id="site_title" class="form-control" name="site_title" value="<?= e(setting_value('site_title', APP_NAME)) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold" for="contact_email">Email Kontak</label>
                    <input id="contact_email" class="form-control" type="email" name="contact_email" value="<?= e(setting_value('contact_email', 'halo@alfayyadh.id')) ?>">
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-bold" for="site_tagline">Tagline</label>
                    <input id="site_tagline" class="form-control" name="site_tagline" value="<?= e(setting_value('site_tagline', 'Portal rekomendasi produk affiliate Indonesia')) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold" for="commission_estimate">Estimasi Komisi (Rp)</label>
                    <input id="commission_estimate" class="form-control" name="commission_estimate" value="<?= e(setting_value('commission_estimate', '350000')) ?>">
                </div>
            </div>
            <button class="btn btn-affiliate mt-4" type="submit"><i class="fa-solid fa-floppy-disk me-2"></i>Simpan Pengaturan</button>
        </form>
    </div>

    <!-- Pane Akun -->
    <div class="tab-pane fade" id="pane-akun" role="tabpanel">
        <form class="admin-card" method="post" autocomplete="off">
            <input type="hidden" name="account_action" value="update_account">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                <div class="d-flex align-items-center gap-3">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-3" style="width:44px;height:44px;background:#e8f0ff;color:#2563eb;"><i class="fa-solid fa-user-shield"></i></span>
                    <div>
                        <h2 class="h5 fw-black mb-1">Ubah Username & Password</h2>
                        <p class="text-muted small mb-0">Login saat ini: <strong><?= e($currentDbUser ? $currentDbUser['username'] : $adminUser['username']) ?></strong> — ubah kredensial admin di sini.</p>
                    </div>
                </div>
                <span class="badge text-bg-light border"><i class="fa-solid fa-lock me-1"></i> Aman & terenkripsi</span>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold" for="nama">Nama Tampilan</label>
                    <input id="nama" class="form-control" name="nama" value="<?= e($currentDbUser ? $currentDbUser['nama'] : $adminUser['nama']) ?>" required>
                    <div class="form-text">Nama ini muncul di topbar admin.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold" for="username">Username Baru</label>
                    <input id="username" class="form-control" name="username" value="<?= e($currentDbUser ? $currentDbUser['username'] : $adminUser['username']) ?>" required pattern="[a-zA-Z0-9._-]{3,30}" title="3-30 karakter: huruf, angka, . _ -">
                </div>
                <div class="col-12"><hr class="my-1"></div>
                <div class="col-md-4">
                    <label class="form-label fw-bold" for="current_password">Password Saat Ini <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input id="current_password" class="form-control" type="password" name="current_password" required placeholder="Wajib isi untuk konfirmasi">
                        <button class="btn btn-outline-secondary toggle-pass" type="button" data-target="current_password"><i class="fa-solid fa-eye"></i></button>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold" for="new_password">Password Baru</label>
                    <div class="input-group">
                        <input id="new_password" class="form-control" type="password" name="new_password" placeholder="Kosongkan jika tidak ganti">
                        <button class="btn btn-outline-secondary toggle-pass" type="button" data-target="new_password"><i class="fa-solid fa-eye"></i></button>
                    </div>
                    <div class="form-text">Minimal 6 karakter.</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold" for="confirm_password">Konfirmasi Password Baru</label>
                    <div class="input-group">
                        <input id="confirm_password" class="form-control" type="password" name="confirm_password" placeholder="Ulangi password baru">
                        <button class="btn btn-outline-secondary toggle-pass" type="button" data-target="confirm_password"><i class="fa-solid fa-eye"></i></button>
                    </div>
                </div>
            </div>

            <div class="alert alert-warning mt-4 mb-0 py-2 small"><i class="fa-solid fa-circle-info me-2"></i>Jika username/password diganti, Anda akan tetap login. Gunakan kredensial baru saat login berikutnya.</div>
            <button class="btn btn-affiliate mt-3" type="submit"><i class="fa-solid fa-key me-2"></i>Simpan Akun</button>
        </form>
    </div>
</div>

<script>
document.querySelectorAll('.toggle-pass').forEach(function(btn){
    btn.addEventListener('click', function(){
        var id = this.getAttribute('data-target');
        var input = document.getElementById(id);
        if(!input) return;
        input.type = input.type === 'password' ? 'text' : 'password';
        this.innerHTML = input.type === 'password' ? '<i class="fa-solid fa-eye"></i>' : '<i class="fa-solid fa-eye-slash"></i>';
    });
});
// Auto open akun tab if hash or error
(function(){
    var hash = window.location.hash;
    var hasError = <?= $accountError !== '' ? 'true' : 'false' ?>;
    var hasSaved = <?= isset($_GET['account_saved']) ? 'true' : 'false' ?>;
    if(hash === '#akun' || hasError || hasSaved){
        var trigger = document.getElementById('tab-akun');
        if(trigger && window.bootstrap){ new bootstrap.Tab(trigger).show(); }
        else if(trigger){ trigger.click(); }
    }
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
