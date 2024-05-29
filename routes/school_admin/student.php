<?php

use App\Http\Controllers\SchoolAdmin\StudentController;


Route::resource('students', StudentController::class);
Route::post('student/get', [StudentController::class, 'getAllStudent'])->name('students.get');


Route::get('students-additionalinformations/{student_id}', [StudentController::class, 'additionalInformationStudents'])->name('students.additionalinformations_create');

Route::post('students/{student_id}/additional-information', [StudentController::class, 'updateAdditionalInformation'])->name('students.additionalInformation.update');




Route::get('students/get-sections/{classId}', [StudentController::class, 'getSections'])->name('student.get-sections');

Route::get('/get-district-by-state/{state_id}', [StudentController::class, 'getDistrict'])->name('get-districts');
Route::get('student/import/index', [StudentController::class, 'importAllStudentIndex'])->name('students.import');

Route::post('/import', [StudentController::class, 'import'])->name('students.bulkimport');