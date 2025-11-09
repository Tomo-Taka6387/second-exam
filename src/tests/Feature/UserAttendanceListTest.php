<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\AttendanceRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class UserAttendanceListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_user_can_see_all_of_their_attendance_records()
    {

        $user = User::factory()->create();
        $other = User::factory()->create();

        AttendanceRecord::factory()->create([
            'user_id' => $user->id,
            'date' => '2025-11-07',
        ]);
        AttendanceRecord::factory()->create([
            'user_id' => $other->id,
            'date' => '2025-11-08',
        ]);

        $response = $this->actingAs($user)->get(route('attendance.list'));
        $response->assertSee('11/07');
    }

    /** @test */
    public function test_current_month_is_displayed_when_opening_attendance_list()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/attendance/list');

        $response->assertStatus(200);
        $response->assertSee(now()->format('Y'));
        $response->assertSee(now()->format('m'));
    }

    /** @test */
    public function test_previous_month_button_displays_previous_month_data()
    {
        $user = User::factory()->create();
        $previousMonth = now()->subMonth();

        $attendancePrev = AttendanceRecord::factory()->create([
            'user_id' => $user->id,
            'date' => $previousMonth->copy()->startOfMonth(),
            'clock_in' => '09:00',
            'clock_out' => '17:00',
        ]);

        $response = $this->actingAs($user)
            ->get('/attendance/list?month=' . $previousMonth->format('Y-m'));

        $response->assertStatus(200);
        $response->assertSee($previousMonth->format('Y'));
        $response->assertSee($previousMonth->format('m'));
    }

    /** @test */
    public function test_next_month_button_displays_next_month_data()
    {
        $user = User::factory()->create();
        $nextMonth = now()->addMonth();

        AttendanceRecord::factory()->create([
            'user_id' => $user->id,
            'date' => Carbon::create(2025, 11, 1),
            'clock_in' => '09:00',
            'clock_out' => '18:00',
        ]);

        $response = $this->actingAs($user)
            ->get('/attendance/list?month=' . $nextMonth->format('Y-m'));

        $response->assertStatus(200);
        $response->assertSee($nextMonth->format('Y'));
        $response->assertSee($nextMonth->format('m'));
    }

    /** @test */
    public function test_detail_button_redirects_to_attendance_detail_page()
    {
        $user = User::factory()->create();
        $attendance = AttendanceRecord::factory()->create([
            'user_id' => $user->id,
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'date' => now(),
        ]);

        $listResponse = $this->actingAs($user)->get('/attendance/list');
        $listResponse->assertStatus(200);
        $listResponse->assertSee('詳細');

        $detailResponse = $this->actingAs($user)->get('/attendance/detail/' . $attendance->id);
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('勤怠詳細');
    }
}
