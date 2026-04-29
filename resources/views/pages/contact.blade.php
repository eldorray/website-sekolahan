@php use App\Models\Setting; @endphp
<x-layouts.public>
    <section class="bg-gradient-to-b from-brand-50 to-white py-14">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-brand-600 font-semibold mb-2">Kontak Kami</p>
            <h1 class="text-4xl sm:text-5xl font-extrabold text-slate-900">Hubungi <span class="text-brand-500">Sekolah</span></h1>
        </div>
    </section>
    <section class="py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 grid lg:grid-cols-2 gap-8">
            <div>
                <h2 class="text-2xl font-bold mb-4 text-slate-900">Informasi</h2>
                <div class="space-y-3 text-slate-600">
                    <div class="flex gap-3"><span class="font-semibold w-24 text-slate-900">Alamat</span><span>{{ Setting::get('address') }}</span></div>
                    <div class="flex gap-3"><span class="font-semibold w-24 text-slate-900">Telepon</span><span>{{ Setting::get('phone') }}</span></div>
                    <div class="flex gap-3"><span class="font-semibold w-24 text-slate-900">Email</span><span>{{ Setting::get('email') }}</span></div>
                    <div class="flex gap-3"><span class="font-semibold w-24 text-slate-900">Website</span><span>{{ Setting::get('website') }}</span></div>
                </div>
                <h3 class="text-xl font-bold mt-10 mb-4 text-slate-900" id="visit">Jadwalkan Kunjungan</h3>
                <livewire:public.visit-form />
            </div>
            <div>
                <h2 class="text-2xl font-bold mb-4 text-slate-900">Kirim Pesan</h2>
                <livewire:public.contact-form />
            </div>
        </div>
    </section>
</x-layouts.public>
