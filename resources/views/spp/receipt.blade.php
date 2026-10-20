<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Kuitansi SPP — {{ $payment->order_id }}</title>
    <style>
        body { font-family: system-ui, sans-serif; color: #182b20; margin: 0; background: #f5f7f5; }
        main { max-width: 640px; margin: 40px auto; padding: 32px; background: white; border: 1px solid #dae3db; border-radius: 20px; }
        h1 { font-size: 26px; margin-bottom: 8px; } p { line-height: 1.6; } .muted { color: #59685d; font-size: 14px; }
        .warning { padding: 16px; background: #fff5da; border: 1px solid #eac67b; border-radius: 12px; }
        dl { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin: 28px 0; } dt { color: #59685d; } dd { margin: 0; text-align: right; overflow-wrap: anywhere; }
        .amount { font-size: 28px; font-weight: 750; border-top: 1px solid #dae3db; padding-top: 20px; }
        button { min-height: 44px; border: 0; border-radius: 10px; padding: 12px 20px; background: #245c3c; color: white; cursor: pointer; }
        @media (max-width: 680px) { main { margin: 16px; padding: 24px; } }
        @media print { body { background: white; } main { border: 0; margin: 0 auto; } .actions { display: none; } }
    </style>
</head>
<body>
<main>
    <p class="muted">{{ \App\Models\Setting::get('school_name', 'Sekolah') }}</p>
    <h1>Kuitansi pembayaran SPP</h1>
    @if ($payment->environment === 'sandbox')
        <p class="warning"><strong>SIMULASI SANDBOX</strong><br>Bukti pengujian sistem, bukan bukti penerimaan uang atau pembayaran SPP sungguhan.</p>
    @endif
    <dl>
        <dt>Nomor transaksi</dt><dd>{{ $payment->order_id }}</dd>
        <dt>Nama siswa</dt><dd>{{ $invoice->student->name }}</dd>
        <dt>NIS</dt><dd>{{ $invoice->student->nis }}</dd>
        <dt>Periode SPP</dt><dd>{{ $invoice->period->translatedFormat('F Y') }}</dd>
        <dt>Tarif</dt><dd>Rp {{ number_format($invoice->base_amount, 0, ',', '.') }}</dd>
        <dt>Potongan / beasiswa</dt><dd>Rp {{ number_format($invoice->discount_amount, 0, ',', '.') }}</dd>
        <dt>Metode</dt><dd>{{ $payment->payment_type === 'qris_static' ? 'QRIS statis' : strtoupper($payment->payment_type ?? 'Pembayaran lama') }}</dd>
        @if ($payment->description)<dt>Keterangan</dt><dd>{{ $payment->description }}</dd>@endif
        @if ($payment->reviewer)<dt>Diverifikasi oleh</dt><dd>{{ $payment->reviewer->name }}</dd>@endif
        <dt>Waktu verifikasi</dt><dd>{{ $invoice->paid_at?->format('d/m/Y H:i') }}</dd>
        <dt>Status</dt><dd>LUNAS{{ $payment->environment === 'sandbox' ? ' (SIMULASI)' : '' }}</dd>
    </dl>
    <p class="amount">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</p>
    <p class="muted">{{ $payment->payment_type === 'qris_static' ? 'Pembayaran QRIS statis telah diverifikasi admin sekolah berdasarkan bukti pembayaran dan mutasi penerimaan.' : 'Kuitansi berdasarkan arsip pembayaran yang telah terverifikasi.' }}</p>
    <div class="actions"><button type="button" onclick="window.print()">Cetak kuitansi</button></div>
</main>
</body>
</html>
