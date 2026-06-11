<x-mail::message>
# Pesan Kontak Baru

- **Nama:** {{ $contact->name }}
- **Email:** {{ $contact->email }}
- **Telepon:** {{ $contact->phone ?? '-' }}
- **Subjek:** {{ $contact->subject }}

**Pesan:**

{{ $contact->message }}

<x-mail::button :url="url('/admin/contacts')">
Lihat di Panel Admin
</x-mail::button>
</x-mail::message>
