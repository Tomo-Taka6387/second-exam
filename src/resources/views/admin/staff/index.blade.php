@extends('layouts.auth')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/staff/index.css')}}?v={{ time() }}">
@endsection


@section('content')
<div class="attendance-index">
    <div class="attendance-container">
        <h1>スタッフ一覧</h1>
        <table class="staff-table">
            <tr class="table-row">
                <th class="table-title">名前</th>
                <th class="table-title">メールアドレス</th>
                <th class="table-title">月次詳細</th>
            </tr>

            @foreach($users as $user)
            <tr class="table-column">
                <td class="table-date">{{ $user->name }}</td>
                <td class="table-date">{{ $user->email }}</td>
                <td class="table-date"><a class="detail-btn" href="{{ route('admin.staff.attendances', $user->id) }}">詳細</a></td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@endsection