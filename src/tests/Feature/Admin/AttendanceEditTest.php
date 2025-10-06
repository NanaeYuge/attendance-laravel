<?php

namespace Tests\Feature\Attendance\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Support\CreatesTestData;
use App\Models\Attendance;

class AttendanceEditTest extends TestCase
{
    use RefreshDatabase, CreatesTestData;

    public function detail_matches_selected()
    {
        $admin = $this->makeAdmin();
        $user = $this->makeUser(['name' => '佐藤']);
        $att = $this->makeAttendanceWithBreaks($user, '2025-10-01');

        $this->actingAs($admin, 'admin')->get("/admin/attendance/{$att->id}")
            ->assertOk()
            ->assertSee('佐藤')
            ->assertSee('2025-10-01');
    }

    protected function patchEdit($admin, $att, $payload)
    {
        return $this->actingAs($admin, 'admin')->patch("/admin/attendance/{$att->id}", $payload);
    }

    public function admin_start_after_end_invalid()
    {
        $admin = $this->makeAdmin();
        $att = $this->makeAttendanceWithBreaks($this->makeUser(), '2025-10-02');

        $res = $this->patchEdit($admin, $att, [
            'clock_in_at' => '19:00',
            'clock_out_at' => '18:00',
            'note' => '修正',
        ]);

        $res->assertSessionHasErrors(['clock_in_at', 'clock_out_at']);
    }

    public function admin_break_start_after_end_invalid()
    {
        $admin = $this->makeAdmin();
        $att = $this->makeAttendanceWithBreaks($this->makeUser(), '2025-10-03');

        $res = $this->patchEdit($admin, $att, [
            'clock_in_at' => '09:00',
            'clock_out_at' => '18:00',
            'breaks' => [
                ['start' => '19:00', 'end' => '19:30'],
            ],
            'note' => '修正',
        ]);

        $res->assertSessionHasErrors(['breaks.0.start', 'breaks.0.end']);
    }


    public function admin_break_end_after_end_invalid_and_note_required()
    {
        $admin = $this->makeAdmin();
        $att = $this->makeAttendanceWithBreaks($this->makeUser(), '2025-10-04');

        $res = $this->patchEdit($admin, $att, [
            'clock_in_at' => '09:00',
            'clock_out_at' => '18:00',
            'breaks' => [
                ['start' => '17:30', 'end' => '19:00'],
            ],
            'note' => '',
        ]);

        $res->assertSessionHasErrors(['breaks.0.end', 'note']);
    }
}
