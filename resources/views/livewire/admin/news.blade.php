<div>
    <div class="grid lg:grid-cols-3 gap-6">
        {{-- Left: News List --}}
        <div class="lg:col-span-2 card">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold text-slate-900">Daftar Berita</h2>
                <input wire:model.live.debounce.300ms="search" placeholder="Cari…" class="input max-w-xs">
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-left text-slate-500 border-b">
                        <tr>
                            <th class="py-2">Judul</th>
                            <th>Kategori</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($items as $n)
                            <tr>
                                <td class="py-2.5">
                                    <div class="font-medium text-slate-900">{{ Str::limit($n->title, 60) }}</div>
                                    <div class="text-xs text-slate-500">oleh {{ $n->author->name ?? '-' }}</div>
                                </td>
                                <td><span
                                        class="rounded bg-brand-100 text-brand-700 px-2 py-0.5 text-xs">{{ $n->category }}</span>
                                </td>
                                <td>{{ $n->published_at?->format('d M Y') }}</td>
                                <td>{!! $n->is_published
                                    ? '<span class="rounded-full bg-brand-100 text-brand-700 px-2 py-0.5 text-xs">Terbit</span>'
                                    : '<span class="rounded-full bg-slate-100 text-slate-500 px-2 py-0.5 text-xs">Draft</span>' !!}</td>
                                <td class="text-right">
                                    <button wire:click="edit({{ $n->id }})"
                                        class="text-brand-600 text-xs hover:underline">Edit</button>
                                    <button
                                        wire:click="confirmDelete({{ $n->id }}, @js($n->title))"
                                        class="text-red-600 text-xs hover:underline ml-2">Hapus</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-slate-500">Belum ada berita.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $items->links() }}</div>
        </div>

        {{-- Right Column: AI Chat + Form --}}
        <div class="space-y-6">

            {{-- AI Chat Card (DI ATAS) --}}
            <div class="card border-2 border-purple-100 bg-gradient-to-b from-purple-50/50 to-white">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-purple-600" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 00-2.455 2.456z" />
                            </svg>
                        </div>
                        <h2 class="font-bold text-slate-900 text-sm">AI Penulis Berita</h2>
                    </div>
                    @if (count($aiMessages) > 0)
                        <button wire:click="clearAiChat"
                            class="text-xs text-slate-400 hover:text-red-500 transition">Clear</button>
                    @endif
                </div>

                {{-- AI Provider Dropdown --}}
                <div class="flex items-center gap-2 mb-3">
                    <label class="text-xs text-slate-500 shrink-0">Model AI:</label>
                    <select wire:model="aiProvider" class="select text-xs py-1.5 px-3 rounded-lg">
                        <option value="gemini">Gemini 2.5 Flash</option>
                        <option value="deepseek">DeepSeek V4 Flash</option>
                    </select>
                </div>

                <p class="text-xs text-slate-500 mb-3">Deskripsikan berita yang ingin dibuat, AI akan mengisi form
                    secara otomatis.</p>

                {{-- Chat Messages --}}
                @if (count($aiMessages) > 0)
                    <div
                        class="max-h-48 overflow-y-auto space-y-2 mb-3 p-2 bg-white rounded-xl border border-slate-100">
                        @foreach ($aiMessages as $msg)
                            <div class="flex {{ $msg['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                                <div
                                    class="max-w-[85%] px-3 py-2 rounded-2xl text-xs leading-relaxed {{ $msg['role'] === 'user' ? 'bg-purple-600 text-white rounded-br-md' : 'bg-slate-100 text-slate-700 rounded-bl-md' }}">
                                    {{ $msg['text'] }}
                                </div>
                            </div>
                        @endforeach

                        @if ($aiLoading)
                            <div class="flex justify-start">
                                <div
                                    class="bg-slate-100 text-slate-500 px-3 py-2 rounded-2xl rounded-bl-md text-xs flex items-center gap-2">
                                    <div class="flex gap-1">
                                        <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce"
                                            style="animation-delay: 0ms"></span>
                                        <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce"
                                            style="animation-delay: 150ms"></span>
                                        <span class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce"
                                            style="animation-delay: 300ms"></span>
                                    </div>
                                    <span>Sedang menulis...</span>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Input --}}
                <form wire:submit="generateWithAi" class="flex gap-2">
                    <input wire:model="aiPrompt" type="text"
                        placeholder="Contoh: Buatkan berita tentang lomba sains..." class="input flex-1 text-xs"
                        @if ($aiLoading) disabled @endif>
                    <button type="submit"
                        class="shrink-0 w-9 h-9 rounded-xl bg-purple-600 hover:bg-purple-700 text-white flex items-center justify-center transition disabled:opacity-50"
                        @if ($aiLoading) disabled @endif>
                        @if ($aiLoading)
                            <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin">
                            </div>
                        @else
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                            </svg>
                        @endif
                    </button>
                </form>

                @if ($aiError && count($aiMessages) === 0)
                    <p class="text-xs text-red-500 mt-2">{{ $aiError }}</p>
                @endif
            </div>

            {{-- Write News Form (DI BAWAH) --}}
            <div class="card">
                <h2 class="font-bold text-slate-900 mb-3">{{ $editingId ? 'Edit Berita' : 'Tulis Berita' }}</h2>
                <form wire:submit="save" class="space-y-3">
                    <div><label class="label">Judul</label><input wire:model="title" id="ai-field-title"
                            class="input">
                        @error('title')
                            <p class="text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div><label class="label">Kategori</label>
                        <select wire:model="category" class="select">
                            <option>KEGIATAN</option>
                            <option>PRESTASI</option>
                            <option>ARTIKEL</option>
                            <option>PENGUMUMAN</option>
                        </select>
                    </div>
                    <div><label class="label">Excerpt</label>
                        <textarea wire:model="excerpt" id="ai-field-excerpt" rows="2" class="textarea"></textarea>
                        @error('excerpt')
                            <p class="text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div><label class="label">Konten</label>
                        <x-rich-editor model="content" :value="$content" placeholder="Tulis isi berita…" />
                        @error('content')
                            <p class="text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div><label class="label">Tanggal Terbit</label><input type="date" wire:model="published_at"
                            class="input"></div>
                    <div>
                        <label class="label">Gambar</label>
                        <div class="space-y-2">
                            {{-- Preview area --}}
                            @if ($image_file)
                                {{-- New file selected: live preview --}}
                                <div class="relative inline-block">
                                    <img src="{{ $image_file->temporaryUrl() }}" alt="Preview"
                                        class="h-20 w-20 rounded-lg object-cover ring-2 ring-brand-200 shadow-sm">
                                    <span
                                        class="absolute -bottom-1.5 -right-1.5 rounded-full bg-brand-500 text-white text-[9px] font-bold px-1.5 py-0.5 shadow">BARU</span>
                                </div>
                            @elseif ($existing_image)
                                {{-- Existing saved image --}}
                                <div class="relative inline-block">
                                    <img src="{{ asset('storage/' . $existing_image) }}" alt="Tersimpan"
                                        class="h-20 w-20 rounded-lg object-cover ring-2 ring-slate-200 shadow-sm">
                                    <button type="button" wire:click="removeImage"
                                        class="absolute -top-1.5 -right-1.5 h-5 w-5 rounded-full bg-red-500 text-white text-xs flex items-center justify-center shadow hover:bg-red-600 transition"
                                        title="Hapus gambar">
                                        ×
                                    </button>
                                </div>
                            @endif

                            <input type="file" wire:model="image_file" accept="image/*" class="text-xs block">
                            @error('image_file')
                                <p class="text-xs text-red-500">{{ $message }}</p>
                            @enderror
                            <div wire:loading wire:target="image_file"
                                class="text-xs text-slate-500 flex items-center gap-1">
                                <div
                                    class="w-3 h-3 border-2 border-brand-500 border-t-transparent rounded-full animate-spin">
                                </div>
                                Mengupload...
                            </div>
                        </div>
                    </div>
                    <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox"
                            wire:model="is_published"> Publikasikan</label>
                    <div class="flex gap-2 pt-1">
                        <button class="btn-primary">{{ $editingId ? 'Update' : 'Simpan' }}</button>
                        @if ($editingId)
                            <button type="button" wire:click="resetForm" class="btn-ghost">Batal</button>
                        @endif
                    </div>
                </form>
            </div>

        </div>
    </div>
    <x-confirm-delete title="Hapus Berita?" description="Berita ini akan dihapus permanen." :show="(bool) $confirmingDeleteId"
        :label="$confirmingDeleteLabel" />

    {{-- Typewriter Effect Script --}}
    @script
        <script>
            window.addEventListener('ai-typewriter', (event) => {
                console.log('AI Typewriter triggered', event.detail);
                const data = event.detail;
                const charSpeed = 18;
                const wordSpeed = 35;
                const wireRef = $wire;

                function typeField(el, text) {
                    return new Promise((resolve) => {
                        el.value = '';
                        el.focus();
                        el.style.borderColor = '#7c3aed';
                        el.style.boxShadow = '0 0 0 2px rgba(124, 58, 237, 0.2)';
                        let i = 0;
                        const interval = setInterval(() => {
                            el.value += text.charAt(i);
                            el.dispatchEvent(new Event('input', {
                                bubbles: true
                            }));
                            i++;
                            if (i >= text.length) {
                                clearInterval(interval);
                                el.style.borderColor = '';
                                el.style.boxShadow = '';
                                setTimeout(resolve, 250);
                            }
                        }, charSpeed);
                    });
                }

                function typeContent(text) {
                    return new Promise((resolve) => {
                        const editorEl = document.querySelector('[data-tiptap-editor]');
                        const hiddenInput = document.querySelector('[data-tiptap-input]');

                        if (editorEl) {
                            const container = editorEl.closest('[data-tiptap-root]');
                            if (container) {
                                container.style.borderColor = '#7c3aed';
                                container.style.boxShadow = '0 0 0 2px rgba(124, 58, 237, 0.2)';
                            }

                            editorEl.innerHTML = '';
                            const plainText = text.replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();
                            const words = plainText.split(' ');
                            let i = 0;

                            const interval = setInterval(() => {
                                editorEl.innerHTML = '<p>' + words.slice(0, i + 1).join(' ') + '</p>';
                                i++;
                                if (i >= words.length) {
                                    clearInterval(interval);
                                    editorEl.innerHTML = text;
                                    if (hiddenInput) {
                                        hiddenInput.value = text;
                                        hiddenInput.dispatchEvent(new Event('input', {
                                            bubbles: true
                                        }));
                                    }
                                    if (container) {
                                        container.style.borderColor = '';
                                        container.style.boxShadow = '';
                                    }
                                    wireRef.set('content', text);
                                    resolve();
                                }
                            }, wordSpeed);
                        } else {
                            wireRef.set('content', text);
                            resolve();
                        }
                    });
                }

                const titleEl = document.getElementById('ai-field-title');
                if (titleEl) {
                    titleEl.closest('.card').scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }

                (async () => {
                    await new Promise(r => setTimeout(r, 400));

                    const titleInput = document.getElementById('ai-field-title');
                    const excerptInput = document.getElementById('ai-field-excerpt');

                    if (titleInput && data.title) {
                        await typeField(titleInput, data.title);
                        wireRef.set('title', data.title);
                    }
                    if (excerptInput && data.excerpt) {
                        await typeField(excerptInput, data.excerpt);
                        wireRef.set('excerpt', data.excerpt);
                    }
                    if (data.content) {
                        await typeContent(data.content);
                    }
                })();
            });
        </script>
    @endscript
</div>
