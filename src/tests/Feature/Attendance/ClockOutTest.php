<?php

namespace Tests\Feature\Attendance;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Support\CreatesTestData;
use App\Models\Attendance;
use Carbon\Carbon;

class ClockOutTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    public function can_clock_out()
    {
        $user = $this->makeUser();
        $att = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'clock_in_at' => now()->copy()->setTime(9,0),
            'clock_out_at' => null,
        ]);

        $res = $this->actingAs($user)->post('/attendance/clock-out');
        $res->assertRedirect();

        $this->assertNotNull($att->fresh()->clock_out_at);
    }

    public function clock_out_time_shown_in_index()
    {
        $user = $this->makeUser();
        $att = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'clock_in_at' => now()->copy()->setTime(9,0),
            'clock_out_at' => now()->copy()->setTime(18,0),
        ]);

        $this->actingAs($user)->get('/attendance')->assertSee('18:00');
    }
}
