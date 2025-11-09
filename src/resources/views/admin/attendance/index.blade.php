@extends('layouts.auth')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/attendance/index.css')}}?v={{ time() }}">
@endsection


@section('content')
<div class="attendance-index">
    <div class="attendance-container">
        <form class="login-form__form" action="{{ route('admin.attendance.index') }}" method="get">
            @csrf
            <h1>{{ $date->format('Y年m月d日') }}の勤怠</h1>

            <div class="attendance-day">
                <div class="yesterday">
                    <a href="{{ route('admin.attendance.index', ['day' => $prevDay]) }}">
                        <img src="{{ asset('storage/images/arrow.png') }}" alt="矢印" class="arrow-icon">
                        前日
                    </a>
                </div>

                <div class="today">
                    <img src="{{ asset('storage/images/calendar.png') }}" alt="カレンダー" class="calendar-icon">
                    {{ $date->format('Y/m/d') }}
                </div>

                <div class="tomorrow">
                    <a href="{{ route('admin.attendance.index', ['day' => $nextDay]) }}">
                        翌日
                        <img src="{{ asset('storage/images/arrow.png') }}" alt="矢印" class="arrow-icon_next">
                    </a>
                </div>
            </div>

            <div class="attendance-table">
                <table>
                    <thead>
                        <tr>
                            <th>名前</th>
                            <th>出勤</th>
                            <th>退勤</th>
                            <th>休憩</th>
                            <th>合計</th>
                            <th>詳細</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $attendance)
                        <tr>
                            <td>{{ $attendance->user->name }}</td>
                            <td>{{ $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '-' }}</td>
                            <td>{{ $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '-' }}</td>
                            <td>{{ $attendance->total_break_time ?? '-' }}</td>
                            <td>{{ $attendance->total_time ?? '-' }}</td>
                            <td><a class="table-date_link" href="{{ route('admin.attendance.show', $attendance->id) }}">詳細</a></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="no-data">データがありません</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>

    </div>
</div>
@endsection