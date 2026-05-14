<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;

Route::get('/mentor', [AttendanceController::class, 'mentorView'])->name('mentor.qr');
Route::post('/mentor/login', [AttendanceController::class, 'mentorAuthenticate'])->name('mentor.login');
Route::get('/api/token', [AttendanceController::class, 'generateToken'])->name('api.token');

Route::get('/scan/{token}', [AttendanceController::class, 'scan'])->name('attendance.scan');
Route::post('/attendance/submit', [AttendanceController::class, 'submit'])->name('attendance.submit');

Route::get('/', function () {
    return redirect()->route('mentor.qr');
});