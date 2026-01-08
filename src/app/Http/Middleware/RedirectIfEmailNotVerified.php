<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectIfEmailNotVerified
{
    public function handle(Request $request, Closure $next)
    {
        // ログイン試行後に未認証ユーザーがセッションに入っている場合
        if (session()->has('unauthenticated_user')) {
            return redirect()->route('verification.notice');
        }

        return $next($request);
    }
}