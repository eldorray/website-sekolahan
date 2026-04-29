<div>
<div class="card">
    <h2 class="font-bold text-slate-900 mb-3">Permintaan Kunjungan</h2>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-left text-slate-500 border-b">
                <tr><th class="py-2">Nama</th><th>Kontak</th><th>Tanggal</th><th>Peserta</th><th>Status</th><th class="text-right">Aksi</th></tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($items as $v)
                    <tr>
                        <td class="py-2.5"><div class="font-medium">{{ $v->name }}</div>@if($v->purpose)<div class="text-xs text-slate-500">{{ Str::limit($v->purpose, 60) }}</div>@endif</td>
                        <td class="text-xs"><div>{{ $v->email }}</div><div>{{ $v->phone }}</div></td>
                        <td>{{ $v->visit_date?->format('d M Y') }}</td>
                        <td>{{ $v->participants }}</td>
                        <td>
                            <span class="text-[10px] uppercase font-bold rounded-full px-2 py-0.5
                                @class(['bg-amber-100 text-amber-700' => $v->status === 'pending', 'bg-brand-100 text-brand-700' => $v->status === 'approved', 'bg-red-100 text-red-700' => $v->status === 'rejected'])">{{ $v->status }}</span>
                        </td>
                        <td class="text-right whitespace-nowrap">
                            @if ($v->status !== 'approved')<button wire:click="setStatus({{ $v->id }}, 'approved')" class="text-brand-600 text-xs hover:underline">Setujui</button>@endif
                            @if ($v->status !== 'rejected')<button wire:click="setStatus({{ $v->id }}, 'rejected')" class="text-red-600 text-xs hover:underline ml-2">Tolak</button>@endif
                            <button wire:click="confirmDelete({{ $v->id }}, @js($v->name))" class="text-red-600 text-xs hover:underline ml-2">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-6 text-center text-slate-500">Belum ada permintaan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $items->links() }}</div>
</div>
<x-confirm-delete title="Hapus Permintaan Kunjungan?" description="Permintaan kunjungan ini akan dihapus permanen." :show="(bool) $confirmingDeleteId" :label="$confirmingDeleteLabel" />
</div>
