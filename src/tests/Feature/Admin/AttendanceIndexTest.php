<?php

namespace Tests\Feature\Attendance\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Support\CreatesTestData;

class AttendanceIndexTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    public function shows_all_users_attendance_of_day()
    {
        $admin = $this->makeAdmin();
        $u1 = $this->makeUser(['name' => 'Aさん']);
        $u2 = $this->makeUser(['name' => 'Bさん']);

        $this->makeAttendanceWithBreaks($u1, '2025-10-01');
        $this->makeAttendanceWithBreaks($u2, '2025-10-01');

        $this->actingAs($admin, 'admin')->get('/admin/attendance?date=2025-10-01')
            ->assertOk()
            ->assertSee('Aさん')
            ->assertSee('Bさん');
    }

    public function current_date_shown_on_load()
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin, 'admin')->get('/admin/attendance')->assertSee(now()->toDateString());
    }

    public function prev_day_shows_prev()
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin, 'admin')->get('/admin/attendance?date=2025-09-30')
            ->assertSee('2025-09-30');
    }

    public function next_day_shows_next()
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin, 'admin')->get('/admin/attendance?date=2025-10-02')
            ->assertSee('2025-10-02');
    }
}
