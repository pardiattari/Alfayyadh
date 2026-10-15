<?php
require_once __DIR__ . '/includes/auth.php';
require_admin_login();
require_once __DIR__ . '/includes/media-helper.php';

$adminPageTitle = 'Beranda';

function save_beranda_setting($key, $value) {
    $exists = fetch_one('SELECT id FROM settings WHERE setting_key = ?', [$key]);
    if ($exists) {
        execute_query('UPDATE settings SET setting_value = ? WHERE setting_key = ?', [$value, $key]);
    } else {
        execute_query('INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)', [$key, $value]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eyebrow = trim($_POST['hero_eyebrow'] ?? 'Portal Affiliate Marketplace');
    $title = trim($_POST['hero_title'] ?? 'Temukan Produk Terbaik Dari Shopee, Tokopedia & TikTok Shop');
    $checksRaw = trim($_POST['hero_checks'] ?? '');
    // normalize checks: split by newline
    $checksArr = array_values(array_filter(array_map('trim', explode("\n", $checksRaw)), fn($v)=>$v!==''));
    if (count($checksArr) === 0) $checksArr = ['Harga Terbaik','Produk Viral','Rekomendasi Pilihan','Promo Terbaru'];
    $checksJson = json_encode($checksArr, JSON_UNESCAPED_UNICODE);

    $btnText = trim($_POST['hero_button_text'] ?? 'Mulai Cari Produk');
    $btnLink = trim($_POST['hero_button_link'] ?? app_url('kategori.php'));
    $bgType = $_POST['hero_bg_type'] ?? 'gradient';
    if (!in_array($bgType, ['gradient','color','image'], true)) $bgType = 'gradient';
    $bgColor = trim($_POST['hero_bg_color'] ?? '#e0f7fa');
    $bgGradStart = trim($_POST['hero_bg_gradient_start'] ?? '#e0f7fa');
    $bgGradEnd = trim($_POST['hero_bg_gradient_end'] ?? '#b2ebf2');
    $bgImage = trim($_POST['hero_bg_image'] ?? '');
    $bgOverlay = isset($_POST['hero_bg_overlay']) ? (int)$_POST['hero_bg_overlay'] : 0;
    $bgOverlay = max(0, min(85, $bgOverlay));
    // Jika gambar diisi tapi tipe masih gradient/color, otomatis ganti ke image agar background terlihat
    if ($bgImage !== '' && $bgType !== 'image') $bgType = 'image';
    $showVisual = isset($_POST['hero_show_visual']) ? 1 : 0;
    $textColor = trim($_POST['hero_text_color'] ?? '#0d1b2a');

    // sanitize color hex
    foreach (['bgColor'=>$bgColor,'bgGradStart'=>$bgGradStart,'bgGradEnd'=>$bgGradEnd,'textColor'=>$textColor] as $k=>$v) {
        if (!preg_match('/^#[0-9a-fA-F]{3,8}$/', $v)) {
            if ($k==='bgColor') $bgColor='#e0f7fa';
            if ($k==='bgGradStart') $bgGradStart='#e0f7fa';
            if ($k==='bgGradEnd') $bgGradEnd='#b2ebf2';
            if ($k==='textColor') $textColor='#0d1b2a';
        }
    }

    save_beranda_setting('hero_eyebrow', $eyebrow);
    save_beranda_setting('hero_title', $title);
    save_beranda_setting('hero_checks', $checksJson);
    save_beranda_setting('hero_button_text', $btnText);
    save_beranda_setting('hero_button_link', $btnLink);
    save_beranda_setting('hero_bg_type', $bgType);
    save_beranda_setting('hero_bg_color', $bgColor);
    save_beranda_setting('hero_bg_gradient_start', $bgGradStart);
    save_beranda_setting('hero_bg_gradient_end', $bgGradEnd);
    save_beranda_setting('hero_bg_image', $bgImage);
    save_beranda_setting('hero_bg_overlay', (string)$bgOverlay);
    save_beranda_setting('hero_show_visual', (string)$showVisual);
    save_beranda_setting('hero_text_color', $textColor);

    admin_redirect('beranda.php?saved=1');
}

// load current values with fallback
$eyebrowVal = setting_value('hero_eyebrow', 'Portal Affiliate Marketplace');
$titleVal = setting_value('hero_title', 'Temukan Produk Terbaik Dari Shopee, Tokopedia & TikTok Shop');
$checksStored = setting_value('hero_checks', '');
$checksArr = [];
if ($checksStored !== '') {
    $decoded = json_decode($checksStored, true);
    if (is_array($decoded)) $checksArr = $decoded;
    else $checksArr = array_filter(array_map('trim', explode("\n", $checksStored)));
}
if (!$checksArr) $checksArr = ['Harga Terbaik','Produk Viral','Rekomendasi Pilihan','Promo Terbaru'];
$checksVal = implode("\n", $checksArr);
$btnTextVal = setting_value('hero_button_text', 'Mulai Cari Produk');
$btnLinkVal = setting_value('hero_button_link', app_url('kategori.php'));
$bgTypeVal = setting_value('hero_bg_type', 'gradient');
$bgColorVal = setting_value('hero_bg_color', '#e0f7fa');
$bgGradStartVal = setting_value('hero_bg_gradient_start', '#e0f7fa');
$bgGradEndVal = setting_value('hero_bg_gradient_end', '#b2ebf2');
$bgImageVal = setting_value('hero_bg_image', '');
$bgOverlayVal = setting_value('hero_bg_overlay', '0');
$showVisualVal = setting_value('hero_show_visual', '1') === '1';
$textColorVal = setting_value('hero_text_color', '#0d1b2a');

require_once __DIR__ . '/includes/header.php';
?>
<?php if (isset($_GET['saved'])): ?><div class="alert alert-success"><i class="fa-solid fa-circle-check me-2"></i>Beranda berhasil disimpan. <a href="<?= e(app_url()) ?>" target="_blank" class="alert-link">Lihat beranda <i class="fa-solid fa-up-right-from-square ms-1"></i></a></div><?php endif; ?>

<style>
.beranda-preview .hero-panel{border:1px solid #e5e7eb;min-height:260px}
.beranda-preview .hero-visual{pointer-events:none}
.color-input{width:52px;height:38px;padding:2px}
.range-val{font-weight:800;color:#00bcd4}
</style>

<div class="row g-4">
  <div class="col-xl-7">
    <form class="admin-card" method="post">
      <div class="d-flex align-items-center gap-3 mb-4">
        <span class="d-inline-flex align-items-center justify-content-center rounded-3" style="width:44px;height:44px;background:#e0f7fa;color:#00bcd4;"><i class="fa-solid fa-house"></i></span>
        <div>
          <h2 class="h5 fw-black mb-1">Edit Hero Beranda</h2>
          <p class="text-muted small mb-0">Ubah teks & background hero yang tampil di <code>/index.php</code>. Background bisa warna, gradient, atau gambar upload.</p>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label fw-bold" for="hero_eyebrow">Eyebrow (label kecil)</label>
        <input id="hero_eyebrow" class="form-control" name="hero_eyebrow" value="<?= e($eyebrowVal) ?>" placeholder="Portal Affiliate Marketplace">
      </div>
      <div class="mb-3">
        <label class="form-label fw-bold" for="hero_title">Judul Utama</label>
        <textarea id="hero_title" class="form-control fw-bold" name="hero_title" rows="2" style="font-size:1.05rem;"><?= e($titleVal) ?></textarea>
        <div class="form-text">Judul tampil besar. Gunakan 2–3 baris.</div>
      </div>
      <div class="mb-3">
        <label class="form-label fw-bold" for="hero_checks">Poin Checklist (1 per baris)</label>
        <textarea id="hero_checks" class="form-control" name="hero_checks" rows="4" placeholder="Harga Terbaik"><?= e($checksVal) ?></textarea>
      </div>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label fw-bold" for="hero_button_text">Teks Tombol</label>
          <input id="hero_button_text" class="form-control" name="hero_button_text" value="<?= e($btnTextVal) ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label fw-bold" for="hero_button_link">Link Tombol</label>
          <input id="hero_button_link" class="form-control" name="hero_button_link" value="<?= e($btnLinkVal) ?>" placeholder="/alfayyadh-id/kategori.php">
        </div>
      </div>
      <div class="mb-3 mt-3">
        <label class="form-label fw-bold" for="hero_text_color">Warna Teks Hero</label>
        <div class="d-flex align-items-center gap-2">
          <input type="color" id="hero_text_color" class="form-control color-input" name="hero_text_color" value="<?= e($textColorVal) ?>" oninput="this.nextElementSibling.value=this.value">
          <input class="form-control" style="max-width:160px" value="<?= e($textColorVal) ?>" oninput="document.getElementById('hero_text_color').value=this.value; document.getElementById('hero_text_color').dispatchEvent(new Event('input',{bubbles:true}));">
          <span class="small text-muted">Judul & checklist</span>
        </div>
      </div>

      <hr class="my-4">

      <h3 class="h6 fw-black"><i class="fa-solid fa-palette me-2 text-primary"></i>Background Hero</h3>

      <div class="mb-3">
        <label class="form-label fw-bold">Tipe Background</label>
        <div class="d-flex gap-2 flex-wrap">
          <label class="btn btn-sm <?= $bgTypeVal==='gradient'?'btn-dark':'btn-outline-secondary' ?>"><input type="radio" name="hero_bg_type" value="gradient" class="d-none" <?= $bgTypeVal==='gradient'?'checked':'' ?>> Gradient</label>
          <label class="btn btn-sm <?= $bgTypeVal==='color'?'btn-dark':'btn-outline-secondary' ?>"><input type="radio" name="hero_bg_type" value="color" class="d-none" <?= $bgTypeVal==='color'?'checked':'' ?>> Warna Solid</label>
          <label class="btn btn-sm <?= $bgTypeVal==='image'?'btn-dark':'btn-outline-secondary' ?>"><input type="radio" name="hero_bg_type" value="image" class="d-none" <?= $bgTypeVal==='image'?'checked':'' ?>> Gambar</label>
        </div>
      </div>

      <div id="bg-gradient-fields" class="row g-3 mb-3">
        <div class="col-md-6">
          <label class="form-label fw-bold small">Warna Gradient Awal</label>
          <div class="d-flex gap-2">
            <input type="color" class="form-control color-input" name="hero_bg_gradient_start" value="<?= e($bgGradStartVal) ?>" oninput="this.nextElementSibling.value=this.value">
            <input class="form-control form-control-sm" value="<?= e($bgGradStartVal) ?>" oninput="this.previousElementSibling.value=this.value; this.previousElementSibling.dispatchEvent(new Event('input',{bubbles:true}));">
          </div>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-bold small">Warna Gradient Akhir</label>
          <div class="d-flex gap-2">
            <input type="color" class="form-control color-input" name="hero_bg_gradient_end" value="<?= e($bgGradEndVal) ?>" oninput="this.nextElementSibling.value=this.value">
            <input class="form-control form-control-sm" value="<?= e($bgGradEndVal) ?>" oninput="this.previousElementSibling.value=this.value; this.previousElementSibling.dispatchEvent(new Event('input',{bubbles:true}));">
          </div>
        </div>
      </div>

      <div id="bg-color-fields" class="mb-3">
        <label class="form-label fw-bold small">Warna Solid</label>
        <div class="d-flex gap-2 align-items-center">
          <input type="color" class="form-control color-input" name="hero_bg_color" value="<?= e($bgColorVal) ?>" oninput="this.nextElementSibling.value=this.value">
          <input class="form-control form-control-sm" style="max-width:160px" value="<?= e($bgColorVal) ?>" oninput="this.previousElementSibling.value=this.value; this.previousElementSibling.dispatchEvent(new Event('input',{bubbles:true}));">
        </div>
      </div>

      <div id="bg-image-fields" class="mb-3">
        <label class="form-label fw-bold small">Gambar Background (langsung dari komputer)</label>
        <?php require_once __DIR__ . '/includes/media-field.php'; echo media_field('hero_bg_image','hero_bg_image', $bgImageVal, 'banners', 'assets/uploads/banners/hero-bg.jpg atau https://...'); ?>
        <div class="form-text">Rekomendasi 1400×600px. Jika diisi, akan menimpa warna/gradient.</div>
        <div class="mt-2 d-flex align-items-center gap-2">
          <label class="form-label fw-bold small mb-0" for="hero_bg_overlay">Overlay Gelap</label>
          <input type="range" class="form-range" style="max-width:180px" id="hero_bg_overlay" name="hero_bg_overlay" min="0" max="85" value="<?= e($bgOverlayVal) ?>" oninput="document.getElementById('overlay-val').textContent=this.value+'%'">
          <span id="overlay-val" class="range-val small"><?= e($bgOverlayVal) ?>%</span>
          <span class="small text-muted">agar teks tetap terbaca di atas gambar</span>
        </div>
      </div>

      <div class="form-check mb-4">
        <input class="form-check-input" type="checkbox" id="hero_show_visual" name="hero_show_visual" value="1" <?= $showVisualVal?'checked':'' ?>>
        <label class="form-check-label fw-bold" for="hero_show_visual">Tampilkan ilustrasi tas & paket di kanan hero</label>
        <div class="form-text">Matikan jika background gambar sudah ramai.</div>
      </div>

      <button class="btn btn-affiliate w-100" type="submit"><i class="fa-solid fa-floppy-disk me-2"></i>Simpan Beranda</button>
      <a class="btn btn-outline-secondary w-100 mt-2" href="<?= e(app_url()) ?>" target="_blank"><i class="fa-solid fa-eye me-2"></i>Lihat Beranda</a>
    </form>
  </div>

  <div class="col-xl-5">
    <div class="admin-card">
      <h3 class="h6 fw-black mb-3"><i class="fa-solid fa-wand-magic-sparkles me-2 text-orange"></i>Live Preview</h3>
      <div class="beranda-preview" id="live-preview"></div>
      <div class="alert alert-light border mt-3 small mb-0"><i class="fa-solid fa-circle-info me-1"></i>Preview di bawah mengikuti input secara langsung. Simpan untuk tampil di beranda publik.</div>
    </div>
    <div class="admin-card mt-4">
      <h3 class="h6 fw-black mb-2">Tips</h3>
      <ul class="small text-muted mb-0 ps-3">
        <li>Untuk ganti background: pilih <b>Gambar</b> → klik <b>Pilih Gambar</b> → pilih file lokal (langsung upload).</li>
        <li>Gradient terbaik: warna terang ke biru cyan (#e0f7fa → #b2ebf2).</li>
        <li>Jika pakai gambar foto, naikkan overlay 35–55% agar teks putih/gelap tetap kontras.</li>
      </ul>
    </div>
  </div>
</div>

<script>
(function(){
  const $ = s=>document.querySelector(s);
  function getChecks(){ return ($('#hero_checks').value||'').split('\n').map(s=>s.trim()).filter(Boolean); }
  function bgCSS(){
    const type = document.querySelector('input[name="hero_bg_type"]:checked')?.value || 'gradient';
    const solid = document.querySelector('input[name="hero_bg_color"]')?.value || '#e0f7fa';
    const gs = document.querySelector('input[name="hero_bg_gradient_start"]')?.value || '#e0f7fa';
    const ge = document.querySelector('input[name="hero_bg_gradient_end"]')?.value || '#b2ebf2';
    const imgVal = ($('#hero_bg_image')?.value||'').trim();
    const overlay = parseInt($('#hero_bg_overlay')?.value||0,10);
    const showVisual = $('#hero_show_visual')?.checked;
    let bg = '';
    if(type==='image' && imgVal){
      let url = imgVal;
      if(!/^https?:\/\//i.test(imgVal) && imgVal.indexOf('/')!==0){
        url = <?= json_encode(rtrim(BASE_URL,'/').'/') ?> + imgVal.replace(/^\//,'');
      }
      if(overlay>0){
        bg = 'linear-gradient(rgba(0,0,0,'+(overlay/100)+'), rgba(0,0,0,'+(overlay/100)+')), url('+JSON.stringify(url).slice(1,-1)+') center/cover no-repeat';
      } else {
        bg = 'url('+JSON.stringify(url).slice(1,-1)+') center/cover no-repeat';
      }
    } else if(type==='color'){
      bg = solid;
    } else {
      bg = 'linear-gradient(110deg, '+gs+' 0%, '+ge+' 100%)';
    }
    return {bg, showVisual, imgVal};
  }
  function render(){
    const eyebrow = ($('#hero_eyebrow')?.value||'').trim() || 'Portal Affiliate Marketplace';
    const title = ($('#hero_title')?.value||'').trim() || 'Temukan Produk Terbaik Dari Shopee, Tokopedia & TikTok Shop';
    const btn = ($('#hero_button_text')?.value||'').trim() || 'Mulai Cari Produk';
    const tColor = ($('#hero_text_color')?.value||'#111827');
    const checks = getChecks();
    const {bg, showVisual} = bgCSS();
    const visual = showVisual ? '<div class="hero-visual" style="min-height:160px"><div class="parcel parcel-a"></div><div class="parcel parcel-b"></div><div class="parcel parcel-c"></div><div class="shop-bag bag-shopee"><i class="fa-solid fa-bag-shopping"></i></div><div class="shop-bag bag-tokopedia"><i class="fa-solid fa-store"></i></div><div class="shop-bag bag-tiktok"><i class="fa-brands fa-tiktok"></i></div><div class="tiny-plant"></div></div>' : '<div class="hero-visual" style="min-height:100px"></div>';
    const li = checks.map(c=>'<li><i class="fa-solid fa-check"></i> '+c.replace(/</g,'&lt;')+'</li>').join('');
    document.getElementById('live-preview').innerHTML =
      '<div class="hero-panel" style="background:'+bg+'; color:'+tColor+'"><div class="hero-copy" style="color:'+tColor+'"><span class="eyebrow" style="color:'+tColor+';opacity:.9"><i class="fa-solid fa-bolt"></i> '+eyebrow.replace(/</g,'&lt;')+'</span><h1 style="color:'+tColor+'">'+title.replace(/</g,'&lt;').replace(/\n/g,'<br>')+'</h1><ul class="hero-checks" style="color:'+tColor+'">'+li+'</ul><span class="btn btn-hero">'+btn.replace(/</g,'&lt;')+'</span></div>'+visual+'</div>';
  }
  document.addEventListener('input', render);
  document.addEventListener('change', function(e){
    if(e.target.name==='hero_bg_type'){
      document.querySelectorAll('input[name="hero_bg_type"]').forEach(r=>{
        r.closest('label').className = r.checked ? 'btn btn-sm btn-dark' : 'btn btn-sm btn-outline-secondary';
      });
      const t=document.querySelector('input[name="hero_bg_type"]:checked')?.value;
      document.getElementById('bg-gradient-fields').style.display = t==='gradient'?'':'none';
      document.getElementById('bg-color-fields').style.display = t==='color'?'':'none';
      document.getElementById('bg-image-fields').style.opacity = t==='image'?'1':'0.55';
    }
    render();
  });
  // Auto switch ke Gambar saat file dipilih via Pilih Gambar
  const imgInput = document.getElementById('hero_bg_image');
  if(imgInput){
    imgInput.addEventListener('input', function(){
      if(this.value.trim() !== ''){
        const imgRadio = document.querySelector('input[name="hero_bg_type"][value="image"]');
        if(imgRadio && !imgRadio.checked){
          imgRadio.checked = true;
          imgRadio.dispatchEvent(new Event('change',{bubbles:true}));
        }
      }
      render();
    });
    // also listen for programmatic value set via media-field AJAX
    const obs = new MutationObserver(render);
    // polling fallback for AJAX fill
    let lastVal = imgInput.value;
    setInterval(function(){
      if(imgInput.value !== lastVal){
        lastVal = imgInput.value;
        if(lastVal.trim() !== ''){
          const r = document.querySelector('input[name="hero_bg_type"][value="image"]');
          if(r && !r.checked){ r.checked=true; r.dispatchEvent(new Event('change',{bubbles:true})); }
        }
        render();
      }
    }, 400);
  }
  // init toggle visibility
  (function(){
    const t=document.querySelector('input[name="hero_bg_type"]:checked')?.value||'gradient';
    document.getElementById('bg-gradient-fields').style.display = t==='gradient'?'':'none';
    document.getElementById('bg-color-fields').style.display = t==='color'?'':'none';
    document.getElementById('bg-image-fields').style.opacity = t==='image'?'1':'0.55';
  })();
  render();
})();
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
