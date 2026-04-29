<?php

namespace App\Livewire\Admin;

use App\Models\ContactMessage;
use App\Models\News;
use App\Models\PpdbRegistration;
use App\Models\User;
use App\Models\VisitSchedule;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $stats = [
            ['label' => 'Total Siswa Daftar', 'value' => PpdbRegistration::count(), 'sub' => PpdbRegistration::where('status', 'pending')->count() . ' pending'],
            ['label' => 'Total Guru', 'value' => User::where('role', 'guru')->count(), 'sub' => 'Aktif'],
            ['label' => 'Total Berita', 'value' => News::count(), 'sub' => News::where('is_published', true)->count() . ' terbit'],
            ['label' => 'Pesan Masuk', 'value' => ContactMessage::count(), 'sub' => ContactMessage::where('is_read', false)->count() . ' belum dibaca'],
            ['label' => 'Permintaan Kunjungan', 'value' => VisitSchedule::count(), 'sub' => VisitSchedule::where('status', 'pending')->count() . ' pending'],
        ];

        $recentPpdb = PpdbRegistration::latest()->take(5)->get();
        $recentMessages = ContactMessage::latest()->take(5)->get();

        return view('livewire.admin.dashboard', compact('stats', 'recentPpdb', 'recentMessages'))
            ->layout('layouts.panel', ['title' => 'Dashboard']);
    }
}
