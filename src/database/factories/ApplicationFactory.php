<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Application;
use App\Models\User;
use App\Models\AttendanceRecord;

class ApplicationFactory extends Factory
{
    protected $model = Application::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'attendance_record_id' => AttendanceRecord::factory(),
            'approval_status' => 'pending',
            'application_date' => now()->toDateString(),
            'new_date' => now()->toDateString(),
            'new_clock_in' => '09:00',
            'new_clock_out' => '18:00',
            'new_break_in' => '12:00',
            'new_break_out' => '13:00',
            'new_break2_in' => null,
            'new_break2_out' => null,
            'comment' => $this->faker->sentence(3),
        ];
    }
}
