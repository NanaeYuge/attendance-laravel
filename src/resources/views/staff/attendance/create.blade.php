@extends('staff.layouts.app')
@section('title','勤怠登録画面')

@section('head')
<link rel="stylesheet" href="{{ asset('css/staff/attendance.css') }}?v={{ file_exists(public_path('css/staff/attendance.css')) ? filemtime(public_path('css/staff/attendance.css')) : now()->format('YmdHi') }}">
@endsection

@section('content')
<div class="stamp-screen">

    @if(session('success'))<div class="alert success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert error">{{ session('error') }}</div>@endif

    <div class="stamp-viewport">

        <span class="chip chip--status">
            {{ $status['label'] ?? '勤務外' }}
        </span>

        {{-- 日付（例：2023年6月1日(木)） --}}
        <p class="stamp-date">
            {{ \Carbon\Carbon::parse($today)->isoFormat('YYYY年M月D日(ddd)') }}
        </p>

        <p class="stamp-time" id="js-clock">
            {{ \Carbon\Carbon::now()->format('H:i') }}
        </p>

        <div class="stamp-actions">
            @if($att->status==='off' && !$att->clock_in)
                <form method="POST" action="{{ route('staff.attendance.clockin') }}">
                    @csrf
                    <button class="btn btn--primary btn--xl">出勤</button>
                </form>
            @endif

            @if($att->status==='working')
                <form method="POST" action="{{ route('staff.attendance.breakin') }}">
                    @csrf
                    <button class="btn">休憩入</button>
                </form>
                <form method="POST" action="{{ route('staff.attendance.clockout') }}">
                    @csrf
                    <button class="btn btn--danger">退勤</button>
                </form>
            @endif

            @if($att->status==='break')
                <form method="POST" action="{{ route('staff.attendance.breakout') }}">
                    @csrf
                    <button class="btn">休憩戻</button>
                </form>
            @endif

            @if($att->status==='done')
                <p class="done-text">お疲れ様でした。</p>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('js-clock');
    if(!el) return;
    const tick = () => {
        const d = new Date();
        const h = String(d.getHours()).padStart(2,'0');
        const m = String(d.getMinutes()).padStart(2,'0');
        el.textContent = `${h}:${m}`;
    };
    tick();
    setInterval(tick, 1000 * 10); // 10秒ごとに表示更新（負荷軽め）
});
</script>
@endsection
