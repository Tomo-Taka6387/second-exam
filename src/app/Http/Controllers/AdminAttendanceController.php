<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\AttendanceRequest;
use App\Models\AttendanceRecord;
use App\Models\Application;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AdminAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->query('day') ? Carbon::parse($request->query('day')) : Carbon::today();

        $prevDay = $date->copy()->subDay()->format('Y-m-d');
        $nextDay = $date->copy()->addDay()->format('Y-m-d');

        $attendances = AttendanceRecord::whereDate('date', $date)
            ->with('user')
            ->get();

        return view('admin.attendance.index', compact('attendances', 'date', 'prevDay', 'nextDay'));
    }


    public function show($id)
    {
        $user = Auth::guard('admin')->user();
        $attendance = AttendanceRecord::with('user')->find($id);

        if (!$attendance) {
            $attendance = new AttendanceRecord([
                'id' => $id,
                'date' => null,
                'clock_in' => null,
                'clock_out' => null,
                'break_in' => null,
                'break_out' => null,
                'break2_in' => null,
                'break2_out' => null,
                'comment' => null,
            ]);
        }

        $application = Application::where('attendance_record_id', $id)->latest()->first();

        return view('admin.attendance.show', compact('attendance', 'user', 'application'));
    }

    public function showStaffAttendance(User $user, Request $request)
    {
        $monthStr = $request->query('month', now()->format('Y-m'));
        [$year, $month] = explode('-', $monthStr);

        $start = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $records = AttendanceRecord::where('user_id', $user->id)
            ->whereBetween('date', [$start, $end])
            ->orderBy('date', 'asc')
            ->get();


        if ($request->query('export') === 'csv') {
            $fileName = "{$year}-{$month}_{$user->name}_attendance.csv";
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename={$fileName}",
            ];

            $callback = function () use ($records) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['日付', '出勤時刻', '退勤時刻', '休憩開始', '休憩終了', '休憩2開始', '休憩2終了', '備考']);
                foreach ($records as $record) {
                    fputcsv($handle, [
                        $record->date,
                        $record->clock_in,
                        $record->clock_out,
                        $record->break_in,
                        $record->break_out,
                        $record->break2_in,
                        $record->break2_out,
                        $record->comment,
                    ]);
                }
                fclose($handle);
            };

            return response()->stream($callback, 200, $headers);
        }

        $attendances = [];
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $record = $records->first(function ($r) use ($date) {
                return Carbon::parse($r->date)->isSameDay($date);
            });
            $attendances[$date->format('Y-m-d')] = $record;
        }

        $prevMonth = $start->copy()->subMonth()->format('Y-m');
        $nextMonth = $start->copy()->addMonth()->format('Y-m');

        return view('admin.staff.attendance', compact(
            'user',
            'attendances',
            'year',
            'month',
            'prevMonth',
            'nextMonth'
        ));
    }

    public function update(AttendanceRequest $request, $id)
    {
        $attendance = AttendanceRecord::find($id);

        if (!$attendance) {
            $attendance = new AttendanceRecord();
            $attendance->user_id = $request->input('user_id');
            $attendance->date = $request->input('date');
        }

        $data = [
            'clock_in'   => $request->input('new_clock_in') ?: null,
            'clock_out'  => $request->input('new_clock_out') ?: null,
            'break_in'   => $request->input('new_break_in') ?: null,
            'break_out'  => $request->input('new_break_out') ?: null,
            'break2_in'  => $request->input('new_break2_in') ?: null,
            'break2_out' => $request->input('new_break2_out') ?: null,
            'comment'    => $request->input('comment'),
        ];

        $attendance->fill($data);
        $attendance->save();

        return redirect()->route('admin.attendance.show', ['id' => $attendance->id]);
    }
}
