<?php

namespace App\Http\Controllers;

use App\Models\SppInvoice;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SppReceiptController extends Controller
{
    public function __invoke(Request $request, SppInvoice $invoice): Response
    {
        $user = $request->user()?->fresh();
        abort_unless($user && $user->is_active && ($user->isAdmin() || $user->isOrangTua()), 403);
        abort_unless($user->isAdmin() || $invoice->student->guardians()->where('users.id', $user->id)->exists(), 404);
        abort_unless($invoice->status === 'paid', 404);

        $payment = $invoice->payments()->whereNotNull('paid_at')->oldest('paid_at')->firstOrFail();

        return response()->view('spp.receipt', compact('invoice', 'payment'))
            ->header('Cache-Control', 'private, no-store, max-age=0')
            ->header('X-Robots-Tag', 'noindex, nofollow');
    }
}
