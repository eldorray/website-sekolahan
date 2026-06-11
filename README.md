# 🏫 Website Sekolahan

**Website Sekolahan** adalah aplikasi website sekolah modern berbasis **Laravel 13**, **Livewire 4**, dan **TailwindCSS 4**. Didesain untuk memudahkan sekolah dalam mengelola informasi, berita, program unggulan, pendaftaran siswa baru (PPDB), hingga data guru — semuanya dalam satu platform yang elegan dan responsif.

![Homepage](screenshots/homepage.png)

---

## ✨ Fitur Utama

| Fitur | Deskripsi |
|---|---|
| 🏠 **Landing Page** | Halaman publik yang modern dan responsif |
| 📰 **Manajemen Berita** | CRUD berita dengan rich text editor (Tiptap) |
| 📚 **Program Sekolah** | Kelola program unggulan sekolah |
| 👨‍🏫 **Data Guru** | Profil guru lengkap dengan sosial media |
| 📝 **PPDB Online** | Formulir pendaftaran siswa baru |
| 📩 **Pesan Kontak** | Terima pesan dari pengunjung website |
| 🗓️ **Jadwal Kunjungan** | Pengelolaan jadwal kunjungan sekolah |
| 👤 **Multi-Role Auth** | Sistem login dengan peran Admin & Guru |
| ⚙️ **Pengaturan Dinamis** | Ubah nama sekolah, warna brand, kontak, dll. dari panel admin |
| 🎨 **Brand Color** | Tema warna yang bisa dikustomisasi dari panel admin |

---

## 🛠️ Tech Stack

- **Backend:** PHP 8.3+, Laravel 13
- **Frontend:** Livewire 4, TailwindCSS 4, Vite 8
- **Rich Editor:** Tiptap
- **Database:** SQLite (default) / MySQL
- **Testing:** PestPHP

---

## 📋 Persyaratan Sistem

Pastikan komputer kamu sudah terinstal:

- **PHP** >= 8.3
- **Composer** >= 2.x
- **Node.js** >= 20.x
- **NPM** >= 10.x
- **Git**

---

## 🚀 Cara Menggunakan Source Code

### 1. Clone Repository

```bash
git clone https://github.com/eldorray/website-sekolahan.git
cd website-sekolahan
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. Konfigurasi Environment

```bash
# Salin file .env
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Setup Database

Secara default, aplikasi menggunakan **SQLite**. Cukup buat file database-nya:

```bash
touch database/database.sqlite
```

> 💡 Jika ingin menggunakan **MySQL**, ubah konfigurasi `DB_CONNECTION` di file `.env`:
>
> ```env
> DB_CONNECTION=mysql
> DB_HOST=127.0.0.1
> DB_PORT=3306
> DB_DATABASE=website_sekolahan
> DB_USERNAME=root
> DB_PASSWORD=
> ```

### 5. Migrasi & Seed Database

```bash
# Jalankan migrasi untuk membuat tabel
php artisan migrate

# Isi data contoh (admin, guru, berita, program, dll.)
php artisan db:seed
```

### 6. Link Storage

```bash
php artisan storage:link
```

### 7. Jalankan Aplikasi

```bash
# Jalankan semua service sekaligus (server, queue, logs, vite)
composer dev
```

Atau jalankan secara terpisah:

```bash
# Terminal 1 — Server
php artisan serve

# Terminal 2 — Vite (frontend build)
npm run dev
```

Buka browser dan akses: **http://localhost:8000**

---

## 🔐 Akun

### Development / demo

`php artisan db:seed` hanya berjalan di environment **non-produksi** dan membuat akun demo
(admin `admin@school.id` & beberapa guru). Password seed default `password` dan dapat
diganti lewat `SEED_PASSWORD` di `.env`:

```bash
SEED_PASSWORD=rahasia-kuat php artisan db:seed
```

### Produksi

Seeder **tidak** membuat akun apa pun di produksi. Buat admin pertama secara interaktif:

```bash
php artisan admin:create
# atau non-interaktif:
php artisan admin:create --name="Nama Admin" --email="admin@sekolah.sch.id"
```

- **Panel Admin:** `/admin`
- **Panel Guru:** `/guru`

> ⚠️ Jangan pernah memakai password demo (`password`) di produksi.

---

## 📁 Struktur Direktori

```
website-sekolahan/
├── app/
│   ├── Http/Middleware/      # EnsureRole middleware
│   ├── Livewire/
│   │   ├── Admin/            # Komponen panel admin
│   │   ├── Auth/             # Login
│   │   ├── Guru/             # Komponen panel guru
│   │   └── Public/           # Komponen halaman publik
│   ├── Models/               # Eloquent models
│   └── Support/              # Helper (ColorPalette, dll.)
├── database/
│   ├── migrations/           # Skema database
│   └── seeders/              # Data contoh
├── resources/
│   ├── css/                  # Stylesheet
│   ├── js/                   # JavaScript
│   └── views/
│       ├── components/       # Blade components
│       ├── layouts/          # Layout templates
│       ├── livewire/         # Livewire views
│       └── pages/            # Halaman publik
├── routes/
│   └── web.php               # Definisi route
└── screenshots/              # Screenshot aplikasi
```

---

## 🌐 Halaman Publik

| Halaman | URL |
|---|---|
| Beranda | `/` |
| Tentang Kami | `/tentang-kami` |
| Program | `/program` |
| Berita | `/berita` |
| Tim Guru | `/tim-guru` |
| Kontak | `/kontak` |
| PPDB | `/ppdb` |

---

## 🏗️ Build untuk Produksi

```bash
npm run build
```

### ✅ Checklist Keamanan Produksi

Sebelum go-live, pastikan langkah berikut sudah dijalankan:

1. **Environment & debug** — di `.env`:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://domain-sekolah-anda.sch.id
   ```
   `APP_DEBUG=true` di produksi membocorkan stack trace, env, dan query — wajib `false`.

2. **Akun admin** — seeder TIDAK membuat akun di produksi. Buat admin:
   ```bash
   php artisan admin:create
   ```
   Jangan pernah memakai password demo (`password`).

3. **Cache konfigurasi** (jalankan ulang setiap kali deploy / ubah `.env`):
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
   Untuk membersihkan: `php artisan optimize:clear`.

4. **HTTPS & sesi** — gunakan HTTPS, dan pertimbangkan `SESSION_ENCRYPT=true`.

5. **Queue** — pastikan worker berjalan (`php artisan queue:work`) agar email & job AI terproses.

6. **Privasi** — set `GEOIP_ENABLED=false` jika tidak ingin mengirim IP pengunjung ke pihak ketiga.

7. **Secret** — jangan commit `.env` / API key ke repo.

---

## 📄 Lisensi

Project ini menggunakan lisensi [MIT](https://opensource.org/licenses/MIT).

---

<p align="center">
  Dibuat dengan ❤️ menggunakan Laravel, Livewire & TailwindCSS
</p>
