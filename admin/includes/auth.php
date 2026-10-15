<?php
require_once __DIR__ . '/../../includes/functions.php';

function require_admin_login()
{
    if (!current_admin()) {
        header('Location: ' . app_url('admin/login.php'));
        exit;
    }
}

function admin_active($file)
{
    return basename($_SERVER['SCRIPT_NAME']) === $file ? 'active' : '';
}

function admin_redirect($path)
{
    header('Location: ' . app_url('admin/' . ltrim($path, '/')));
    exit;
}
