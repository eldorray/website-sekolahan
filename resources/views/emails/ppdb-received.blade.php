<x-mail::message>
# Terima Kasih Telah Mendaftar

Halo **{{ $registration->full_name }}**,

Pendaftaran PPDB Anda di **{{ $schoolName }}** telah kami terima.

**Nomor Pendaftaran:** {{ $registration->registration_number }}
**Jenjang yang dituju:** {{ $registration->grade_target }}

Simpan nomor pendaftaran ini sebagai bukti. Panitia akan menghubungi Anda
melalui kontak yang terdaftar untuk proses selanjutnya.

Terima kasih,
Panitia PPDB {{ $schoolName }}
</x-mail::message>
