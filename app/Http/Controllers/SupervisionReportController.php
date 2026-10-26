<?php

namespace App\Http\Controllers;

use App\Models\Supervision;
use App\Models\User;
use Illuminate\Http\Request;

class SupervisionReportController extends Controller
{
    protected function staff(): User
    {
        $user = User::find(auth()->id());
        abort_unless($user && $user->is_active && in_array($user->role, ['admin', 'kepala_madrasah', 'guru'], true), 403);

        return $user;
    }

    public function show(int $supervision)
    {
        $record = Supervision::visibleTo($this->staff())->with(['teacher', 'supervisor'])->findOrFail($supervision);

        return response()->view('supervision-report', ['record' => $record])->header('Cache-Control', 'private, no-store');
    }

    public function recap(Request $request)
    {
        $user = $this->staff();
        $filters = $request->validate(['year' => ['nullable', 'regex:/^\d{4}\/\d{4}$/'], 'semester' => 'nullable|in:1,2']);
        $items = Supervision::visibleTo($user)->with(['teacher', 'supervisor'])
            ->when($filters['year'] ?? null, fn ($q, $year) => $q->where('school_year', $year))
            ->when($filters['semester'] ?? null, fn ($q, $semester) => $q->where('semester', $semester))
            ->orderBy('scheduled_at')->get();

        return response()->view('supervision-report', compact('items', 'filters'))->header('Cache-Control', 'private, no-store');
    }
}
