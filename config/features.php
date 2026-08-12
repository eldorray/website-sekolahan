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

];
