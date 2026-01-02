<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class StaffController extends Controller
{
    /**
     * スタッフ一覧表示
     */
    public function index()
    {
        // 全ユーザーを取得（必要なら並び替え）
        $users = User::orderBy('id')->get();

        // ビューに渡す
        return view('admin.staff.index', compact('users'));
    }
}