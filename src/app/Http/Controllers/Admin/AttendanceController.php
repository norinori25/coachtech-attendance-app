<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;

class AttendanceController extends Controller
{

    public function index(Request $request)
    {
        $date = $request->input('date', now()->toDateString());
        $currentDate = Carbon::parse($date);

        $attendances = Attendance::with(['user', 'breakRecords'])
            ->whereDate('date', $currentDate)
            ->orderBy('user_id')
            ->get();

        return view('admin.attendance.index', compact(
            'attendances',
            'currentDate'
        ));
    }

    /**
     * 勤怠詳細（1件）
     */
    public function show($id)
    {
        $attendance = Attendance::with([
            'user',
            'breakRecords',
            'attendanceRequest',
        ])->findOrFail($id);

        $attendanceRequest = $attendance->attendanceRequest ?? null;

        return view('user.attendance.show', compact(
            'attendance',
            'attendanceRequest'
        ));
    }

    /**
     * 管理者：勤怠更新
     */
    public function update(Request $request, $id)
    {
        // バリデーション
        $request->validate([
            'start_time' => [
                'required',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->end_time && $value > $request->end_time) {
                        $fail('出勤時間もしくは退勤時間が不適切な値です');
                    }
                }
            ],
            'end_time' => ['required'],
            'break_start_time' => [
                'nullable',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value && $request->end_time && $value > $request->end_time) {
                        $fail('休憩時間が不適切な値です');
                    }
                }
            ],
            'break_end_time' => [
                'nullable',
                function ($attribute, $value, $fail) use ($request) {
                    if ($value && $request->end_time && $value > $request->end_time) {
                        $fail('休憩時間もしくは退勤時間が不適切な値です');
                    }
                }
            ],
            'note' => ['required', 'string'],
        ], [
            // ★ これが必要！
            'note.required' => '備考を記入してください',
        ]);

        // 勤怠データ更新
        $attendance = Attendance::findOrFail($id);

        $attendance->update([
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'note' => $request->note,
        ]);

        return back()->with('message', '勤怠情報を更新しました');
    }

    /**
     * スタッフ別勤怠一覧（月次）
     */
    public function staffList($id, Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $currentMonth = Carbon::parse($month . '-01');

        $user = User::findOrFail($id);

        $attendances = Attendance::with('breakRecords')
            ->where('user_id', $id)
            ->whereYear('date', $currentMonth->year)
            ->whereMonth('date', $currentMonth->month)
            ->orderBy('date')
            ->get();

        return view(
            'admin.staff.attendance',
            compact('user', 'attendances', 'currentMonth')
        );
    }

    public function exportCsv($id)
    {
        $user = User::findOrFail($id);
        $attendances = Attendance::where('user_id', $id)->get();

        $csvData = [];
        $csvData[] = ['名前', '日付', '出勤', '退勤', '休憩合計', '勤務時間'];

        foreach ($attendances as $attendance) {
            $csvData[] = [
                $user->name,
                '="' . Carbon::parse($attendance->date)->format('y-m-d') . '"',
                optional($attendance->start_time)->format('H:i'),
                optional($attendance->end_time)->format('H:i'),
                $attendance->break_total,
                $attendance->total_hours,
            ];
        }

        $stream = fopen('php://temp', 'r+');
        foreach ($csvData as $line) {
            $line = array_map(function ($v) {
                return mb_convert_encoding($v, 'SJIS-win', 'UTF-8');
            }, $line);
            fputcsv($stream, $line);
        }
        rewind($stream);

        $filename = $user->name . '_attendance.csv';

        return response()->streamDownload(function () use ($stream) {
            fpassthru($stream);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=Shift_JIS',
        ]);
    }
}