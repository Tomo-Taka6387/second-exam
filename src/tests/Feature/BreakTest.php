<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\AttendanceRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class BreakTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_break_in_button_works_correctly()
    {
        $user = User::factory()->create(['attendance_status' => '出勤中']);
        $record = AttendanceRecord::factory()->clockedIn()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->get('/attendance');
        $response->assertSee('休憩入');


        $this->post('/attendance', ['action' => 'breakIn']);

        $record->refresh();
        $this->assertNotNull($record->break_in);
        $this->assertEquals('休憩中', $record->status);
    }

    /** @test */
    public function test_user_can_take_multiple_breaks_in_a_day()
    {
        $user = User::factory()->create(['attendance_status' => '出勤中']);
        $record = AttendanceRecord::factory()->clockedIn()->create(['user_id' => $user->id]);

        $this->actingAs($user);


        $this->post('/attendance', ['action' => 'breakIn']);
        $this->post('/attendance', ['action' => 'breakOut']);


        $response = $this->get('/attendance');
        $response->assertSee('休憩入');
    }

    /** @test */
    public function test_break_out_button_works_correctly()
    {
        $user = User::factory()->create(['attendance_status' => '出勤中']);
        $record = AttendanceRecord::factory()->clockedIn()->create(['user_id' => $user->id]);

        $this->actingAs($user);


        $this->post('/attendance', ['action' => 'breakIn']);
        $record->refresh();
        $this->assertEquals('休憩中', $record->status);

        $this->post('/attendance', ['action' => 'breakOut']);
        $record->refresh();

        $this->assertNotNull($record->break_out);
        $this->assertEquals('出勤中', $record->status);
    }

    /** @test */
    public function test_user_can_return_from_break_multiple_times()
    {
        $user = User::factory()->create(['attendance_status' => '出勤中']);
        $record = AttendanceRecord::factory()->clockedIn()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $this->post('/attendance', ['action' => 'breakIn']);
        $this->post('/attendance', ['action' => 'breakOut']);

        $this->post('/attendance', ['action' => 'breakIn']);
        $response = $this->get('/attendance');
        $response->assertSee('休憩戻');
    }

    /** @test */
    public function test_break_time_is_displayed_in_attendance_list()
    {
        $user = User::factory()->create(['attendance_status' => '出勤中']);
        $record = AttendanceRecord::factory()
            ->clockedIn()
            ->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $this->post('/attendance', ['action' => 'breakIn']);
        $this->post('/attendance', ['action' => 'breakOut']);
        $record->refresh();

        $response = $this->get('/attendance/list');
        $response->assertStatus(200);
        $response->assertSee(Carbon::parse($record->break_in)->format('H:i'));
        $response->assertSee(Carbon::parse($record->break_out)->format('H:i'));
    }
}
