# Deployment Guide: Alfayyadh.ID ke Cloudflare + Platform PHP

## Arsitektur Deployment

```
┌─────────────┐     ┌──────────────┐     ┌─────────────────┐
│   User      │────▶│  Cloudflare  │────▶│  PHP Platform   │
│  (Browser)  │     │  (DNS/Proxy) │     │  (Railway/      │
└─────────────┘     └──────────────┘     │   Render/       │
                                          │   Fly.io/VPS)   │
                                          └────────┬────────┘
                                                   │
                                          ┌────────▼────────┐
                                          │  Managed MySQL  │
                                          │  (PlanetScale/  │
                                          │   Neon/Railway) │
                                          └─────────────────┘
```

## Opsi Platform PHP (Pilih Satu)

### 1. **Railway** (Paling Mudah - Direkomendasikan)
- ✅ Support PHP + MySQL native
- ✅ Deploy dari GitHub otomatis
- ✅ Environment variables management
- ✅ Custom domain + SSL gratis
- 💰 $5/bulan untuk hobby projects

### 2. **Render**
- ✅ Free tier tersedia (dengan cold start)
- ✅ Managed PostgreSQL/MySQL
- ✅ Auto-deploy dari Git
- 💰 $7/bulan untuk paid tier

### 3. **Fly.io**
- ✅ Docker-based, full control
- ✅ Global edge deployment
- ✅ Gratis untuk small apps
- ⚠️ Kurva belajar lebih tinggi

### 4. **VPS + Cloudflare Tunnel** (Full Control)
- ✅ Total control, biaya rendah ($4-6/bulan)
- ✅ Bisa pakai Docker Compose existing
- ⚠️ Perlu manage server sendiri

---

## Persiapan Umum

### 1. Environment Variables (Wajib)
Buat file `.env` di production dengan:

```env
# Database (dari managed MySQL provider)
DB_HOST=your-db-host
DB_NAME=alfayyadh_db
DB_USER=your-db-user
DB_PASS=your-secure-password

# App
BASE_URL=https://alfayyadh.id
APP_NAME=Alfayyadh.ID

# Security (generate random strings)
SESSION_SECRET=your-random-32-char-string
```

### 2. Database Setup
1. Buat database di managed MySQL provider
2. Import `database/alfayyadh_db.sql`
3. Catat connection details (host, port, user, pass, database name)

### 3. File Uploads
Untuk production, gunakan **object storage** (S3/R2/Cloudflare R2) bukan local filesystem:

```php
// Di includes/config.php tambahkan:
define('UPLOAD_DRIVER', 's3'); // atau 'local' untuk development
define('S3_BUCKET', 'your-bucket');
define('S3_KEY', 'your-key');
define('S3_SECRET', 'your-secret');
define('S3_REGION', 'auto'); // untuk Cloudflare R2
define('S3_ENDPOINT', 'https://your-account.r2.cloudflarestorage.com');
```

---

## Deployment ke Railway (Direkomendasikan)

### Langkah 1: Push ke GitHub
```bash
git init
git add .
git commit -m "Initial commit for deployment"
git remote add origin https://github.com/username/alfayyadh-id.git
git push -u origin main
```

### Langkah 2: Setup di Railway
1. Buka [railway.app](https://railway.app) → "New Project" → "Deploy from GitHub repo"
2. Pilih repo `alfayyadh-id`
3. Railway akan detect Dockerfile otomatis

### Langkah 3: Tambah Database MySQL
1. Di Railway project → "New Service" → "Database" → "MySQL"
2. Copy connection variables ke Environment Variables

### Langkah 4: Set Environment Variables
Di Railway → Variables, tambahkan:
```env
DB_HOST=${{MySQL.MYSQL_HOST}}
DB_NAME=${{MySQL.MYSQL_DATABASE}}
DB_USER=${{MySQL.MYSQL_USER}}
DB_PASS=${{MySQL.MYSQL_PASSWORD}}
BASE_URL=https://your-app.railway.app
SESSION_SECRET=generate-random-32-chars
```

### Langkah 5: Custom Domain + Cloudflare
1. Railway → Settings → Domains → "Custom Domain"
2. Tambah `alfayyadh.id` dan `www.alfayyadh.id`
3. Di Cloudflare DNS, tambah CNAME:
   - `alfayyadh.id` → `your-app.railway.app` (Proxied ☁️)
   - `www` → `your-app.railway.app` (Proxied ☁️)
4. Cloudflare SSL/TLS → "Full (strict)"

---

## Deployment ke Render

### render.yaml (Simpan di root project)
```yaml
services:
  - type: web
    name: alfayyadh-web
    runtime: docker
    dockerfilePath: ./Dockerfile
    envVars:
      - key: DB_HOST
        fromDatabase:
          name: alfayyadh-db
          property: host
      - key: DB_NAME
        fromDatabase:
          name: alfayyadh-db
          property: database
      - key: DB_USER
        fromDatabase:
          name: alfayyadh-db
          property: user
      - key: DB_PASS
        fromDatabase:
          name: alfayyadh-db
          property: password
      - key: BASE_URL
        value: https://alfayyadh.id
      - key: SESSION_SECRET
        generateValue: true

databases:
  - name: alfayyadh-db
    databaseName: alfayyadh_db
    user: alfayyadh
    plan: free
```

---

## Cloudflare Configuration (Setelah App Live)

### 1. DNS Settings
| Type | Name | Target | Proxy |
|------|------|--------|-------|
| CNAME | @ | your-app.railway.app | ☁️ Proxied |
| CNAME | www | your-app.railway.app | ☁️ Proxied |

### 2. SSL/TLS
- **Mode**: Full (strict)
- **Edge Certificates**: Always Use HTTPS ✅
- **Automatic HTTPS Rewrites** ✅

### 3. Speed Optimization
- **Auto Minify**: CSS, JS, HTML ✅
- **Brotli** ✅
- **Early Hints** ✅
- **Rocket Loader** ⚠️ Test dulu (bisa break JS)

### 4. Caching (Page Rules / Cache Rules)
```
# Cache static assets agresif
assets/* → Cache Level: Cache Everything, Edge TTL: 1 year

# Cache halaman publik (bukan admin)
/* → Cache Level: Standard, Edge TTL: 1 hour, Browser TTL: 30 min

# Bypass cache untuk admin
/admin/* → Cache Level: Bypass
```

### 5. Security (WAF)
- **Security Level**: Medium
- **Bot Fight Mode** ✅
- **Rate Limiting**: Buat rule untuk `/admin/login.php` (5 req/menit)

### 6. Workers (Optional - untuk SEO/Performance)
```javascript
// Cloudflare Worker untuk inject meta tags, redirects, dll
export default {
  async fetch(request, env, ctx) {
    const response = await fetch(request);
    // Add security headers
    const newHeaders = new Headers(response.headers);
    newHeaders.set('X-Content-Type-Options', 'nosniff');
    newHeaders.set('X-Frame-Options', 'SAMEORIGIN');
    newHeaders.set('Referrer-Policy', 'strict-origin-when-cross-origin');
    return new Response(response.body, {
      status: response.status,
      statusText: response.statusText,
      headers: newHeaders
    });
  }
}
```

---

## Checklist Pre-Launch

- [ ] Database migrated dan tested
- [ ] Environment variables semua diset
- [ ] `BASE_URL` pakai HTTPS domain final
- [ ] File uploads ke object storage (R2/S3)
- [ ] SSL certificate valid (Cloudflare handle)
- [ ] Admin login berfungsi
- [ ] Affiliate redirect (`redirect.php`) berfungsi
- [ ] Gambar produk tampil
- [ ] Search & filter kategori berfungsi
- [ ] Google Analytics / Search Console setup
- [ ] `robots.txt` dan `sitemap.xml` (buat jika belum ada)

---

## Monitoring & Maintenance

### Logs
- Railway/Render: Built-in logs dashboard
- Cloudflare: Analytics → Logs (butuh plan paid untuk logpush)

### Backup Database
- PlanetScale/Neon: Built-in branching & backup
- Railway: Manual `mysqldump` via cron job

### Updates
```bash
# Push ke main branch → auto deploy (Railway/Render)
git add .
git commit -m "Update: ..."
git push
```

---

## Estimasi Biaya Bulanan

| Komponen | Railway | Render | Fly.io | VPS (DigitalOcean) |
|----------|---------|--------|--------|-------------------|
| PHP App | $5 | $7 (paid) | ~$5 | $4-6 |
| MySQL | $5 | Included | ~$5 | $15 (managed) |
| Cloudflare | Free | Free | Free | Free |
| **Total** | **~$10** | **~$7** | **~$10** | **~$19** |

---

## Troubleshooting Umum

| Masalah | Solusi |
|---------|--------|
| "Connection refused" DB | Cek `DB_HOST` pakai internal hostname (bukan localhost) |
| Gambar tidak muncul | Cek `BASE_URL` + upload driver config |
| Redirect loop | Cloudflare SSL mode "Full (strict)" + app generate HTTPS URLs |
| Session tidak persist | Set `SESSION_SECRET` sama di semua instance |
| Upload gagal | Permission folder `assets/uploads` atau migrasi ke S3/R2 |

---

## Next Steps

1. **Pilih platform** (Railway direkomendasikan untuk kemudahan)
2. **Setup managed MySQL** (PlanetScale free tier bagus)
3. **Configure Cloudflare DNS + Proxy**
4. **Test thoroughly** di staging domain dulu
5. **Switch production traffic**

Butuh bantuan setup spesifik platform mana? Saya bisa bantu konfigurasi detail untuk Railway, Render, atau VPS.
