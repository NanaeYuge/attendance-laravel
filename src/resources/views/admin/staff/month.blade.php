@extends('admin.layouts.app')
@section('title','スタッフ別勤怠一覧')

@section('head')
<link rel="stylesheet" href="{{ asset('css/admin/staff-month.css') }}?v={{ now()->format('YmdHi') }}">
@endsection

@section('content')
<section class="staff-month">
    <h1 class="page-title"><span>{{ $user->name }}さんの勤怠</span></h1>

    <nav class="month-nav">
    <a class="month-nav__side" href="{{ route('admin.attendance.staff.month', ['id'=>$user->id,'ym'=>$prevYm]) }}">← 前月</a>
    <div class="month-nav__center">
        <span class="cal-ico" aria-hidden="true">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" role="img" aria-label="calendar">
            <rect x="3" y="5" width="18" height="16" rx="2" stroke="currentColor" stroke-width="2"/>
            <path d="M8 3v4M16 3v4M3 9h18" stroke="currentColor" stroke-width="2"/>
        </svg>
        </span>
        <strong class="month-nav__current">{{ $month->format('Y/m') }}</strong>
    </div>
    <a class="month-nav__side month-nav__side--next" href="{{ route('admin.attendance.staff.month', ['id'=>$user->id,'ym'=>$nextYm]) }}">翌月 →</a>
    </nav>

    @php
    $hm = function($m){ return is_null($m) ? '—' : sprintf('%d:%02d', intdiv($m,60), $m%60); };
    @endphp
    <div class="table-card">
    <table class="table staff-month__table">
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
            <td class="mono">{{ $att->work_date->format('m/d(D)') }}</td>
            <td class="mono">{{ optional($att->clock_in)->format('H:i') ?? '—' }}</td>
            <td class="mono">{{ optional($att->clock_out)->format('H:i') ?? '—' }}</td>
            <td class="mono">{{ $hm($att->totalBreakMinutes()) }}</td>
            <td class="mono">{{ $hm($att->workedMinutes()) }}</td>
            <td><a class="link-detail" href="{{ route('admin.attendance.show', $att->id) }}">詳細</a></td>
        </tr>
        @endforeach
        </tbody>
    </table>
    </div>

    <div class="actions">
    <a class="btn-csv" href="{{ route('admin.attendance.staff.csv', ['id'=>$user->id,'ym'=>$month->format('Y-m')]) }}">CSV出力</a>
    </div>
</section>
@endsection
