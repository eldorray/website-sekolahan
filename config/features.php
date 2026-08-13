<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Fitur per-unit sekolah
    |--------------------------------------------------------------------------
    |
    | Satu codebase dipakai beberapa deployment (MI, SMP). Fitur yang hanya
    | relevan untuk sebagian unit dinyalakan lewat .env masing-masing server,
    | bukan lewat branch terpisah. Default mati: unit baru tidak ikut kebagian
    | fitur sampai sengaja dinyalakan.
    |
    */

    'adiwiyata' => (bool) env('ADIWIYATA_ENABLED', false),

    /*
    | PIN bersama untuk menyimpan penilaian Adiwiyata. Kosong = halaman jadi
    | baca-saja, tidak ada yang bisa mengubah. Bagikan hanya ke tim Adiwiyata.
    */
    'adiwiyata_pin' => (string) env('ADIWIYATA_PIN', ''),

    'ypdh_ai' => (bool) env('YPDH_AI_ENABLED', false),

    /*
    | PIN bersama untuk membuka YPDH AI. Kosong = tidak ada yang bisa masuk.
    | Tiap panggilan AI keluar biaya, jadi jangan dibagikan sembarangan.
    */
    'ypdh_ai_pin' => (string) env('YPDH_AI_PIN', ''),

];
