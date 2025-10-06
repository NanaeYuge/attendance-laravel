@extends('staff.layouts.app')
@section('title','勤怠詳細')

@section('head')
<link rel="stylesheet" href="{{ asset('css/staff/attendance-show.css') }}?v={{ filemtime(public_path('css/staff/attendance-show.css')) }}">
@endsection

@section('content')
<div class="page-attendance-show">
    <h1 class="page-title">勤怠詳細</h1>

    @php
    $pending = \App\Models\CorrectionRequest::where('attendance_id',$att->id)->where('status','pending')->first();
    $disabled = $pending ? 'disabled' : '';
    // 休憩行数：既存数 + 1 行（空行）
    $rows = max($att->breaks->count(), 0) + 1;
    @endphp

    @if($pending)
    <div class="alert alert--info">承認待ちのため修正はできません。</div>
    @endif

    <form method="POST" action="{{ route('staff.requests.store',$att->id) }}" class="card">
    @csrf

    <div class="card-row">
        <div class="th">名前</div>
        <div class="td td--center">{{ $user->name }}</div>
    </div>

    <div class="card-row">
        <div class="th">日付</div>
        <div class="td td--date">
            <span class="date-y">{{ $att->work_date->isoFormat('YYYY年') }}</span>
            <span class="date-md">{{ $att->work_date->isoFormat('M月D日') }}</span>
        </div>
    </div>

    <div class="card-row">
        <div class="th">出勤・退勤</div>
        <div class="td td--range">
        @php
            $in  = old('clock_in', optional($att->clock_in)->format('H:i'));
            $out = old('clock_out', optional($att->clock_out)->format('H:i'));
        @endphp
            <input type="time" name="clock_in" value="{{ $in }}" class="time-input" {{ $disabled }}>
            <span class="tilde">〜</span>
            <input type="time" name="clock_out" value="{{ $out }}" class="time-input" {{ $disabled }}>
        </div>
    </div>
    @error('clock_in')<p class="form-error">{{ $message }}</p>@enderror
    @error('clock_out')<p class="form-error">{{ $message }}</p>@enderror

    @for($i=0; $i<$rows; $i++)
        @php
            $b0 = $att->breaks[0] ?? null;
            $b1 = $att->breaks[1] ?? null;

            $b0in  = old('breaks.0.in',  optional($b0?->break_in)->format('H:i'));
            $b0out = old('breaks.0.out', optional($b0?->break_out)->format('H:i'));

            $b1in  = old('breaks.1.in',  optional($b1?->break_in)->format('H:i'));
            $b1out = old('breaks.1.out', optional($b1?->break_out)->format('H:i'));
        @endphp

        <div class="card-row">
            <div class="th">休憩</div>
            <div class="td td--range">
                <input type="time" name="breaks[0][in]"  value="{{ $b0in  }}" class="time-input" {{ $disabled }}>
                <span class="tilde">〜</span>
                <input type="time" name="breaks[0][out]" value="{{ $b0out }}" class="time-input" {{ $disabled }}>
            </div>
            </div>
            @error('breaks.0.in')  <p class="form-error">{{ $message }}</p> @enderror
            @error('breaks.0.out') <p class="form-error">{{ $message }}</p> @enderror

        <div class="card-row">
            <div class="th">休憩2</div>
            <div class="td td--range">
                <input type="time" name="breaks[1][in]"  value="{{ $b1in  }}" class="time-input" {{ $disabled }}>
                <span class="tilde">〜</span>
                <input type="time" name="breaks[1][out]" value="{{ $b1out }}" class="time-input" {{ $disabled }}>
            </div>
        </div>
        @error('breaks.1.in')  <p class="form-error">{{ $message }}</p> @enderror
        @error('breaks.1.out') <p class="form-error">{{ $message }}</p> @enderror

    @endfor

    <div class="card-row card-row--last">
        <div class="th">備考</div>
        <div class="td">
            <textarea name="note" rows="3" class="note-input" {{ $disabled }}>{{ old('note') }}</textarea>
        </div>
    </div>
    @error('note')<p class="form-error">{{ $message }}</p>@enderror

    <div class="actions">
        <button class="btn-primary" {{ $disabled }}>修正</button>
    </div>
    </form>
</div>
@endsection
