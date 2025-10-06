@extends('staff.layouts.app')
@section('title','勤怠一覧')

@section('head')
<link rel="stylesheet" href="{{ asset('css/staff/attendance-list.css') }}?v={{ filemtime(public_path('css/staff/attendance-list.css')) }}">
@endsection

@section('content')
<div class="page-attendance-list">

    <header class="list-header">
        <h1 class="page-title">勤怠一覧</h1>
        <nav class="month-nav">
            <a class="nav-link" href="{{ route('staff.attendance.index',['ym'=>$prevYm]) }}">← 前月</a>
            <strong class="current-month">
                <img src="{{ asset('storage/calender.png') }}" alt="カレンダー" class="icon-calendar">
                {{ $month->format('Y/m') }}
            </strong>

            <a class="nav-link" href="{{ route('staff.attendance.index',['ym'=>$nextYm]) }}">翌月 →</a>
        </nav>
    </header>

    <div class="table-wrap">
        <table class="attendance-table">
            <thead>
                <tr>
                    <th>日付</th>
                    <th>出勤</th>
                    <th>退勤</th>
                    <th>休憩</th>
                    <th>合計</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $att)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($att->work_date)->isoFormat('MM/DD(ddd)') }}</td>
                    <td>{{ optional($att->clock_in)->format('H:i') }}</td>
                    <td>{{ optional($att->clock_out)->format('H:i') }}</td>
                    <td>{{ $att->totalBreakHours() }}</td>
                    <td>{{ $att->workedHours() }}</td>
                    <td><a class="btn-detail" href="{{ route('staff.attendance.show',$att->id) }}">詳細</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection
