<?php

namespace Database\Factories;

use App\Models\AttendanceRequest;
use App\Models\Attendance;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceRequestFactory extends Factory
{
    protected $model = AttendanceRequest::class;

    public function definition(): array
    {
        $attendance = Attendance::factory()->done()->create();

        return [
            'attendance_id' => $attendance->id,
            'requested_clock_in_at'  => $attendance->clock_in_at,
            'requested_clock_out_at' => $attendance->clock_out_at,
            'payload' => json_encode([
                'clock_in_at'  => optional($attendance->clock_in_at)->format('H:i:s'),
                'clock_out_at' => optional($attendance->clock_out_at)->format('H:i:s'),
                'breaks'       => [],
            ], JSON_UNESCAPED_UNICODE),
            'note'   => '打刻の微修正をお願いします',
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function pending(): self
    {
        return $this->state(fn () => ['status' => 'pending']);
    }

    public function approved(): self
    {
        return $this->state(fn () => ['status' => 'approved']);
    }

    public function rejected(): self
    {
        return $this->state(fn () => ['status' => 'rejected']);
    }
}
