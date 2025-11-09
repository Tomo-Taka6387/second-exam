<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\AttendanceRecord;
use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserAttendanceUpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_shows_error_when_clock_in_is_after_clock_out()
    {
        $user = User::factory()->create();
        $attendance = AttendanceRecord::factory()->create(['user_id' => $user->id]);


        $response = $this->actingAs($user)
            ->from(route('attendance.show', $attendance->id))
            ->post(route('applications.store', $attendance->id), [
                'new_clock_in' => '18:00',
                'new_clock_out' => '09:00',
                'comment' => 'テストコメント',
            ]);

        $response->assertRedirect(route('attendance.show', $attendance->id));
        $response->assertSessionHasErrors(['new_clock_out']);
    }

    /** @test */
    public function it_shows_error_when_break_in_is_after_clock_out()
    {
        $user = User::factory()->create();
        $attendance = AttendanceRecord::factory()->create(['user_id' => $user->id]);


        $response = $this->actingAs($user)
            ->from(route('attendance.show', $attendance->id))
            ->post(route('applications.store', $attendance->id), [
                'new_clock_in' => '09:00',
                'new_clock_out' => '18:00',
                'new_break_in' => '19:00',
                'new_break_out' => '20:00',
                'comment' => 'テストコメント',
            ]);

        $response->assertRedirect(route('attendance.show', $attendance->id));
        $response->assertSessionHasErrors(['new_break_in']);
    }

    /** @test */
    public function it_shows_error_when_break_out_is_after_clock_out()
    {
        $user = User::factory()->create();
        $attendance = AttendanceRecord::factory()->create(['user_id' => $user->id]);


        $response = $this->actingAs($user)
            ->from(route('attendance.show', $attendance->id))
            ->post(route('applications.store', $attendance->id), [
                'new_clock_in' => '09:00',
                'new_clock_out' => '18:00',
                'new_break_in' => '12:00',
                'new_break_out' => '19:00',
                'comment' => 'テストコメント',
            ]);

        $response->assertRedirect(route('attendance.show', $attendance->id));
        $response->assertSessionHasErrors(['new_break_out']);
    }

    /** @test */
    public function it_shows_error_when_comment_is_missing()
    {
        $user = User::factory()->create();
        $attendance = AttendanceRecord::factory()->create(['user_id' => $user->id]);


        $response = $this->actingAs($user)
            ->from(route('attendance.show', $attendance->id))
            ->post(route('applications.store', $attendance->id), [
                'new_clock_in' => '09:00',
                'new_clock_out' => '18:00',
            ]);

        $response->assertRedirect(route('attendance.show', $attendance->id));
        $response->assertSessionHasErrors(['comment']);
    }

    /** @test */
    public function it_creates_a_pending_application_successfully()
    {
        $user = User::factory()->create();
        $attendance = AttendanceRecord::factory()->create(['user_id' => $user->id]);


        $response = $this->actingAs($user)
            ->post(route('applications.store', $attendance->id), [
                'new_clock_in' => '09:00',
                'new_clock_out' => '18:00',
                'comment' => 'テストコメント',
            ]);

        $response->assertRedirect(route('attendance.show', $attendance->id));

        $this->assertDatabaseHas('applications', [
            'user_id' => $user->id,
            'attendance_record_id' => $attendance->id,
            'approval_status' => 'pending',
        ]);
    }

    /** @test */
    public function it_displays_pending_applications_on_pending_list()
    {
        $user = User::factory()->create();
        $pendingApp = Application::factory()->create([
            'user_id' => $user->id,
            'approval_status' => 'pending',
            'comment' => '承認待ちコメント',
        ]);

        $response = $this->actingAs($user)
            ->get(route('applications.index', ['status' => 'pending']));

        $response->assertStatus(200);
        $response->assertSee('承認待ちコメント');
    }

    /** @test */
    public function it_displays_approved_applications_on_approved_list()
    {
        $user = User::factory()->create();
        $approvedApp = Application::factory()->create([
            'user_id' => $user->id,
            'approval_status' => 'approved',
            'comment' => '承認済みコメント',
        ]);

        $response = $this->actingAs($user)
            ->get('/stamp_correction_request/list?page=approval');


        $response->assertStatus(200);
        $response->assertSee('承認済みコメント');
    }

    /** @test */
    public function it_navigates_to_attendance_detail_from_application_detail_button()
    {
        $user = User::factory()->create();
        $attendance = AttendanceRecord::factory()->create(['user_id' => $user->id]);
        $application = Application::factory()->create([
            'user_id' => $user->id,
            'attendance_record_id' => $attendance->id,
        ]);

        $response = $this->actingAs($user)
            ->get(route('attendance.show', $attendance->id));

        $response->assertStatus(200);
    }
}
