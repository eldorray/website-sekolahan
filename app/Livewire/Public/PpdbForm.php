<?php

namespace App\Livewire\Public;

use App\Models\PpdbRegistration;
use Livewire\Component;

class PpdbForm extends Component
{
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

    public ?string $registrationNumber = null;

    public function submit(): void
    {
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
        ]);

        $data['registration_number'] = 'PPDB-' . now()->format('Y') . '-' . str_pad((string) (PpdbRegistration::count() + 1), 4, '0', STR_PAD_LEFT);
        $reg = PpdbRegistration::create($data);
        $this->registrationNumber = $reg->registration_number;
    }

    public function render()
    {
        return view('livewire.public.ppdb-form');
    }
}
