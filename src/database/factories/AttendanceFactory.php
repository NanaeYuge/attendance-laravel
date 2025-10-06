<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class AttendanceFactory extends Factory
{

    protected $model = Attendance::class;

    public function definition(): array
    {

        $workDate = Carbon::today()->subDays(rand(0, 30))->toDateString();

        $in  = Carbon::parse("$workDate 09:00:00")->addMinutes(rand(0, 20));
        $out = (clone $in)->setTime(18, 0)->addMinutes(rand(0, 20));

        $clockOut = $this->faker->boolean(80) ? $out : null;

        return [
            'user_id'      => User::factory(),
            'work_date'    => $workDate,
            'clock_in_at'  => $in,
            'clock_out_at' => $clockOut,
            'created_at'   => now(),
            'updated_at'   => now(),
        ];
    }

    public function working(): self
    {
        return $this->state(fn () => ['clock_out_at' => null]);
    }

    public function done(): self
    {
        return $this->state(function (array $attrs) {
            $workDate = $attrs['work_date'] ?? Carbon::today()->toDateString();
            $out = Carbon::parse("$workDate 18:00:00")->addMinutes(rand(0, 20));
            return ['clock_out_at' => $out];
        });
    }

    public function today(): self
    {
        return $this->state(function () {
            $workDate = Carbon::today()->toDateString();
            $in  = Carbon::parse("$workDate 09:00:00")->addMinutes(rand(0, 20));
            $out = (clone $in)->setTime(18, 0)->addMinutes(rand(0, 20));
            return [
                'work_date'    => $workDate,
                'clock_in_at'  => $in,
                'clock_out_at' => $out,
            ];
        });
    }
}
