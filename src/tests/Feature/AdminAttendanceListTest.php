<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\AttendanceRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class AdminAttendanceListTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_displays_all_users_attendance_for_today()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user1 = User::factory()->create(['name' => '山田 太郎']);
        $user2 = User::factory()->create(['name' => '佐々木 健一']);

        $today = Carbon::today()->format('Y-m-d');

        AttendanceRecord::factory()->create([
            'user_id' => $user1->id,
            'date' => $today,
            'clock_in' => '09:00',
            'clock_out' => '18:00',
        ]);

        AttendanceRecord::factory()->create([
            'user_id' => $user2->id,
            'date' => $today,
            'clock_in' => '10:00',
            'clock_out' => '19:00',
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.attendance.index', ['day' => $today]));

        $response->assertStatus(200);
        $response->assertSee('山田 太郎');
        $response->assertSee('佐々木 健一');
        $response->assertSee('09:00');
        $response->assertSee('18:00');
        $response->assertSee('10:00');
        $response->assertSee('19:00');
    }

    /** @test */
    public function it_shows_current_date_on_attendance_list()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $today = Carbon::today()->format('Y-m-d');

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.attendance.index', ['day' => $today]));

        $response->assertStatus(200);
        $response->assertSee(Carbon::parse($today)->format('Y/m/d'));
    }

    /** @test */
    public function it_displays_previous_day_attendance()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['name' => '山田 太郎']);
        $yesterday = Carbon::yesterday()->format('Y-m-d');

        AttendanceRecord::factory()->create([
            'user_id' => $user->id,
            'date' => $yesterday,
            'clock_in' => '08:30',
            'clock_out' => '17:30',
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.attendance.index', ['day' => $yesterday]));

        $response->assertStatus(200);
        $response->assertSee('山田 太郎');
        $response->assertSee('08:30');
        $response->assertSee('17:30');
        $response->assertSee(Carbon::parse($yesterday)->format('Y/m/d'));
    }

    /** @test */
    public function it_displays_next_day_attendance()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['name' => '佐々木 健一']);
        $tomorrow = Carbon::tomorrow()->format('Y-m-d');

        AttendanceRecord::factory()->create([
            'user_id' => $user->id,
            'date' => $tomorrow,
            'clock_in' => '10:00',
            'clock_out' => '19:00',
        ]);

        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.attendance.index', ['day' => $tomorrow]));

        $response->assertStatus(200);
        $response->assertSee('佐々木 健一');
        $response->assertSee('10:00');
        $response->assertSee('19:00');
        $response->assertSee(Carbon::parse($tomorrow)->format('Y/m/d'));
    }
}
