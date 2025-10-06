<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function daily(Request $request)
    {
        $day  = Carbon::parse($request->get('date', now()->toDateString()));
        $list = Attendance::with(['user','breaks' => fn($q) => $q->orderBy('break_in')])
            ->where('work_date', $day->toDateString())
            ->orderBy('user_id')
            ->get();

        return view('admin.attendance.daily', compact('day','list'));
    }

    public function show($id)
    {
        $att = Attendance::with(['user','breaks' => fn($q) => $q->orderBy('break_in')])->findOrFail($id);

        $b1 = $att->breaks[0] ?? null;
        $b2 = $att->breaks[1] ?? null;

        return view('admin.attendance.show', [
            'att' => $att,
            'b1'  => $b1,
            'b2'  => $b2,
        ]);
    }

    public function staffList()
    {
        $users = User::orderBy('id')->get();
        return view('admin.staff.index', compact('users'));
    }

    public function staffMonth($id, Request $request)
    {
        $user   = User::findOrFail($id);
        $month  = Carbon::parse($request->get('ym', now()->format('Y-m')) . '-01');
        $prevYm = $month->copy()->subMonth()->format('Y-m');
        $nextYm = $month->copy()->addMonth()->format('Y-m');

        $items = Attendance::with(['breaks' => fn($q) => $q->orderBy('break_in')])
            ->where('user_id', $user->id)
            ->whereBetween('work_date', [
                $month->copy()->startOfMonth()->toDateString(),
                $month->copy()->endOfMonth()->toDateString()
            ])
            ->orderBy('work_date')
            ->get();

        return view('admin.staff.month', compact('user','items','month','prevYm','nextYm'));
    }

    public function exportCsv($id, Request $request)
    {
        $user  = User::findOrFail($id);
        $month = Carbon::parse($request->get('ym', now()->format('Y-m')) . '-01');

        $items = Attendance::with(['breaks' => fn($q) => $q->orderBy('break_in')])
            ->where('user_id', $user->id)
            ->whereBetween('work_date', [
                $month->copy()->startOfMonth()->toDateString(),
                $month->copy()->endOfMonth()->toDateString()
            ])
            ->orderBy('work_date')
            ->get();

        $csv = "日付,出勤,退勤,休憩合計(分),実働(分)\n";
        foreach ($items as $att) {
            $csv .= implode(',', [
                $att->work_date->format('Y-m-d'),
                $att->clock_in?->format('H:i'),
                $att->clock_out?->format('H:i'),
                $att->totalBreakMinutes(),
                $att->workedMinutes() ?? ''
            ]) . "\n";
        }

        $filename = sprintf("attendance_%s_%s.csv", $user->id, $month->format('Y_m'));
        return response($csv)
            ->header('Content-Type','text/csv')
            ->header('Content-Disposition',"attachment; filename={$filename}");
    }

    public function update(Request $request, Attendance $attendance)
    {
        $data = $request->validate([
            'clock_in'   => ['nullable','date_format:H:i'],
            'clock_out'  => ['nullable','date_format:H:i'],
            'break1_in'  => ['nullable','date_format:H:i'],
            'break1_out' => ['nullable','date_format:H:i'],
            'break2_in'  => ['nullable','date_format:H:i'],
            'break2_out' => ['nullable','date_format:H:i'],
            'note'       => ['nullable','string','max:2000'],
        ]);

        DB::transaction(function () use ($attendance, $data) {
            $workDate = $attendance->work_date->toDateString();

            $attendance->clock_in  = empty($data['clock_in'])  ? null : Carbon::parse("$workDate {$data['clock_in']}");
            $attendance->clock_out = empty($data['clock_out']) ? null : Carbon::parse("$workDate {$data['clock_out']}");
            if (array_key_exists('note', $data)) {
                $attendance->note = $data['note'];
            }

            $attendance->status = 'approved';
            $attendance->save();

            $existing = $attendance->breaks()->orderBy('break_in')->get()->values();

            $pairs = [
                ['in' => $data['break1_in'] ?? null, 'out' => $data['break1_out'] ?? null],
                ['in' => $data['break2_in'] ?? null, 'out' => $data['break2_out'] ?? null],
            ];

            foreach ([0,1] as $i) {
                $in  = $pairs[$i]['in'];
                $out = $pairs[$i]['out'];

                $bt = $existing[$i] ?? null;

                if (empty($in) && empty($out)) {
                    if ($bt) $bt->delete();
                    continue;
                }

                if (!$bt) {
                    $bt = new BreakTime();
                    $bt->attendance_id = $attendance->id;
                }

                $bt->break_in  = empty($in)  ? null : Carbon::parse("$workDate {$in}");
                $bt->break_out = empty($out) ? null : Carbon::parse("$workDate {$out}");
                $bt->save();
            }
        });

        return redirect()
            ->route('admin.attendance.show', $attendance->id)
            ->with('status', '勤怠を修正し、承認済みに更新しました。');
    }
}
