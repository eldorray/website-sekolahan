<div>
    <div class="grid sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        @foreach ($stats as $s)
            <div class="card">
                <div class="text-xs text-slate-500">{{ $s['label'] }}</div>
                <div class="text-3xl font-bold text-slate-900 mt-2">{{ $s['value'] }}</div>
                <div class="text-xs text-brand-600 mt-1">{{ $s['sub'] }}</div>
            </div>
        @endforeach
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        <div class="card">
            <h3 class="font-bold text-slate-900 mb-3">Pendaftaran PPDB Terbaru</h3>
            <div class="divide-y">
                @forelse ($recentPpdb as $r)
                    <div class="py-2.5 flex justify-between items-center">
                        <div>
                            <div class="font-medium text-sm text-slate-900">{{ $r->full_name }}</div>
                            <div class="text-xs text-slate-500">{{ $r->grade_target }} • {{ $r->registration_number }}</div>
                        </div>
                        <span class="text-[10px] uppercase font-bold rounded-full px-2 py-0.5
                            @class(['bg-amber-100 text-amber-700' => $r->status === 'pending', 'bg-brand-100 text-brand-700' => $r->status === 'accepted', 'bg-red-100 text-red-700' => $r->status === 'rejected'])">{{ $r->status }}</span>
                    </div>
                @empty
                    <div class="text-sm text-slate-500 py-4">Belum ada pendaftaran.</div>
                @endforelse
            </div>
        </div>

        <div class="card">
            <h3 class="font-bold text-slate-900 mb-3">Pesan Terbaru</h3>
            <div class="divide-y">
                @forelse ($recentMessages as $m)
                    <div class="py-2.5">
                        <div class="font-medium text-sm text-slate-900">{{ $m->subject }}</div>
                        <div class="text-xs text-slate-500">{{ $m->name }} • {{ $m->created_at->diffForHumans() }}</div>
                    </div>
                @empty
                    <div class="text-sm text-slate-500 py-4">Belum ada pesan.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
