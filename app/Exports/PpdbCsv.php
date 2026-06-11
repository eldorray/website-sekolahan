<?php

namespace App\Exports;

use App\Models\PpdbRegistration;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PpdbCsv
{
    public const HEADER = [
        'No. Pendaftaran',
        'Nama Lengkap',
        'Panggilan',
        'Jenis Kelamin',
        'Tempat Lahir',
        'Tanggal Lahir',
        'Asal Sekolah',
        'Alamat',
        'Nama Ayah',
        'Nama Ibu',
        'Telepon Ortu',
        'Email Ortu',
        'Jenjang',
        'Status',
        'Tanggal Daftar',
    ];

    /**
     * Map a registration to a CSV row in the same order as HEADER.
     */
    public static function row(PpdbRegistration $r): array
    {
        return [
            $r->registration_number,
            $r->full_name,
            $r->nickname,
            $r->gender === 'P' ? 'Perempuan' : 'Laki-laki',
            $r->birthplace,
            optional($r->birthdate)->format('Y-m-d'),
            $r->previous_school,
            $r->address,
            $r->father_name,
            $r->mother_name,
            $r->parent_phone,
            $r->parent_email,
            $r->grade_target,
            $r->status,
            optional($r->created_at)->format('Y-m-d H:i'),
        ];
    }

    /**
     * Stream the given query as a CSV download (chunked, memory-safe).
     */
    public static function download(Builder $query, string $filename): StreamedResponse
    {
        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            // UTF-8 BOM so Excel renders accents correctly.
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, self::HEADER);

            $query->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $r) {
                    fputcsv($out, self::row($r));
                }
            });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
