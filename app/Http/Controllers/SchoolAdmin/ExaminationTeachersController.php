<?php

namespace App\Http\Controllers\SchoolAdmin;

use Log;
use App\Models\User;
use App\Models\Classg;
use App\Models\Section;
use App\Models\Examination;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Models\ExaminationTeachers;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ExaminationTeachersController extends Controller
{
    //
    public function assignTeachers(string $id)
    {
        $examinations = Examination::find($id);
        $page_title = "Assign Teachers To " . $examinations->exam;
        $schoolId = session('school_id');
        $classes = Classg::where('school_id', $schoolId)
            ->orderBy('created_at', 'desc')
            ->get();

        $teachers = User::where('role_id', 6)
            ->where('school_id', $schoolId)
            ->pluck('f_name', 'id');
        $examinationTeachers = ExaminationTeachers::all();
        // dd($teachers);

        return view('backend.school_admin.examination.teacher.create', compact('page_title', 'classes', 'examinations', 'teachers', 'examinationTeachers'));
    }

    public function storeAssignTeachers(Request $request)
    {

        try {
            // Retrieve data from the request
            $examinationId = $request->input('examination_id');
            $classId = $request->input('class_id');
            $sectionId = $request->input('section_id');
            $teacherId = $request->input('user_id');
            // Update existing record or create a new one
            ExaminationTeachers::updateOrCreate(
                [
                    'examination_id' => $examinationId,
                    'class_id' => $classId,
                    'section_id' => $sectionId
                ],
                [
                    'user_id' => $teacherId,
                    'student_session_id' => 1,
                ]
            );
            return response()->json(['message' => 'Record stored successfully']);
        } catch (ModelNotFoundException $e) {

            return response()->json(['error' => 'Record not found.'], 404);
        } catch (\Exception $e) {

            return response()->json(['error' => 'Error processing data: ' . $e->getMessage()], 500);
        }
    }


    public function getAllExaminationsTeachers(Request $request)
    {
        $examinationTeachers = $this->getForDataTable($request->all());

        return Datatables::of($examinationTeachers)
            ->escapeColumns([])

            ->addColumn('class_id', function ($examinationTeachers) {
                return $examinationTeachers->class->class;
            })

            ->addColumn('section_id', function ($examinationTeachers) {
                return $examinationTeachers->section->section_name;
            })

            ->addColumn('user_id', function ($examinationTeachers) {
                return $examinationTeachers->user->f_name;
            })

            ->addColumn('actions', function ($examinationTeachers) {
                return view('backend.school_admin.examination.teacher.partials.controller_action', ['examinationTeachers' => $examinationTeachers])->render();
            })

            ->make(true);
    }

    public function getForDataTable($request)
    {
        $dataTableQuery = ExaminationTeachers::where(function ($query) use ($request) {
            if (isset($request->id)) {
                $query->where('id', $request->id);
            }
        })
            ->get();

        return $dataTableQuery;
    }
    public function edit(Request $request, $id)
    {
        try {
            // Find the ExaminationTeachers record by ID
            $page_title = 'Edit Examination Teacher';
            $examinations = Examination::find($id);
            // $page_title = "Assign Teachers To " . $examinations->exam;
            $schoolId = session('school_id');
            $classes = Classg::where('school_id', $schoolId)
                ->orderBy('created_at', 'desc')
                ->get();
            $examTeacher = ExaminationTeachers::findOrFail($id);
            $teachers = User::where('role_id', 6)
                ->where('school_id', $schoolId)
                ->pluck('f_name', 'id');
            $examinationTeachers = ExaminationTeachers::all();
            // Perform any necessary operations or data retrieval for editing

            // Return the view for editing with the necessary data
            $sections = Section::all(); // Fetch sections data
            return view('backend.school_admin.examination.teacher.update', compact('examTeacher', 'classes', 'teachers', 'examinationTeachers', 'examinations', 'examTeacher', 'page_title', 'sections'));
        } catch (ModelNotFoundException $e) {
            // Return error response if the record is not found
            return response()->json(['error' => 'Record not found.'], 404);
        } catch (\Exception $e) {
            // Return error response for any other exceptions
            return response()->json(['error' => 'Error editing record: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Retrieve the ExaminationTeachers record by ID
            $examTeacher = ExaminationTeachers::findOrFail($id);

            // Retrieve the examination_id from the request
            $examinationId = $request->input('examination_id');

            // Update the record with the new data from the request
            $examTeacher->update($request->all());

            return response()->json(['message' => 'Record updated successfully']);
        } catch (ModelNotFoundException $e) {
            // Return error response if the record is not found
            return response()->json(['error' => 'Record not found.'], 404);
        } catch (\Exception $e) {
            // Return error response for any other exceptions
            return response()->json(['error' => 'Error updating record: ' . $e->getMessage()], 500);
        }
    }


    public function deleteAssignTeachers($id)
    {
        try {

            // Find the ExaminationTeachers record by ID and delete it
            $examTeacher = ExaminationTeachers::findOrFail($id);
            $examTeacher->delete();

            return redirect()->back()->withToastSuccess('Exam Teacher has been Successfully Deleted!');
        } catch (ModelNotFoundException $e) {
            // Return error response if the record is not found
            return redirect()->back()->withToastSuccess('Record not found.');
        } catch (\Exception $e) {
            // Return error response for any other exceptions
            return response()->json(['error' => 'Error deleting record: ' . $e->getMessage()], 500);
        }
    }
}
