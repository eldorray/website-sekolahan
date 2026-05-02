@php use App\Models\Setting; @endphp
<x-layouts.public>
    <section class="py-8 animate-fade-up">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto liquid-glass rounded-[2rem] p-8 sm:p-12 border border-white/80 shadow-sm">
                <div class="inline-flex items-center gap-2 bg-white/60 backdrop-blur-md text-slate-700 px-5 py-2.5 rounded-full text-sm font-semibold w-fit mb-4 border border-white/60 shadow-sm">
                    Kontak Kami
                </div>
                <h1 class="text-4xl sm:text-5xl font-extrabold text-slate-900 drop-shadow-sm">Hubungi <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-500 to-brand-700">Sekolah</span></h1>
            </div>
        </div>
    </section>
    <section class="py-12 animate-fade-up delay-100">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 grid lg:grid-cols-2 gap-8 items-start">
            <div class="space-y-8">
                <div class="liquid-glass rounded-[2rem] p-8 border border-white/80 shadow-sm apple-transition hover:-translate-y-1 hover:shadow-md">
                    <h2 class="text-2xl font-bold mb-6 text-slate-900 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-brand-50 text-brand-500 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" /></svg>
                        </div>
                        Informasi
                    </h2>
                    <div class="space-y-4 text-[14px] font-medium text-slate-600">
                        <div class="flex gap-4 p-3 rounded-xl hover:bg-white/50 transition-colors"><span class="font-bold w-24 text-slate-900 shrink-0">Alamat</span><span>{{ Setting::get('address') }}</span></div>
                        <div class="flex gap-4 p-3 rounded-xl hover:bg-white/50 transition-colors"><span class="font-bold w-24 text-slate-900 shrink-0">Telepon</span><span>{{ Setting::get('phone') }}</span></div>
                        <div class="flex gap-4 p-3 rounded-xl hover:bg-white/50 transition-colors"><span class="font-bold w-24 text-slate-900 shrink-0">Email</span><span>{{ Setting::get('email') }}</span></div>
                        <div class="flex gap-4 p-3 rounded-xl hover:bg-white/50 transition-colors"><span class="font-bold w-24 text-slate-900 shrink-0">Website</span><span>{{ Setting::get('website') }}</span></div>
                    </div>
                </div>

                <div class="liquid-glass rounded-[2rem] p-8 border border-white/80 shadow-sm apple-transition hover:-translate-y-1 hover:shadow-md" id="visit">
                    <h3 class="text-xl font-bold mb-6 text-slate-900 flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z" /></svg>
                        </div>
                        Jadwalkan Kunjungan
                    </h3>
                    <div class="bg-white/40 rounded-[1.5rem] p-6 border border-white/60">
                        <livewire:public.visit-form />
                    </div>
                </div>
            </div>

            <div class="liquid-glass rounded-[2rem] p-8 border border-white/80 shadow-sm apple-transition hover:-translate-y-1 hover:shadow-md h-full">
                <h2 class="text-2xl font-bold mb-6 text-slate-900 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-green-50 text-green-500 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                    </div>
                    Kirim Pesan
                </h2>
                <div class="bg-white/40 rounded-[1.5rem] p-6 border border-white/60">
                    <livewire:public.contact-form />
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
