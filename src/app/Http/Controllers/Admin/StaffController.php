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
        $users = User::orderBy('id')->get();

        return view('admin.staff.index', compact('users'));
    }
}