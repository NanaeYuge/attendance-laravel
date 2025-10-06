@extends('admin.layouts.app')
@section('title','修正申請詳細')

@section('head')
<link rel="stylesheet" href="{{ asset('css/admin/request-show.css') }}?v={{ now()->format('YmdHi') }}">
@endsection

@section('content')
<section class="request-show">

    <h1 class="page-title"><span>勤怠詳細</span></h1>

    @if(session('success'))
    <div class="alert success">{{ session('success') }}</div>
    @endif

    @php
    $att = $req->attendance;
    $date = $att->work_date;
    $p = $req->payload ?? [];

    $pill = fn($v) => $v ?: '—';

    $bIn1  = $p['breaks'][0]['in']  ?? '';
    $bOut1 = $p['breaks'][0]['out'] ?? '';
    $bIn2  = $p['breaks'][1]['in']  ?? '';
    $bOut2 = $p['breaks'][1]['out'] ?? '';
    @endphp

    <div class="table-card">
    <table class="detail-table">
        <tbody>
        <tr>
            <th>名前</th>
            <td colspan="3">{{ $att->user->name }}</td>
        </tr>

        <tr>
            <th>日付</th>
            <td class="mono center">{{ $date->format('Y') }}年</td>
            <td class="mono center" colspan="2">{{ $date->format('n月j日') }}</td>
        </tr>

        <tr>
            <th>出勤・退勤</th>
            <td class="center"><span class="pill">{{ $pill(optional($att->clock_in)->format('H:i')) }}</span></td>
            <td class="center tilde">〜</td>
            <td class="center"><span class="pill">{{ $pill(optional($att->clock_out)->format('H:i')) }}</span></td>
        </tr>

        <tr>
            <th>休憩</th>
            <td class="center"><span class="pill">{{ $pill($bIn1) }}</span></td>
            <td class="center tilde">〜</td>
            <td class="center"><span class="pill">{{ $pill($bOut1) }}</span></td>
        </tr>

        <tr>
            <th>休憩2</th>
            <td class="center"><span class="pill">{{ $pill($bIn2) }}</span></td>
            <td class="center tilde">〜</td>
            <td class="center"><span class="pill">{{ $pill($bOut2) }}</span></td>
        </tr>

        <tr>
            <th>備考</th>
            <td colspan="3"><div class="note-box">{{ $p['note'] ?? '' }}</div></td>
        </tr>
        </tbody>
    </table>
    </div>

    @if($req->status === 'pending')
    <div class="actions">
        <form method="POST" action="{{ route('admin.requests.approve', $req->id) }}">
        @csrf
        <button type="submit" class="btn-approve">承認</button>
        </form>
    </div>
    @else
    <div class="actions actions--approved">
        <span class="badge-approved">承認済み</span>
    </div>
    @endif

</section>
@endsection
