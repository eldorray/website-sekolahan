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

    {{-- Detail Modal — teleported to body to escape parent transform --}}
    <template x-teleport="body">
        @if ($detail)
            <div
                x-data
                x-init="document.body.style.overflow = 'hidden'"
                @keydown.escape.window="$wire.set('viewing', null)"
                class="fixed inset-0 z-[90] flex items-center justify-center p-4"
            >
                {{-- Backdrop --}}
                <div
                    x-transition:enter="transition ease-ios duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    wire:click="$set('viewing', null)"
                    class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
                ></div>

                {{-- Card --}}
                <div
                    x-transition:enter="transition ease-ios-spring duration-400"
                    x-transition:enter-start="opacity-0 scale-90 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    class="relative bg-white rounded-3xl max-w-2xl w-full p-6 shadow-2xl max-h-[90vh] overflow-y-auto"
                >
                    {{-- Header --}}
                    <div class="flex justify-between items-start mb-5">
                        <div>
                            <h3 class="font-bold text-lg text-slate-900">Detail Pendaftaran</h3>
                            <p class="text-xs text-slate-500 font-mono mt-0.5">{{ $detail->registration_number }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-[10px] uppercase font-bold rounded-full px-2.5 py-1
                                @class(['bg-amber-100 text-amber-700' => $detail->status === 'pending', 'bg-brand-100 text-brand-700' => $detail->status === 'accepted', 'bg-red-100 text-red-700' => $detail->status === 'rejected'])">{{ $detail->status }}</span>
                            <button wire:click="$set('viewing', null)" class="h-8 w-8 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-600 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                    </div>

                    {{-- Data Siswa --}}
                    <div class="mb-5">
                        <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Data Calon Siswa</h4>
                        <div class="grid sm:grid-cols-2 gap-3 text-sm">
                            <div class="rounded-xl bg-slate-50 px-3.5 py-2.5"><span class="text-slate-400 text-xs block mb-0.5">Nama Lengkap</span><div class="font-medium text-slate-900">{{ $detail->full_name }}</div></div>
                            <div class="rounded-xl bg-slate-50 px-3.5 py-2.5"><span class="text-slate-400 text-xs block mb-0.5">Panggilan</span><div class="font-medium text-slate-900">{{ $detail->nickname ?: '-' }}</div></div>
                            <div class="rounded-xl bg-slate-50 px-3.5 py-2.5"><span class="text-slate-400 text-xs block mb-0.5">Jenis Kelamin</span><div class="font-medium text-slate-900">{{ $detail->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</div></div>
                            <div class="rounded-xl bg-slate-50 px-3.5 py-2.5"><span class="text-slate-400 text-xs block mb-0.5">Jenjang</span><div class="font-medium text-slate-900">{{ $detail->grade_target }}</div></div>
                            <div class="rounded-xl bg-slate-50 px-3.5 py-2.5"><span class="text-slate-400 text-xs block mb-0.5">Tempat, Tanggal Lahir</span><div class="font-medium text-slate-900">{{ $detail->birthplace }}, {{ $detail->birthdate?->format('d M Y') }}</div></div>
                            <div class="rounded-xl bg-slate-50 px-3.5 py-2.5"><span class="text-slate-400 text-xs block mb-0.5">Asal Sekolah</span><div class="font-medium text-slate-900">{{ $detail->previous_school ?: '-' }}</div></div>
                            <div class="sm:col-span-2 rounded-xl bg-slate-50 px-3.5 py-2.5"><span class="text-slate-400 text-xs block mb-0.5">Alamat</span><div class="font-medium text-slate-900">{{ $detail->address }}</div></div>
                        </div>
                    </div>

                    {{-- Data Orang Tua --}}
                    <div class="mb-5">
                        <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Data Orang Tua / Wali</h4>
                        <div class="grid sm:grid-cols-2 gap-3 text-sm">
                            <div class="rounded-xl bg-slate-50 px-3.5 py-2.5"><span class="text-slate-400 text-xs block mb-0.5">Nama Ayah</span><div class="font-medium text-slate-900">{{ $detail->father_name }}</div></div>
                            <div class="rounded-xl bg-slate-50 px-3.5 py-2.5"><span class="text-slate-400 text-xs block mb-0.5">Nama Ibu</span><div class="font-medium text-slate-900">{{ $detail->mother_name }}</div></div>
                            <div class="rounded-xl bg-slate-50 px-3.5 py-2.5"><span class="text-slate-400 text-xs block mb-0.5">No. Telepon</span><div class="font-medium text-slate-900">{{ $detail->parent_phone }}</div></div>
                            <div class="rounded-xl bg-slate-50 px-3.5 py-2.5"><span class="text-slate-400 text-xs block mb-0.5">Email</span><div class="font-medium text-slate-900">{{ $detail->parent_email ?: '-' }}</div></div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-2 pt-2 border-t border-slate-100">
                        <button wire:click="$set('viewing', null)"
                            class="rounded-full px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100 transition-all ease-ios duration-200 active:scale-95">
                            Tutup
                        </button>
                        @if ($detail->status !== 'accepted')
                            <button wire:click="setStatus({{ $detail->id }}, 'accepted')"
                                class="rounded-full bg-brand-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-600 transition-all ease-ios duration-200 active:scale-95">
                                Terima Pendaftaran
                            </button>
                        @endif
                        @if ($detail->status !== 'rejected')
                            <button wire:click="setStatus({{ $detail->id }}, 'rejected')"
                                class="rounded-full bg-red-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-700 transition-all ease-ios duration-200 active:scale-95">
                                Tolak Pendaftaran
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endif
        @if (! $detail)
            <div x-data x-init="document.body.style.overflow = ''" class="hidden"></div>
        @endif
    </template>

    {{-- Delete Confirm Modal --}}
    <template x-teleport="body">
        <x-confirm-delete title="Hapus Pendaftaran?" description="Data pendaftaran PPDB ini akan dihapus permanen." :show="(bool) $confirmingDeleteId" :label="$confirmingDeleteLabel" />
    </template>
</div>
