<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\AttendanceRecord;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;

class UserController extends Controller
{
    public function create()
    {
        return view('user.auth.register');
    }


    public function store(RegisterRequest $request)
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => 'user',
        ]);

        Auth::login($user);

        return redirect()->route('verification.notice');
    }

    public function login(LoginRequest $request)
    {

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return back()->withErrors([
                'email' => 'ログイン情報が登録されていません。',
            ]);
        }


        $request->session()->regenerate();
        $user = Auth::user();

        if ($user->role === 'admin') {
            Auth::logout();
            return back()->withErrors([
                'email' => 'このアカウントではログインできません。',
            ]);
        }

        $today = now()->format('Y-m-d');

        $attendance = AttendanceRecord::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance) {
            session()->flash('attendance_status', 'not_worked');
        } elseif (is_null($attendance->clock_out)) {
            session()->flash('attendance_status', 'working');
        } else {
            session()->flash('attendance_status', 'finished');
        }

        return redirect()->route('attendance.index');
    }



    public function update(RegisterRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();

        $user->name = $validated['name'];
        $user->save();

        return redirect()->route('attendance.index');
    }


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('user.submit');
    }
}
