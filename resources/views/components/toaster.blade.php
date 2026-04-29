<div
    x-data="{
        toasts: [],
        push(t) {
            const id = Date.now() + Math.random();
            this.toasts.push({ id, type: t.type || 'success', message: t.message || '' });
            setTimeout(() => this.dismiss(id), 4000);
        },
        dismiss(id) { this.toasts = this.toasts.filter(t => t.id !== id); }
    }"
    @toast.window="push($event.detail)"
    class="fixed top-4 right-4 left-4 sm:left-auto sm:top-5 sm:right-5 z-[100] flex flex-col gap-2 sm:w-80 pointer-events-none"
>
    <template x-for="t in toasts" :key="t.id">
        <div
            x-transition:enter="transition ease-ios-spring duration-450"
            x-transition:enter-start="opacity-0 translate-y-[-12px] sm:translate-y-0 sm:translate-x-6 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 translate-x-0 scale-100"
            x-transition:leave="transition ease-ios duration-250"
            x-transition:leave-start="opacity-100 translate-x-0 scale-100"
            x-transition:leave-end="opacity-0 sm:translate-x-6 scale-95"
            :class="{
                'bg-white ring-brand-200 text-brand-700': t.type === 'success',
                'bg-white ring-red-200 text-red-700': t.type === 'error',
                'bg-white ring-blue-200 text-blue-700': t.type === 'info',
            }"
            class="pointer-events-auto rounded-2xl shadow-xl ring-1 px-4 py-3 flex items-start gap-3 backdrop-blur"
        >
            <div class="mt-0.5 shrink-0">
                <template x-if="t.type === 'success'">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                </template>
                <template x-if="t.type === 'error'">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.008h.008M3 12a9 9 0 1 1 18 0 9 9 0 0 1-18 0Z"/></svg>
                </template>
                <template x-if="t.type === 'info'">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/></svg>
                </template>
            </div>
            <div class="flex-1 text-sm" x-text="t.message"></div>
            <button @click="dismiss(t.id)" class="text-slate-400 hover:text-slate-600 text-sm transition-colors">✕</button>
        </div>
    </template>
</div>
