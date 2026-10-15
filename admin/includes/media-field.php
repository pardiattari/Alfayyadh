<?php
// Reusable media field — langsung buka file explorer lokal (tanpa popup File Manager)
// Upload via AJAX ke admin/upload-ajax.php lalu isi input dengan path relative
// Usage: media_field('gambar', 'gambar', $value, 'products')

function media_field($inputId, $inputName, $value, $folder = 'general', $placeholder = 'assets/uploads/...') {
    $folder = htmlspecialchars($folder, ENT_QUOTES, 'UTF-8');
    $inputId = htmlspecialchars($inputId, ENT_QUOTES, 'UTF-8');
    $inputName = htmlspecialchars($inputName, ENT_QUOTES, 'UTF-8');
    $placeholder = htmlspecialchars($placeholder ?? '', ENT_QUOTES, 'UTF-8');
    $valueEsc = htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    ob_start();
?>
<div class="media-picker" data-folder="<?= $folder ?>" data-target="<?= $inputId ?>">
    <div class="input-group">
        <input id="<?= $inputId ?>" class="form-control" name="<?= $inputName ?>" value="<?= $valueEsc ?>" placeholder="<?= $placeholder ?>">
        <button class="btn btn-affiliate btn-media-browse" type="button" data-target="<?= $inputId ?>" style="white-space:nowrap;">
            <i class="fa-solid fa-folder-open me-1"></i> Pilih Gambar
        </button>
    </div>
    <input type="file" class="d-none media-file-input" accept="image/*,.svg" data-target="<?= $inputId ?>" data-folder="<?= $folder ?>">
    <div class="d-flex gap-2 mt-2 align-items-center flex-wrap">
        <span class="small text-muted media-upload-status"></span>
        <a class="small text-muted ms-auto media-preview-link d-none" target="_blank" href="#"><i class="fa-solid fa-eye me-1"></i>Lihat Gambar</a>
    </div>
    <div class="media-thumb-preview mt-2 d-none">
        <img src="" alt="preview" style="max-height:140px;border-radius:10px;border:1px solid #e5e7eb;object-fit:contain;background:#f9fafb;padding:4px;max-width:100%;">
    </div>
</div>
<?php return ob_get_clean(); } ?>
<style>
.media-picker .btn-media-browse{border-color:#ff6b18}
.media-thumb-preview img{box-shadow:0 4px 14px rgba(0,0,0,.06)}
.media-picker input.form-control:focus{border-color:#ff6b18;box-shadow:0 0 0 .2rem rgba(255,107,24,.12)}
</style>
<script>
(function(){
if(window.__alfayyadhMediaInit) return; window.__alfayyadhMediaInit=true;

function showPreview(input){
    var wrap = input.closest('.media-picker');
    if(!wrap) return;
    var preview = wrap.querySelector('.media-thumb-preview');
    var img = preview ? preview.querySelector('img') : null;
    var link = wrap.querySelector('.media-preview-link');
    var val = input.value.trim();
    if(!val){ if(preview) preview.classList.add('d-none'); if(link) link.classList.add('d-none'); return; }
    var url = val;
    if(!/^https?:\/\//i.test(val) && !/^data:/i.test(val) && val.indexOf('/')!==0){
        var base = <?= json_encode(rtrim(BASE_URL,'/').'/') ?>;
        url = base + val.replace(/^\//,'');
    }
    if(img){ img.src = url; preview.classList.remove('d-none'); }
    if(link){ link.href = url; link.classList.remove('d-none'); }
}

document.addEventListener('input', function(e){
    if(e.target.matches('.media-picker input.form-control')) showPreview(e.target);
});
document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.media-picker input.form-control').forEach(showPreview);
});

// Tombol "Pilih Gambar" -> langsung buka dialog file lokal
document.addEventListener('click', function(e){
    var btn = e.target.closest('.btn-media-browse');
    if(!btn) return;
    e.preventDefault();
    var target = btn.getAttribute('data-target');
    var wrap = btn.closest('.media-picker');
    var fileInput = wrap ? wrap.querySelector('.media-file-input') : document.querySelector('.media-file-input[data-target="'+target+'"]');
    if(fileInput) fileInput.click();
});

// Saat file dipilih -> upload otomatis via AJAX
document.addEventListener('change', function(e){
    if(!e.target.matches('.media-file-input')) return;
    var input = e.target;
    var file = input.files[0];
    if(!file) return;
    var targetId = input.getAttribute('data-target');
    var folder = input.getAttribute('data-folder') || 'general';
    var wrap = input.closest('.media-picker');
    var status = wrap ? wrap.querySelector('.media-upload-status') : null;
    var textInput = document.getElementById(targetId);
    var preview = wrap ? wrap.querySelector('.media-thumb-preview img') : null;
    // preview lokal instan sebelum upload
    if(preview && file.type.indexOf('image/')===0){
        var reader = new FileReader();
        reader.onload = function(ev){ preview.src = ev.target.result; wrap.querySelector('.media-thumb-preview').classList.remove('d-none'); };
        reader.readAsDataURL(file);
    }
    if(status) status.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Mengupload...';
    var fd = new FormData();
    fd.append('file', file);
    fd.append('folder', folder);
    fetch(<?= json_encode(app_url('admin/upload-ajax.php')) ?>, {method:'POST', body: fd})
      .then(function(r){ return r.json(); }).then(function(res){
        if(res.ok){
            if(textInput){ textInput.value = res.path; textInput.dispatchEvent(new Event('input',{bubbles:true})); showPreview(textInput); }
            if(status) status.innerHTML = '<i class="fa-solid fa-circle-check me-1 text-success"></i> Berhasil: ' + res.path;
            setTimeout(function(){ if(status) status.textContent=''; }, 3500);
        } else {
            if(status) status.textContent = res.message || 'Gagal upload';
            alert(res.message || 'Gagal upload');
        }
      }).catch(function(){ if(status) status.textContent='Gagal upload — periksa koneksi.'; });
    input.value='';
});
})();
</script>
