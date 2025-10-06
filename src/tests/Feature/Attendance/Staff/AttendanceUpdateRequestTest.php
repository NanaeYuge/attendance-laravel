<?php

namespace Tests\Feature\Attendance\Staff;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Support\CreatesTestData;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceUpdateRequestTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    protected function submitRequest($user, $payload)
    {

        return $this->actingAs($user)->post('/attendance/request', $payload);
    }

    public function start_after_end_is_invalid()
    {
        $user = $this->makeUser();
        $att = $this->makeAttendanceWithBreaks($user, '2025-10-05');

        $res = $this->submitRequest($user, [
            'attendance_id' => $att->id,
            'clock_in_at' => '19:00',
            'clock_out_at' => '18:00',
            'note' => '修正お願いします',
        ]);

        $res->assertSessionHasErrors(['clock_in_at', 'clock_out_at']);
    }

    public function break_start_after_end_is_invalid()
    {
        $user = $this->makeUser();
        $att = $this->makeAttendanceWithBreaks($user, '2025-10-06');

        $res = $this->submitRequest($user, [
            'attendance_id' => $att->id,
            'clock_in_at' => '09:00',
            'clock_out_at' => '18:00',
            'breaks' => [
                ['start' => '19:00', 'end' => '19:30'],
            ],
            'note' => '修正お願いします',
        ]);

        $res->assertSessionHasErrors(['breaks.0.start', 'breaks.0.end']);
    }

    public function break_end_after_end_is_invalid()
    {
        $user = $this->makeUser();
        $att = $this->makeAttendanceWithBreaks($user, '2025-10-07');

        $res = $this->submitRequest($user, [
            'attendance_id' => $att->id,
            'clock_in_at' => '09:00',
            'clock_out_at' => '18:00',
            'breaks' => [
                ['start' => '17:30', 'end' => '19:00'],
            ],
            'note' => '修正お願いします',
        ]);

        $res->assertSessionHasErrors(['breaks.0.end']);
    }

    public function note_is_required()
    {
        $user = $this->makeUser();
        $att = $this->makeAttendanceWithBreaks($user, '2025-10-08');

        $res = $this->submitRequest($user, [
            'attendance_id' => $att->id,
            'clock_in_at' => '09:00',
            'clock_out_at' => '18:00',
            'breaks' => [],
            'note' => '',
        ]);

        $res->assertSessionHasErrors(['note']);
    }

    public function request_is_created()
    {
        $user = $this->makeUser();
        $att = $this->makeAttendanceWithBreaks($user, '2025-10-09');

        $res = $this->submitRequest($user, [
            'attendance_id' => $att->id,
            'clock_in_at' => '09:10',
            'clock_out_at' => '18:05',
            'breaks' => [['start' => '12:30', 'end' => '13:05']],
            'note' => '出勤遅れを修正',
        ]);

        $res->assertRedirect();
        $this->assertDatabaseHas('attendance_requests', [
            'attendance_id' => $att->id,
            'status' => 'pending',
            'note' => '出勤遅れを修正',
        ]);
    }

    public function requests_tabs_and_detail_link()
    {
        $user = $this->makeUser();
        $this->makeAttendanceWithBreaks($user, '2025-10-10');
        \DB::table('attendance_requests')->insert([
            'attendance_id' => \App\Models\Attendance::first()->id,
            'requested_clock_in_at' => now()->copy()->setTime(9,10),
            'requested_clock_out_at' => now()->copy()->setTime(18,5),
            'payload' => json_encode([]),
            'note' => 'pending申請',
            'status' => 'pending',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        \DB::table('attendance_requests')->insert([
            'attendance_id' => \App\Models\Attendance::first()->id,
            'requested_clock_in_at' => now()->copy()->setTime(9,0),
            'requested_clock_out_at' => now()->copy()->setTime(18,0),
            'payload' => json_encode([]),
            'note' => 'approved申請',
            'status' => 'approved',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->actingAs($user)->get('/requests?tab=pending')
            ->assertOk()
            ->assertSee('pending申請')
            ->assertDontSee('approved申請');

        $this->actingAs($user)->get('/requests?tab=approved')
            ->assertOk()
            ->assertSee('approved申請');

        $this->actingAs($user)->get('/attendance/2025-10-10')->assertOk();
    }
}
