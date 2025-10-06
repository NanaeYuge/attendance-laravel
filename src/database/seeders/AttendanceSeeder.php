<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {

        $user = User::where('email', 'taro@example.com')->first();

        if (!$user) {
            $this->command->warn('taro@example.com が未作成のため、AttendanceSeeder をスキップしました。');
            return;
        }

        $made = 0;
        $date = Carbon::today();

        while ($made < 3) {
            $date = $date->subDay();

            if (in_array($date->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY])) {
                continue;
            }

            $in  = Carbon::parse($date->toDateString() . ' 09:00:00');
            $out = Carbon::parse($date->toDateString() . ' 18:00:00');

            $attendance = Attendance::updateOrCreate(
                [
                    'user_id'   => $user->id,
                    'work_date' => $date->toDateString(),
                ],
                [
                    'clock_in'  => $in,
                    'clock_out' => $out,
                    'status'    => 'done',
                    'note'      => null,
                ]
            );

            BreakTime::updateOrCreate(
                [
                    'attendance_id' => $attendance->id,
                    'break_in'      => Carbon::parse($date->toDateString() . ' 12:00:00'),
                ],
                [
                    'break_out'     => Carbon::parse($date->toDateString() . ' 13:00:00'),
                ]
            );

            $made++;
        }
    }
}
