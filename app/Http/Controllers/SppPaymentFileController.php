<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\SppPayment;
use App\Models\User;
use App\Services\SppPayments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SppPaymentFileController extends Controller
{
    public function qris(Request $request, SppPayments $payments): StreamedResponse
    {
        $this->actor($request);

        return $this->publicQris($payments);
    }

    public function publicQris(SppPayments $payments): StreamedResponse
    {
        $path = $payments->qrisPath();
        abort_unless($path !== null, 404);

        return $this->image($path, 'qris-sekolah');
    }

    public function proof(Request $request, SppPayment $payment): StreamedResponse
    {
        $actor = $this->actor($request);
        abort_unless($actor->isAdmin() || $payment->student?->guardians()->where('users.id', $actor->id)->exists(), 404);
        $path = $payment->proof_path;
        abort_unless(is_string($path) && preg_match('~\Aspp/proofs/[a-zA-Z0-9_-]+\.(?:png|jpe?g|webp)\z~', $path)
            && Storage::disk('local')->exists($path), 404);

        return $this->image($path, 'bukti-'.$payment->order_id);
    }

    private function actor(Request $request): User
    {
        $actor = $request->user()?->fresh();
        abort_unless($actor && $actor->is_active && ($actor->isAdmin() || $actor->isOrangTua()), 403);

        return $actor;
    }

    private function image(string $path, string $name): StreamedResponse
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $type = match ($extension) {
            'jpg', 'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            default => 'image/png',
        };

        return Storage::disk('local')->response($path, $name.'.'.$extension, [
            'Content-Type' => $type,
            'Cache-Control' => 'private, no-store, max-age=0',
            'X-Content-Type-Options' => 'nosniff',
            'X-Robots-Tag' => 'noindex, nofollow',
        ]);
    }
}
