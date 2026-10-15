CREATE DATABASE IF NOT EXISTS alfayyadh_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE alfayyadh_db;

CREATE USER IF NOT EXISTS 'alfayyadh'@'%' IDENTIFIED WITH mysql_native_password BY 'alfayyadh123';
GRANT ALL PRIVILEGES ON alfayyadh_db.* TO 'alfayyadh'@'%';
FLUSH PRIVILEGES;

DROP TABLE IF EXISTS clicks;
DROP TABLE IF EXISTS product_offers;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS banners;
DROP TABLE IF EXISTS articles;
DROP TABLE IF EXISTS marketplaces;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS settings;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama VARCHAR(100) NOT NULL,
    role ENUM('admin', 'editor') NOT NULL DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL,
    icon VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE marketplaces (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_marketplace VARCHAR(100) NOT NULL,
    logo VARCHAR(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_produk VARCHAR(180) NOT NULL,
    kategori_id INT NULL,
    deskripsi TEXT NOT NULL,
    harga INT NOT NULL DEFAULT 0,
    gambar VARCHAR(255) NULL,
    rating DECIMAL(2,1) NOT NULL DEFAULT 0,
    review_count INT NOT NULL DEFAULT 0,
    marketplace VARCHAR(100) NOT NULL,
    link_affiliate VARCHAR(255) NOT NULL,
    status ENUM('aktif', 'draft') NOT NULL DEFAULT 'aktif',
    is_trending TINYINT(1) NOT NULL DEFAULT 0,
    is_viral TINYINT(1) NOT NULL DEFAULT 0,
    badge VARCHAR(60) NULL,
    spesifikasi TEXT NULL,
    kelebihan TEXT NULL,
    kekurangan TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_products_category FOREIGN KEY (kategori_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE product_offers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    marketplace_id INT NOT NULL,
    harga INT NOT NULL,
    link_affiliate VARCHAR(255) NOT NULL,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_product_marketplace (product_id, marketplace_id),
    CONSTRAINT fk_offers_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    CONSTRAINT fk_offers_marketplace FOREIGN KEY (marketplace_id) REFERENCES marketplaces(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(180) NOT NULL,
    gambar VARCHAR(255) NULL,
    isi TEXT NOT NULL,
    tanggal DATE NOT NULL,
    penulis VARCHAR(100) NOT NULL DEFAULT 'Admin Alfayyadh',
    is_rekomendasi TINYINT(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE banners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(160) NOT NULL,
    subtitle VARCHAR(255) NOT NULL,
    gambar VARCHAR(255) NULL,
    button_text VARCHAR(80) NOT NULL,
    button_link VARCHAR(255) NOT NULL,
    status ENUM('aktif', 'draft') NOT NULL DEFAULT 'aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE clicks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    tanggal DATETIME NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    CONSTRAINT fk_clicks_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(80) NOT NULL UNIQUE,
    setting_value TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO users (username, password, nama, role) VALUES
('admin', 'admin123', 'Admin Alfayyadh', 'admin');

INSERT INTO categories (nama_kategori, icon) VALUES
('Elektronik', 'fa-solid fa-tv'),
('Fashion', 'fa-solid fa-shirt'),
('Rumah Tangga', 'fa-solid fa-house-chimney'),
('Bayi & Anak', 'fa-solid fa-baby'),
('Gadget', 'fa-solid fa-mobile-screen-button'),
('Kecantikan', 'fa-solid fa-spray-can-sparkles'),
('Dapur', 'fa-solid fa-kitchen-set'),
('Muslim', 'fa-solid fa-person-praying');

INSERT INTO marketplaces (nama_marketplace, logo) VALUES
('Shopee', 'fa-solid fa-bag-shopping'),
('Tokopedia', 'fa-solid fa-store'),
('TikTok Shop', 'fa-brands fa-tiktok');

INSERT INTO products
(nama_produk, kategori_id, deskripsi, harga, gambar, rating, review_count, marketplace, link_affiliate, status, is_trending, is_viral, badge, spesifikasi, kelebihan, kekurangan, created_at)
VALUES
('Xiaomi CCTV Smart Camera 2K', 1, 'Kamera CCTV indoor dengan resolusi 2K, night vision jernih, deteksi gerakan, dan audio dua arah. Cocok untuk memantau rumah, toko kecil, atau kamar bayi.', 299000, 'https://placehold.co/640x640/f8fafc/111827?text=Smart+Camera+2K', 4.9, 12500, 'Shopee', 'https://shopee.co.id/affiliate-demo-camera', 'aktif', 1, 0, 'Trending', 'Resolusi 2K Super HD\nNight Vision Jernih\nBisa Dipantau dari HP\nDeteksi Gerakan\nAudio Dua Arah', 'Gambar jernih dan tajam\nMudah instalasi\nAplikasi stabil', 'Butuh koneksi WiFi stabil', '2026-08-12 09:00:00'),
('Lampu LED Sensor Otomatis', 3, 'Lampu sensor gerak hemat energi untuk area tangga, dapur, gudang, dan kamar. Menyala otomatis saat mendeteksi gerakan.', 35000, 'https://placehold.co/640x640/f8fafc/111827?text=Lampu+Sensor', 4.8, 6100, 'TikTok Shop', 'https://www.tiktok.com/shop/affiliate-demo-lampu', 'aktif', 1, 1, 'Produk Viral', 'Sensor gerak otomatis\nDaya rendah\nCahaya putih lembut\nBaterai isi ulang', 'Harga terjangkau\nPraktis ditempel\nCocok untuk rumah', 'Ukuran kecil untuk ruangan besar', '2026-08-13 10:20:00'),
('Rak Serbaguna Minimalis', 3, 'Rak susun minimalis untuk kamar, dapur, dan ruang kerja. Desain ringan tetapi kuat untuk menata barang harian.', 89000, 'https://placehold.co/640x640/f8fafc/111827?text=Rak+Minimalis', 4.7, 4280, 'Tokopedia', 'https://tokopedia.com/affiliate-demo-rak', 'aktif', 1, 1, 'Pilihan Admin', 'Material metal ringan\nEmpat tingkat\nMudah dirakit\nDesain minimalis', 'Kapasitas lega\nTampilan rapi\nHarga kompetitif', 'Perlu perakitan manual', '2026-08-14 12:00:00'),
('Vacuum Cleaner Dermini DX700', 3, 'Vacuum cleaner kabel dengan daya hisap kuat, cocok untuk rumah, apartemen, dan kos. Bodinya ramping sehingga mudah disimpan.', 599000, 'https://placehold.co/640x640/f8fafc/111827?text=Vacuum+Cleaner', 4.9, 8300, 'Shopee', 'https://shopee.co.id/affiliate-demo-vacuum', 'aktif', 1, 0, 'Best Deal', 'Daya hisap kuat\nFilter mudah dicuci\nKabel panjang\nNozzle multifungsi', 'Hisap debu efektif\nMudah dibersihkan\nAksesoris lengkap', 'Suara cukup terdengar', '2026-08-15 08:30:00'),
('Jam Tangan Digital Sport Waterproof', 5, 'Jam tangan sport anti air dengan tampilan digital, stopwatch, alarm, dan desain ringan untuk aktivitas harian.', 125000, 'https://placehold.co/640x640/f8fafc/111827?text=Jam+Digital', 4.8, 5020, 'TikTok Shop', 'https://www.tiktok.com/shop/affiliate-demo-watch', 'aktif', 1, 0, 'Murah', 'Waterproof harian\nAlarm dan stopwatch\nStrap silikon\nBaterai awet', 'Ringan dipakai\nDesain sporty\nHarga ramah', 'Fitur bukan smartwatch penuh', '2026-08-16 11:15:00'),
('Soundbar Bluetooth Speaker', 1, 'Speaker soundbar ringkas untuk TV, laptop, dan smartphone. Suara lebih tebal dengan koneksi Bluetooth dan AUX.', 299000, 'https://placehold.co/640x640/f8fafc/111827?text=Soundbar', 4.8, 3900, 'Tokopedia', 'https://tokopedia.com/affiliate-demo-soundbar', 'aktif', 0, 0, NULL, 'Bluetooth 5.0\nAUX dan USB\nBass boost\nRemote control', 'Suara jernih\nMudah tersambung\nUkuran ringkas', 'Bass sedang untuk ruangan besar', '2026-08-10 09:45:00'),
('Headset Gaming LED', 5, 'Headset gaming dengan lampu LED, mikrofon fleksibel, dan bantalan telinga empuk untuk bermain lebih lama.', 156000, 'https://placehold.co/640x640/f8fafc/111827?text=Headset+Gaming', 4.7, 5100, 'TikTok Shop', 'https://www.tiktok.com/shop/affiliate-demo-headset', 'aktif', 0, 0, NULL, 'LED gaming\nMic fleksibel\nJack 3.5mm\nBantalan empuk', 'Nyaman dipakai\nMic cukup jelas\nDesain menarik', 'Butuh adaptor untuk beberapa perangkat', '2026-08-09 17:10:00'),
('Smart TV 22 Inch HD', 1, 'Smart TV ukuran compact dengan resolusi HD, cocok untuk kamar tidur, kos, atau dapur kecil.', 1299000, 'https://placehold.co/640x640/f8fafc/111827?text=Smart+TV+22', 4.8, 2180, 'Shopee', 'https://shopee.co.id/affiliate-demo-tv', 'aktif', 0, 0, NULL, 'Panel HD\nPort HDMI dan USB\nAplikasi streaming\nSpeaker stereo', 'Ukuran compact\nGambar tajam\nMudah dipasang', 'Tidak cocok untuk ruang besar', '2026-08-08 08:40:00'),
('Powerbank 20000mAh Fast Charging', 5, 'Powerbank kapasitas besar dengan dukungan fast charging, cocok untuk perjalanan dan penggunaan harian.', 99000, 'https://placehold.co/640x640/f8fafc/111827?text=Powerbank+20K', 4.8, 6540, 'Tokopedia', 'https://tokopedia.com/affiliate-demo-powerbank', 'aktif', 0, 0, 'Hemat', 'Kapasitas 20000mAh\nFast charging\nDual output\nIndikator baterai', 'Kapasitas besar\nHarga bagus\nPort lengkap', 'Bobot agak berat', '2026-08-07 13:20:00'),
('Keyboard Mechanical RGB', 5, 'Keyboard mechanical compact dengan RGB dan switch tactile untuk kerja, mengetik, dan gaming.', 345000, 'https://placehold.co/640x640/f8fafc/111827?text=Keyboard+RGB', 4.8, 4920, 'TikTok Shop', 'https://www.tiktok.com/shop/affiliate-demo-keyboard', 'aktif', 0, 0, NULL, 'Switch tactile\nRGB mode\nLayout compact\nKabel detachable', 'Typing feel nyaman\nLampu terang\nBuild solid', 'Suara switch cukup keras', '2026-08-06 15:00:00'),
('Set Panci Dapur Ceramic', 7, 'Set panci anti lengket untuk memasak harian. Lapisan ceramic mudah dibersihkan dan tampil modern di dapur.', 179000, 'https://placehold.co/640x640/f8fafc/111827?text=Set+Panci', 4.6, 3100, 'Shopee', 'https://shopee.co.id/affiliate-demo-panci', 'aktif', 0, 0, NULL, 'Lapisan ceramic\nHandle anti panas\nBisa untuk berbagai kompor\nMudah dibersihkan', 'Anti lengket\nDesain cantik\nCocok untuk keluarga kecil', 'Perlu perawatan agar awet', '2026-08-05 10:05:00'),
('Tas Muslimah Daily', 8, 'Tas daily wanita dengan desain simpel, warna netral, dan ruang penyimpanan cukup untuk aktivitas harian.', 135000, 'https://placehold.co/640x640/f8fafc/111827?text=Tas+Muslimah', 4.7, 2760, 'Tokopedia', 'https://tokopedia.com/affiliate-demo-tas', 'aktif', 0, 0, NULL, 'Bahan sintetis halus\nTali adjustable\nKompartemen utama luas\nWarna netral', 'Mudah dipadukan\nRingan\nHarga terjangkau', 'Tidak untuk beban terlalu berat', '2026-08-04 18:30:00');

INSERT INTO product_offers (product_id, marketplace_id, harga, link_affiliate) VALUES
(1, 1, 299000, 'https://shopee.co.id/affiliate-demo-camera'),
(1, 2, 310000, 'https://tokopedia.com/affiliate-demo-camera'),
(1, 3, 305000, 'https://www.tiktok.com/shop/affiliate-demo-camera'),
(2, 1, 39000, 'https://shopee.co.id/affiliate-demo-lampu'),
(2, 2, 42000, 'https://tokopedia.com/affiliate-demo-lampu'),
(2, 3, 35000, 'https://www.tiktok.com/shop/affiliate-demo-lampu'),
(3, 1, 95000, 'https://shopee.co.id/affiliate-demo-rak'),
(3, 2, 89000, 'https://tokopedia.com/affiliate-demo-rak'),
(3, 3, 93000, 'https://www.tiktok.com/shop/affiliate-demo-rak'),
(4, 1, 599000, 'https://shopee.co.id/affiliate-demo-vacuum'),
(4, 2, 625000, 'https://tokopedia.com/affiliate-demo-vacuum'),
(4, 3, 610000, 'https://www.tiktok.com/shop/affiliate-demo-vacuum'),
(5, 1, 132000, 'https://shopee.co.id/affiliate-demo-watch'),
(5, 2, 129000, 'https://tokopedia.com/affiliate-demo-watch'),
(5, 3, 125000, 'https://www.tiktok.com/shop/affiliate-demo-watch'),
(6, 1, 310000, 'https://shopee.co.id/affiliate-demo-soundbar'),
(6, 2, 299000, 'https://tokopedia.com/affiliate-demo-soundbar'),
(6, 3, 319000, 'https://www.tiktok.com/shop/affiliate-demo-soundbar'),
(7, 1, 165000, 'https://shopee.co.id/affiliate-demo-headset'),
(7, 2, 172000, 'https://tokopedia.com/affiliate-demo-headset'),
(7, 3, 156000, 'https://www.tiktok.com/shop/affiliate-demo-headset'),
(8, 1, 1299000, 'https://shopee.co.id/affiliate-demo-tv'),
(8, 2, 1320000, 'https://tokopedia.com/affiliate-demo-tv'),
(8, 3, 1345000, 'https://www.tiktok.com/shop/affiliate-demo-tv'),
(9, 1, 105000, 'https://shopee.co.id/affiliate-demo-powerbank'),
(9, 2, 99000, 'https://tokopedia.com/affiliate-demo-powerbank'),
(9, 3, 112000, 'https://www.tiktok.com/shop/affiliate-demo-powerbank'),
(10, 1, 359000, 'https://shopee.co.id/affiliate-demo-keyboard'),
(10, 2, 355000, 'https://tokopedia.com/affiliate-demo-keyboard'),
(10, 3, 345000, 'https://www.tiktok.com/shop/affiliate-demo-keyboard'),
(11, 1, 179000, 'https://shopee.co.id/affiliate-demo-panci'),
(11, 2, 188000, 'https://tokopedia.com/affiliate-demo-panci'),
(11, 3, 185000, 'https://www.tiktok.com/shop/affiliate-demo-panci'),
(12, 1, 145000, 'https://shopee.co.id/affiliate-demo-tas'),
(12, 2, 135000, 'https://tokopedia.com/affiliate-demo-tas'),
(12, 3, 139000, 'https://www.tiktok.com/shop/affiliate-demo-tas');

INSERT INTO articles (judul, gambar, isi, tanggal, penulis, is_rekomendasi) VALUES
('5 Rekomendasi Vacuum Cleaner Terbaik 2026', 'https://placehold.co/900x560/f8fafc/111827?text=Vacuum+Cleaner+Review', 'Vacuum cleaner terbaik bukan hanya soal daya hisap besar, tetapi juga bobot, panjang kabel, kemudahan membersihkan filter, dan ketersediaan aksesoris.\n\nUntuk kebutuhan rumah harian, pilih model yang mudah disimpan dan punya nozzle tambahan. Jika ruangan luas, perhatikan kapasitas debu dan kenyamanan genggaman.\n\nKami merekomendasikan produk dengan rating tinggi, harga kompetitif, dan ulasan pembeli yang konsisten.', '2026-08-20', 'Admin Alfayyadh', 1),
('10 HP Terbaik Harga 3 Jutaan Tahun 2026', 'https://placehold.co/900x560/f8fafc/111827?text=HP+3+Jutaan+2026', 'Segmen HP 3 jutaan menjadi salah satu yang paling ramai karena sudah menawarkan kamera bagus, layar halus, dan baterai besar.\n\nSebelum membeli, cek chipset, RAM, penyimpanan, kualitas layar, dan dukungan update. Untuk gaming ringan, pilih perangkat dengan pendinginan yang baik.\n\nBandingkan harga antar marketplace karena selisih promo sering cukup besar.', '2026-08-18', 'Admin Alfayyadh', 0),
('Cara Memilih Kamera CCTV Rumah yang Tepat', 'https://placehold.co/900x560/f8fafc/111827?text=Panduan+CCTV', 'CCTV rumah idealnya memiliki resolusi minimal Full HD, night vision, deteksi gerakan, dan aplikasi mobile yang stabil.\n\nUntuk ruangan indoor, kamera kecil dengan rotasi 360 derajat lebih praktis. Untuk luar ruangan, pilih produk tahan air dengan housing yang kuat.\n\nPastikan juga penyimpanan cloud atau kartu memori sesuai kebutuhan.', '2026-08-16', 'Admin Alfayyadh', 0),
('Tips Belanja Produk Viral Agar Tidak Salah Pilih', 'https://placehold.co/900x560/f8fafc/111827?text=Produk+Viral', 'Produk viral sering menarik karena diskon dan review yang ramai. Namun, tetap cek spesifikasi, reputasi penjual, foto pembeli, dan kebijakan retur.\n\nGunakan perbandingan harga agar tidak terjebak harga naik sebelum promo. Simpan produk pilihan dan beli saat voucher aktif.\n\nPortal affiliate seperti Alfayyadh.ID membantu menyaring produk populer yang layak dipertimbangkan.', '2026-08-14', 'Admin Alfayyadh', 0);

INSERT INTO banners (judul, subtitle, gambar, button_text, button_link, status) VALUES
('FLASH SALE Diskon Hingga 70%', 'Promo produk pilihan dari Shopee, Tokopedia, dan TikTok Shop dengan update rekomendasi harian.', NULL, 'Ambil Promo', '/alfayyadh-id/kategori.php?sort=trending', 'aktif');

INSERT INTO clicks (product_id, tanggal, ip_address) VALUES
(1, '2026-08-16 08:05:00', '127.0.0.1'),
(1, '2026-08-16 10:20:00', '127.0.0.1'),
(2, '2026-08-17 11:15:00', '127.0.0.1'),
(3, '2026-08-17 14:05:00', '127.0.0.1'),
(4, '2026-08-18 09:30:00', '127.0.0.1'),
(4, '2026-08-18 16:22:00', '127.0.0.1'),
(5, '2026-08-19 12:11:00', '127.0.0.1'),
(1, '2026-08-20 07:45:00', '127.0.0.1'),
(9, '2026-08-21 19:40:00', '127.0.0.1'),
(10, '2026-08-22 09:10:00', '127.0.0.1');

INSERT INTO settings (setting_key, setting_value) VALUES
('site_title', 'Alfayyadh.ID'),
('site_tagline', 'Portal rekomendasi produk affiliate Indonesia'),
('contact_email', 'halo@alfayyadh.id'),
('commission_estimate', '350000');
