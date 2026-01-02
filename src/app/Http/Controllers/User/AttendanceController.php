<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\BreakRecord;

class AttendanceController extends Controller
{
    // 打刻画面表示
    public function index()
    {
        $user = auth()->user();
        $today = now()->toDateString();

        $attendance = Attendance::firstOrCreate(
            ['user_id' => $user->id, 'date' => $today],
            ['status' => '勤務外']
        );

        return view('user.attendance.create', [
            'attendance' => $attendance,
            'status' => $attendance->status,
            'now' => now()->format('Y-m-d H:i'),
        ]);
    }

    // 打刻処理
    public function store(Request $request)
    {
        $user = auth()->user();
        $today = now()->toDateString();

        $attendance = Attendance::firstOrCreate(
            ['user_id' => $user->id, 'date' => $today],
            ['status' => '勤務外']
        );

        switch ($request->input('action')) {
            case 'start': // 出勤
                if ($attendance->status === '勤務外') {
                    $attendance->update([
                        'status' => '出勤中',
                        'start_time' => now(),
                    ]);
                }
                break;

            case 'break_in': // 休憩入
                if ($attendance->status === '出勤中') {
                    $attendance->update(['status' => '休憩中']);
                    BreakRecord::create([
                        'attendance_id' => $attendance->id,
                        'break_start' => now(),
                    ]);
                }
                break;

            case 'break_out': // 休憩戻
                if ($attendance->status === '休憩中') {
                    $attendance->update(['status' => '出勤中']);
                    $break = BreakRecord::where('attendance_id', $attendance->id)
                        ->whereNull('break_end')
                        ->latest()
                        ->first();
                    if ($break) {
                        $break->update(['break_end' => now()]);
                    }
                }
                break;

            case 'end': // 退勤
                if ($attendance->status === '出勤中') {
                    $attendance->update([
                        'status' => '退勤済',
                        'end_time' => now(),
                    ]);
                    return redirect()->back()->with('message', 'お疲れ様でした。');
                }
                break;
        }

        return redirect()->back();
    }

    // 勤怠一覧表示
    public function list(Request $request)
    {
        $user = auth()->user();

        // クエリパラメータから月を取得（なければ今月）
        $month = $request->input('month', now()->format('Y-m'));
        $currentMonth = \Carbon\Carbon::createFromFormat('Y-m', $month);

        // 当月の勤怠データを取得
        $attendances = Attendance::where('user_id', $user->id)
            ->whereYear('date', $currentMonth->year)
            ->whereMonth('date', $currentMonth->month)
            ->with('breakRecords')
            ->orderBy('date', 'asc')
            ->get();

        return view('user.attendance.index', compact('attendances', 'currentMonth'));
    }

    public function show($id)
    {
        $attendance = Attendance::with(['breakRecords','attendanceRequest'])->findOrFail($id);
        $attendanceRequest = $attendance->attendanceRequest ?? null;

        return view('user.attendance.show', [
            'attendance' => $attendance,
            'attendanceRequest' => $attendanceRequest,
        ]);
    }
}