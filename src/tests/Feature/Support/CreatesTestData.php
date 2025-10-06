<?php

namespace Tests\Feature\Support;

use App\Models\User;
use App\Models\Admin;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

trait CreatesTestData
{
    protected function makeUser(array $overrides = [], bool $verified = true): User
    {
        $defaults = [
            'name' => '太郎',
            'email' => Str::random(8).'@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => $verified ? now() : null,
        ];
        $u = User::factory()->create(array_merge($defaults, $overrides));
        return $u;
    }

    protected function makeAdmin(array $overrides = []): Admin
    {
        $defaults = [
            'name' => '管理者',
            'email' => Str::random(8).'@example.com',
            'password' => Hash::make('password'),
        ];
        /** @var Admin $a */
        $a = Admin::factory()->create(array_merge($defaults, $overrides));
        return $a;
    }

    /**
     * 勤怠＋休憩をまとめて作成
     * $breaks = [['start' => '12:30', 'end' => '13:00'], ...]
     */
    protected function makeAttendanceWithBreaks(User $user, string $date = null, array $breaks = []): Attendance
    {
        $workDate = $date ? Carbon::parse($date)->toDateString() : now()->toDateString();

        $att = Attendance::factory()->create([
            'user_id' => $user->id,
            'work_date' => $workDate,
            'clock_in_at' => Carbon::parse("$workDate 09:00:00"),
            'clock_out_at' => Carbon::parse("$workDate 18:00:00"),
        ]);

        foreach ($breaks as $b) {
            BreakTime::factory()->create([
                'attendance_id' => $att->id,
                'break_start_at' => Carbon::parse("$workDate " . ($b['start'] ?? '12:00') . ':00'),
                'break_end_at'   => isset($b['end']) ? Carbon::parse("$workDate {$b['end']}:00") : null,
            ]);
        }

        return $att->fresh('breakTimes');
    }
}
