<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;
use App\Models\User;
use App\Models\AttendanceRecord;


class ClockInTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_off_duty_user_can_see_clock_in_button_and_clock_in_successfully()
    {
        $user = User::factory()->create([
            'attendance_status' => '勤務外',
        ]);

        $this->actingAs($user);

        $response = $this->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('出勤');
        $response = $this->post('/attendance', ['action' => 'clockIn']);
        $response->assertRedirect('/attendance');

        $this->assertDatabaseHas('attendance_records', [
            'user_id' => $user->id,
            'date' => now()->toDateString(),
        ]);

        $user->refresh();
        $this->assertEquals('出勤中', $user->attendance_status);
    }

    /** @test */
    public function test_clock_in_button_is_hidden_for_user_already_clocked_out()
    {
        $user = User::factory()->create([
            'attendance_status' => '退勤済',
        ]);

        AttendanceRecord::factory()->create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => now()->subHours(8)->format('H:i:s'),
            'clock_out' => now()->subHours(1)->format('H:i:s'),
        ]);

        $this->actingAs($user);

        $response = $this->get('/attendance');
        $response->assertStatus(200);
        $response->assertDontSee('出勤');
    }

    /** @test */
    public function test_clock_in_time_is_displayed_in_attendance_list()
    {
        $user = User::factory()->create([
            'attendance_status' => '勤務外',
        ]);

        $this->actingAs($user);
        $this->post('/attendance', ['action' => 'clockIn']);

        $record = AttendanceRecord::where('user_id', $user->id)->first();
        $this->assertNotNull($record);
        $this->assertNotNull($record->clock_in);

        $response = $this->get('/attendance/list');
        $response->assertStatus(200);
        $response->assertSee(now()->format('Y/n'));
        $response->assertSee(Carbon::parse($record->clock_in)->format('H:i'));
    }

    /** @test */
    public function test_user_can_clock_in_only_once_per_day()
    {
        $user = User::factory()->create([
            'attendance_status' => '勤務外',
        ]);

        $this->actingAs($user);


        $this->post('/attendance', ['action' => 'clockIn']);
        $this->assertDatabaseCount('attendance_records', 1);

        $this->post('/attendance', ['action' => 'clockIn']);
        $this->assertDatabaseCount('attendance_records', 1);
    }
}
