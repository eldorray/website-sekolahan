<x-mail::message>
# Pendaftar PPDB Baru

Ada pendaftar baru pada sistem PPDB.

- **Nomor:** {{ $registration->registration_number }}
- **Nama:** {{ $registration->full_name }}
- **Jenjang:** {{ $registration->grade_target }}
- **Telepon orang tua:** {{ $registration->parent_phone }}
- **Email orang tua:** {{ $registration->parent_email ?? '-' }}

<x-mail::button :url="url('/admin/ppdb')">
Lihat di Panel Admin
</x-mail::button>
</x-mail::message>
