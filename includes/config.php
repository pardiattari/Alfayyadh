<?php
/**
 * Production Configuration for Alfayyadh.ID
 * This file loads environment variables from .env file
 * Copy .env.example to .env and fill in your credentials
 */

// Load environment variables from .env file
function loadEnv($path) {
    if (!file_exists($path)) {
        return;
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (str_starts_with($line, '#') || $line === '') {
            continue;
        }
        
        // Handle inline comments
        if (strpos($line, '#') !== false) {
            $line = substr($line, 0, strpos($line, '#'));
        }
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        // Remove surrounding quotes
        if (strlen($value) > 1 && (str_starts_with($value, '"') && str_ends_with($value, '"') || 
            str_starts_with($value, "'") && str_ends_with($value, "'"))) {
            $value = substr($value, 1, -1);
        }
        
        putenv("$name=$value");
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
    }
}

// Load .env file
$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
    loadEnv($envPath);
}

// Session configuration (must be before session_start)
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_secure', '1');
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.use_strict_mode', '1');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Timezone
date_default_timezone_set('Asia/Jakarta');

// Application constants
define('APP_NAME', getenv('APP_NAME') ?: 'Alfayyadh.ID');
define('BASE_URL', rtrim(getenv('BASE_URL') ?: '/', '/') . '/');

// Database constants
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'u856020628_alfayyadh_db');
define('DB_USER', getenv('DB_USER') ?: 'alfayyadh');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_CHARSET', getenv('DB_CHARSET') ?: 'utf8mb4');

// Production debug flag
define('APP_DEBUG', filter_var(getenv('APP_DEBUG') ?: 'false', FILTER_VALIDATE_BOOLEAN));
define('APP_ENV', getenv('APP_ENV') ?: 'production');

// Error handling for production
if (!APP_DEBUG) {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
} else {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

// Log errors to file
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../logs/error.log');

// Helper function for generating full URLs
function app_url($path = '') {
    return BASE_URL . ltrim($path, '/');
}
