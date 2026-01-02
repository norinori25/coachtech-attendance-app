<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AttendanceRequest;
use App\Models\Attendance;
use App\Http\Requests\AttendanceCorrectionRequest;

class AttendanceRequestController extends Controller
{
    /**
     * 勤怠修正申請一覧表示
     */
    public function list(Request $request)
    {
        $user   = auth()->user();
        $status = $request->input('status'); // 'pending' or 'approved'

        $query = AttendanceRequest::where('user_id', $user->id);

        if ($status === 'pending') {
            $query->where('status', '承認待ち');
        } elseif ($status === 'approved') {
            $query->where('status', '承認済み');
        }

        $requests = $query->orderByDesc('created_at')->get();

        return view('user.request.index', compact('requests'));
    }

    /**
     * 勤怠修正申請登録
     */
    public function store(AttendanceCorrectionRequest $request)
    {
        $attendance = Attendance::find($request->attendance_id);

        // 既存の「承認待ち」申請がある場合は修正不可
        $existingRequest = AttendanceRequest::where('attendance_id', $request->attendance_id)
            ->where('user_id', auth()->id())
            ->where('status', '承認待ち')
            ->first();

        if ($existingRequest) {
            return back()->withErrors([
                'message' => '承認待ちのため修正はできません。'
            ]);
        }

        AttendanceRequest::create([
            'attendance_id'   => $request->attendance_id,
            'user_id'         => auth()->id(),
            'attendance_date' => $attendance?->date,
            'reason'          => $request->note,
            'break_start'     => $request->break_start_new,
            'break_end'       => $request->break_end_new,
            'status'          => '承認待ち',
        ]);

        return redirect()->route('attendance_request.index', ['status' => 'pending'])
            ->with('message', '修正申請を送信しました。承認待ち一覧に表示されます。');
    }

    /**
     * 勤怠修正申請詳細表示
     */
    public function show($id)
    {
        $attendanceRequest = AttendanceRequest::with(['attendance', 'user'])
            ->findOrFail($id);

        return view('user.request.show', compact('attendanceRequest'));
    }
}