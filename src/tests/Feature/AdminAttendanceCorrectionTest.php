<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Application;
use App\Models\AttendanceRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminAttendanceCorrectionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_displays_pending_correction_requests()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $application = Application::factory()->create([
            'approval_status' => 'pending',
            'comment' => '勤務時間修正',
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.request.index', ['page' => 'wait']));

        $response->assertStatus(200);
        $response->assertSee($application->comment);
    }

    /** @test */
    public function it_displays_approved_correction_requests()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $application = Application::factory()->create([
            'approval_status' => 'approved',
            'comment' => '勤務時間修正済み',
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.request.index', ['page' => 'approval']));

        $response->assertStatus(200);
        $response->assertSee($application->comment);
    }

    /** @test */
    public function it_shows_correction_request_details()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $application = Application::factory()->create([
            'approval_status' => 'pending',
            'comment' => '勤務時間修正',
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.request.show', ['id' => $application->id]));

        $response->assertStatus(200);
        $response->assertSee($application->comment);
    }

    /** @test */
    public function it_can_approve_a_correction_request()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $attendance = AttendanceRecord::factory()->create([
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'break_in' => '12:00',
            'break_out' => '13:00',
        ]);

        $application = Application::factory()->create([
            'attendance_record_id' => $attendance->id,
            'approval_status' => 'pending',
            'new_clock_in' => '09:00',
            'new_clock_out' => '18:00',
            'new_break_in' => '12:00',
            'new_break_out' => '13:00',
            'comment' => '勤務時間修正',
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->post(route('admin.request.approve', ['id' => $application->id]), [
                'new_date' => $application->new_date,
                'new_clock_in' => $application->new_clock_in,
                'new_clock_out' => $application->new_clock_out,
                'new_break_in' => $application->new_break_in,
                'new_break_out' => $application->new_break_out,
                'new_break2_in' => $application->new_break2_in,
                'new_break2_out' => $application->new_break2_out,
                'comment' => $application->comment,
            ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('applications', [
            'id' => $application->id,
            'approval_status' => 'approved',
        ]);

        $this->assertDatabaseHas('attendance_records', [
            'id' => $attendance->id,
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'break_in' => '12:00',
            'break_out' => '13:00',
        ]);
    }
}
