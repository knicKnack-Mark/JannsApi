<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\Api\StaffController;
use App\Http\Controllers\Api\PayrollController;
use App\Http\Controllers\Api\StaffAttendanceController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/admin/dashboard/stats', [DashboardController::class, 'stats']);
});

Route::prefix('bookings')->group(function () {
    Route::get('/', [BookingController::class, 'index']);
    Route::post('/', [BookingController::class, 'store']);
    Route::put('/{id}', [BookingController::class, 'update']);
    Route::delete('/{id}', [BookingController::class, 'destroy']);
    Route::post('/{id}/payment', [BookingController::class, 'addPayment']);
});

Route::post('/bookings/{id}/cancel', [BookingController::class, 'cancel']);

Route::prefix('attendance')->group(function () {
    Route::get('/', [AttendanceController::class, 'index']);
    Route::post('/', [AttendanceController::class, 'store']);
    Route::delete('/{id}', [AttendanceController::class, 'destroy']);
});

Route::apiResource('rooms', RoomController::class);
Route::apiResource('schedules', ScheduleController::class);
Route::apiResource('staff', StaffController::class);

Route::prefix('payrolls')->group(function () {
    Route::get('/', [PayrollController::class, 'index']);
    Route::post('/generate', [PayrollController::class, 'generate']);
    Route::patch('/{payroll}/paid', [PayrollController::class, 'markAsPaid']);
});

Route::prefix('staff-attendance')->group(function () {
    Route::get('/', [StaffAttendanceController::class, 'index']);
    Route::patch('/{attendance}/time-in', [StaffAttendanceController::class, 'timeIn']);
    Route::patch('/{attendance}/time-out', [StaffAttendanceController::class, 'timeOut']);
    Route::patch('/{attendance}/absent', [StaffAttendanceController::class, 'markAbsent']);
    Route::patch('/{attendance}/remarks', [StaffAttendanceController::class, 'updateRemarks']);
});