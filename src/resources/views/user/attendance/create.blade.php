@extends('layouts.user')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user/attendance/create.css')}}?v={{ time() }}">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@700;800&display=swap" rel="stylesheet">
@endsection

@section('content')
<div class="attendance-view">
    <div class="attendance-status">
        @php
        $weekdays = ['日','月','火','水','木','金','土'];
        $today = now();
        @endphp
        <p class="user_status">{{ $user->attendance_status ?? '勤務外' }}</p>
        <p class="attendance_day">{{ now()->format('Y年n月j日') }} ({{ $weekdays[$today->dayOfWeek] }})</p>
        <p class="attendance_time">{{ now()->format('H:i') }}</p>
    </div>

    <div class="attendance-buttons">
        @if(($user->attendance_status ?? '勤務外') === '勤務外')
        <form method="post" action="{{ route('attendance.store') }}">
            @csrf
            <button type="submit" class="btn-clock-in" name="action" value="clockIn">出勤</button>
        </form>

        @elseif(($attendance->break_in && !$attendance->break_out) || ($attendance->break2_in && !$attendance->break2_out))
        <form method="post" action="{{ route('attendance.store') }}">
            @csrf
            <button type="submit" class="btn-break-out" name="action" value="breakOut">休憩戻</button>
        </form>

        @elseif($attendance->clock_in && !$attendance->clock_out)
        <form method="post" action="{{ route('attendance.store') }}">
            @csrf
            <button type="submit" class="btn-clock-out" name="action" value="clockOut">退勤</button>
        </form>
        <form method="post" action="{{ route('attendance.store') }}">
            @csrf
            <button type="submit" class="btn-break-in" name="action" value="breakIn">休憩入</button>
        </form>

        @elseif($attendance->clock_out)
        <p class="out-message">お疲れ様でした。</p>
        @endif
    </div>

</div>
@endsection