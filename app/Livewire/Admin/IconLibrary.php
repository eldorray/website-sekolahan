<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class IconLibrary extends Component
{
    public string $search = '';
    public string $style = 'outline'; // outline | solid

    public function render()
    {
        return view('livewire.admin.icon-library')
            ->layout('layouts.panel', ['title' => 'Icon Library']);
    }
}
