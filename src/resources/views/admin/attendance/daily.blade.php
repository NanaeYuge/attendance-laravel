@extends('admin.layouts.app')
@section('title','勤怠一覧')

@section('head')
<link rel="stylesheet" href="{{ asset('css/admin/daily.css') }}?v={{ now()->format('YmdHi') }}">
@endsection

@section('content')
<section class="daily">

    <h1 class="page-title">
        <span>{{ $day->format('Y年n月j日') }}の勤怠</span>
    </h1>

    @php
        $prev = $day->copy()->subDay()->toDateString();
        $next = $day->copy()->addDay()->toDateString();
    @endphp
    <div class="day-nav">
        <a class="day-nav__side" href="{{ route('admin.attendance.daily', ['date' => $prev]) }}">← 前日</a>

    <form method="GET" action="{{ route('admin.attendance.daily') }}" class="day-nav__center">
        <span class="cal-ico" aria-hidden="true">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <rect x="3" y="5" width="18" height="16" rx="2" stroke="currentColor" stroke-width="2"/>
            <path d="M8 3v4M16 3v4M3 9h18" stroke="currentColor" stroke-width="2"/>
        </svg>
        </span>
        <input type="date" name="date" value="{{ $day->toDateString() }}" class="day-nav__date">
        <button class="day-nav__btn" type="submit" aria-label="表示">表示</button>
    </form>

    <a class="day-nav__side day-nav__side--next" href="{{ route('admin.attendance.daily', ['date' => $next]) }}">翌日 →</a>
    </div>

    <div class="table-card">
        <table class="table daily-table">
        <thead>
        <tr>
            <th class="col-name">名前</th>
            <th>出勤</th>
            <th>退勤</th>
            <th>休憩</th>
            <th>合計</th>
            <th>詳細</th>
        </tr>
        </thead>
    <tbody>
        @foreach($list as $att)
        @php
            $cin  = optional($att->clock_in)->format('H:i');
            $cout = optional($att->clock_out)->format('H:i');
            $br   = $att->totalBreakMinutes();
            $wrk  = $att->workedMinutes();
            $hm = function($m){ return is_null($m) ? '—' : sprintf('%d:%02d', intdiv($m,60), $m%60); };
        @endphp
        <tr>
            <td class="col-name">{{ $att->user->name }}</td>
            <td class="mono">{{ $cin ?? '—' }}</td>
            <td class="mono">{{ $cout ?? '—' }}</td>
            <td class="mono">{{ $hm($br) }}</td>
            <td class="mono">{{ $hm($wrk) }}</td>
            <td><a class="link-detail" href="{{ route('admin.attendance.show', $att->id) }}">詳細</a></td>
        </tr>
        @endforeach
        </tbody>
    </table>
    </div>

</section>
@endsection
