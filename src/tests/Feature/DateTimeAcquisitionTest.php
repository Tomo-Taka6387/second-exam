<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class DateTimeAcquisitionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_current_datetime_is_displayed()
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);

        $response = $this->get('/attendance');
        $response->assertStatus(200);

        $currentDay = now()->format('Y年n月j日');
        $currentTime = now()->format('H:i');

        $response->assertSee($currentDay);
        $response->assertSee($currentTime);
    }
}
