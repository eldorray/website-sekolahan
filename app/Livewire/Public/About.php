<?php

namespace App\Livewire\Public;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.public')]
class About extends Component
{
    public function render()
    {
        return view('livewire.public.about');
    }
}
