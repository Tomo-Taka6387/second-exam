<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ApplicationController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminAttendanceController;
use App\Http\Controllers\AdminRequestController;


Route::get('/register', [UserController::class, 'create'])->name('register');
Route::post('/register', [UserController::class, 'store'])->name('register.submit');

Route::post('/login', [UserController::class, 'login'])->name('user.submit');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');


Route::get('/email/verify', function () {
    return view('user.auth.verify-email');
})->middleware('auth')->name('verification.notice');


Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    if (!auth()->check()) {
        $user = \App\Models\User::find($request->route('id'));
        if ($user) {
            Auth::login($user);
        }
    }

    $request->fulfill();

    return redirect()->route('attendance.index')->with('verified', true);
})->middleware(['signed'])->name('verification.verify');


Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('resent', true);
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');


Route::middleware(['auth:web', 'verified'])->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/attendance/list', [AttendanceController::class, 'list'])->name('attendance.list');
    Route::get('/attendance/detail/{id}', [AttendanceController::class, 'show'])->name('attendance.show');
    Route::post('/attendance/detail/{id}', [AttendanceController::class, 'update'])->name('attendance.update');
    Route::get('/stamp_correction_request/list', [ApplicationController::class, 'index'])->name('applications.index');
    Route::post('/stamp_correction_request/list/{id}', [ApplicationController::class, 'store'])->name('applications.store');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminController::class, 'login'])->name('login.submit');


    Route::middleware('auth:admin')->group(function () {
        Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
        Route::get('/users', [AdminController::class, 'index'])->name('staff.index');
        Route::get('/users/{user}/attendances', [AdminAttendanceController::class, 'showStaffAttendance'])->name('staff.attendances');
        Route::get('/attendances', [AdminAttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/attendances/{id}', [AdminAttendanceController::class, 'show'])->name('attendance.show');
        Route::put('/attendances/{id}', [AdminAttendanceController::class, 'update'])->name('attendance.update');
        Route::get('/requests', [AdminRequestController::class, 'index'])->name('request.index');
        Route::get('/requests/{id}', [AdminRequestController::class, 'show'])->name('request.show');
        Route::post('/requests/{id}', [AdminRequestController::class, 'approve'])->name('request.approve');
    });

});