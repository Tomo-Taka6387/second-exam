<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\AttendanceRecord;


class UserAttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function attendance_detail_shows_logged_in_user_name()
    {
        $user = User::factory()->create(['name' => '山田太郎']);
        $attendance = AttendanceRecord::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-11-05',
            'clock_in' => '09:00',
            'clock_out' => '18:00',
        ]);

        $response = $this->actingAs($user)
            ->get(route('attendance.show', ['id' => $attendance->id]));

        $response->assertStatus(200);
        $response->assertSee('山田太郎');
    }

    /** @test */
    public function attendance_detail_shows_selected_date()
    {
        $user = User::factory()->create();
        $attendance = AttendanceRecord::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-11-05',
        ]);

        $response = $this->actingAs($user)
            ->get(route('attendance.show', ['id' => $attendance->id]));

        $response->assertStatus(200);
        $response->assertSee('2025年');
        $response->assertSee('11月5日');
    }

    /** @test */
    public function attendance_detail_shows_correct_clock_in_out_times()
    {
        $user = User::factory()->create();
        $attendance = AttendanceRecord::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-11-05',
            'clock_in' => '09:00',
            'clock_out' => '18:00',
        ]);

        $response = $this->actingAs($user)
            ->get(route('attendance.show', ['id' => $attendance->id]));

        $response->assertStatus(200);
        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    /** @test */
    public function attendance_detail_shows_correct_break_times()
    {
        $user = User::factory()->create();
        $attendance = AttendanceRecord::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-11-05',
            'break_in' => '12:00',
            'break_out' => '13:00',
        ]);

        $response = $this->actingAs($user)
            ->get(route('attendance.show', ['id' => $attendance->id]));

        $response->assertStatus(200);
        $response->assertSee('12:00');
        $response->assertSee('13:00');
    }
}
