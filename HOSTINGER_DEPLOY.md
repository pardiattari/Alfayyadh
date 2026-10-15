# Panduan Deployment ke Hostinger Shared Hosting

## Persiapan Sebelum Deploy

### 1. Persiapkan Database di Hostinger
1. Login ke **hPanel Hostinger**
2. Masuk ke **Databases** → **MySQL Databases**
3. Buat database baru:
   - Nama database: `u1234567_alfayyadh` (sesuai format Hostinger)
   - Username: `u1234567_alfayyadh`
   - Password: **generate password yang kuat**
4. Catat kredensial database (host, nama db, user, password)
5. Masuk ke **phpMyAdmin** dan import file `database/alfayyadh_db.sql`

### 2. Persiapkan File Environment
1. Salin `.env.example` menjadi `.env`:
   ```bash
   cp .env.example .env
   ```
2. Edit file `.env` dengan kredensial Hostinger Anda:
   ```env
   DB_HOST=localhost
   DB_NAME=u1234567_alfayyadh
   DB_USER=u1234567_alfayyadh
   DB_PASS=your_strong_password
   BASE_URL=https://yourdomain.com
   APP_KEY=generate_random_32_char_key
   ```

### 3. Generate APP_KEY
Jalankan di terminal lokal atau gunakan online generator:
```bash
openssl rand -hex 32
# atau PHP:
php -r "echo bin2hex(random_bytes(32));"
```

## Upload ke Hostinger

### Opsi A: File Manager (Disarankan untuk pemula)
1. Buka **File Manager** di hPanel
2. Masuk ke folder `public_html` (atau subfolder jika di subdomain)
3. **Upload semua file** kecuali:
   - `.env.example` (opsional)
   - `Dockerfile`
   - `docker-compose.yml`
   - `database/` (folder, sudah di-import ke phpMyAdmin)
   - `.vscode/`
   - `README.md` (opsional)
   - `AGENTS.md` (opsional)

### Opsi B: FTP/SFTP (Lebih cepat untuk file banyak)
1. Gunakan FileZilla atau WinSCP
2. Host: `ftp.yourdomain.com` atau IP server
3. Username/Password: kredensial FTP dari Hostinger
4. Upload ke folder `public_html`

## Struktur Folder di Hostinger

```
public_html/
├── .htaccess          ← WAJIB (sudah dikonfigurasi untuk shared hosting)
├── .user.ini          ← WAJIB (konfigurasi PHP)
├── .env               ← WAJIB (isi kredensial database & BASE_URL)
├── index.php
├── produk.php
├── kategori.php
├── compare.php
├── blog.php
├── redirect.php
├── includes/
│   ├── config.php
│   ├── db.php
│   ├── functions.php
│   ├── header.php
│   └── footer.php
├── admin/
│   ├── index.php
│   ├── login.php
│   ├── dashboard.php
│   ├── products.php
│   ├── categories.php
│   ├── marketplaces.php
│   ├── banners.php
│   ├── articles.php
│   ├── settings.php
│   ├── statistics.php
│   ├── upload-ajax.php
│   ├── logout.php
│   └── includes/
│       ├── auth.php
│       ├── header.php
│       ├── footer.php
│       ├── media-field.php
│       └── media-helper.php
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── main.js
│   └── uploads/
│       ├── products/
│       └── logo/
└── logs/              ← Buat folder ini manual, chmod 755
```

## Langkah Pasca-Upload

### 1. Buat Folder Logs
Di File Manager, buat folder `logs` di root (`public_html/logs`) dan set permission **755**.

### 2. Set Permission Folder Uploads
Pastikan folder `assets/uploads/products` dan `assets/uploads/logo` memiliki permission **755** (writable).

### 3. Setup SSL/HTTPS
1. Di hPanel, masuk ke **SSL** → **Install SSL**
2. Pilih **Let's Encrypt** (gratis) atau SSL berbayar
3. Tunggu hingga aktif (biasanya 5-15 menit)

### 4. Update BASE_URL ke HTTPS
Edit file `.env`:
```env
BASE_URL=https://yourdomain.com
```

### 5. Test Website
- Buka `https://yourdomain.com` → Harus menampilkan beranda
- Buka `https://yourdomain.com/admin/login.php` → Login admin
- Default login: `admin` / `admin123` **(GANTI SEGERA SETELAH LOGIN PERTAMA!)**

## Konfigurasi Tambahan

### 1. Ganti Password Admin Default
1. Login ke admin panel
2. Masuk ke **Pengaturan** → **Akun Admin**
3. Ganti password `admin123` ke password yang kuat

### 2. Upload Logo
1. Di admin panel, **Beranda** → Upload logo di bagian Hero
2. Atau upload manual ke `assets/uploads/logo/ALFAYYADH.png`

### 3. Konfigurasi Email (Opsional)
Jika ingin fitur kontak/email berfungsi, konfigurasikan di **Pengaturan** → **Email** atau gunakan SMTP plugin.

### 4. Backup Otomatis
Aktifkan **Backup Harian** di hPanel Hostinger (gratis di paket tertentu).

## Troubleshooting Umum

| Masalah | Solusi |
|---------|--------|
| **Error 500 / White Screen** | Cek `logs/error.log`; pastikan `.env` benar; cek permission folder |
| **Database connection failed** | Verifikasi kredensial DB di `.env`; pastikan user DB punya akses ke database |
| **Asset (CSS/JS) tidak load** | Cek `BASE_URL` di `.env` harus benar (dengan trailing slash); cek `.htaccess` |
| **Upload gambar gagal** | Pastikan `assets/uploads/products` permission 755; cek `.user.ini` upload limit |
| **Session tidak kerja** | Pastikan `session.save_path` di `.user.ini` writable; cek cookie domain |
| **Redirect loop** | Cek `BASE_URL` konsisten (pakai www atau non-www); cek `.htaccess` redirect rules |

## Optimasi Performa (Opsional)

### 1. Enable Cloudflare (Gratis)
- Daftar di Cloudflare
- Ubah nameserver ke Cloudflare
- Aktifkan: **Auto Minify** (CSS, JS, HTML), **Brotli**, **Rocket Loader** (Off untuk PHP)

### 2. Caching Browser
Sudah dikonfigurasi di `.htaccess` (mod_expires).

### 3. Gzip Compression
Sudah dikonfigurasi di `.htaccess` (mod_deflate).

## Keamanan

✅ **Sudah dikonfigurasi otomatis:**
- Directory browsing disabled
- Sensitive files protected (`.env`, `database/`, `includes/`)
- Security headers (XSS, CSRF, Clickjacking protection)
- HTTPS enforcement ready
- PHP error display disabled in production
- Session cookies secure + HttpOnly + SameSite

⚠️ **Harus Anda lakukan manual:**
- [ ] Ganti password admin default
- [ ] Generate `APP_KEY` unik di `.env`
- [ ] Aktifkan SSL/HTTPS
- [ ] Setup backup harian di hPanel
- [ ] Monitor `logs/error.log` berkala

## Update Website ke Versi Baru

1. Backup database di phpMyAdmin → Export
2. Backup folder `assets/uploads/` (gambar produk/logo)
3. Upload file PHP baru (timpa yang lama)
4. **JANGAN** timpa file `.env` dan folder `assets/uploads/`
5. Jika ada perubahan database, jalankan SQL migration manual di phpMyAdmin

---

**Butuh bantuan?** Cek `logs/error.log` untuk detail error, atau hubungi support Hostinger 24/7 via live chat.
