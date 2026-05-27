<?php

namespace App\Livewire\Public;

use App\Models\Program;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.public')]
class ProgramShow extends Component
{
    public string $slug = '';

    public function mount(string $slug): void
    {
        $this->slug = $slug;
    }

    public function render()
    {
        $program = Program::where('slug', $this->slug)
            ->where('is_active', true)
            ->firstOrFail();

        return view('livewire.public.program-show', compact('program'));
    }
}
