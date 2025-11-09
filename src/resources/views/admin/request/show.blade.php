@extends('layouts.auth')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/request/show.css') }}?v={{ time() }}">
@endsection

@section('content')
<div class="request-index">
    <div class="request-container">
        <h1>勤怠詳細</h1>

        <form action="{{ route('admin.request.approve', ['id' => $application->id]) }}" method="post">
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
                                {{ \Carbon\Carbon::parse($attendance->new_date ?? $attendance->date)->format('Y年') }}
                            </div>
                        </div>
                        <div class="date-block">
                            <div class="date-text readonly">
                                {{ \Carbon\Carbon::parse($attendance->new_date ?? $attendance->date)->format('n月j日') }}
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th>出勤・退勤</th>
                    <td class="time-row">
                        <div class="time-block">
                            <div class="time-input-group">
                                @if($application->approval_status === 'approved')
                                <input type="text" class="time-box readonly"
                                    value="{{ $application->new_clock_in ? \Carbon\Carbon::parse($application->new_clock_in)->format('H:i') : '' }}" readonly>
                                <span class="separator readonly">〜</span>
                                <input type="text" class="time-box_out readonly"
                                    value="{{ $application->new_clock_out ? \Carbon\Carbon::parse($application->new_clock_out)->format('H:i') : '' }}" readonly>
                                @else
                                <input type="text" class="time-box" name="new_clock_in"
                                    value="{{ old('new_clock_in', $application->new_clock_in ? \Carbon\Carbon::parse($application->new_clock_in)->format('H:i') : '') }}">
                                <span class="separator">〜</span>
                                <input type="text" class="time-box_out" name="new_clock_out"
                                    value="{{ old('new_clock_out', $application->new_clock_out ? \Carbon\Carbon::parse($application->new_clock_out)->format('H:i') : '') }}">
                                @endif
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
                                @if($application->approval_status === 'approved')
                                <input type="text" class="time-box readonly"
                                    value="{{ $application->new_break_in ? \Carbon\Carbon::parse($application->new_break_in)->format('H:i') : '' }}" readonly>
                                <span class="separator readonly">〜</span>
                                <input type="text" class="time-box_out readonly"
                                    value="{{ $application->new_break_out ? \Carbon\Carbon::parse($application->new_break_out)->format('H:i') : '' }}" readonly>
                                @else
                                <input type="text" class="time-box" name="new_break_in"
                                    value="{{ old('new_break_in', $application->new_break_in ? \Carbon\Carbon::parse($application->new_break_in)->format('H:i') : '') }}">
                                <span class="separator">〜</span>
                                <input type="text" class="time-box_out" name="new_break_out"
                                    value="{{ old('new_break_out', $application->new_break_out ? \Carbon\Carbon::parse($application->new_break_out)->format('H:i') : '') }}">
                                @endif
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
                                @if($application->approval_status === 'approved')
                                <input type="text" class="time-box readonly"
                                    value="{{ $application->new_break2_in ? \Carbon\Carbon::parse($application->new_break2_in)->format('H:i') : '' }}" readonly>
                                <span class="separator readonly">〜</span>
                                <input type="text" class="time-box_out readonly"
                                    value="{{ $application->new_break2_out ? \Carbon\Carbon::parse($application->new_break2_out)->format('H:i') : '' }}" readonly>
                                @else
                                <input type="text" class="time-box" name="new_break2_in"
                                    value="{{ old('new_break2_in', $application->new_break2_in ? \Carbon\Carbon::parse($application->new_break2_in)->format('H:i') : '') }}">
                                <span class="separator">〜</span>
                                <input type="text" class="time-box_out" name="new_break2_out"
                                    value="{{ old('new_break2_out', $application->new_break2_out ? \Carbon\Carbon::parse($application->new_break2_out)->format('H:i') : '') }}">
                                @endif
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
                    <td class="comment-row">
                        @if($application->approval_status === 'approved')
                        <p class="comment readonly-text">{{ $application->comment ?? $attendance->comment }}</p>
                        @else
                        <textarea class="comment" name="comment">{{ old('comment', $application->comment ?? $attendance->comment) }}</textarea>
                        <p class="error-message">
                            @error('comment') {{ $message }} @enderror
                        </p>
                        @endif
                    </td>
                </tr>
            </table>

            @if($application->approval_status === 'approved')
            <p class="info-message">承認済み</p>
            @else
            <div class="request-btn">
                <button type="submit" class="submit-btn">承認</button>
            </div>
            @endif

        </form>
    </div>
</div>
@endsection