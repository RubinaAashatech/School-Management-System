<?php

// use App\Models\GenerateMarkSheet;

use App\Http\Controllers\SchoolAdmin\GenerateMarkSheetController;

Route::resource('generate-marksheets', GenerateMarkSheetController::class);

Route::get('generate-marksheets/examination-id/{examination_id}', [GenerateMarkSheetController::class, 'getAllMarksheets'])->name('generate.marksheets.create');

Route::get('generate-marksheets/get-sections/{classId}', [GenerateMarkSheetController::class, 'getSections'])->name('generate.marksheet.get-sections');

Route::post('generate-marksheets/student/get', [GenerateMarkSheetController::class, 'getAllStudent'])->name('generate-student-marksheet.get');

Route::get('generate-marksheets/show-marksheet-design/{student_id}/{class_id}/{section_id}/{marksheetdesign_id}/{examination_id}', [GenerateMarkSheetController::class, 'showMarkSheetDesign'])->name('showmarksheetdesign.get');
// Route::get('/download-admit-card/{studentId}/{admitCardId}/{examinationId}', [GenerateMarkSheetController::class, 'downloadStudentMarkSheet'])->name('download.marksheet');
Route::get('generate-marksheets/download-marksheet/{student_id}/{class_id}/{section_id}/{marksheetdesign_id}/{examination_id}', [GenerateMarkSheetController::class, 'downloadStudentMarkSheet'])->name('downloadstudentmarksheet.get');