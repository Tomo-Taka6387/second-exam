<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\AttendanceRecord;
use App\Models\Application;


class AttendanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = now()->toDateString();

        $attendance = AttendanceRecord::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            $user->attendance_status = '勤務外';
        } elseif ($attendance->clock_out) {
            $user->attendance_status = '退勤済';
        } elseif ($attendance->break_in && !$attendance->break_out) {
            $user->attendance_status = '休憩中';
        } elseif ($attendance->clock_in) {
            $user->attendance_status = '出勤中';
        } else {
            $user->attendance_status = '勤務外';
        }

        $user->save();

        return view('user.attendance.create', compact('attendance', 'user'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $action = $request->input('action');
        $today = now()->toDateString();

        $attendance = AttendanceRecord::firstOrNew(
            [
                'user_id' => $user->id,
                'date' => $today,
            ]
        );

        switch ($action) {
            case 'clockIn':
                if (!$attendance->clock_in) {
                    $attendance->clock_in = now()->format('H:i:s');
                    $attendance->save();
                }
                break;

            case 'breakIn':
                if (!$attendance->break_in) {
                    $attendance->break_in = now()->format('H:i:s');
                } elseif (!$attendance->break2_in) {
                    $attendance->break2_in = now()->format('H:i:s');
                }
                $attendance->save();
                break;

            case 'breakOut':
                if ($attendance->break_in && !$attendance->break_out) {
                    $attendance->break_out = now()->format('H:i:s');
                } elseif ($attendance->break2_in && !$attendance->break2_out) {
                    $attendance->break2_out = now()->format('H:i:s');
                }
                $attendance->save();
                break;

            case 'clockOut':
                if (!$attendance->clock_out) {
                    $attendance->clock_out = now()->format('H:i:s');
                }
                $attendance->save();
                break;

            default:
                break;
        }



        $attendance->refresh();

        if ($attendance->clock_out) {
            $user->attendance_status = '退勤済';
        } elseif (($attendance->break2_in && !$attendance->break2_out) || ($attendance->break_in && !$attendance->break_out)) {
            $user->attendance_status = '休憩中';
        } elseif ($attendance->clock_in) {
            $user->attendance_status = '出勤中';
        } else {
            $user->attendance_status = '勤務外';
        }
        $user->save();



        return redirect()
            ->route('attendance.index')
            ->with('message', $this->getMessage($action));
    }

    private function getMessage($action)
    {
        switch ($action) {
            case 'clockIn':
                return '出勤しました。';
            case 'clockOut':
                return 'お疲れ様でした。';
            case 'breakIn':
                return '休憩に入りました。';
            case 'breakOut':
                return '休憩を終了しました。';
            default:
                return '勤怠を更新しました。';
        }
    }


    public function list(Request $request)
    {
        $user = auth()->user();

        $monthStr = $request->query('month', now()->format('Y-m'));
        [$year, $month] = explode('-', $monthStr);

        $start = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $attendances = [];

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $record = AttendanceRecord::where('user_id', $user->id)
                ->where('date', $date->toDateString())
                ->first();

            if (!$record) {
                $record = new AttendanceRecord(['date' => $date->toDateString()]);
            }

            $attendances[$date->format('Y-m-d')] = $record;
        }

        $prevMonth = $start->copy()->subMonth()->format('Y-m');
        $nextMonth = $start->copy()->addMonth()->format('Y-m');

        return view('user.attendance.index', compact('attendances', 'year', 'month', 'prevMonth', 'nextMonth'));
    }
    public function show($id, Request $request)
    {
        if ($id == 0) {
            $date = $request->query('date');
            $user = auth()->user();
            $attendance = AttendanceRecord::where('user_id', $user->id)
                ->where('date', $date)
                ->first();

            if (!$attendance) {
                $attendance = new AttendanceRecord([
                    'user_id' => $user->id,
                    'date' => $date,
                ]);
            }

            $application = null;
            return view('user.attendance.show', compact('attendance', 'user', 'application'));
        }

        $attendance = AttendanceRecord::findOrFail($id);
        $user = auth()->user();
        $application = Application::where('attendance_record_id', $attendance->id)
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        if ($attendance->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403);
        }

        return view('user.attendance.show', compact('attendance', 'user', 'application'));
    }



    public function update(Request $request, $id)
    {
        $attendance = AttendanceRecord::findOrFail($id);

        if ($attendance->user_id !== auth()->id() && auth()->user()->role !== 'admin') {
            abort(403);
        }

        if (auth()->user()->role !== 'admin') {
            $application = Application::where('attendance_record_id', $attendance->id)
                ->where('user_id', auth()->id())
                ->latest()
                ->first();

            if ($application && in_array($application->approval_status, ['pending', 'approved'])) {
                return redirect()
                    ->back();
            }
        }

        $attendance->status = 'pending_edit';
        $attendance->save();

        return redirect()->route('attendance.show', $attendance->id);
    }
}
