<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Application;
use App\Models\AttendanceRecord;
use App\Http\Requests\AttendanceRequest;

class AdminRequestController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->query('page', 'wait');

        $applications = Application::with('user')
            ->where('approval_status', $page === 'approval' ? 'approved' : 'pending')
            ->orderBy('application_date', 'desc')
            ->get();

        return view('admin.request.index', compact('applications', 'page'));
    }

    public function show($id)
    {
        $application = Application::with('user', 'attendance')->findOrFail($id);
        $attendance = $application->attendance;

        return view('admin.request.show', compact('application', 'attendance'));
    }

    public function store(Request $request)
    {
        $attendance = AttendanceRecord::findOrFail($request->attendance_record_id);

        $attendanceDate = $attendance->date instanceof \Carbon\Carbon
            ? $attendance->date->toDateString()
            : date('Y-m-d', strtotime($attendance->date));

        Application::create([
            'user_id'              => $attendance->user_id,
            'attendance_record_id' => $attendance->id,
            'approval_status'      => 'pending',
            'application_date'     => now()->toDateString(),
            'new_date'             => $attendanceDate,
            'new_clock_in'         => $attendance->clock_in,
            'new_clock_out'        => $attendance->clock_out,
            'new_break_in'         => $attendance->break_in,
            'new_break_out'        => $attendance->break_out,
            'new_break2_in'        => $attendance->break2_in,
            'new_break2_out'       => $attendance->break2_out,
            'comment'              => $attendance->comment,
        ]);
    }

    public function approve(AttendanceRequest $request, $id)
    {
        $application = Application::with('attendance')->findOrFail($id);
        $attendance = $application->attendance;

        $newDate = $request->input('new_date')
            ?: ($application->new_date ?? $attendance->date);

        $application->update([
            'new_date'        => $newDate,
            'new_clock_in'    => $request->input('new_clock_in') ?? $attendance->clock_in,
            'new_clock_out'   => $request->input('new_clock_out') ?? $attendance->clock_out,
            'new_break_in'    => $request->input('new_break_in') ?? $attendance->break_in,
            'new_break_out'   => $request->input('new_break_out') ?? $attendance->break_out,
            'new_break2_in'   => $request->input('new_break2_in') ?? $attendance->break2_in,
            'new_break2_out'  => $request->input('new_break2_out') ?? $attendance->break2_out,
            'comment'         => $request->input('comment') ?? $attendance->comment,
            'approval_status' => 'approved',
        ]);

        $attendance->update([
            'clock_in'    => $application->new_clock_in ?? $attendance->clock_in,
            'clock_out'   => $application->new_clock_out ?? $attendance->clock_out,
            'break_in'    => $application->new_break_in ?? $attendance->break_in,
            'break_out'   => $application->new_break_out ?? $attendance->break_out,
            'break2_in'   => $application->new_break2_in ?? $attendance->break2_in,
            'break2_out'  => $application->new_break2_out ?? $attendance->break2_out,
            'comment'     => $application->comment ?? $attendance->comment,
        ]);

        return redirect()->route('admin.request.show', ['id' => $application->id]);
    }
}
