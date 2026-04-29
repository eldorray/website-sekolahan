<div>
    <div class="card">
        <h2 class="font-bold text-slate-900 mb-3">Pesan Masuk</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left text-slate-500 border-b">
                    <tr><th class="py-2">Pengirim</th><th>Subjek</th><th>Tanggal</th><th>Status</th><th class="text-right">Aksi</th></tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($items as $m)
                        <tr class="{{ $m->is_read ? '' : 'bg-brand-50/30 font-medium' }}">
                            <td class="py-2.5"><div>{{ $m->name }}</div><div class="text-xs text-slate-500">{{ $m->email }}</div></td>
                            <td>{{ $m->subject }}</td>
                            <td class="text-xs">{{ $m->created_at->diffForHumans() }}</td>
                            <td>{!! $m->is_read ? '<span class="text-xs text-slate-500">Dibaca</span>' : '<span class="rounded-full bg-brand-500 text-white px-2 py-0.5 text-xs">Baru</span>' !!}</td>
                            <td class="text-right">
                                <button wire:click="view({{ $m->id }})" class="text-brand-600 text-xs hover:underline">Lihat</button>
                                <button wire:click="confirmDelete({{ $m->id }}, @js($m->subject))" class="text-red-600 text-xs hover:underline ml-2">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-6 text-center text-slate-500">Belum ada pesan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $items->links() }}</div>
    </div>

    @if ($detail)
        <div class="fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4" wire:click.self="$set('viewing', null)">
            <div class="bg-white rounded-2xl max-w-xl w-full p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-lg">{{ $detail->subject }}</h3>
                    <button wire:click="$set('viewing', null)" class="text-slate-400 hover:text-slate-600">✕</button>
                </div>
                <div class="text-sm space-y-2">
                    <div><span class="text-slate-500">Dari:</span> {{ $detail->name }} &lt;{{ $detail->email }}&gt;</div>
                    @if($detail->phone)<div><span class="text-slate-500">Telepon:</span> {{ $detail->phone }}</div>@endif
                    <div><span class="text-slate-500">Tanggal:</span> {{ $detail->created_at->format('d M Y H:i') }}</div>
                    <hr class="my-3">
                    <div class="whitespace-pre-line text-slate-700">{{ $detail->message }}</div>
                </div>
            </div>
        </div>
    @endif
    <x-confirm-delete title="Hapus Pesan?" description="Pesan ini akan dihapus permanen." :show="(bool) $confirmingDeleteId" :label="$confirmingDeleteLabel" />
</div>
