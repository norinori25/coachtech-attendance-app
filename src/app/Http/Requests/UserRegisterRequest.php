<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRegisterRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'お名前を入力してください',
            'email.required' => 'メールアドレスを入力してください',
            'email.unique' => 'このメールアドレスは既に登録されています。',
            'password.required' => 'パスワードを入力してください',

            'password.min' => 'パスワードは8文字以上で入力してください',

            'password.confirmed' => 'パスワードと一致しません',
        ];
    }
}
