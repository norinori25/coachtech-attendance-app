<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        // 管理者ログイン画面から来た場合のみ
        if ($request->has('is_admin_login')) {
            return redirect('/admin/attendance/list');
        }

        return redirect('/attendance');
    }
}

