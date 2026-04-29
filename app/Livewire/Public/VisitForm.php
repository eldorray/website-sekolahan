<?php

namespace App\Livewire\Public;

use App\Models\VisitSchedule;
use Livewire\Component;

class VisitForm extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $visit_date = '';
    public int $participants = 1;
    public string $purpose = '';
    public bool $sent = false;

    public function submit(): void
    {
        $data = $this->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:160',
            'phone' => 'required|string|max:30',
            'visit_date' => 'required|date|after:today',
            'participants' => 'required|integer|min:1|max:200',
            'purpose' => 'nullable|string|max:500',
        ]);

        VisitSchedule::create($data);
        $this->reset(['name', 'email', 'phone', 'visit_date', 'purpose']);
        $this->participants = 1;
        $this->sent = true;
    }

    public function render()
    {
        return view('livewire.public.visit-form');
    }
}
