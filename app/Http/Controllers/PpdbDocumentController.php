<?php

namespace App\Http\Controllers;

use App\Models\PpdbRegistration;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PpdbDocumentController extends Controller
{
    /** Allowed document types mapped to their model column. */
    protected const TYPES = [
        'kk' => 'kk_file',
        'akte' => 'birth_certificate_file',
    ];

    public function __invoke(PpdbRegistration $registration, string $type): StreamedResponse
    {
        abort_unless(isset(self::TYPES[$type]), 404);

        $path = $registration->{self::TYPES[$type]};

        abort_if(! $path || ! Storage::disk('local')->exists($path), 404);

        $filename = $registration->registration_number.'-'.$type.'.'.pathinfo($path, PATHINFO_EXTENSION);

        return Storage::disk('local')->download($path, $filename);
    }
}
