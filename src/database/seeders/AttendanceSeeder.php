<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\AttendanceRecord;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run()
    {
        AttendanceRecord::query()->delete();

        $startDate = Carbon::now()->subMonths(3)->startOfMonth();
        $endDate   = Carbon::now()->subMonth()->endOfMonth();

        $users = User::where('role', 'user')->get();

        foreach ($users as $user) {
            $date = $startDate->copy();

            while ($date->lte($endDate)) {

                if (rand(0, 9) < 2) {
                    $date->addDay();
                    continue;
                }

                $hasSecondBreak = rand(0, 1);

                $clockIn = Carbon::parse($date->toDateString() . ' ' . rand(9, 10) . ':' . rand(0, 59))->format('H:i');
                $clockOut = Carbon::parse($date->toDateString() . ' ' . rand(17, 18) . ':' . rand(0, 59))->format('H:i');
                $breakIn = Carbon::parse($date->toDateString() . ' 12:00')->format('H:i');
                $breakOut = Carbon::parse($date->toDateString() . ' 13:00')->format('H:i');
                $break2In = $hasSecondBreak ? Carbon::parse($date->toDateString() . ' 15:30')->format('H:i') : null;
                $break2Out = $hasSecondBreak ? Carbon::parse($date->toDateString() . ' 15:45')->format('H:i') : null;

                $attendance = AttendanceRecord::create([
                    'user_id'     => $user->id,
                    'date'        => $date->toDateString(),
                    'clock_in'    => $clockIn,
                    'clock_out'   => $clockOut,
                    'break_in'    => $breakIn,
                    'break_out'   => $breakOut,
                    'break2_in'   => $break2In,
                    'break2_out'  => $break2Out,
                    'total_time'       => null,
                    'total_break_time' => null,
                    'comment'          => null,
                ]);

                $date->addDay();
            }
        }
        $allRecords = AttendanceRecord::all();
        foreach ($allRecords as $record) {
            $record->calculateTotals();
            $record->save();
        }
    }
}
