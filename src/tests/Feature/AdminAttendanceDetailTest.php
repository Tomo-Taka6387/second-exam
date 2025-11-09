<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\AttendanceRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminAttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->user  = User::factory()->create(['role' => 'user']);
    }

    /** @test */
    public function attendance_detail_displays_selected_data()
    {
        $attendance = AttendanceRecord::factory()->create([
            'user_id' => $this->user->id,
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'break_in' => '12:00',
            'break_out' => '13:00',
            'comment' => '備考テスト',
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.attendance.show', ['id' => $attendance->id]));

        $response->assertStatus(200);
        $response->assertSee($this->user->name);
        $response->assertSee('09:00');
        $response->assertSee('18:00');
        $response->assertSee('12:00');
        $response->assertSee('13:00');
        $response->assertSee('備考テスト');
    }

    /** @test */
    public function shows_error_if_clock_in_after_clock_out()
    {
        $attendance = AttendanceRecord::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->admin, 'admin')
            ->from(route('admin.attendance.show', ['id' => $attendance->id]))
            ->put(route('admin.attendance.update', ['id' => $attendance->id]), [
                'new_clock_in'  => '18:00',
                'new_clock_out' => '09:00',
                'new_break_in'  => '12:00',
                'new_break_out' => '13:00',
                'comment'       => 'テスト',
            ]);

        $response->assertRedirect(route('admin.attendance.show', $attendance->id));
        $response->assertSessionHasErrors(['new_clock_out']);
    }

    /** @test */
    public function shows_error_if_break_in_after_clock_out()
    {
        $attendance = AttendanceRecord::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->admin, 'admin')
            ->from(route('admin.attendance.show', ['id' => $attendance->id]))
            ->put(route('admin.attendance.update', ['id' => $attendance->id]), [
                'new_clock_in'  => '09:00',
                'new_clock_out' => '18:00',
                'new_break_in'  => '19:00',
                'new_break_out' => '20:00',
                'comment'       => 'テスト',
            ]);

        $response->assertRedirect(route('admin.attendance.show', $attendance->id));
        $response->assertSessionHasErrors(['new_break_in']);
    }

    /** @test */
    public function shows_error_if_break_out_after_clock_out()
    {
        $attendance = AttendanceRecord::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->admin, 'admin')
            ->from(route('admin.attendance.show', ['id' => $attendance->id]))
            ->put(route('admin.attendance.update', ['id' => $attendance->id]), [
                'new_clock_in'  => '09:00',
                'new_clock_out' => '18:00',
                'new_break_in'  => '12:00',
                'new_break_out' => '19:00',
                'comment'       => 'テスト',
            ]);

        $response->assertRedirect(route('admin.attendance.show', $attendance->id));
        $response->assertSessionHasErrors(['new_break_out']);
    }

    /** @test */
    public function shows_error_if_comment_is_empty()
    {
        $attendance = AttendanceRecord::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->admin, 'admin')
            ->from(route('admin.attendance.show', ['id' => $attendance->id]))
            ->put(route('admin.attendance.update', ['id' => $attendance->id]), [
                'new_clock_in'  => '09:00',
                'new_clock_out' => '18:00',
                'new_break_in'  => '12:00',
                'new_break_out' => '13:00',
                'comment'       => '',
            ]);

        $response->assertRedirect(route('admin.attendance.show', $attendance->id));
        $response->assertSessionHasErrors(['comment']);
    }
}
