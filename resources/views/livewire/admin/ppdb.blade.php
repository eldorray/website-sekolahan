<div>
    <div class="card">
        <div class="flex flex-wrap items-center gap-3 justify-between mb-4">
            <h2 class="font-bold text-slate-900">Pendaftaran PPDB</h2>
            <div class="flex gap-2">
                <select wire:model.live="statusFilter" class="select"><option value="">Semua Status</option><option value="pending">Pending</option><option value="accepted">Diterima</option><option value="rejected">Ditolak</option></select>
                <input wire:model.live.debounce.300ms="search" placeholder="Cari nama / no…" class="input">
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left text-slate-500 border-b">
                    <tr><th class="py-2">No. Daftar</th><th>Nama</th><th>Jenjang</th><th>Ortu</th><th>Tanggal</th><th>Status</th><th class="text-right">Aksi</th></tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($items as $r)
                        <tr>
                            <td class="py-2.5 text-xs font-mono">{{ $r->registration_number }}</td>
                            <td><div class="font-medium">{{ $r->full_name }}</div><div class="text-xs text-slate-500">{{ $r->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</div></td>
                            <td>{{ $r->grade_target }}</td>
                            <td class="text-xs"><div>{{ $r->father_name }}</div><div class="text-slate-500">{{ $r->parent_phone }}</div></td>
                            <td class="text-xs">{{ $r->created_at->format('d M Y') }}</td>
                            <td>
                                <span class="text-[10px] uppercase font-bold rounded-full px-2 py-0.5
                                    @class(['bg-amber-100 text-amber-700' => $r->status === 'pending', 'bg-brand-100 text-brand-700' => $r->status === 'accepted', 'bg-red-100 text-red-700' => $r->status === 'rejected'])">{{ $r->status }}</span>
                            </td>
                            <td class="text-right whitespace-nowrap">
                                <button wire:click="$set('viewing', {{ $r->id }})" class="text-brand-600 text-xs hover:underline">Detail</button>
                                @if ($r->status !== 'accepted')<button wire:click="setStatus({{ $r->id }}, 'accepted')" class="text-brand-600 text-xs hover:underline ml-2">Terima</button>@endif
                                @if ($r->status !== 'rejected')<button wire:click="setStatus({{ $r->id }}, 'rejected')" class="text-red-600 text-xs hover:underline ml-2">Tolak</button>@endif
                                <button wire:click="confirmDelete({{ $r->id }}, @js($r->full_name))" class="text-red-600 text-xs hover:underline ml-2">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-6 text-center text-slate-500">Belum ada pendaftaran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $items->links() }}</div>
    </div>

    @if ($detail)
        <div class="fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4" wire:click.self="$set('viewing', null)">
            <div class="bg-white rounded-2xl max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-lg">Detail Pendaftaran</h3>
                    <button wire:click="$set('viewing', null)" class="text-slate-400 hover:text-slate-600">✕</button>
                </div>
                <div class="grid sm:grid-cols-2 gap-3 text-sm">
                    <div><span class="text-slate-500 text-xs">No. Daftar</span><div class="font-mono">{{ $detail->registration_number }}</div></div>
                    <div><span class="text-slate-500 text-xs">Status</span><div>{{ $detail->status }}</div></div>
                    <div><span class="text-slate-500 text-xs">Nama Lengkap</span><div>{{ $detail->full_name }}</div></div>
                    <div><span class="text-slate-500 text-xs">Panggilan</span><div>{{ $detail->nickname }}</div></div>
                    <div><span class="text-slate-500 text-xs">Jenis Kelamin</span><div>{{ $detail->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</div></div>
                    <div><span class="text-slate-500 text-xs">Jenjang</span><div>{{ $detail->grade_target }}</div></div>
                    <div><span class="text-slate-500 text-xs">TTL</span><div>{{ $detail->birthplace }}, {{ $detail->birthdate?->format('d M Y') }}</div></div>
                    <div><span class="text-slate-500 text-xs">Asal Sekolah</span><div>{{ $detail->previous_school ?: '-' }}</div></div>
                    <div class="sm:col-span-2"><span class="text-slate-500 text-xs">Alamat</span><div>{{ $detail->address }}</div></div>
                    <div><span class="text-slate-500 text-xs">Ayah</span><div>{{ $detail->father_name }}</div></div>
                    <div><span class="text-slate-500 text-xs">Ibu</span><div>{{ $detail->mother_name }}</div></div>
                    <div><span class="text-slate-500 text-xs">Telepon</span><div>{{ $detail->parent_phone }}</div></div>
                    <div><span class="text-slate-500 text-xs">Email</span><div>{{ $detail->parent_email }}</div></div>
                </div>
            </div>
        </div>
    @endif
    <x-confirm-delete title="Hapus Pendaftaran?" description="Data pendaftaran PPDB ini akan dihapus permanen." :show="(bool) $confirmingDeleteId" :label="$confirmingDeleteLabel" />
</div>
