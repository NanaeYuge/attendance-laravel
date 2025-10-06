@extends('admin.layouts.app')
@section('title','スタッフ一覧')

@section('head')
<link rel="stylesheet" href="{{ asset('css/admin/staff.css') }}?v={{ now()->format('YmdHi') }}">
@endsection

@section('content')
<section class="staff">
    <h1 class="page-title"><span>スタッフ一覧</span></h1>

    <div class="table-card staff-card">
    <table class="table staff-table">
        <thead>
        <tr>
            <th class="col-name">名前</th>
            <th>メールアドレス</th>
            <th>月次勤怠</th>
        </tr>
        </thead>
        <tbody>
        @foreach($users as $u)
            <tr>
            <td class="col-name">{{ $u->name }}</td>
            <td class="mono">{{ $u->email }}</td>
            <td><a class="link-detail" href="{{ route('admin.attendance.staff.month',$u->id) }}">詳細</a></td>
            </tr>
        @endforeach
        </tbody>
    </table>
    </div>
</section>
@endsection
