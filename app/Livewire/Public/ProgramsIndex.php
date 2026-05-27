<?php

namespace App\Livewire\Public;

use App\Models\Program;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.public')]
class ProgramsIndex extends Component
{
    public function render()
    {
        $programs = Program::where('is_active', true)->orderBy('order')->get();

        return view('livewire.public.programs-index', compact('programs'));
    }
}
