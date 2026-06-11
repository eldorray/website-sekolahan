# Prompt: Perbaikan Keamanan & Fitur — website-sekolahan

Kamu adalah AI coding agent. Repo target: **Laravel 13 + Livewire 4 + TailwindCSS 4** (template website sekolah). Kerjakan tugas di bawah ini **urut prioritas**, satu per satu, dengan commit terpisah per item dan jelaskan perubahan yang kamu buat. Jangan ubah perilaku fitur lain.

---

## 🔴 Kerentanan (WAJIB diperbaiki dulu)

1. **Stored XSS lewat konten kaya.** `news-show.blade.php` dan `program-show.blade.php` merender `{!! $news->content !!}` / `{!! $program->description !!}` tanpa sanitasi (sumber: editor Tiptap + generator AI). Pasang sanitizer HTML **saat menyimpan** (mis. `mews/purifier`), whitelist tag aman (`p, strong, em, ul, ol, li, a, img, h2-h4, blockquote`). Berlaku untuk `Admin/News`, `Guru/MyNews`, `Admin/Programs`, dan output `AiWriter`.
2. **Login tanpa rate limiting.** `app/Livewire/Auth/Login.php` memanggil `Auth::attempt` tanpa throttle. Tambah `RateLimiter` (mis. 5 percobaan per kombinasi email+IP / menit) + pesan lockout.
3. **Kredensial default terpublikasi.** README & seeder memuat `admin@school.id / password`. Hapus seeding admin default di produksi; buat artisan command interaktif untuk membuat admin, dan/atau paksa ganti password saat login pertama.
4. **Form publik tanpa anti-spam.** `PpdbForm` & `ContactForm` tanpa captcha/honeypot/throttle. Tambah honeypot (`spatie/laravel-honeypot`) + rate limit per IP.
5. **Hardening produksi belum ada.** `.env.example` set `APP_DEBUG=true` dan README tak punya checklist. Tambah ke README: `APP_ENV=production`, `APP_DEBUG=false`, lalu `php artisan config:cache route:cache view:cache`.

## 🟡 Masalah lebih kecil

6. **Race condition nomor PPDB.** `PpdbRegistration::count() + 1` bisa duplikat saat submit bersamaan. Ganti dengan kolom auto-increment khusus atau `DB::transaction` + lock baris.
7. **Mass assignment rapuh.** Model `News` pakai `$guarded = []`. Ganti ke `$fillable` eksplisit (cek juga model lain).
8. **Privasi GeoIp.** `GeoIp` mengirim IP pengunjung ke pihak ketiga `ipapi.co`. Bukan SSRF (IP divalidasi), tapi tambahkan opsi nonaktif + catat di kebijakan privasi.

## ⚙️ Fitur yang belum ada (tambahkan setelah keamanan beres)

9. **Notifikasi email:** konfirmasi ke pendaftar PPDB + alert ke admin untuk PPDB/pesan kontak baru (`MAIL_MAILER` masih `log`).
10. **Lupa password / reset password** untuk admin & guru.
11. **Ekspor data PPDB** ke Excel/PDF untuk panitia.
12. **Queue untuk panggilan AI:** `AiWriter` sinkron (timeout 60s) memblokir request — pindahkan ke job antrian.
13. **Audit log & soft delete:** jejak perubahan/penghapusan + pemulihan data terhapus.
14. **SEO:** `sitemap.xml` + meta/OG tag per halaman.
15. **2FA opsional** untuk panel admin.

---

## ✅ Urutan eksekusi
1 → 2 → 3 → 4 → 5 → 6 → 7 → 8 → lalu fitur 9–15.

## Aturan kerja
- Jangan menyentuh fungsi lain di luar item yang sedang dikerjakan.
- Tulis test (PestPHP) untuk setiap perbaikan keamanan (no. 1–4).
- Untuk tiap item: jelaskan **apa**, **kenapa**, **file yang diubah**, dan cara verifikasinya.
- Jangan pernah menyimpan secret/API key ke repo.

eksekusi semua yang ada diatas !
