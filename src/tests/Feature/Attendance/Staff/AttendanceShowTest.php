<?php

namespace Tests\Feature\Attendance\Staff;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Support\CreatesTestData;

class AttendanceShowTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    public function detail_shows_user_name()
    {
        $user = $this->makeUser(['name' => '花子']);
        $this->makeAttendanceWithBreaks($user, '2025-10-01');

        $this->actingAs($user)->get('/attendance/2025-10-01')
            ->assertOk()
            ->assertSee('花子');
    }

    public function detail_shows_selected_date()
    {
        $user = $this->makeUser();
        $this->makeAttendanceWithBreaks($user, '2025-10-02');

        $this->actingAs($user)->get('/attendance/2025-10-02')->assertSee('2025-10-02');
    }

    public function detail_shows_clock_in_out_times()
    {
        $user = $this->makeUser();
        $this->makeAttendanceWithBreaks($user, '2025-10-03');

        $this->actingAs($user)->get('/attendance/2025-10-03')
            ->assertSee('09:00')
            ->assertSee('18:00');
    }

    public function detail_shows_break_times()
    {
        $user = $this->makeUser();
        $this->makeAttendanceWithBreaks($user, '2025-10-04', [
            ['start' => '12:30', 'end' => '13:00']
        ]);

        $this->actingAs($user)->get('/attendance/2025-10-04')
            ->assertSee('12:30')
            ->assertSee('13:00');
    }
}
