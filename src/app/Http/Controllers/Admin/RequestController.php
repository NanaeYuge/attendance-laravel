<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\CorrectionRequest;
use App\Models\BreakTime;

class RequestController extends Controller
{

    public function index(Request $request)
    {
        $tab = $request->get('tab', 'pending');

        $pending = CorrectionRequest::with(['attendance.user'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        $approved = CorrectionRequest::with(['attendance.user'])
            ->where('status', 'approved')
            ->latest()
            ->get();

        return view('admin.requests.index', compact('tab', 'pending', 'approved'));
    }


    public function show($id)
    {
        $req = CorrectionRequest::with(['attendance.user'])->findOrFail($id);
        return view('admin.requests.show', compact('req'));
    }

    public function approve($id)
    {
        $req = CorrectionRequest::with(['attendance.user', 'attendance.breaks'])->findOrFail($id);
        $att = $req->attendance;

        if (empty($att?->work_date)) {
            return back()->with('error', '勤怠データに対象日が存在しません。');
        }

        DB::transaction(function () use ($req, $att) {
            $p = $req->payload;

            $date = $att->work_date instanceof Carbon
                ? $att->work_date->format('Y-m-d')
                : Carbon::parse($att->work_date)->format('Y-m-d');

            if (!empty($p['clock_in'])) {
                $att->clock_in = Carbon::parse("$date {$p['clock_in']}:00");
            }

            if (!empty($p['clock_out'])) {
                $att->clock_out = Carbon::parse("$date {$p['clock_out']}:00");
                $att->status = 'done';
            }

            if (!empty($p['note'])) {
                $att->note = $p['note'];
            }

            $att->save();

            $att->breaks()->delete();
            foreach ($p['breaks'] ?? [] as $b) {
                BreakTime::create([
                    'attendance_id' => $att->id,
                    'break_in'  => !empty($b['in'])  ? Carbon::parse("$date {$b['in']}:00")  : null,
                    'break_out' => !empty($b['out']) ? Carbon::parse("$date {$b['out']}:00") : null,
                ]);
            }

            $req->status = 'approved';
            $req->save();
        });

        return redirect()
            ->route('admin.requests.show', $req->id)
            ->with('success', '承認しました。勤怠へ反映済みです。');
    }
}
