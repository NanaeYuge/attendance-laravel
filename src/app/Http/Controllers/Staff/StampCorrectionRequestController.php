<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\CorrectionRequest;
use App\Http\Requests\AttendanceCorrectionRequest as CorrectionForm;

class StampCorrectionRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $tab = $request->get('tab','pending');
        $pending = CorrectionRequest::with('attendance')->where('user_id',$user->id)->where('status','pending')->latest()->get();
        $approved= CorrectionRequest::with('attendance')->where('user_id',$user->id)->where('status','approved')->latest()->get();
        return view('staff.requests.index', compact('tab','pending','approved'));
    }

    public function store(CorrectionForm $request, Attendance $attendance)
    {
        $user = Auth::user();
        if (CorrectionRequest::where('attendance_id',$attendance->id)->where('status','pending')->exists()) {
            return back()->with('error','既に承認待ちの申請があります。');
        }
        $payload = [
            'clock_in'=>$request->input('clock_in'),
            'clock_out'=>$request->input('clock_out'),
            'breaks'=>array_values(array_filter($request->input('breaks',[]), fn($b)=>($b['in']??null)||($b['out']??null))),
            'note'=>$request->input('note'),
        ];
        CorrectionRequest::create([
            'attendance_id'=>$attendance->id,
            'user_id'=>$user->id,
            'status'=>'pending',
            'payload'=>$payload,
        ]);
        return redirect()->route('staff.attendance.show',$attendance->id)->with('success','修正申請を受け付けました。');
    }
}
