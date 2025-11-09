@extends('layouts.user')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user/attendance/show.css') }}?v={{ time() }}">
@endsection

@section('content')
<div class="attendance-details">
    <div class="attendance-container">
        <h1>勤怠詳細</h1>

        @if(!$attendance->id)
        <p class="no-attendance-message">この日の勤怠は登録されていません。</p>
        @else
        <form action="{{ route('applications.store', ['id' => $attendance->id]) }}" method="post">
            @csrf

            <table class="details-table">
                <tr>
                    <th>名前</th>
                    <td>{{ $attendance->user->name }}</td>
                </tr>

                <tr>
                    <th>日付</th>
                    <td class="date-row">
                        <div class="date-block">
                            <div class="date-text readonly">
                                {{ \Carbon\Carbon::parse($attendance->date)->format('Y年') }}
                            </div>
                        </div>

                        <div class="date-block">
                            <div class="date-text readonly">
                                {{ \Carbon\Carbon::parse($attendance->date)->format('n月j日') }}
                            </div>
                        </div>
                    </td>
                </tr>


                <tr>
                    <th>出勤・退勤</th>
                    <td class="time-row @if($application && $application->approval_status === 'approved') approved @endif">
                        <div class="time-block">
                            <div class="time-input-group">
                                @if($application && in_array($application->approval_status, ['pending', 'approved']))
                                <input type="text" class="time-box readonly"
                                    value="{{ \Carbon\Carbon::parse(optional($application)->new_clock_in ?? $attendance->clock_in)->format('H:i') }}" readonly>
                                <span class="separator">〜</span>
                                <input type="text" class="time-box readonly right-shift"
                                    value="{{ \Carbon\Carbon::parse(optional($application)->new_clock_out ?? $attendance->clock_out)->format('H:i') }}" readonly>
                                @else
                                <input type="text" class="time-box" name="new_clock_in"
                                    value="{{ old('new_clock_in', \Carbon\Carbon::parse(optional($application)->new_clock_in ?? $attendance->clock_in)->format('H:i')) }}">
                                <span class="separator">〜</span>
                                <input type="text" class="time-box" name="new_clock_out"
                                    value="{{ old('new_clock_out', \Carbon\Carbon::parse(optional($application)->new_clock_out ?? $attendance->clock_out)->format('H:i')) }}">
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>休憩</th>
                    <td class="time-row @if($application && $application->approval_status === 'approved') approved @endif">
                        <div class="time-block">
                            <div class="time-input-group">
                                @if($application && in_array($application->approval_status, ['pending', 'approved']))
                                <input type="text" class="time-box readonly"
                                    value="{{ optional($application)->new_break_in
                                                ? \Carbon\Carbon::parse($application->new_break_in)->format('H:i')
                                                : ($attendance->break_in ? \Carbon\Carbon::parse($attendance->break_in)->format('H:i') : '') }}" readonly>
                                <span class="separator">〜</span>
                                <input type="text" class="time-box readonly right-shift"
                                    value="{{ optional($application)->new_break_out
                                                ? \Carbon\Carbon::parse($application->new_break_out)->format('H:i')
                                                : ($attendance->break_out ? \Carbon\Carbon::parse($attendance->break_out)->format('H:i') : '') }}" readonly>
                                @else
                                <input type="text" class="time-box" name="new_break_in"
                                    value="{{ old('new_break_in',
                                                optional($application)->new_break_in
                                                    ? \Carbon\Carbon::parse($application->new_break_in)->format('H:i')
                                                    : ($attendance->break_in ? \Carbon\Carbon::parse($attendance->break_in)->format('H:i') : '')
                                            ) }}">
                                <span class="separator">〜</span>
                                <input type="text" class="time-box" name="new_break_out"
                                    value="{{ old('new_break_out',
                                                optional($application)->new_break_out
                                                    ? \Carbon\Carbon::parse($application->new_break_out)->format('H:i')
                                                    : ($attendance->break_out ? \Carbon\Carbon::parse($attendance->break_out)->format('H:i') : '')
                                            ) }}">
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>休憩２</th>
                    <td class="time-row @if($application && $application->approval_status === 'approved') approved @endif">
                        <div class="time-block">
                            <div class="time-input-group">
                                @if($application && in_array($application->approval_status, ['pending', 'approved']))
                                <input type="text" class="time-box readonly"
                                    value="{{ optional($application)->new_break2_in
                                                ? \Carbon\Carbon::parse($application->new_break2_in)->format('H:i')
                                                : ($attendance->break2_in ? \Carbon\Carbon::parse($attendance->break2_in)->format('H:i') : '') }}" readonly>
                                <span class="separator">〜</span>
                                <input type="text" class="time-box readonly right-shift"
                                    value="{{ optional($application)->new_break2_out
                                                ? \Carbon\Carbon::parse($application->new_break2_out)->format('H:i')
                                                : ($attendance->break2_out ? \Carbon\Carbon::parse($attendance->break2_out)->format('H:i') : '') }}" readonly>
                                @else
                                <input type="text" class="time-box" name="new_break2_in"
                                    value="{{ old('new_break2_in',
                                                optional($application)->new_break2_in
                                                    ? \Carbon\Carbon::parse($application->new_break2_in)->format('H:i')
                                                    : ($attendance->break2_in ? \Carbon\Carbon::parse($attendance->break2_in)->format('H:i') : '')
                                            ) }}">
                                <span class="separator">〜</span>
                                <input type="text" class="time-box" name="new_break2_out"
                                    value="{{ old('new_break2_out',
                                                optional($application)->new_break2_out
                                                    ? \Carbon\Carbon::parse($application->new_break2_out)->format('H:i')
                                                    : ($attendance->break2_out ? \Carbon\Carbon::parse($attendance->break2_out)->format('H:i') : '')
                                            ) }}">
                                @endif
                            </div>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>備考</th>
                    <td>
                        @if($application && in_array($application->approval_status, ['pending', 'approved']))
                        <p class="comment readonly-text">
                            {{ optional($application)->comment ?? $attendance->comment }}
                        </p>
                        @else
                        <textarea class="comment" name="comment">{{ old('comment', optional($application)->comment ?? $attendance->comment) }}</textarea>
                        <p class="error-message">
                            @error('comment') {{ $message }} @enderror
                        </p>
                        @endif
                    </td>
                </tr>
            </table>

            @if ($application && in_array($application->approval_status, ['pending', 'approved']))
            @if($application->approval_status === 'pending')
            <p class="info-message">*承認待ちのため修正はできません。</p>
            @elseif($application->approval_status === 'approved')
            <p class="info-message">*承認済みのため修正はできません。</p>
            @endif
            @else
            <button type="submit" class="submit-btn" value="edit">修正</button>
            @endif

        </form>
        @endif
    </div>
</div>
@endsection