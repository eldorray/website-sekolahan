# Pembayaran sekolah — QRIS statis

Orang tua membayar tanpa login dan tanpa menunggu admin menerbitkan tagihan. Pembayaran memakai QRIS sekolah, dengan pilihan SPP per bulan atau Lainnya. Bukti dikonfirmasi orang tua dan diperiksa admin; tidak ada payment gateway, webhook, pemeriksaan saldo, atau pelunasan otomatis.

## Persiapan admin

1. Di Siswa, pilih **Sinkron dari Data Induk**. Isi URL server, pilih MI/SMP, dan tarif siswa baru. Ulangi untuk unit lain jika diperlukan.
2. Atur tarif serta potongan siswa melalui Edit. Nominal SPP memakai tarif bulanan dikurangi potongan. Tarif nol/potongan penuh tidak dapat ditagih melalui pembayaran SPP; hubungi admin jika tarif belum benar. Akun wali opsional untuk mengakses arsip portal.
3. Di **Pembayaran Siswa → Atur QRIS**, unggah QRIS resmi sekolah (PNG/JPG/WebP, maksimal 2 MB). Pastikan gambar dapat dipindai dan nama merchant/rekening benar.
4. Admin cukup memeriksa pembayaran masuk. Tidak ada penerbitan tagihan pada alur publik, dan jadwal penerbitan otomatis telah dinonaktifkan.

## Sinkronisasi siswa dari Data Induk

Integrasi memakai kontrak API proyek `data-induk`: `GET /api/siswa-mi/all` atau `GET /api/siswa-smp/all`, dengan respons `success`, `data`, dan `total`. Semua permintaan dari website ini hanya membaca sumber. URL dan pilihan unit disimpan bersama perubahan siswa setelah sinkron berhasil. Setelah ada siswa terhubung, URL server dikunci agar ID dari database lain tidak menimpa identitas dan riwayat siswa; pemindahan alamat server memerlukan pemeriksaan operator. Kedua endpoint pada proyek sumber saat ini tidak memerlukan token.

- NISN dipetakan ke kolom identitas pembayaran (`nis`), `nama_lengkap` ke nama, `tingkat_rombel` ke kelas, dan status Aktif ke siswa aktif. Status Tidak Aktif/Lulus/Pindah/Keluar menonaktifkan siswa lokal yang cocok. Kelas kosong diberi label Belum diatur.
- ID sumber disimpan bersama unit MI/SMP agar ID yang sama pada dua tabel berbeda tidak tertukar, dan koreksi NISN tetap memperbarui siswa yang sama. Siswa lokal tanpa tautan hanya dicocokkan jika NISN serta nama cocok. Perpindahan MI ke SMP (atau sebaliknya) memakai siswa lokal yang sama hanya jika NISN dan nama cocok, siswa di unit lama sudah nonaktif, dan data unit baru aktif. Tarif, wali, dan histori tetap; sesuaikan tarif secara manual jika berubah karena perpindahan unit. Sinkron unit lama yang nonaktif tidak menonaktifkan kembali siswa aktif di unit baru. Bentrok identitas lain atau siswa aktif di dua unit dilewati.
- NISN kosong/ganda, data tidak valid, dan bentrok identitas dilaporkan sebagai jumlah yang dilewati. Perbaiki sumber lalu sinkron ulang. Nama/kelas yang melebihi batas lokal tidak dipotong diam-diam.
- Tarif, potongan, awal penagihan, tautan wali, serta tagihan dan pembayaran yang sudah tersimpan tidak ditimpa. Tarif baru di dialog hanya diterapkan ke siswa baru. Sinkronisasi tidak menerbitkan tagihan, menghapus siswa, ataupun otomatis menonaktifkan siswa yang hilang dari respons sumber.
- Respons gagal/tidak lengkap, pengalihan URL, atau kegagalan koneksi tidak mengubah data siswa. Perubahan lokal diproses dalam transaksi. Sinkron ulang dengan data yang sama tidak menggandakan siswa.
- Hanya identitas, kelas, status, dan ID sumber yang disimpan; NIK, alamat, telepon, tanggal lahir, serta data keluarga dari respons API tidak disalin ke website pembayaran.

Migrasi `2026_10_19_000003_add_data_induk_identity_to_students` menambahkan identitas sumber tanpa mengubah data keuangan yang ada.

## Alur orang tua

1. Buka **Bayar SPP/Lainnya** atau `/bayar-spp`.
2. Gunakan satu kolom pencarian: isi **NISN atau nama lengkap**, cukup salah satu. Nama dicocokkan penuh tanpa membedakan huruf besar/kecil dan spasi berlebih; NISN harus persis termasuk nol di awal. Pilih siswa dari hasil pencarian berdasarkan nama, kelas, unit, dan empat digit terakhir NISN. Pencarian dibatasi 10 kali per menit per IP dan tidak menerima pencarian nama sebagian.
3. Pilih jenis pembayaran. **SPP** menampilkan bulan/tahun dan menggunakan nominal tarif siswa dari server. **Lainnya** menampilkan keterangan (misalnya bayar seragam) dan nominal rupiah bulat lebih dari nol, maksimal Rp 100.000.000.
4. Klik **Bayar** untuk melihat QRIS dan nominal tepat. Bayar melalui aplikasi bank/dompet digital dan periksa nama penerima. QRIS dapat diunduh untuk dipindai dari galeri jika aplikasi mendukungnya.
5. Unggah bukti JPG/PNG/WebP maksimal 5 MB, lalu klik **Konfirmasi pembayaran**. Status Menunggu verifikasi ditampilkan dalam riwayat siswa; jangan membayar ulang selama pemeriksaan.
6. Riwayat menampilkan jenis, bulan SPP atau keterangan pembayaran lainnya, nominal, waktu konfirmasi, serta status. Berhasil berarti admin telah menyetujui. Ditolak dapat dikirim ulang setelah bukti diperbaiki; konfirmasi sebelumnya tetap tersimpan.

Riwayat publik tidak menampilkan file bukti, akun pengirim, atau catatan pemeriksaan admin. Bukti tetap privat. Nama lengkap atau NISN cukup untuk memilih siswa dan melihat ringkasan riwayat sesuai alur publik ini. Arsip portal `/orang-tua/spp` beserta bukti/kuitansi tagihan lama tetap memerlukan akun wali yang terhubung.

## Pemeriksaan admin

Halaman **Pembayaran Siswa** menampilkan antrean seluruh bulan dan riwayat pembayaran yang dapat dicari per siswa, bulan transaksi, dan status. Antrean tidak disembunyikan oleh filter riwayat. Buka Periksa untuk melihat siswa, jenis pembayaran, periode SPP bila ada, nominal, keterangan, waktu konfirmasi, dan bukti privat.

- Setujui setelah mencocokkan bukti, nominal, dan dana masuk pada mutasi bank/merchant. Identitas dan waktu pemeriksa dicatat. Pembayaran langsung tidak membuat tagihan baru.
- Tolak dengan alasan jika bukti/penerimaan tidak cocok. Riwayat penolakan tetap tersimpan; orang tua dapat mengirim bukti yang diperbaiki.
- SPP yang menunggu verifikasi atau sudah berhasil diblokir per siswa/bulan. Penolakan membuka kesempatan konfirmasi baru. UUID permintaan mencegah pengiriman berulang membuat duplikat, termasuk untuk pembayaran Lainnya; pembayaran Lainnya yang memang berbeda boleh dibuat melalui formulir baru.
- Nominal SPP diverifikasi ulang saat konfirmasi; perubahan tarif setelah QRIS disiapkan meminta orang tua memeriksa rincian kembali. Identitas siswa dan data pembayaran yang disiapkan dikunci dari perubahan klien.
- Bukti disimpan pada disk lokal privat; hanya admin aktif atau wali aktif yang masih terhubung dengan siswa yang dapat mengaksesnya.

## Migrasi dan arsip

Migrasi `2026_10_19_000004_support_direct_student_payments` menambahkan siswa, kategori, dan periode pada pembayaran, mengisi relasi dari arsip tagihan lama, dan memungkinkan pembayaran tanpa tagihan. Identitas, nominal, status, bukti, serta label sandbox arsip tetap tersimpan. Rollback diblokir jika sudah ada pembayaran langsung agar riwayat tidak hilang.

SPP dengan tagihan lama tetap memakai nominal arsip dan menghormati pembayaran lama agar bulan yang sudah dibayar tidak dibayar lagi. Persetujuan pembayaran untuk tagihan lama tetap memperbarui status arsip tersebut. Transaksi sandbox tetap dilabeli simulasi dan tidak dihitung sebagai penerimaan nyata pada ringkasan admin.

Pesanan gateway lama yang belum selesai tetap memerlukan pemeriksaan operator. `php artisan spp:release-legacy ID --admin=ID_ADMIN --reason="Hasil pemeriksaan"` hanya melepas pesanan lama yang belum dibayar, mempertahankan arsip, dan tidak melunasi tagihan. Produksi memerlukan `--confirmed-closed-unpaid` setelah operator memastikan pesanan ditutup tanpa dana masuk. Pembayaran langsung/QRIS statis tidak dapat dilepas melalui command ini.

Command `spp:generate` hanya dipertahankan untuk kompatibilitas arsip/operator dan tidak dijadwalkan. Alur baru tidak memerlukannya. Setelah backup, jalankan migrasi deployment dengan `php artisan migrate --force`; jangan gunakan migrate:fresh/db:wipe pada database kerja/produksi.

## Verifikasi

`php artisan test --compact`

`npm run build`

Tes memakai sqlite terisolasi dan bukti/QRIS uji, bukan transfer uang nyata. QRIS sekolah yang sah harus dipasang sebelum menerima pembayaran.
