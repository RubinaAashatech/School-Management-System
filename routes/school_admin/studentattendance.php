<?php

use App\Http\Controllers\SchoolAdmin\StudentAttendanceController;

Route::resource('student-attendances', StudentAttendanceController::class);
Route::post('student-attendances/get', [StudentAttendanceController::class, 'getAllStudentAttendance'])->name('student-attendances.get');

Route::post('student-attendances/save-attendance', [StudentAttendanceController::class, 'saveAttendance']);
