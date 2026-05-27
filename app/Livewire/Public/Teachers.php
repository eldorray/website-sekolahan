<?php

namespace App\Livewire\Public;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.public')]
class Teachers extends Component
{
    public function render()
    {
        $teachers = User::where('role', 'guru')
            ->where('is_active', true)
            ->get();

        return view('livewire.public.teachers', compact('teachers'));
    }
}
