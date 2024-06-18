<?php

use App\Http\Controllers\municipalityAdmin\AttendenceReportController;

Route::get('/attendance-reports', [AttendenceReportController::class, 'index'])->name('attendance_reports.index');
Route::get('/attendance-reports/report', [AttendenceReportController::class, 'report'])->name('attendance_reports.report');