<?php

use App\Http\Controllers\SchoolAdmin\SchoolAttendanceReportController;

Route::get('/attendance-reports', [SchoolAttendanceReportController::class, 'index'])->name('attendance_reports.index');
Route::get('/attendance-reports/schoolreport', [SchoolAttendanceReportController::class, 'report'])->name('attendance_reports.schoolreport');
Route::get('admin/attendance-reports/data', [SchoolAttendanceReportController::class, 'getData'])->name('attendance_reports.data');
Route::get('/get-sections-by-class/{classId}', [SchoolAttendanceReportController::class, 'getSectionsByClass'])->name('get.sections.by.class');



