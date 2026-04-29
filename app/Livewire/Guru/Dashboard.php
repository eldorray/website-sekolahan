<?php

namespace App\Livewire\Guru;

use App\Models\News;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $user = auth()->user();
        $stats = [
            ['label' => 'Berita Saya', 'value' => News::where('user_id', $user->id)->count()],
            ['label' => 'Terbit', 'value' => News::where('user_id', $user->id)->where('is_published', true)->count()],
            ['label' => 'Draft', 'value' => News::where('user_id', $user->id)->where('is_published', false)->count()],
        ];
        $latest = News::where('user_id', $user->id)->latest()->take(5)->get();

        return view('livewire.guru.dashboard', compact('stats', 'latest'))
            ->layout('layouts.panel', ['title' => 'Dashboard Guru']);
    }
}
