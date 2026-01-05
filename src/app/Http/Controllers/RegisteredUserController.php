<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use App\Actions\Fortify\CreateNewUser;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }
    
    public function store(
        Request $request,
        CreateNewUser $creator
    ) {
        event(new Registered($user = $creator->create($request->all())));

        // 認証前ユーザーをメール認証画面で利用するために保存
        session()->put('unauthenticated_user', $user);

        return redirect()->route('verification.notice');
    }
}