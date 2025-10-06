<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Support\CreatesTestData;

class UsersIndexTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    public function admin_can_see_all_users_name_email()
    {
        $admin = $this->makeAdmin();
        $u1 = $this->makeUser(['name' => '一郎', 'email' => 'ichiro@example.com']);
        $u2 = $this->makeUser(['name' => '次郎', 'email' => 'jiro@example.com']);

        $this->actingAs($admin, 'admin')->get('/admin/users')
            ->assertOk()
            ->assertSee('一郎')->assertSee('ichiro@example.com')
            ->assertSee('次郎')->assertSee('jiro@example.com');
    }

    public function admin_user_attendance_month_navigation_and_detail()
    {
        $admin = $this->makeAdmin();
        $u = $this->makeUser(['name' => '三郎']);

        $this->makeAttendanceWithBreaks($u, '2025-08-01');
        $this->makeAttendanceWithBreaks($u, '2025-09-01');
        $this->makeAttendanceWithBreaks($u, '2025-10-01');

        $this->actingAs($admin, 'admin')->get("/admin/users/{$u->id}/attendance?month=2025-09")
            ->assertOk()
            ->assertSee('2025年09月')
            ->assertSee('2025-09-01')
            ->assertDontSee('2025-08-01')
            ->assertDontSee('2025-10-01');

        $this->actingAs($admin, 'admin')->get("/admin/users/{$u->id}/attendance?month=2025-08")
            ->assertSee('2025年08月')->assertSee('2025-08-01');

        $this->actingAs($admin, 'admin')->get("/admin/users/{$u->id}/attendance?month=2025-10")
            ->assertSee('2025年10月')->assertSee('2025-10-01');

        $this->actingAs($admin, 'admin')->get("/admin/attendance/date/2025-10-01/user/{$u->id}")
            ->assertOk()->assertSee('三郎');
    }
}
