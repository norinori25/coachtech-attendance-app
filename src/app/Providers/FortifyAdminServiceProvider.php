<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\LogoutResponse;

class FortifyAdminServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // 管理者ログイン画面
        Fortify::loginView(function () {
            return view('admin.auth.login');
        });

        // 管理者認証処理
        Fortify::authenticateUsing(function ($request) {
            $credentials = $request->only('email', 'password');
            return Auth::guard('admin')->attempt($credentials)
                ? Auth::guard('admin')->user()
                : null;
        });

        // 管理者ログイン成功後のリダイレクト
        $this->app->singleton(LoginResponse::class, function ($app) {
            return new class implements LoginResponse {
                public function toResponse($request)
                {
                    return redirect()->intended('/admin/attendance/list');
                }
            };
        });

        // 管理者ログアウト後のリダイレクト
        $this->app->singleton(LogoutResponse::class, function ($app) {
            return new class implements LogoutResponse {
                public function toResponse($request)
                {
                    return redirect('/admin/login');
                }
            };
        });
    }
}