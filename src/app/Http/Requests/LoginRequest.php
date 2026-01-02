<?php

namespace App\Http\Requests;

use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Http\Requests\LoginRequest as FortifyLoginRequest;

class LoginRequest extends FortifyLoginRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8'],
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'メールアドレスを入力してください',
            'email.email' => 'メール形式で入力してください',
            'password.required' => 'パスワードを入力してください',
            'password.min' => 'パスワードは8文字以上で入力してください',
        ];
    }

    public function username()
    {
        return 'email';
    }

    /**
     * 管理者ログイン判定
     */
    protected function passedValidation()
    {
        // 管理者ログイン画面から来た場合
        if ($this->input('is_admin_login')) {

            // 認証はまだなので一旦 attempt する
            if (! auth()->attempt($this->only('email', 'password'))) {
                throw ValidationException::withMessages([
                    'email' => '認証情報が正しくありません',
                ]);
            }

            // 管理者でなければ弾く
            if (! auth()->user()->is_admin) {
                auth()->logout();

                throw ValidationException::withMessages([
                    'email' => '管理者アカウントではありません',
                ]);
            }
        }
    }
}
