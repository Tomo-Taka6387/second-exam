<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\AttendanceRecord;
use App\Models\User;
use Carbon\Carbon;

class AttendanceRecordFactory extends Factory
{
    protected $model = AttendanceRecord::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'date' => Carbon::now()->toDateString(),
            'clock_in' => null,
            'break_in' => null,
            'break_out' => null,
            'break2_in' => null,
            'break2_out' => null,
            'clock_out' => null,
            'total_break_time' => '00:00',
            'total_time' => '00:00',
        ];
    }

    public function clockedIn()
    {
        return $this->state(function () {
            return [
                'clock_in' => Carbon::now()->format('H:i:s'),
                'clock_out' => null,
            ];
        });
    }


    public function clockedOut()
    {
        return $this->state(function () {
            return [
                'clock_in' => Carbon::now()->subHours(8)->format('H:i:s'),
                'clock_out' => Carbon::now()->format('H:i:s'),
                'total_time' => '08:00',
            ];
        });
    }
}
