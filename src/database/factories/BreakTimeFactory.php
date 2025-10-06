<?php

namespace Database\Factories;

use App\Models\BreakTime;
use App\Models\Attendance;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class BreakTimeFactory extends Factory
{

    protected $model = BreakTime::class;

    public function definition(): array
    {

        $attendance = Attendance::factory()->create();
        $date = $attendance->work_date;

        $start = Carbon::parse("$date 12:00:00")->addMinutes(rand(0, 20));
        $end   = (clone $start)->addMinutes(rand(15, 45));

        $maybeEnd = $this->faker->boolean(85) ? $end : null;

        return [
            'attendance_id' => $attendance->id,
            'break_start_at'=> $start,
            'break_end_at'  => $maybeEnd,
            'created_at'    => now(),
            'updated_at'    => now(),
        ];
    }

    public function ongoing(): self
    {
        return $this->state(fn () => ['break_end_at' => null]);
    }

    public function finished(): self
    {
        return $this->state(function (array $attrs) {
            $start = $attrs['break_start_at'] ?? now()->setTime(12, 0);
            return ['break_end_at' => Carbon::parse($start)->addMinutes(rand(15, 45))];
        });
    }
}
