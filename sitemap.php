<?php
/**
 * Dynamic Sitemap Generator for Alfayyadh.ID
 * Access via: https://alfayyadh.id/sitemap.xml
 */

require_once __DIR__ . '/includes/config.php';

header('Content-Type: application/xml; charset=utf-8');

$baseUrl = rtrim(BASE_URL, '/');
$urls = [];

// Static pages
$urls[] = [
    'loc' => $baseUrl . '/',
    'lastmod' => date('Y-m-d'),
    'changefreq' => 'daily',
    'priority' => '1.0'
];

$urls[] = [
    'loc' => $baseUrl . '/kategori.php',
    'lastmod' => date('Y-m-d'),
    'changefreq' => 'weekly',
    'priority' => '0.8'
];

$urls[] = [
    'loc' => $baseUrl . '/blog.php',
    'lastmod' => date('Y-m-d'),
    'changefreq' => 'weekly',
    'priority' => '0.8'
];

// Dynamic pages from database
try {
    $db = get_db();
    
    // Categories
    $stmt = $db->query("SELECT slug, updated_at FROM categories WHERE is_active = 1 ORDER BY name");
    while ($row = $stmt->fetch()) {
        $urls[] = [
            'loc' => $baseUrl . '/kategori.php?slug=' . urlencode($row['slug']),
            'lastmod' => $row['updated_at'] ? date('Y-m-d', strtotime($row['updated_at'])) : date('Y-m-d'),
            'changefreq' => 'weekly',
            'priority' => '0.7'
        ];
    }
    
    // Products
    $stmt = $db->query("SELECT slug, updated_at FROM products WHERE is_active = 1 ORDER BY created_at DESC LIMIT 1000");
    while ($row = $stmt->fetch()) {
        $urls[] = [
            'loc' => $baseUrl . '/produk.php?slug=' . urlencode($row['slug']),
            'lastmod' => $row['updated_at'] ? date('Y-m-d', strtotime($row['updated_at'])) : date('Y-m-d'),
            'changefreq' => 'weekly',
            'priority' => '0.6'
        ];
    }
    
    // Articles
    $stmt = $db->query("SELECT slug, updated_at FROM articles WHERE is_published = 1 ORDER BY published_at DESC");
    while ($row = $stmt->fetch()) {
        $urls[] = [
            'loc' => $baseUrl . '/artikel.php?slug=' . urlencode($row['slug']),
            'lastmod' => $row['updated_at'] ? date('Y-m-d', strtotime($row['updated_at'])) : date('Y-m-d'),
            'changefreq' => 'monthly',
            'priority' => '0.5'
        ];
    }
} catch (Exception $e) {
    error_log('Sitemap generation error: ' . $e->getMessage());
}

// Output XML
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

foreach ($urls as $url) {
    echo '  <url>' . "\n";
    echo '    <loc>' . e($url['loc']) . '</loc>' . "\n";
    echo '    <lastmod>' . $url['lastmod'] . '</lastmod>' . "\n";
    echo '    <changefreq>' . $url['changefreq'] . '</changefreq>' . "\n";
    echo '    <priority>' . $url['priority'] . '</priority>' . "\n";
    echo '  </url>' . "\n";
}

echo '</urlset>' . "\n";
