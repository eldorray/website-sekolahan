<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ isset($record) ? 'Laporan Supervisi #'.$record->id : 'Rekap Supervisi' }}</title>
    <style>
        body{font:14px/1.5 Arial,sans-serif;color:#17212b;max-width:1100px;margin:32px auto;padding:0 20px}h1{font-size:24px}h2{font-size:18px;margin-top:24px}table{border-collapse:collapse;width:100%;margin:12px 0}th,td{border:1px solid #b8c1ca;padding:8px;text-align:left;vertical-align:top}th{background:#f1f5f9}.prose{white-space:pre-wrap;overflow-wrap:anywhere}small{color:#475569}.actions{display:flex;gap:16px;align-items:center}button{padding:10px 16px;cursor:pointer}tr{break-inside:avoid}@page{size:A4;margin:14mm}@media print{body{margin:0;padding:0;font-size:10pt;max-width:none}.actions{display:none}thead{display:table-header-group}h2{break-after:avoid}a{color:inherit;text-decoration:none}}
    </style>
</head>
<body>
    <div class="actions"><button type="button" onclick="window.print()">Cetak / Simpan PDF</button><a href="{{ route('supervision') }}">Kembali ke supervisi</a><small>Gunakan A4; orientasi lanskap untuk rekap lebar.</small></div>
    <h1>{{ \App\Models\Setting::get('school_name', 'Madrasah Ibtidaiyah') }}</h1>
    @isset($record)
        @php($summary = $record->summary())
        <h2>Laporan Supervisi Akademik MI #{{ $record->id }}</h2>
        <table><tbody>
            <tr><th>Guru</th><td>{{ $record->teacher->name }}</td><th>Supervisor</th><td>{{ $record->supervisor->name }}</td></tr>
            <tr><th>Kelas / mapel</th><td>{{ $record->class_name }} / {{ $record->subject }}</td><th>Jadwal</th><td>{{ $record->scheduled_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i').' WIB' }}</td></tr>
            <tr><th>Periode</th><td>{{ $record->school_year }} / Semester {{ $record->semester }}</td><th>Status</th><td>{{ \App\Models\Supervision::STATUSES[$record->status] }}</td></tr>
        </tbody></table>
        <p><small>Instrumen adaptasi internal {{ $record->instrument_version }}; belum merupakan instrumen resmi tervalidasi. Narasi dapat disusun dengan bantuan AI dan perlu diperiksa supervisor.</small></p>
        <h2>Rekap hasil berbasis nilai tersimpan</h2>
        <table><thead><tr><th>Aspek</th><th>Persentase</th><th>Dinilai</th><th>Belum teramati</th><th>Tidak relevan</th></tr></thead><tbody>
            @foreach(\App\Support\SupervisionInstrument::GROUPS as $group => $label)
            @php($part = $record->summary($group))
            <tr><td>{{ $label }}</td><td>{{ $part['percent'] !== null ? $part['percent'].'%' : '—' }}</td><td>{{ $part['rated'] }}</td><td>{{ $part['missing'] }}</td><td>{{ $part['irrelevant'] }}</td></tr>
            @endforeach
            <tr><th>Total</th><td>{{ $summary['percent'] !== null ? $summary['percent'].'%' : '—' }}</td><td>{{ $summary['rated'] }}</td><td>{{ $summary['missing'] }}</td><td>{{ $summary['irrelevant'] }}</td></tr>
        </tbody></table>
        <small>Persentase = jumlah nilai / (4 × jumlah indikator dinilai) × 100. Belum teramati dan tidak relevan tidak masuk penyebut. Hasil bersifat sementara bila bukti belum lengkap.</small>
        <h2>Konteks dan fokus pengembangan</h2><div class="prose">{{ $record->context ?: 'Belum diisi.' }}</div>
        <h2>Persiapan / praobservasi</h2><div class="prose">{{ $record->pre_observation ?: 'Belum diisi.' }}</div>
        <h2>Instrumen dan bukti observasi</h2>
        <table><thead><tr><th>Indikator</th><th>Penilaian</th><th>Bukti / alasan</th></tr></thead><tbody>
            @foreach($record->instrument as $code => $label)<tr><td>{{ $code }}. {{ $label }}</td><td>{{ \App\Support\SupervisionInstrument::RATINGS[$record->observations[$code]['rating'] ?? ''] ?? 'Belum diisi' }}</td><td class="prose">{{ $record->observations[$code]['evidence'] ?? '' }}</td></tr>@endforeach
        </tbody></table>
        <h2>Refleksi guru</h2><div class="prose">{{ $record->reflection ?: 'Belum tersedia.' }}</div>
        <h2>Rencana tindak lanjut</h2><div class="prose">{{ $record->follow_up ?: 'Belum tersedia.' }}</div>
        <p>Tenggat: {{ $record->follow_up_due?->format('d/m/Y') ?? 'Belum ditetapkan' }}</p>
        <h2>Bukti pelaksanaan dari guru</h2><div class="prose">{{ $record->follow_up_evidence ?: 'Belum tersedia.' }}</div>
        <h2>Verifikasi kepala madrasah</h2><div class="prose">{{ $record->verification ?: 'Belum diverifikasi.' }}</div>
        <h2>Narasi laporan</h2><div class="prose">{{ $record->report ?: 'Belum disusun.' }}</div>
        <table style="margin-top:36px;border:0"><tr><td style="border:0">Guru<br><br><br>{{ $record->teacher->name }}</td><td style="border:0">Supervisor<br><br><br>{{ $record->supervisor->name }}</td></tr></table>
    @else
        <h2>Rekap Supervisi Akademik MI</h2>
        <p>Tahun ajaran: {{ $filters['year'] ?? 'Semua' }} · Semester: {{ $filters['semester'] ?? 'Semua' }} · Jumlah: {{ $items->count() }}</p>
        <p>@foreach(\App\Models\Supervision::STATUSES as $value => $label){{ $label }}: {{ $items->where('status', $value)->count() }}{{ !$loop->last ? ' · ' : '' }}@endforeach</p>
        <table><thead><tr><th>Guru / supervisor</th><th>Jadwal / periode</th><th>Kelas / mapel</th><th>Status</th><th>Nilai / cakupan</th><th>Tindak lanjut / tenggat</th></tr></thead><tbody>
            @forelse($items as $item)
            @php($summary = $item->summary())
            <tr><td>{{ $item->teacher->name }}<br><small>{{ $item->supervisor->name }}</small></td><td>{{ $item->scheduled_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i').' WIB' }}<br>{{ $item->school_year }} / {{ $item->semester }}</td><td>{{ $item->class_name }} / {{ $item->subject }}</td><td>{{ \App\Models\Supervision::STATUSES[$item->status] }}</td><td>{{ $summary['percent'] !== null ? $summary['percent'].'%' : '—' }}<br>{{ $summary['rated'] }} dinilai; {{ $summary['missing'] }} belum teramati</td><td class="prose">{{ $item->follow_up ?: 'Belum tersedia' }}<br>{{ $item->follow_up_due?->format('d/m/Y') }}</td></tr>
            @empty<tr><td colspan="6">Belum ada supervisi pada periode ini.</td></tr>@endforelse
        </tbody></table>
        <small>Angka dihitung sistem dari bukti penilaian tersimpan. Narasi tindak lanjut dapat disusun dengan AI. Rekap tidak menunjukkan peringkat guru.</small>
    @endisset
    <p><small>Dicetak {{ now('Asia/Jakarta')->format('d/m/Y H:i').' WIB' }} · Dokumen internal madrasah.</small></p>
</body>
</html>
