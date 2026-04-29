@php use App\Models\Setting; @endphp
<x-layouts.public>
    <section class="bg-gradient-to-b from-brand-50 to-white py-14">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-brand-600 font-semibold mb-2">Penerimaan Peserta Didik Baru</p>
            <h1 class="text-4xl sm:text-5xl font-extrabold text-slate-900">PPDB <span class="text-brand-500">{{ Setting::get('ppdb_year') }}</span></h1>
            <p class="mt-4 text-slate-600">Lengkapi form di bawah untuk mendaftarkan putra-putri Anda di {{ Setting::get('school_name') }}.</p>
        </div>
    </section>
    <section class="pb-14">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <livewire:public.ppdb-form />
        </div>
    </section>
</x-layouts.public>
