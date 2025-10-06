<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Admin;

class AttendanceFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
        $this->seed();
    }

    public function test_user_can_clock_and_see_list(): void
    {
        $user = User::first();
        $this->actingAs($user);

        $this->get('/attendance')->assertStatus(200);
        $this->post('/attendance/clock-in')->assertRedirect();
        $this->post('/attendance/start-break')->assertRedirect();
        $this->post('/attendance/end-break')->assertRedirect();
        $this->post('/attendance/clock-out')->assertRedirect();

        $this->get('/attendance/list')->assertStatus(200)->assertSee('勤怠一覧');
    }

    public function test_admin_can_download_csv(): void
    {
        $admin = Admin::first();
        $this->actingAs($admin, 'admin');

        $user = \App\Models\User::first();
        $ym = now()->format('Y-m');
        $res = $this->get("/admin/attendance/staff/{$user->id}/csv?ym={$ym}");
        $res->assertStatus(200);
        $this->assertStringStartsWith('text/csv', $res->headers->get('content-type'));
    }
}
