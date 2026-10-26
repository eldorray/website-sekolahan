<?php

namespace App\Support;

class SupervisionInstrument
{
    public const VERSION = 'mi-internal-2026.1';

    public const GROUPS = ['A' => 'Perencanaan', 'B' => 'Observasi pembelajaran', 'C' => 'Asesmen', 'D' => 'Adab dan nilai keislaman'];

    public const RATINGS = ['1' => '1 — Belum terlaksana', '2' => '2 — Mulai terlaksana', '3' => '3 — Sesuai kebutuhan', '4' => '4 — Konsisten dan responsif', 'unobserved' => 'Belum teramati', 'na' => 'Tidak relevan'];

    public static function items(): array
    {
        return [
            'A1' => 'Tujuan sesuai kurikulum, kelas/fase, dan materi.',
            'A2' => 'Perencanaan mempertimbangkan kemampuan awal dan kebutuhan murid.',
            'A3' => 'Kegiatan belajar mengarah pada tujuan.',
            'A4' => 'Bahan, media, dan waktu sesuai usia dan kondisi kelas.',
            'A5' => 'Asesmen sesuai kemampuan yang ingin dicapai.',
            'A6' => 'Guru merencanakan bantuan bagi murid yang kesulitan.',
            'B1' => 'Guru membangun kesiapan dan menghubungkan pengetahuan sebelumnya.',
            'B2' => 'Murid memahami tugas dan tujuan dengan bahasa sesuai usia.',
            'B3' => 'Materi dan contoh yang disampaikan akurat.',
            'B4' => 'Murid mendapat kesempatan berpikir dan menjelaskan alasan.',
            'B5' => 'Kesempatan berpartisipasi menjangkau beragam murid.',
            'B6' => 'Guru memeriksa pemahaman selama pelajaran.',
            'B7' => 'Guru menyesuaikan bantuan berdasarkan kesulitan murid.',
            'B8' => 'Pengelolaan kelas tertib dan menghargai martabat anak.',
            'B9' => 'Media dan sumber sekitar membantu pemahaman.',
            'B10' => 'Murid mendapat kesempatan mencoba dan menerapkan materi.',
            'B11' => 'Umpan balik menunjukkan langkah perbaikan.',
            'B12' => 'Penutup menunjukkan yang sudah dan belum dipahami.',
            'C1' => 'Asesmen mengukur tujuan pembelajaran.',
            'C2' => 'Kriteria keberhasilan dapat dipahami murid.',
            'C3' => 'Guru menelaah kesalahan dan kebutuhan belajar.',
            'C4' => 'Hasil asesmen digunakan untuk bantuan atau pengayaan.',
            'D1' => 'Guru mencontohkan adab, kasih sayang, kejujuran, dan menghargai perbedaan.',
            'D2' => 'Nilai keislaman dihubungkan secara relevan dengan materi dan kehidupan anak.',
        ];
    }
}
