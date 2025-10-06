@extends('admin.layouts.app')
@section('title','修正申請一覧')

@section('head')
<link rel="stylesheet" href="{{ asset('css/admin/requests.css') }}?v={{ now()->format('YmdHi') }}">
@endsection

@section('content')
<section class="requests">
    <h1 class="page-title"><span>申請一覧</span></h1>

    <nav class="tab-nav" role="tablist" aria-label="申請タブ">
        <a class="tab-nav__link {{ $tab==='pending' ? 'is-active' : '' }}"
            href="{{ route('admin.requests.index',['tab'=>'pending']) }}"
            role="tab" aria-selected="{{ $tab==='pending' ? 'true' : 'false' }}">承認待ち</a>
        <a class="tab-nav__link {{ $tab==='approved' ? 'is-active' : '' }}"
            href="{{ route('admin.requests.index',['tab'=>'approved']) }}"
            role="tab" aria-selected="{{ $tab==='approved' ? 'true' : 'false' }}">承認済み</a>
    </nav>

    <div class="table-card">
        <table class="table requests-table">
            <thead>
            <tr>
                <th class="col-id">ID</th>
                <th>状態</th>
                <th>名前</th>
                <th>対象日時</th>
                <th>申請理由</th>
                <th>申請日時</th>
                <th>詳細</th>
            </tr>
            </thead>
            <tbody>
            @php
                $list = $tab==='pending' ? $pending : $approved;
            @endphp

            @forelse($list as $req)
                <tr>
                    <td class="col-id">#{{ $req->id }}</td>
                    <td>{{ $tab==='pending' ? '承認待ち' : '承認済み' }}</td>

                    {{-- 名前 --}}
                    <td>{{ $req->attendance?->user?->name ?? $req->user?->name ?? '—' }}</td>

                    {{-- 対象日時 --}}
                    <td class="mono">
                        @php
                            $wd = $req->attendance?->work_date;
                            if ($wd && !($wd instanceof \Illuminate\Support\Carbon)) {
                                $wd = \Illuminate\Support\Carbon::parse($wd);
                            }
                        @endphp
                        {{ $wd?->format('Y/m/d') ?? '—' }}
                    </td>

                    {{-- 申請理由 --}}
                    <td class="col-reason">
                        @php
                            $reason = data_get($req->payload, 'note')
                                ?? data_get($req->payload, 'reason')
                                ?? $req->note;
                        @endphp
                        <span title="{{ $reason ?? '—' }}">{{ $reason ?? '—' }}</span>
                    </td>

                    {{-- 申請日時 --}}
                    <td class="mono">
                        {{ $req->created_at?->format('Y/m/d') ?? '—' }}
                    </td>

                    {{-- 詳細 --}}
                    <td>
                        <a class="link-detail" href="{{ route('admin.requests.show', $req->id) }}">詳細</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="col-id"></td>
                    <td colspan="6" class="empty">
                        {{ $tab==='pending' ? '承認待ちはありません' : '承認済みはありません' }}
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
