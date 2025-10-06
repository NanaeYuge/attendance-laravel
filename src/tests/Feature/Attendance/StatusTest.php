<?php

namespace Tests\Feature\Attendance;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;
use Tests\Feature\Support\CreatesTestData;
use App\Models\Attendance;
use App\Models\BreakTime;

class StatusTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    public function status_off_duty()
    {
        $user = $this->makeUser();
        $this->actingAs($user)->get('/attendance')->assertSee('勤務外');
    }

    public function status_working()
    {
        $user = $this->makeUser();
        $today = now()->toDateString();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => $today,
            'clock_in_at' => now()->copy()->setTime(9,0),
            'clock_out_at' => null,
        ]);

        $this->actingAs($user)->get('/attendance')->assertSee('出勤中');
    }

    public function status_breaking()
    {
        $user = $this->makeUser();
        $a = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'clock_in_at' => now()->copy()->setTime(9,0),
            'clock_out_at' => null,
        ]);
        BreakTime::factory()->create([
            'attendance_id' => $a->id,
            'break_start_at' => now()->copy()->setTime(12,0),
            'break_end_at' => null,
        ]);

        $this->actingAs($user)->get('/attendance')->assertSee('休憩中');
    }

    public function status_done()
    {
        $user = $this->makeUser();
        Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'clock_in_at' => now()->copy()->setTime(9,0),
            'clock_out_at' => now()->copy()->setTime(18,0),
        ]);

        $this->actingAs($user)->get('/attendance')->assertSee('退勤済み');
    }
}
