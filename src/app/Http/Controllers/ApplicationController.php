<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AttendanceRequest;
use App\Models\Application;
use App\Models\AttendanceRecord;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $page = $request->query('page', 'wait');

        $query = Application::with('user')
            ->when($page === 'wait', fn($q) => $q->where('approval_status', 'pending'))
            ->when($page === 'approval', fn($q) => $q->where('approval_status', 'approved'));

        if ($user->role === 'user') {
            $query->where('user_id', $user->id);
        }

        $applications = $query
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.application.index', compact('applications', 'page'));
    }

    public function store(AttendanceRequest $request, $id)
    {
        $user = Auth::user();
        $attendance = AttendanceRecord::findOrFail($id);


        if ($attendance->user_id !== $user->id) {
            abort(403);
        }

        $existing = Application::where('attendance_record_id', $attendance->id)
            ->where('user_id', $user->id)
            ->where('approval_status', 'pending')
            ->first();

        if ($existing) {
            return redirect()->route('attendance.show', $attendance->id);
        }

        $validated = $request->validated();

        Application::create([
            'user_id' => $user->id,
            'attendance_record_id' => $id,
            'approval_status' => 'pending',
            'application_date' => now()->toDateString(),
            'new_date' => $attendance->date,
            'new_clock_in' => $validated['new_clock_in'],
            'new_clock_out' => $validated['new_clock_out'],
            'new_break_in' => $validated['new_break_in'] ?? null,
            'new_break_out' => $validated['new_break_out'] ?? null,
            'new_break2_in' => $validated['new_break2_in'] ?? null,
            'new_break2_out' => $validated['new_break2_out'] ?? null,
            'comment' => $validated['comment'],
        ]);

        return redirect()->route('attendance.show', $attendance->id);
    }
}
