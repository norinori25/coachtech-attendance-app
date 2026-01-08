<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Support\Facades\Auth;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        // 管理者ログイン
        if (
            $request->has('is_admin_login') &&
            Auth::check() &&
            Auth::user()->is_admin
        ) {
            return redirect()->intended('/admin/attendance/list');
        }

        // 一般ユーザー
        return redirect()->intended('/attendance');
    }
}
