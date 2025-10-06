<?php

namespace Tests\Feature\Attendance;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Support\CreatesTestData;
use App\Models\Attendance;
use Carbon\Carbon;

class ClockInTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    public function clock_in_button_works()
    {
        $user = $this->makeUser();
        Carbon::setTestNow('2025-10-01 09:00:00');

        $res = $this->actingAs($user)->post('/attendance/clock-in');

        $res->assertRedirect();
        $this->assertDatabaseHas('attendances', [
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
        ]);
    }

    public function can_clock_in_only_once_a_day()
    {
        $user = $this->makeUser();
        Carbon::setTestNow('2025-10-01 09:00:00');

        $this->actingAs($user)->post('/attendance/clock-in');
        $res = $this->actingAs($user)->post('/attendance/clock-in');

        $res->assertSessionHasErrors();
    }

    public function clock_in_time_shown_in_index()
    {
        $user = $this->makeUser();
        Carbon::setTestNow('2025-10-01 09:00:00');
        $this->actingAs($user)->post('/attendance/clock-in');

        $this->actingAs($user)->get('/attendance')
            ->assertSee('09:00');
    }
}
