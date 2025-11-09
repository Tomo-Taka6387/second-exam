<?php


namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use App\Actions\Fortify\CreateNewUser;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Contracts\LogoutResponse;
use App\Http\Requests\LoginRequest;
use Illuminate\Cache\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;


class FortifyServiceProvider extends ServiceProvider
{
    public function register() {}

    public function boot()
    {
        Fortify::loginView(function () {
            if (request()->is('admin/*')) {
                return view('admin.auth.login');
            }
            return view('user.auth.login');
        });



        Fortify::createUsersUsing(CreateNewUser::class);


        Fortify::registerView(function () {
            return view('user.auth.register');
        });

        $this->app->singleton(RegisterResponse::class, function () {
            return new class implements RegisterResponse {
                public function toResponse($request)
                {
                    return redirect('/attendance');
                }
            };
        });


        $this->app->singleton(LoginResponse::class, function () {
            return new class implements LoginResponse {
                public function toResponse($request)
                {

                    if (Auth::guard('admin')->check()) {
                        return redirect()->route('admin.attendance.index');
                    }

                    return redirect()->route('attendance.index');
                }
            };
        });


        $this->app->singleton(LogoutResponse::class, function () {
            return new class implements LogoutResponse {
                public function toResponse($request)
                {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    return redirect('/login');
                }
            };
        });

        $rateLimiter = app(RateLimiter::class);
        $rateLimiter->for('login', function (Request $request) {
            return Limit::perMinute(10)->by($request->email . $request->ip());
        });

        app()->bind(LoginRequest::class, LoginRequest::class);
    }
}
