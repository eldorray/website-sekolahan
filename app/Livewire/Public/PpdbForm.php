<?php

namespace App\Livewire\Public;

use App\Livewire\Concerns\WithSpamProtection;
use App\Mail\PpdbAdminAlertMail;
use App\Mail\PpdbReceivedMail;
use App\Models\PpdbRegistration;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithFileUploads;

class PpdbForm extends Component
{
    use WithFileUploads, WithSpamProtection;

    public string $full_name = '';

    public string $nickname = '';

    public string $gender = 'L';

    public string $birthplace = '';

    public string $birthdate = '';

    public string $previous_school = '';

    public string $address = '';

    public string $father_name = '';

    public string $mother_name = '';

    public string $parent_phone = '';

    public string $parent_email = '';

    public string $grade_target = 'SD Kelas 1';

    public $kk_file;

    public $birth_certificate_file;

    public ?string $registrationNumber = null;

    public function submit(): void
    {
        $this->guardAgainstSpam('ppdb');

        $data = $this->validate([
            'full_name' => 'required|string|max:160',
            'nickname' => 'nullable|string|max:60',
            'gender' => 'required|in:L,P',
            'birthplace' => 'required|string|max:120',
            'birthdate' => 'required|date|before:today',
            'previous_school' => 'nullable|string|max:160',
            'address' => 'required|string|max:500',
            'father_name' => 'required|string|max:160',
            'mother_name' => 'required|string|max:160',
            'parent_phone' => 'required|string|max:30',
            'parent_email' => 'nullable|email|max:160',
            'grade_target' => 'required|string|max:60',
            'kk_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'birth_certificate_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ], [], [
            'kk_file' => 'Kartu Keluarga',
            'birth_certificate_file' => 'Akte Kelahiran',
        ]);

        // Store on the private "local" disk — these are personal documents and
        // must not be publicly accessible.
        $data['kk_file'] = $this->kk_file->store('ppdb/kk', 'local');
        $data['birth_certificate_file'] = $this->birth_certificate_file->store('ppdb/akte', 'local');

        $reg = PpdbRegistration::createWithNumber($data);
        $this->registrationNumber = $reg->registration_number;

        $this->sendNotifications($reg);
    }

    /**
     * Queue confirmation to the registrant and an alert to the admin.
     * Mail failures must never block a successful registration.
     */
    protected function sendNotifications(PpdbRegistration $reg): void
    {
        try {
            if ($reg->parent_email) {
                Mail::to($reg->parent_email)->queue(new PpdbReceivedMail($reg));
            }

            if ($adminEmail = $this->adminEmail()) {
                Mail::to($adminEmail)->queue(new PpdbAdminAlertMail($reg));
            }
        } catch (\Throwable $e) {
            Log::warning('PPDB notification failed', ['error' => $e->getMessage()]);
        }
    }

    protected function adminEmail(): ?string
    {
        return Setting::get('email') ?: config('mail.from.address');
    }

    public function render()
    {
        return view('livewire.public.ppdb-form');
    }
}
