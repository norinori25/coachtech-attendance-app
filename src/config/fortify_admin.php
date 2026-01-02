<?php

use App\Providers\RouteServiceProvider;
use Laravel\Fortify\Features;

return [
    'guard' => 'admin',
    'passwords' => 'users',
    'username' => 'email',
    'email' => 'email',
    'lowercase_usernames' => true,

    // 管理者ログイン後のリダイレクト先
    'home' => RouteServiceProvider::ADMIN_HOME,

    // 管理者用のルートは /admin/login などになる
    'prefix' => 'admin',
    'domain' => null,
    'middleware' => ['web'],

    'limiters' => [
        'login' => 'login',
        'two-factor' => 'two-factor',
    ],

    'views' => true,

    'features' => [
        Features::resetPasswords(),
        Features::updatePasswords(),
    ],
];