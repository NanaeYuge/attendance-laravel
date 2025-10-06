<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Support\CreatesTestData;

class RequestsTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    public function admin_can_see_pending_and_approved_requests()
    {
        $admin = $this->makeAdmin();
        $u = $this->makeUser();

        $att = $this->makeAttendanceWithBreaks($u, '2025-10-01');

        \DB::table('attendance_requests')->insert([
            'attendance_id' => $att->id, 'note' => 'P1', 'status' => 'pending',
            'payload' => json_encode([]), 'created_at' => now(), 'updated_at' => now(),
        ]);

        \DB::table('attendance_requests')->insert([
            'attendance_id' => $att->id, 'note' => 'A1', 'status' => 'approved',
            'payload' => json_encode([]), 'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->actingAs($admin, 'admin')->get('/admin/requests?tab=pending')
            ->assertOk()->assertSee('P1')->assertDontSee('A1');

        $this->actingAs($admin, 'admin')->get('/admin/requests?tab=approved')
            ->assertOk()->assertSee('A1');
    }

    public function admin_can_view_request_detail()
    {
        $admin = $this->makeAdmin();
        $u = $this->makeUser();
        $att = $this->makeAttendanceWithBreaks($u, '2025-10-02');

        $id = \DB::table('attendance_requests')->insertGetId([
            'attendance_id' => $att->id,
            'note' => '詳細確認',
            'status' => 'pending',
            'payload' => json_encode(['requested_clock_in_at' => '09:10']),
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->actingAs($admin, 'admin')->get("/admin/requests/{$id}")
            ->assertOk()->assertSee('詳細確認');
    }

    public function admin_can_approve_request_and_apply()
    {
        $admin = $this->makeAdmin();
        $u = $this->makeUser();
        $att = $this->makeAttendanceWithBreaks($u, '2025-10-03');

        $id = \DB::table('attendance_requests')->insertGetId([
            'attendance_id' => $att->id,
            'note' => '承認テスト',
            'status' => 'pending',
            'payload' => json_encode([
                'clock_in_at' => '09:05:00',
                'clock_out_at' => '18:02:00',
            ]),
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $res = $this->actingAs($admin, 'admin')->post("/admin/requests/{$id}/approve");
        $res->assertRedirect();

        $this->assertDatabaseHas('attendance_requests', ['id' => $id, 'status' => 'approved']);
        $this->assertDatabaseHas('attendances', [
            'id' => $att->id,
            'clock_in_at' => $att->work_date.' 09:05:00',
            'clock_out_at' => $att->work_date.' 18:02:00',
        ]);
    }
}
