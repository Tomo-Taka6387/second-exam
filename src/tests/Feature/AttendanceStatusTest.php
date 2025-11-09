<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\AttendanceRecord;

class AttendanceStatusTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_status_not_worked()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('勤務外');
    }

    /** @test */
    public function test_status_working()
    {
        $user = User::factory()->create();
        $today = now()->format('Y-m-d');


        $attendance = AttendanceRecord::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => now()->format('H:i:s'),
            'break_in' => null,
            'break_out' => null,
        ]);


        $attendance = AttendanceRecord::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => now()->format('H:i:s'),
            'break_in' => now()->format('H:i:s'),
            'break_out' => null,
        ]);


        $this->actingAs($user);
        $response = $this->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('出勤中');
    }

    /** @test */
    public function test_status_on_break()
    {
        $user = User::factory()->create();


        AttendanceRecord::create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => now()->subHours(2)->format('H:i:s'),
            'break_in' => now()->subHour()->format('H:i:s'),
            'break_out' => null,
            'clock_out' => null,
        ]);

        $this->actingAs($user);
        $response = $this->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('休憩中');
    }

    /** @test */
    public function test_status_finished()
    {
        $user = User::factory()->create();
        $today = now()->format('Y-m-d');

        AttendanceRecord::create([
            'user_id' => $user->id,
            'date' => $today,
            'clock_in' => now()->subHours(8),
            'clock_out' => now(),
        ]);

        $this->actingAs($user);
        $response = $this->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('退勤済');
    }
}
