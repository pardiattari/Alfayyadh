# Alfayyadh.ID

Portal affiliate marketplace aggregator untuk rekomendasi produk Shopee, Tokopedia, dan TikTok Shop. Website ini tidak memiliki checkout; semua tombol beli diarahkan ke link affiliate.

## Cara Menjalankan dengan Docker (Recommended)

1. Pastikan Docker dan Docker Compose sudah terinstall.
2. Salin file `.env.example` ke `.env` (opsional, jika ingin mengubah port/kredensial):
   ```bash
   cp .env.example .env
   ```
3. Jalankan container:
   ```bash
   docker compose up -d --build
   ```
4. Akses website: `http://localhost:8027`
5. Admin panel: `http://localhost:8027/admin/login.php`
6. phpMyAdmin: `http://localhost:8028`

Default login admin:
- Username: `admin`
- Password: `admin123`

Port yang digunakan:
- Web: 8027
- MySQL: 8023
- phpMyAdmin: 8028

## Cara Menjalankan di XAMPP

1. Salin folder `alfayyadh-id` ke `C:\xampp\htdocs\alfayyadh-id`.
2. Buka phpMyAdmin, lalu import file `database/alfayyadh_db.sql`.
3. Jika credential MySQL berbeda, ubah `includes/config.php`.
4. Buka `http://localhost/alfayyadh-id/`.
5. Admin panel: `http://localhost/alfayyadh-id/admin/login.php`.

Default login admin:

- Username: `admin`
- Password: `admin123`

## Struktur Utama

- `index.php` - Beranda dengan produk trending, kategori, viral, promo, rekomendasi, dan artikel SEO.
- `produk.php` - Detail produk dengan galeri, marketplace affiliate, tab deskripsi, spesifikasi, dan review.
- `kategori.php` - Grid produk dengan filter harga, marketplace, rating, dan pencarian.
- `compare.php` - Perbandingan harga produk lintas marketplace.
- `blog.php` - Listing dan detail artikel SEO.
- `redirect.php` - Pencatat klik affiliate sebelum mengarahkan ke marketplace.
- `admin/` - Login, dashboard, CRUD produk, kategori, marketplace, banner, artikel, statistik, dan pengaturan.
- `database/alfayyadh_db.sql` - Struktur database dan dummy data.

## Catatan

Gambar dummy memakai URL placeholder agar mudah diganti dari admin. Untuk produksi, upload gambar produk asli ke `assets/uploads/products/` lalu simpan path-nya pada kolom `gambar`.
