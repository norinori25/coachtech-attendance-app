<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Http\Requests\LoginRequest as CustomLoginRequest;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\RegisterResponse;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\LogoutResponse;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;
use App\Http\Responses\LoginResponse as CustomLoginResponse;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * サービス登録
     */
    public function register(): void
    {
        // 会員登録後はメール認証案内へ
        $this->app->singleton(RegisterResponse::class, function () {
            return new class implements RegisterResponse {
                public function toResponse($request)
                {
                    return redirect()->route('verification.notice');
                }
            };
        });

        // ログイン後リダイレクト（is_adminで分岐）
        $this->app->singleton(LoginResponse::class, CustomLoginResponse::class);

        // ログアウト後
        $this->app->singleton(LogoutResponse::class, function () {
            return new class implements LogoutResponse {
                public function toResponse($request)
                {
                    return redirect('/login');
                }
            };
        });

        // Fortify標準LoginRequest → 自作FormRequest
        $this->app->singleton(FortifyLoginRequest::class, CustomLoginRequest::class);
    }

    /**
     * 起動処理
     */
    public function boot(): void
    {
        // 新規登録処理
        Fortify::createUsersUsing(CreateNewUser::class);

        // 会員登録画面
        Fortify::registerView(function () {
            return view('auth.register');
        });

        // ログイン画面（共通）
        Fortify::loginView(function () {
            return view('auth.login');
        });

        // ログイン試行制限
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;

            return Limit::perMinute(10)->by($email . $request->ip());
        });
    }
}
