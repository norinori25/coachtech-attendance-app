<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AttendanceRequest;

class RequestController extends Controller
{
    /**
     * 修正申請一覧表示（管理者用）
     */
    public function index(Request $request)
    {
        $status = $request->input('status'); // 'pending' or 'approved'

        $query = AttendanceRequest::with(['user', 'attendance'])
            ->orderByDesc('created_at');

        if ($status === 'pending') {
            $query->where('status', '承認待ち');
        } elseif ($status === 'approved') {
            $query->where('status', '承認済み');
        }

        $requests = $query->get();

        return view('admin.request.index', compact('requests'));
    }

    /**
     * 修正申請承認フォーム表示
     */
    public function approveForm($attendance_correct_request_id)
    {
        $request = AttendanceRequest::with(['user', 'attendance'])
            ->findOrFail($attendance_correct_request_id);

        return view('admin.request.approve', compact('request'));
    }

    /**
     * 修正申請承認処理
     */
    public function approve($attendance_correct_request_id)
    {
        $request = AttendanceRequest::findOrFail($attendance_correct_request_id);
        $request->update(['status' => '承認済み']);

        return redirect()
            ->route('admin.attendance_request.index')
            ->with('message', '修正申請を承認しました。');
    }
}