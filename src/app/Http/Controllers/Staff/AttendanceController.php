<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\BreakTime;

class AttendanceController extends Controller
{
    public function create()
    {
        $user = Auth::user();
        $today = Carbon::today();
        $att = Attendance::firstOrCreate(
            ['user_id'=>$user->id,'work_date'=>$today->toDateString()],
            ['status'=>'off']
        );
        $status = ['label'=>$this->statusLabel($att->status),'value'=>$att->status];
        return view('staff.attendance.create', compact('att','status','user','today'));
    }

    public function clockIn()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();
        $att = Attendance::firstOrCreate(['user_id'=>$user->id,'work_date'=>$today]);
        if ($att->clock_in) return back()->with('error','本日は既に出勤済みです。');
        $att->clock_in = now(); $att->status='working'; $att->save();
        return back()->with('success','出勤しました。');
    }

    public function startBreak()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();
        $att = Attendance::where('user_id',$user->id)->where('work_date',$today)->firstOrFail();
        if ($att->status!=='working') return back()->with('error','出勤中のみ休憩に入れます。');

        $att->status='break'; $att->save();
        BreakTime::create(['attendance_id'=>$att->id,'break_in'=>now()]);
        return back()->with('success','休憩に入りました。');
    }

    public function endBreak()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();
        $att = Attendance::where('user_id',$user->id)->where('work_date',$today)->firstOrFail();
        if ($att->status!=='break') return back()->with('error','休憩中のみ休憩戻が可能です。');

        $open = $att->breaks()->whereNull('break_out')->latest()->first();
        if ($open){ $open->break_out=now(); $open->save(); }
        $att->status='working'; $att->save();
        return back()->with('success','休憩から戻りました。');
    }

    public function clockOut()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();
        $att = Attendance::where('user_id',$user->id)->where('work_date',$today)->firstOrFail();
        if ($att->clock_out) return back()->with('error','本日は既に退勤済みです。');
        $att->clock_out=now(); $att->status='done'; $att->save();
        return back()->with('success','お疲れ様でした。');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $month = Carbon::parse($request->get('ym', now()->format('Y-m')).'-01');
        $prevYm = $month->copy()->subMonth()->format('Y-m');
        $nextYm = $month->copy()->addMonth()->format('Y-m');

        $items = Attendance::with('breaks')
            ->where('user_id',$user->id)
            ->whereBetween('work_date', [$month->copy()->startOfMonth()->toDateString(), $month->copy()->endOfMonth()->toDateString()])
            ->orderBy('work_date')->get();

        $toHm = fn(?int $m)=> $m===null?'':sprintf('%d:%02d', intdiv($m,60), $m%60);
        return view('staff.attendance.index', compact('items','month','prevYm','nextYm','toHm'));
    }

    public function show($id)
    {
        $user = Auth::user();
        $att = Attendance::with('breaks')->where('user_id',$user->id)->findOrFail($id);
        return view('staff.attendance.show', compact('att','user'));
    }

    private function statusLabel(string $s): string {
        return match($s){'off'=>'勤務外','working'=>'出勤中','break'=>'休憩中','done'=>'退勤済', default=>$s};
    }
}
