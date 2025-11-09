@extends('layouts.auth')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/attendance/show.css') }}?v={{ time() }}">
@endsection

@section('content')
<div class="attendance-details">
    <div class="attendance-container">
        <h1>勤怠詳細</h1>

        @if(!$attendance || !$attendance->id)
        <p class="no-attendance-message">この日の勤怠は登録されていません。</p>

        @else
        <form action="{{ route('admin.attendance.update', $attendance->id) }}" method="POST">
            @csrf
            @method('PUT')

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
                    <td class="time-row">
                        <div class="time-block">
                            <div class="time-input-group">
                                <input type="text" class="time-box" name="new_clock_in"
                                    value="{{ old('new_clock_in', $attendance->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '') }}">
                                <span class="separator">〜</span>
                                <input type="text" class="time-box" name="new_clock_out"
                                    value="{{ old('new_clock_out', $attendance->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '') }}">
                            </div>
                            <p class="error-message">
                                @error('new_clock_in') {{ $message }} @enderror
                                @error('new_clock_out') {{ $message }} @enderror
                            </p>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>休憩</th>
                    <td class="time-row">
                        <div class="time-block">
                            <div class="time-input-group">
                                <input type="text" class="time-box" name="new_break_in"
                                    value="{{ old('new_break_in', $attendance->break_in ? \Carbon\Carbon::parse($attendance->break_in)->format('H:i') : '') }}">
                                <span class="separator">〜</span>
                                <input type="text" class="time-box" name="new_break_out"
                                    value="{{ old('new_break_out', $attendance->break_out ? \Carbon\Carbon::parse($attendance->break_out)->format('H:i') : '') }}">
                            </div>
                            <p class="error-message">
                                @error('new_break_in') {{ $message }} @enderror
                                @error('new_break_out') {{ $message }} @enderror
                            </p>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>休憩2</th>
                    <td class="time-row">
                        <div class="time-block">
                            <div class="time-input-group">
                                <input type="text" class="time-box" name="new_break2_in"
                                    value="{{ old('new_break2_in', $attendance->break2_in ? \Carbon\Carbon::parse($attendance->break2_in)->format('H:i') : '') }}">
                                <span class="separator">〜</span>
                                <input type="text" class="time-box" name="new_break2_out"
                                    value="{{ old('new_break2_out', $attendance->break2_out ? \Carbon\Carbon::parse($attendance->break2_out)->format('H:i') : '') }}">
                            </div>
                            <p class="error-message">
                                @error('new_break2_in') {{ $message }} @enderror
                                @error('new_break2_out') {{ $message }} @enderror
                            </p>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>備考</th>
                    <td>
                        <textarea class="comment" name="comment">{{ old('comment', $attendance->comment ?? '') }}</textarea>
                        <p class="error-message">
                            @error('comment') {{ $message }} @enderror
                        </p>
                    </td>
                </tr>
            </table>

            <button type="submit" class="submit-btn" value="edit">修正</button>
        </form>
        @endif
    </div>
</div>
@endsection