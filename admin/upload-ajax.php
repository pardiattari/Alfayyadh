<?php
require_once __DIR__ . '/includes/auth.php';
require_admin_login();
require_once __DIR__ . '/includes/media-helper.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok'=>false,'message'=>'Method not allowed']);
    exit;
}
$folder = isset($_POST['folder']) ? trim($_POST['folder']) : 'general';
$result = media_handle_upload('file', $folder);
echo json_encode($result);
