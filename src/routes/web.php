<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisteredUserController;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\User\AttendanceController;
use App\Http\Controllers\User\AttendanceRequestController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\RequestController;
use App\Http\Controllers\Auth\VerifyEmailController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ==============================
// 認証（Fortify）
// ==============================

// 会員登録
Route::get('/register', [RegisteredUserController::class, 'create']);
Route::post('/register', [RegisteredUserController::class, 'store']);

// ログイン（一般・管理者）
Route::get('/login', fn() => view('auth.login'))
->middleware('redirect.unverified')
->name('login');
Route::get('/admin/login', fn() => view('admin.auth.login'))->name('admin.login');

Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// ==============================
// 管理者（is_admin）
// ==============================
Route::middleware(['auth:web', 'can:admin'])->group(function () {

    // 勤怠一覧
    Route::get('/admin/attendance/list', [AdminAttendanceController::class, 'index'])
        ->name('admin.attendance.index');

    // 勤怠詳細（表示）
    Route::get('/admin/attendance/{id}', [AdminAttendanceController::class, 'show'])
        ->name('admin.attendance.show');

    // 勤怠更新
    Route::post('/admin/attendance/{id}', [AdminAttendanceController::class, 'update'])
        ->name('admin.attendance.update');

    // スタッフ一覧
    Route::get('/admin/staff/list', [StaffController::class, 'index'])
        ->name('admin.staff.index');

    // スタッフ別勤怠
    Route::get('/admin/attendance/staff/{id}', [AdminAttendanceController::class, 'staffList'])
        ->name('admin.attendance.staffList');

    Route::get('/admin/attendance/staff/{id}/export', [AdminAttendanceController::class, 'exportCsv'])
        ->name('admin.attendance.exportCsv');

    // 修正申請一覧（管理者用）
    Route::get('/admin/stamp_correction_request/list', [RequestController::class, 'index'])
        ->name('admin.attendance_request.index');

    // 修正申請承認
    Route::get('/admin/stamp_correction_request/approve/{attendance_correct_request_id}', [RequestController::class, 'approveForm'])
        ->name('admin.attendance_request.approveForm');

    Route::post('/admin/stamp_correction_request/approve/{attendance_correct_request_id}', [RequestController::class, 'approve'])
        ->name('admin.attendance_request.approve');
});

// ==============================
// 一般ユーザー（ログイン＋メール認証後）
// ==============================
Route::middleware(['auth:web', 'verified'])->group(function () {

    // 出勤登録
    Route::get('/attendance', [AttendanceController::class, 'index']);
    Route::post('/attendance', [AttendanceController::class, 'store']);

    // 勤怠一覧
    Route::get('/attendance/list', [AttendanceController::class, 'list'])
        ->name('attendance.index');

    // 勤怠詳細
    Route::get('/attendance/detail/{id}', [AttendanceController::class, 'show'])
        ->name('attendance.show');

    // 勤怠修正申請一覧（ユーザー用）
    Route::get('/stamp_correction_request/list', [AttendanceRequestController::class, 'list'])
        ->name('attendance_request.index');

    Route::get('/stamp_correction_request/{id}', [AttendanceRequestController::class, 'show'])
        ->name('attendance_request.show');

    // 修正申請保存
    Route::post('/stamp_correction_request', [AttendanceRequestController::class, 'store'])
        ->name('attendance_request.store');
});

// ==============================
// メール認証
// ==============================
Route::get('/email/verify', [VerifyEmailController::class, 'notice'])
    ->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, 'verify'])
    ->middleware('signed')
    ->name('verification.verify');

Route::post('/email/verification-notification', [VerifyEmailController::class, 'resend'])
    ->name('verification.send');