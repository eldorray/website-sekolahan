<div class="space-y-6">
    @php
        $isTeacher = auth()->user()->isGuru();
        $instrument = $selected?->instrument ?? \App\Support\SupervisionInstrument::items();
        $statusStyles = [
            'dijadwalkan' => 'bg-sky-50 text-sky-700 ring-sky-200 dark:bg-sky-500/10 dark:text-sky-300 dark:ring-sky-500/30',
            'observasi' => 'bg-indigo-50 text-indigo-700 ring-indigo-200 dark:bg-indigo-500/10 dark:text-indigo-300 dark:ring-indigo-500/30',
            'refleksi' => 'bg-amber-50 text-amber-800 ring-amber-200 dark:bg-amber-500/10 dark:text-amber-300 dark:ring-amber-500/30',
            'tindak_lanjut' => 'bg-violet-50 text-violet-700 ring-violet-200 dark:bg-violet-500/10 dark:text-violet-300 dark:ring-violet-500/30',
            'selesai' => 'bg-emerald-50 text-emerald-700 ring-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-300 dark:ring-emerald-500/30',
        ];
        $filled = fn (array $entry) => ! empty($entry['rating']) && $entry['rating'] !== 'unobserved';
        $ratedCount = collect($isTeacher ? ($selected?->observations ?? []) : $this->observations)->filter($filled)->count();
    @endphp
    <section class="card p-5 space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div><h2 class="text-xl font-bold">Supervisi Akademik MI</h2><p class="text-sm text-slate-600">Pendampingan berbasis bukti, refleksi, dan perbaikan pembelajaran.</p></div>
            @unless($isTeacher)<button type="button" wire:click="newRecord" class="btn-primary">Jadwalkan supervisi</button>@endunless
        </div>
        <div class="flex flex-wrap items-end gap-3">
            <label class="text-sm w-40">Tahun ajaran<select wire:model.live="yearFilter" class="select"><option value="">Semua tahun</option>@foreach($years as $year)<option value="{{ $year }}">{{ $year }}</option>@endforeach</select></label>
            <label class="text-sm w-36">Semester<select wire:model.live="semesterFilter" class="select"><option value="">Semua semester</option><option value="1">Ganjil</option><option value="2">Genap</option></select></label>
            <a class="btn-ghost" target="_blank" href="{{ route('supervision.recap', ['year' => $yearFilter, 'semester' => $semesterFilter]) }}">Cetak rekap</a>
        </div>
        <div class="flex flex-wrap gap-2" role="group" aria-label="Filter status">
            @foreach(\App\Models\Supervision::STATUSES as $value => $label)
                <button type="button" wire:click="filterByStatus('{{ $value }}')" wire:key="chip-{{ $value }}"
                    class="rounded-full px-3.5 py-1.5 text-sm font-medium ring-1 transition {{ $statusFilter === $value ? 'bg-brand-600 text-white ring-brand-600' : 'bg-slate-50 text-slate-700 ring-slate-200 hover:bg-slate-100 dark:bg-slate-800 dark:text-slate-300 dark:ring-slate-700 dark:hover:bg-slate-700' }}"
                    aria-pressed="{{ $statusFilter === $value ? 'true' : 'false' }}">
                    {{ $label }} <span class="tabular-nums {{ $statusFilter === $value ? 'text-white/80' : 'text-slate-400' }}">{{ $counts[$value] ?? 0 }}</span>
                </button>
            @endforeach
        </div>
        <div class="overflow-x-auto"><table class="w-full text-left text-sm"><thead><tr class="border-b text-slate-500"><th class="p-2">Guru / Jadwal</th><th class="p-2">Kelas / Mapel</th><th class="p-2">Status</th><th class="p-2">Hasil sementara</th><th class="p-2">Aksi</th></tr></thead><tbody>
        @forelse($items as $item)
            @php $summary = $item->summary(); @endphp
            <tr class="border-b hover:bg-slate-50/60 dark:hover:bg-slate-700/30"><td class="p-2 font-medium">{{ $item->teacher->name }}<br><span class="font-normal text-slate-500">{{ $item->scheduled_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i').' WIB' }}</span></td><td class="p-2">{{ $item->class_name }} / {{ $item->subject }}<br><span class="text-slate-500">{{ $item->school_year }} · {{ $item->semester }}</span></td><td class="p-2"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $statusStyles[$item->status] ?? '' }}">{{ \App\Models\Supervision::STATUSES[$item->status] }}</span>@if($item->follow_up_due && $item->follow_up_due->format('Y-m-d') < now('Asia/Jakarta')->toDateString() && $item->status !== 'selesai')<br><span class="text-xs font-semibold text-red-600">Tenggat lewat: {{ $item->follow_up_due->format('d/m/Y') }}</span>@endif</td>
            <td class="p-2 w-44">
                @if($summary['percent'] !== null)
                    <div class="flex items-center gap-2"><div class="h-1.5 flex-1 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-700"><div class="h-full rounded-full bg-brand-500" style="width: {{ min(100, $summary['percent']) }}%"></div></div><span class="text-sm font-semibold tabular-nums">{{ $summary['percent'] }}%</span></div>
                    <span class="text-xs text-slate-500">{{ $summary['rated'] }} dinilai · {{ $summary['missing'] }} belum</span>
                @else<span class="text-slate-500">Belum dinilai</span>@endif
            </td>
            <td class="p-2 whitespace-nowrap"><button type="button" wire:click="edit({{ $item->id }})" class="rounded-full bg-brand-50 px-3.5 py-1.5 text-xs font-semibold text-brand-700 ring-1 ring-brand-200 transition hover:bg-brand-100 dark:bg-slate-800 dark:text-brand-300 dark:ring-slate-700">Buka</button> <a target="_blank" class="ml-1 text-xs text-slate-500 underline hover:text-brand-700" href="{{ route('supervision.print', $item) }}">Cetak</a></td></tr>
        @empty<tr><td colspan="5" class="p-8 text-center">
            <p class="font-medium text-slate-700 dark:text-slate-300">Belum ada supervisi pada periode ini.</p>
            <p class="mt-1 text-sm text-slate-500">@unless($isTeacher)Jadwalkan supervisi pertama atau ubah filter di atas.@elseSupervisi Anda akan muncul di sini setelah dijadwalkan.@endunless</p>
        </td></tr>@endforelse
        </tbody></table></div>
        {{ $items->links() }}
    </section>
    @if(!$isTeacher || $selected)
    <section class="card p-5 space-y-5" wire:key="supervision-form-{{ $editingId ?? 'new' }}">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h2 class="text-lg font-bold">{{ $selected ? 'Detail supervisi #'.$selected->id : 'Jadwal baru' }}</h2>
            @if($selected)
                <ol class="flex flex-wrap items-center gap-1 text-xs" aria-label="Tahapan supervisi">
                    @foreach(\App\Models\Supervision::STATUSES as $value => $label)
                        @php $active = $value === $selected->status; @endphp
                        <li class="flex items-center gap-1">
                            <span class="rounded-full px-2.5 py-1 font-semibold {{ $active ? 'bg-brand-600 text-white' : 'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-300' }}">{{ $label }}</span>
                            @if(!$loop->last)<span aria-hidden="true" class="text-slate-300">→</span>@endif
                        </li>
                    @endforeach
                </ol>
            @endif
        </div>
        <p class="rounded-xl bg-amber-50 p-3 text-sm text-amber-900 dark:bg-amber-500/10 dark:text-amber-200">Instrumen adaptasi internal MI {{ $selected?->instrument_version ?? \App\Support\SupervisionInstrument::VERSION }}, belum merupakan instrumen resmi tervalidasi. Catat bukti tanpa nama atau data pribadi murid. Nilai dihitung sistem; hasil AI adalah draf yang perlu diperiksa.</p>
        @if($errors->any())<div role="alert" class="rounded-lg bg-red-50 p-3 text-red-800 dark:bg-red-500/10 dark:text-red-300"><p class="font-semibold">Periksa isian berikut:</p><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        <form wire:submit="{{ $isTeacher ? 'saveTeacher' : 'save' }}" class="space-y-6">
            @unless($isTeacher)
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <label class="label">Guru<select wire:model="teacherId" class="select" required @disabled($selected)><option value="">Pilih guru</option>@foreach($teachers as $teacher)<option value="{{ $teacher->id }}">{{ $teacher->name }}</option>@endforeach</select></label>
                <label class="label">Jadwal (WIB)<input type="datetime-local" wire:model="scheduledAt" class="input" required></label>
                <label class="label">Tahun ajaran<input wire:model="schoolYear" placeholder="2026/2027" class="input" required></label>
                <label class="label">Semester<select wire:model="semester" class="select"><option value="1">Ganjil</option><option value="2">Genap</option></select></label>
                <label class="label">Kelas / fase<input wire:model="className" class="input" placeholder="IV / Fase B" required></label>
                <label class="label">Mata pelajaran<input wire:model="subject" class="input" required></label>
                <label class="label">Status<select wire:model="status" class="select">@foreach(\App\Models\Supervision::STATUSES as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach</select></label>
            </div>
            <label class="label block">Konteks kelas dan fokus pengembangan<textarea wire:model="context" class="input mt-1" rows="3" placeholder="Tujuan, karakteristik kelas, fokus observasi, dan kebutuhan pendampingan"></textarea></label>
            @else
            <div class="rounded-xl bg-slate-50 p-4 text-sm dark:bg-slate-700/40">
                <p class="font-medium">{{ $selected->teacher->name }} · {{ $selected->class_name }} · {{ $selected->subject }} · {{ $selected->scheduled_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i').' WIB' }}</p>
                <p class="mt-1 whitespace-pre-line text-slate-600 dark:text-slate-300">{{ $selected->context }}</p>
            </div>
            @endunless
            <label class="label block">Persiapan / praobservasi guru<textarea wire:model="preObservation" class="input mt-1" rows="3" @disabled($isTeacher && $selected->status === 'selesai') placeholder="Tujuan belajar, kegiatan, asesmen, kendala, dan perangkat ajar yang akan ditelaah"></textarea></label>
            <div class="rounded-xl border border-slate-200 dark:border-slate-700">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 px-4 py-3 dark:border-slate-700">
                    <p class="font-semibold">Instrumen dan bukti pengamatan</p>
                    <div class="flex items-center gap-2 text-sm">
                        <div class="h-2 w-36 overflow-hidden rounded-full bg-slate-100 dark:bg-slate-700"><div class="h-full rounded-full bg-brand-500 transition-all" style="width: {{ round($ratedCount / max(1, count($instrument)) * 100) }}%"></div></div>
                        <span class="tabular-nums {{ $ratedCount === count($instrument) ? 'font-semibold text-emerald-600' : 'text-slate-500' }}">{{ $ratedCount }}/{{ count($instrument) }} dinilai</span>
                    </div>
                </div>
                <p class="px-4 pt-3 text-sm text-slate-600 dark:text-slate-400">1: belum terlaksana; 2: mulai tetapi terbatas; 3: sesuai tujuan dan kebutuhan mayoritas murid; 4: konsisten, responsif pada beragam kebutuhan, dengan bukti manfaat. Pilih <em>Belum teramati</em> bila indikator tidak sempat diamati — berbeda dari nilai 1. Bukti/alasan disarankan tetapi tidak wajib.</p>
                @foreach(\App\Support\SupervisionInstrument::GROUPS as $group => $heading)
                @php
                    $groupCodes = array_filter(array_keys($instrument), fn ($code) => str_starts_with($code, $group));
                    $groupFilled = collect($isTeacher ? ($selected?->observations ?? []) : $this->observations)
                        ->only($groupCodes)->filter($filled)->count();
                @endphp
                <details open class="group border-t border-slate-100 px-4 dark:border-slate-700/60">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-2 py-3 font-semibold">
                        <span class="flex items-center gap-2"><span class="text-slate-400 transition group-open:rotate-90" aria-hidden="true">▸</span>{{ $group }}. {{ $heading }}</span>
                        <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold tabular-nums {{ $groupFilled === count($groupCodes) ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300' : 'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-300' }}">{{ $groupFilled }}/{{ count($groupCodes) }}</span>
                    </summary>
                    <div class="pb-3">
                    @foreach($instrument as $code => $indicator)
                        @if(str_starts_with($code, $group))
                        <div wire:key="indicator-{{ $editingId }}-{{ $code }}" class="border-t border-slate-100 py-3 dark:border-slate-700/60">
                            <p class="mb-2 text-sm font-medium">{{ $code }}. {{ $indicator }}</p>
                            @if($isTeacher)
                                <p class="text-sm">{{ \App\Support\SupervisionInstrument::RATINGS[$selected->observations[$code]['rating'] ?? ''] ?? 'Belum diisi' }}</p><p class="whitespace-pre-line text-sm text-slate-600 dark:text-slate-400">{{ $selected->observations[$code]['evidence'] ?? '' }}</p>
                            @else
                            <div class="grid gap-2 md:grid-cols-[240px_1fr]">
                                <label class="text-sm">Penilaian {{ $code }}<select wire:model.live="observations.{{ $code }}.rating" class="select" {{ $selected && $selected->status === 'selesai' ? 'disabled' : '' }}><option value="">Pilih hasil pengamatan</option>@foreach(\App\Support\SupervisionInstrument::RATINGS as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach</select></label>
                                <div class="text-sm">
                                    @if(!empty($observations[$code]['rating']))
                                        <label class="sr-only" for="evidence-{{ $code }}">Bukti / alasan {{ $code }}</label>
                                        <textarea id="evidence-{{ $code }}" wire:model="observations.{{ $code }}.evidence" rows="2" class="input" placeholder="Waktu, tindakan guru, respons murid, hasil kerja, atau alasan tidak relevan (opsional)"></textarea>
                                    @else
                                        <p class="px-4 py-2.5 text-xs text-slate-400 dark:text-slate-500">Kolom bukti muncul setelah hasil pengamatan dipilih.</p>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif
                    @endforeach
                    </div>
                </details>
                @endforeach
            </div>
            @if($selected)
            <section class="space-y-3 border-t pt-4">
                <h3 class="font-bold">Refleksi guru</h3>
                @if($isTeacher)
                    <button type="button" wire:click="generateAi('reflection')" wire:loading.attr="disabled" class="btn-secondary" @disabled($selected->status === 'selesai')>Susun draf refleksi dengan AI</button>
                    <label class="label block">Refleksi yang telah Anda periksa<textarea wire:model="reflection" rows="5" class="input mt-1" @disabled($selected->status === 'selesai')></textarea></label>
                @else<p class="whitespace-pre-line text-sm">{{ $selected->reflection ?: 'Guru belum menyimpan refleksi.' }}</p>@endif
            </section>
            <section class="space-y-3 border-t pt-4">
                <h3 class="font-bold">Tindak lanjut dan verifikasi</h3>
                @unless($isTeacher)
                    <button type="button" wire:click="generateAi('follow_up')" wire:loading.attr="disabled" class="btn-secondary">Susun saran tindak lanjut dengan AI</button>
                    <label class="label block">Rencana tindak lanjut<textarea wire:model="followUp" rows="5" class="input mt-1" placeholder="Tindakan guru, dukungan kepala, indikator keberhasilan, dan penanggung jawab"></textarea></label>
                    <label class="label block">Tenggat<input type="date" wire:model="followUpDue" class="input"></label>
                    <p class="text-sm whitespace-pre-line">Bukti dari guru: {{ $selected->follow_up_evidence ?: 'Belum tersedia' }}</p>
                    <label class="label block">Hasil verifikasi kepala madrasah<textarea wire:model="verification" rows="3" class="input mt-1"></textarea></label>
                @else
                    <p class="whitespace-pre-line">{{ $selected->follow_up ?: 'Rencana belum tersedia.' }}</p>
                    <p class="text-sm">Tenggat: {{ $selected->follow_up_due?->format('d/m/Y') ?? 'Belum ditetapkan' }}</p>
                    <label class="label block">Bukti pelaksanaan tindak lanjut<textarea wire:model="followUpEvidence" rows="4" class="input mt-1" @disabled($selected->status === 'selesai') placeholder="Perubahan praktik, hasil murid, tanggal pelaksanaan, dan perangkat yang diperbaiki"></textarea></label>
                    <p class="text-sm whitespace-pre-line">Verifikasi kepala: {{ $selected->verification ?: 'Belum diverifikasi' }}</p>
                @endunless
            </section>
            <section class="space-y-3 border-t pt-4">
                <h3 class="font-bold">Narasi laporan AI</h3>
                @unless($isTeacher)
                    <button type="button" wire:click="generateAi('report')" wire:loading.attr="disabled" class="btn-secondary">Susun laporan dengan AI</button>
                    <label class="label block">Narasi yang telah diperiksa<textarea wire:model="report" rows="7" class="input mt-1"></textarea></label>
                @else<p class="whitespace-pre-line text-sm">{{ $selected->report ?: 'Laporan belum disusun.' }}</p>@endunless
                <p class="text-xs text-slate-500">AI memakai data yang sudah tersimpan. Simpan perubahan sebelum membuat draf. Permintaan mengirim konteks dan catatan ke penyedia AI yang dikonfigurasi; jangan memasukkan identitas murid.</p>
            </section>
            @endif
            <div wire:loading wire:target="generateAi" role="status" class="text-sm text-brand-700">AI sedang menyusun draf…</div>
            <div class="flex flex-wrap gap-3">
                @if(!$isTeacher || $selected->status !== 'selesai')<button type="submit" class="btn-primary" wire:loading.attr="disabled">{{ $isTeacher ? 'Simpan persiapan, refleksi & bukti' : 'Simpan supervisi' }}</button>@endif
                @if($selected)<a class="btn-secondary" target="_blank" href="{{ route('supervision.print', $selected) }}">Cetak laporan tersimpan / PDF</a>@endif
                @if($selected || $editingId)<button type="button" wire:click="closeForm" class="btn-ghost">Tutup</button>@endif
            </div>
        </form>
    </section>
    @endif
</div>
