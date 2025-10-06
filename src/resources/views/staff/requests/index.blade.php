@extends('staff.layouts.app')
@section('title','申請一覧')

@section('head')
<link rel="stylesheet" href="{{ asset('css/staff/requests-list.css') }}?v={{ filemtime(public_path('css/staff/requests-list.css')) }}">
@endsection

@section('content')
<div class="page-requests">

    <h1 class="page-title">申請一覧</h1>

    <nav class="tab-nav">
        <a class="tab-link {{ $tab==='pending' ? 'is-active' : '' }}"
           href="{{ route('staff.requests.index',['tab'=>'pending']) }}">承認待ち</a>
        <a class="tab-link {{ $tab==='approved' ? 'is-active' : '' }}"
           href="{{ route('staff.requests.index',['tab'=>'approved']) }}">承認済み</a>
    </nav>
    <div class="tab-sep"></div>

    <div class="table-wrap">
        <table class="requests-table">
            <thead>
                <tr>
                    <th>状態</th>
                    <th>名前</th>
                    <th>対象日時</th>
                    <th>申請理由</th>
                    <th>申請日時</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
            @if($tab==='pending')
                @forelse($pending as $r)
                    <tr>
                        <td>{{ $r->status_label ?? '承認待ち' }}</td>
                        <td>{{ $r->user->name }}</td>
                        <td>{{ $r->attendance->work_date->isoFormat('YYYY/MM/DD') }}</td>
                        <td>{{ $r->reason ?? '―' }}</td>
                        <td>{{ $r->created_at->isoFormat('YYYY/MM/DD') }}</td>
                        <td><a class="link-detail" href="{{ route('staff.attendance.show',$r->attendance->id) }}">詳細</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">承認待ちはありません</td></tr>
                @endforelse
            @else
                @forelse($approved as $r)
                    <tr>
                        <td>{{ $r->status_label ?? '承認済み' }}</td>
                        <td>{{ $r->user->name }}</td>
                        <td>{{ $r->attendance->work_date->isoFormat('YYYY/MM/DD') }}</td>
                        <td>{{ $r->reason ?? '―' }}</td>
                        <td>{{ $r->created_at->isoFormat('YYYY/MM/DD') }}</td>
                        <td><a class="link-detail" href="{{ route('staff.attendance.show',$r->attendance->id) }}">詳細</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="empty">承認済みはありません</td></tr>
                @endforelse
            @endif
            </tbody>
        </table>
    </div>

</div>
@endsection
