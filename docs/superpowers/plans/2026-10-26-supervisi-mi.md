# Supervisi MI

Rancangan disetujui melalui percakapan: jadwal, praobservasi, instrumen MI usulan 24 indikator, catatan bukti, refleksi, tindak lanjut dan laporan. AI menyusun draf refleksi/tindak lanjut/narasi laporan berdasarkan data tersimpan; angka dihitung aplikasi. Instrumen merupakan adaptasi internal, bukan instrumen resmi tervalidasi.

Implementasi langsung di checkout aktif:
1. Uji akses kepala madrasah/guru, isolasi data, skor dan respons AI terlebih dahulu.
2. Tambah tabel/model supervisi dengan snapshot instrumen, relasi guru/supervisor, konteks dan hasil AI teks biasa.
3. Tambah Livewire supervisi bersama, pemeriksaan peran di setiap request, pembatasan guru pada data sendiri dan refleksi.
4. Tambah peran kepala_madrasah pada pengelolaan pengguna, tujuan login dan navigasi. Admin membantu pengelolaan supervisi.
5. Gunakan konfigurasi AI yang ada; batasi payload, validasi output, tangani gagal tanpa menghapus hasil lama, batasi frekuensi.
6. Cetak laporan per supervisi serta rekap periode melalui browser. Escape seluruh teks termasuk hasil AI.
7. Jalankan pengujian fitur, regresi, Pint dan build aset. Jangan panggil provider nyata dalam pengujian.

Data supervisi tidak dipublikasikan. Guru tidak dapat mengubah nilai/jadwal/tindak lanjut kepala. Tidak ada penghapusan supervisi atau dependensi tambahan.
