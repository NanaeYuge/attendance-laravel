<?php

namespace Tests\Feature\Attendance;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Support\CreatesTestData;
use App\Models\Attendance;
use Carbon\Carbon;

class BreakTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    public function can_start_break()
    {
        $user = $this->makeUser();
        $att = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'clock_in_at' => now()->copy()->setTime(9,0),
        ]);

        $res = $this->actingAs($user)->post('/attendance/break-start');
        $res->assertRedirect();
        $this->assertDatabaseHas('break_times', [
            'attendance_id' => $att->id,
            'break_end_at' => null,
        ]);
    }

    public function can_start_break_multiple_times()
    {
        $user = $this->makeUser();
        $att = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'clock_in_at' => now()->copy()->setTime(9,0),
        ]);

        $this->actingAs($user)->post('/attendance/break-start');
        $this->actingAs($user)->post('/attendance/break-end');

        $res = $this->actingAs($user)->post('/attendance/break-start');
        $res->assertRedirect();
    }

    public function can_end_break()
    {
        $user = $this->makeUser();
        $att = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'clock_in_at' => now()->copy()->setTime(9,0),
        ]);

        $this->actingAs($user)->post('/attendance/break-start');
        $res = $this->actingAs($user)->post('/attendance/break-end');
        $res->assertRedirect();
    }

    public function can_end_break_multiple_times()
    {
        $user = $this->makeUser();
        $att = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => now()->toDateString(),
            'clock_in_at' => now()->copy()->setTime(9,0),
        ]);

        $this->actingAs($user)->post('/attendance/break-start');
        $this->actingAs($user)->post('/attendance/break-end');
        $this->actingAs($user)->post('/attendance/break-start');

        $res = $this->actingAs($user)->post('/attendance/break-end');
        $res->assertRedirect();
    }

    public function break_times_appear_in_list()
    {
        $user = $this->makeUser();
        $this->makeAttendanceWithBreaks($user, now()->toDateString(), [
            ['start' => '12:30', 'end' => '13:00']
        ]);

        $this->actingAs($user)->get('/attendance')
            ->assertSee('12:30')
            ->assertSee('13:00');
    }
}
