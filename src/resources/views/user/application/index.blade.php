@extends('layouts.user')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user/application/index.css')}}?v={{ time() }}">
@endsection


@section('content')
<div class="application-index">
    <div class="application-container">
        <h1>申請一覧</h1>

        <div class="application-toggle">
            <a href="{{ route('applications.index', ['page' => 'wait']) }}"
                class="tab {{ $page === 'wait' ? 'active' : '' }}">承認待ち</a>
            <a href="{{ route('applications.index', ['page' => 'approval']) }}"
                class="tab {{ $page === 'approval' ? 'active' : '' }}">承認済み</a>
        </div>

        <table class="application-table">
            <tr>
                <th class="table-column">状態</th>
                <th class="table-column">名前</th>
                <th class="table-column">対象日時</th>
                <th class="table-column">申請理由</th>
                <th class="table-column">申請日時</th>
                <th class="table-column">詳細</th>
            </tr>

            @forelse ($applications as $application)
            <tr>
                <td class="table-date">
                    @if($application->approval_status === 'pending')
                    承認待ち
                    @elseif($application->approval_status === 'approved')
                    承認済み
                    @elseif($application->approval_status === 'rejected')
                    却下
                    @else
                    {{ $application->approval_status }}
                    @endif
                </td>
                <td class="table-date">{{ $application->user->name }}</td>
                <td class="table-date_day">{{ optional($application->new_date)->format('Y/m/d') }}</td>
                <td class="table-date">{{ $application->comment }}</td>
                <td class="table-date_day">{{ optional($application->application_date)->format('Y/m/d') }}</td>
                <td class="table-date_link">
                    <a href="{{ route('attendance.show', ['id' => $application->attendance_record_id]) }}">詳細</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="no-data">データがありません</td>
            </tr>
            @endforelse
        </table>

    </div>
</div>
@endsection