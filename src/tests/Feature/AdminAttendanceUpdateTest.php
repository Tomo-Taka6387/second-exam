<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\AttendanceRecord;

class AdminAttendanceUpdateTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        $this->user = User::factory()->create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'role' => 'user',
        ]);
    }

    /** @test */
    public function admin_can_view_all_users_names_and_emails()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.staff.index'));

        $response->assertStatus(200);
        $response->assertSee($this->user->name);
        $response->assertSee($this->user->email);
    }

    /** @test */
    public function admin_can_view_users_attendance_correctly()
    {
        $attendance = AttendanceRecord::factory()->create([
            'user_id' => $this->user->id,
            'date' => '2025-07-01',
            'clock_in' => '09:00',
            'clock_out' => '18:00',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.staff.attendances', [
                'user' => $this->user->id,
                'month' => '2025-07'
            ]));

        $response->assertStatus(200);
        $response->assertSee('07/01');
        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    /** @test */
    public function previous_month_button_displays_previous_month_attendance()
    {
        AttendanceRecord::factory()->create([
            'user_id' => $this->user->id,
            'date' => '2025-08-15',
            'clock_in' => '09:00',
            'clock_out' => '18:00',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.staff.attendances', [
                'user' => $this->user->id,
                'month' => '2025-08'
            ]));

        $response->assertStatus(200);
        $response->assertSee('08/15');
    }

    /** @test */
    public function next_month_button_displays_next_month_attendance()
    {
        AttendanceRecord::factory()->create([
            'user_id' => $this->user->id,
            'date' => '2025-09-01',
            'clock_in' => '09:00',
            'clock_out' => '18:00',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.staff.attendances', [
                'user' => $this->user->id,
                'month' => '2025-09'
            ]));

        $response->assertStatus(200);
        $response->assertSee('09/01');
    }

    /** @test */
    public function detail_button_redirects_to_attendance_detail_page()
    {
        $attendance = AttendanceRecord::factory()->create([
            'user_id' => $this->user->id,
            'date' => '2025-08-05',
            'clock_in' => '09:00',
            'clock_out' => '18:00',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.attendance.show', ['id' => $attendance->id]));

        $response->assertStatus(200);
        $response->assertViewIs('admin.attendance.show');
        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }
}
