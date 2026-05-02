<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Program;
use App\Models\User;

class HomeController extends Controller
{
    public function __invoke()
    {
        $programs = Program::where('is_active', true)->take(4)->get();
        $news = News::where('is_published', true)->latest('published_at')->take(3)->get();
        $teachers = User::where('role', 'guru')->where('is_active', true)->take(4)->get();

        $stats = [
            'students' => 1250,
            'teachers' => User::where('role', 'guru')->count(),
            'programs' => Program::where('is_active', true)->count(),
        ];

        return view('welcome', compact('programs', 'news', 'teachers', 'stats'));
    }
}