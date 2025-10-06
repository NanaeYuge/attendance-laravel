<?php

namespace Tests\Feature\Attendance\Staff;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Support\CreatesTestData;
use Carbon\Carbon;

class AttendanceIndexTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    public function shows_all_my_attendances()
    {
        $user = $this->makeUser();
        $other = $this->makeUser();

        $this->makeAttendanceWithBreaks($user, '2025-09-01');
        $this->makeAttendanceWithBreaks($user, '2025-09-02');
        $this->makeAttendanceWithBreaks($other, '2025-09-03');

        $this->actingAs($user)->get('/attendance')
            ->assertOk()
            ->assertSee('2025-09-01')
            ->assertSee('2025-09-02')
            ->assertDontSee('2025-09-03');
    }

    public function current_month_is_shown_on_load()
    {
        $user = $this->makeUser();
        Carbon::setTestNow('2025-10-01 10:00:00');

        $this->actingAs($user)->get('/attendance')->assertSee('2025年10月');
    }

    public function prev_month_shows_prev_data()
    {
        $user = $this->makeUser();
        $this->makeAttendanceWithBreaks($user, '2025-08-15');

        $this->actingAs($user)->get('/attendance?month=2025-08')
            ->assertSee('2025年08月')
            ->assertSee('2025-08-15');
    }

    public function next_month_shows_next_data_and_can_go_detail()
    {
        $user = $this->makeUser();
        $this->makeAttendanceWithBreaks($user, '2025-11-10');

        $index = $this->actingAs($user)->get('/attendance?month=2025-11');
        $index->assertSee('2025年11月')->assertSee('2025-11-10');

        $show = $this->actingAs($user)->get('/attendance/2025-11-10');
        $show->assertOk()->assertSee('2025-11-10');
    }
}
