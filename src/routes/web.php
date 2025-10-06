<?php

use Illuminate\Support\Facades\Route;

// ===== スタッフ側 =====
Route::redirect('/', '/login');
Route::redirect('/home', '/attendance');

Route::middleware('guest')->group(function () {
    Route::get('/login', fn () => view('staff.auth.login'))->name('login');
    Route::get('/register', fn () => view('staff.auth.register'))->name('register');
});

Route::get('/email/verify', fn () => view('auth.verify-email'))
    ->middleware('auth')
    ->name('verification.notice');

Route::middleware(['auth', 'verified'])->group(function () {
    // 打刻
    Route::get('/attendance', [\App\Http\Controllers\Staff\AttendanceController::class, 'create'])
        ->name('staff.attendance.create');
    Route::post('/attendance/clock-in', [\App\Http\Controllers\Staff\AttendanceController::class, 'clockIn'])
        ->name('staff.attendance.clockin');
    Route::post('/attendance/start-break', [\App\Http\Controllers\Staff\AttendanceController::class, 'startBreak'])
        ->name('staff.attendance.breakin');
    Route::post('/attendance/end-break', [\App\Http\Controllers\Staff\AttendanceController::class, 'endBreak'])
        ->name('staff.attendance.breakout');
    Route::post('/attendance/clock-out', [\App\Http\Controllers\Staff\AttendanceController::class, 'clockOut'])
        ->name('staff.attendance.clockout');

    // 一覧・詳細
    Route::get('/attendance/list', [\App\Http\Controllers\Staff\AttendanceController::class, 'index'])
        ->name('staff.attendance.index');
    Route::get('/attendance/detail/{id}', [\App\Http\Controllers\Staff\AttendanceController::class, 'show'])
        ->name('staff.attendance.show');

    // 申請（スタッフ）
    Route::get('/requests', [\App\Http\Controllers\Staff\StampCorrectionRequestController::class, 'index'])
        ->name('staff.requests.index');
    Route::post('/attendance/{attendance}/request-correction', [\App\Http\Controllers\Staff\StampCorrectionRequestController::class, 'store'])
        ->name('staff.requests.store');
});


// ===== 管理者側 =====
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;

Route::prefix('admin')->name('admin.')->group(function () {

    // 認証
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login.form');
        Route::post('/login', [AdminLoginController::class, 'login'])->name('login');
    });

    Route::middleware('auth:admin')->group(function () {
        Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');

        // ダッシュボード/一覧
        Route::get('/attendance', [AdminAttendanceController::class, 'daily'])->name('attendance.daily');
        Route::get('/attendance/detail/{id}', [AdminAttendanceController::class, 'show'])->name('attendance.show');

        Route::patch('/attendance/{attendance}', [AdminAttendanceController::class, 'update'])->name('attendance.update');

        // スタッフ関連
        Route::get('/staff', [AdminAttendanceController::class, 'staffList'])->name('staff.index');
        Route::get('/attendance/staff/{id}', [AdminAttendanceController::class, 'staffMonth'])->name('attendance.staff.month');
        Route::get('/attendance/staff/{id}/csv', [AdminAttendanceController::class, 'exportCsv'])->name('attendance.staff.csv');

        // 申請（管理者）
        Route::get('/requests', [\App\Http\Controllers\Admin\RequestController::class, 'index'])->name('requests.index');
        Route::get('/requests/{id}', [\App\Http\Controllers\Admin\RequestController::class, 'show'])->name('requests.show');
        Route::post('/requests/{id}/approve', [\App\Http\Controllers\Admin\RequestController::class, 'approve'])->name('requests.approve');
    });
});
