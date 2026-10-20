# SPP — kontrak QRIS statis

Alur aktif: Pembayaran tanpa login → cari NISN atau nama lengkap → pilih siswa → SPP beserta bulan atau Lainnya beserta keterangan dan nominal → Bayar → QRIS → bukti dan konfirmasi → verifikasi admin → riwayat per siswa.

- Laravel 13 + Livewire 4 + Blade; gunakan domain siswa, wali, tagihan yang sudah ada. Penambahan siswa menggunakan sinkron API Data Induk MI/SMP melalui halaman Siswa; identitas sumber stabil, NISN, nama, kelas, dan status disalin tanpa menimpa pengaturan SPP atau histori keuangan.
- Gambar QRIS diunggah admin dan tersedia melalui route publik pembayaran. Bukti disimpan privat pada disk lokal dan hanya dilayani melalui route terotorisasi.
- Konfirmasi berstatus pending_review, approved, rejected. Pembayaran langsung tidak membuat tagihan; admin cukup memverifikasi dan melihat riwayat. Arsip yang sudah terkait tagihan tetap kompatibel. Jadwal penerbitan tagihan dinonaktifkan.
- Rekam nominal server-side, keterangan, bukti, waktu konfirmasi, pemeriksa, waktu pemeriksaan, serta alasan keputusan; jangan izinkan pelunasan dari input klien.
- Pertahankan satu SPP aktif/berhasil per siswa/bulan, UUID idempotensi untuk setiap pengiriman, dan histori penolakan; periksa kecocokan siswa aktif pada setiap aksi publik, serta kepemilikan dan role aktif pada portal yang memakai login.
- Akun wali bersifat opsional untuk pembayaran; konfirmasi publik tidak membuat akun dan dicatat dengan user_id kosong. Ringkasan riwayat tersedia setelah memilih siswa; file bukti dan arsip kuitansi tetap terotorisasi.
- Hapus integrasi, kredensial konfigurasi, webhook/CSRF exception, dan scheduler rekonsiliasi gateway. Pertahankan arsip lama tanpa mengubah simulasi menjadi uang nyata.
- Uji akses antarwali, validasi unggahan, alur modal, persetujuan/penolakan, idempotensi, kuitansi, antrean lintas bulan, dan build.
- Perubahan skema lokal memakai migrasi tambahan setelah pencadangan database. Tanpa mengubah .env, memigrasikan database produksi, commit, push, atau deploy tanpa instruksi.

Panduan operasional lengkap: ../spp-payments.md.
