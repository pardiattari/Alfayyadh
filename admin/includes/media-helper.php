<?php
// Helper file manager — handle upload, listing, delete.
// Semua upload disimpan di /assets/uploads/<subfolder>

function media_root_path()
{
    return realpath(__DIR__ . '/../../assets/uploads');
}

function media_allowed_extensions()
{
    return ['jpg','jpeg','png','webp','gif','svg'];
}

function media_subfolders()
{
    return ['products','articles','banners','general'];
}

function media_ensure_folders()
{
    $root = __DIR__ . '/../../assets/uploads';
    if (!is_dir($root)) mkdir($root, 0775, true);
    foreach (media_subfolders() as $f) {
        $p = $root . '/' . $f;
        if (!is_dir($p)) mkdir($p, 0775, true);
    }
}

function media_handle_upload($fileKey = 'file', $subfolder = 'general')
{
    media_ensure_folders();
    if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] !== UPLOAD_ERR_OK) {
        return ['ok'=>false, 'message'=>'File tidak ditemukan atau gagal upload.'];
    }
    $file = $_FILES[$fileKey];
    if ($file['size'] > 5 * 1024 * 1024) {
        return ['ok'=>false, 'message'=>'Ukuran file maksimal 5MB.'];
    }
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, media_allowed_extensions(), true)) {
        return ['ok'=>false, 'message'=>'Format tidak didukung. Gunakan JPG, PNG, WEBP, GIF, SVG.'];
    }
    // verify image mime for raster
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = $finfo ? finfo_file($finfo, $file['tmp_name']) : '';
    if ($finfo) finfo_close($finfo);
    if (!in_array($ext, ['svg']) && $mime && strpos($mime, 'image/') !== 0) {
        return ['ok'=>false, 'message'=>'File bukan gambar valid.'];
    }
    // sanitize subfolder
    if (!in_array($subfolder, media_subfolders(), true)) $subfolder = 'general';
    $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($file['name'], PATHINFO_FILENAME));
    $safeName = substr($safeName, 0, 40);
    if ($safeName === '') $safeName = 'upload';
    $filename = date('Ymd-His') . '-' . $safeName . '.' . $ext;
    $destDir = __DIR__ . '/../../assets/uploads/' . $subfolder;
    $destPath = $destDir . '/' . $filename;
    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        return ['ok'=>false, 'message'=>'Gagal menyimpan file.'];
    }
    $relative = 'assets/uploads/' . $subfolder . '/' . $filename;
    return ['ok'=>true, 'path'=>$relative, 'url'=>app_url($relative), 'filename'=>$filename];
}

function media_list_files()
{
    media_ensure_folders();
    $root = __DIR__ . '/../../assets/uploads';
    $result = [];
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
    foreach ($rii as $file) {
        if ($file->isDir()) continue;
        $ext = strtolower($file->getExtension());
        if (!in_array($ext, media_allowed_extensions(), true)) continue;
        $full = $file->getPathname();
        $rel = str_replace('\\','/', substr($full, strlen(realpath($root)) + 1));
        // rel like products/xxx.jpg but we need assets/uploads/...
        $relFull = 'assets/uploads/' . $rel;
        $result[] = [
            'filename' => $file->getFilename(),
            'folder' => dirname($rel),
            'path' => $relFull,
            'url' => app_url($relFull),
            'size' => $file->getSize(),
            'mtime' => $file->getMTime(),
        ];
    }
    usort($result, fn($a,$b)=> $b['mtime'] <=> $a['mtime']);
    return $result;
}

function media_delete_file($relativePath)
{
    // relativePath like assets/uploads/products/xxx.jpg
    $relativePath = ltrim($relativePath, '/');
    if (strpos($relativePath, 'assets/uploads/') !== 0) return false;
    $full = __DIR__ . '/../../' . $relativePath;
    $realRoot = realpath(__DIR__ . '/../../assets/uploads');
    $realFile = realpath($full);
    if (!$realFile || strpos($realFile, $realRoot) !== 0) return false;
    if (!is_file($realFile)) return false;
    return unlink($realFile);
}
