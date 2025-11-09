<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\AttendanceRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class ClockOutTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function clock_out_button_is_displayed_and_works()
    {

        $user = User::factory()->create([
            'attendance_status' => '勤務中',
        ]);

        AttendanceRecord::factory()->create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => now()->subHours(3),
            'clock_out' => null,
        ]);

        $this->actingAs($user);


        $response = $this->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('退勤');

        $this->post('/attendance', ['action' => 'clockOut']);

        $response = $this->get('/attendance');
        $response->assertSee('退勤済');

        $user->refresh();
        $this->assertEquals('退勤済', $user->attendance_status);
    }

    /** @test */
    public function clock_out_time_can_be_seen_in_attendance_list()
    {
        $user = User::factory()->create([
            'attendance_status' => '勤務外',
        ]);

        $this->actingAs($user);

        $this->post('/attendance', ['action' => 'clockIn']);

        $this->post('/attendance', ['action' => 'clockOut']);


        $response = $this->get('/attendance/list');
        $response->assertStatus(200);

        $record = AttendanceRecord::where('user_id', $user->id)
            ->where('date', now()->toDateString())
            ->first();

        $this->assertNotNull($record);
        $this->assertNotNull($record->clock_out);

        $response->assertSee(Carbon::parse($record->clock_out)->format('H:i'));
    }
}