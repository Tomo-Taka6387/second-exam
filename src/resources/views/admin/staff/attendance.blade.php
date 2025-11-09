@extends('layouts.auth')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/staff/attendance.css') }}?v={{ time() }}">
@endsection

@section('content')
<div class="attendance-index">
    <div class="attendance-container">
        <h1>{{ $user->name }}さんの勤怠</h1>

        {{-- 月切替 --}}
        <div class="attendance-month">
            <div class="last-month">
                <a href="{{ route('admin.staff.attendances', ['user' => $user->id, 'month' => $prevMonth]) }}">
                    <img src="{{ asset('storage/images/arrow.png') }}" alt="矢印" class="arrow-icon">
                    前月
                </a>
            </div>

            <div class="this-month">
                <img src="{{ asset('storage/images/calendar.png') }}" alt="カレンダ" class="calendar-icon">
                {{ $year }}/{{ str_pad($month, 2, '0', STR_PAD_LEFT) }}
            </div>

            <div class="next-month">
                <a href="{{ route('admin.staff.attendances', ['user' => $user->id, 'month' => $nextMonth]) }}">
                    翌月
                    <img src="{{ asset('storage/images/arrow.png') }}" alt="矢印" class="arrow-icon_next">
                </a>
            </div>
        </div>

        {{-- 勤怠テーブル --}}
        <div class="attendance-table">
            @php
            $weekdays = ['日','月','火','水','木','金','土'];
            @endphp

            <table>
                <tr>
                    <th class="table-column_day">日付</th>
                    <th class="table-column">出勤</th>
                    <th class="table-column">退勤</th>
                    <th class="table-column">休憩</th>
                    <th class="table-column">合計</th>
                    <th class="table-column">詳細</th>
                </tr>

                @foreach($attendances as $date => $attendance)
                @php
                $carbonDate = \Carbon\Carbon::parse($date);
                $weekday = $weekdays[$carbonDate->dayOfWeek];
                // データがある場合はIDを、ない場合は0を使用
                $linkId = $attendance && $attendance->id ? $attendance->id : 0;
                $query = !$attendance ? '?date=' . urlencode($date) . '&user=' . $user->id : '';
                $url = route('admin.attendance.show', ['id' => $linkId]) . $query;
                @endphp
                <tr>
                    <td class="table-data_day">{{ $carbonDate->format('m/d') }}({{ $weekday }})</td>
                    <td class="table-data">{{ $attendance && $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '' }}</td>
                    <td class="table-data">{{ $attendance && $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '' }}</td>
                    <td class="table-data">{{ $attendance ? $attendance->total_break_time : '' }}</td>
                    <td class="table-data">{{ $attendance ? $attendance->total_time : '' }}</td>
                    <td class="table-data_link">
                        <a href="{{ $url }}">詳細</a>
                    </td>
                </tr>
                @endforeach

            </table>
        </div>

        <div class="csv-export">
            <form action="{{ route('admin.staff.attendances', ['user' => $user->id]) }}" method="GET">
                <input type="hidden" name="month" value="{{ request()->query('month', sprintf('%04d-%02d', $year, $month)) }}">
                <input type="hidden" name="export" value="csv">
                <button type="submit" class="csv-btn">CSV出力</button>
            </form>
        </div>

    </div>
</div>
@endsection