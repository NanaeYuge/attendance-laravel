@extends('admin.layouts.app')
@section('title','勤怠詳細')

@section('head')
<link rel="stylesheet" href="{{ asset('css/admin/attendance-show.css') }}@if(file_exists(public_path('css/admin/attendance-show.css')))?v={{ filemtime(public_path('css/admin/attendance-show.css')) }}@endif">
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet">
@endsection

@section('content')
<div class="detail-page">
    <h1 class="detail-title">勤怠詳細</h1>

    @if(session('status')) <div class="flash">{{ session('status') }}</div> @endif

    <form method="POST" action="{{ route('admin.attendance.update', $att) }}" class="detail-card">
    @csrf @method('PATCH')

    <div class="row">
        <div class="cell label">名前</div>
        <div class="cell value">{{ $att->user->name }}</div>
    </div>

    <div class="row">
        <div class="cell label">日付</div>
        <div class="cell value">
        <span class="ym">{{ $att->work_date->format('Y') }}年</span>
        <span class="dm">{{ $att->work_date->format('n月j日') }}</span>
        </div>
    </div>

    <div class="row">
        <div class="cell label">出勤・退勤</div>
        <div class="cell value times">
        <input class="time" type="text" name="clock_in"  inputmode="numeric" pattern="^\d{2}:\d{2}$" value="{{ old('clock_in', optional($att->clock_in)->format('H:i')) }}">
        <span class="sep">〜</span>
        <input class="time" type="text" name="clock_out" inputmode="numeric" pattern="^\d{2}:\d{2}$" value="{{ old('clock_out', optional($att->clock_out)->format('H:i')) }}">
        </div>
    </div>

    <div class="row">
        <div class="cell label">休憩</div>
        <div class="cell value times">
        <input class="time" type="text" name="break1_in"  inputmode="numeric" pattern="^\d{2}:\d{2}$" value="{{ old('break1_in', optional($b1?->break_in)->format('H:i')) }}">
        <span class="sep">〜</span>
        <input class="time" type="text" name="break1_out" inputmode="numeric" pattern="^\d{2}:\d{2}$" value="{{ old('break1_out', optional($b1?->break_out)->format('H:i')) }}">
        </div>
    </div>

    <div class="row">
        <div class="cell label">休憩2</div>
        <div class="cell value times">
        <input class="time" type="text" name="break2_in"  inputmode="numeric" pattern="^\d{2}:\d{2}$" value="{{ old('break2_in', optional($b2?->break_in)->format('H:i')) }}">
        <span class="sep">〜</span>
        <input class="time" type="text" name="break2_out" inputmode="numeric" pattern="^\d{2}:\d{2}$" value="{{ old('break2_out', optional($b2?->break_out)->format('H:i')) }}">
        </div>
    </div>

    <div class="row">
        <div class="cell label">備考</div>
        <div class="cell value">
        <textarea name="note" rows="3" class="note">{{ old('note', $att->note) }}</textarea>
        </div>
    </div>

    <div class="footer">
        <button type="submit" class="btn-change">修正</button>
    </div>

    @foreach (['clock_in','clock_out','break1_in','break1_out','break2_in','break2_out','note'] as $f)
        @error($f) <p class="err">{{ $message }}</p> @enderror
    @endforeach
    </form>
</div>
@endsection
