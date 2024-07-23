<?php

namespace App\Http\Controllers\SchoolAdmin;

use Validator;
use App\Models\Classg;
use App\Models\Subject;
use App\Models\ExamResult;
use App\Models\Examination;
use App\Models\ExamStudent;
use App\Models\ExamSchedule;
use Illuminate\Http\Request;
use App\Models\StudentSession;
use App\Models\Student;
use App\Imports\CombinedImport;
use Yajra\Datatables\Datatables;
use App\Http\Services\FormService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;

class ExamResultController extends Controller
{
    protected $formService;

    public function __construct(FormService $formService)
    {
        $this->formService = $formService;
    }

    public function destroy(string $id)
    {
        $exam_result = ExamResult::find($id);

        try {
            $exam_result->delete();
            return redirect()->back()->withToastSuccess('Exam Result has been Successfully Deleted!');
        } catch (\Exception $e) {
            return back()->withToastError($e->getMessage());
        }
    }

    public function getAllExamResult(Request $request)
    {
        $exam_results = $this->getForDataTable($request->all());

        return Datatables::of($exam_results)
            ->escapeColumns([])
            ->addColumn('attendance', fn($exam_result) => $exam_result->attendance)
            ->addColumn('rank', fn($exam_result) => $exam_result->marks)
            ->addColumn('notes', fn($exam_result) => $exam_result->notes)
            ->addColumn('created_at', fn($exam_result) => $exam_result->created_at->diffForHumans())
            ->addColumn('status', fn($exam_result) => $exam_result->is_active ? '<span class="btn-sm btn-success">Active</span>' : '<span class="btn-sm btn-danger">Inactive</span>')
            ->addColumn('actions', fn($exam_result) => view('backend.school_admin.exam_result.partials.controller_action', ['exam_result' => $exam_result])->render())
            ->make(true);
    }

    public function getForDataTable(array $request)
    {
        return ExamResult::when($request['id'] ?? null, fn($query, $id) => $query->where('id', $id))->get();
    }

    public function assignStudents(string $id)
    {
        $examination = Examination::findOrFail($id);
        $page_title = "Store Students Marks To " . $examination->exam;
        $classes = Classg::where('school_id', session('school_id'))->latest()->get();

        return view('backend.school_admin.examination.results.create', compact('page_title', 'classes', 'examination'));
    }

    public function getRoutineDetails(Request $request)
    {
        $request->validate([
            'sections' => 'required',
            'class_id' => 'required',
            'examination_id' => 'required',
        ]);

        $examSchedule = ExamSchedule::where('class_id', $request->class_id)
            ->where('section_id', $request->sections)
            ->where('examination_id', $request->examination_id)
            ->get();

        if ($examSchedule->isEmpty()) {
            return response()->json(['message' => 'No exam routine or schedule has been set yet!!!'], 400);
        }

        return view('backend.school_admin.examination.results.ajax_subject', compact('examSchedule'));
    }

    public function getStudentsDetails(Request $request)
    {
        $sectionId = $request->input('section_id');
        $classId = $request->input('class_id');
        $subjectId = $request->input('subject_id');
        $examinationId = $request->input('examination_id');
        $examinationScheduleId = $request->input('examination_schedule_id');
        $subjectName = Subject::findOrFail($request->subject_id)->subject;
        // $examStudent = collect([
        //     (object) [
        //         'student' => (object) [
        //             'id' => 1,
        //             'user_id' => 'ADM001',
        //             'roll_no' => 'R001'
        //         ],
        //         'user' => (object) [
        //             'f_name' => 'John',
        //             'm_name' => 'Doe',
        //             'l_name' => 'Smith',
        //             'father_name' => 'Mr. Smith',
        //             'gender' => 'Male'
        //         ],
        //         'exam_student_id' => 1,
        //         'id' => 1,
        //         'attendance' => '1',
        //         'participant_assessment' => 10,
        //         'practical_assessment' => 20,
        //         'theory_assessment' => 30,
        //         'notes' => 'Good'
        //     ],
        //     (object) [
        //         'student' => (object) [
        //             'id' => 2,
        //             'user_id' => 'ADM002',
        //             'roll_no' => 'R002'
        //         ],
        //         'user' => (object) [
        //             'f_name' => 'Jane',
        //             'm_name' => 'Doe',
        //             'l_name' => 'Smith',
        //             'father_name' => 'Mr. Smith',
        //             'gender' => 'Female'
        //         ],
        //         'exam_student_id' => 2,
        //         'id' => 2,
        //         'attendance' => '0',
        //         'participant_assessment' => 15,
        //         'practical_assessment' => 25,
        //         'theory_assessment' => 35,
        //         'notes' => 'Very Good'
        //     ]
        // ]);
        $examStudent = $this->formService->getExamAssignStudentDetails(
            $request->examination_id,
            $request->examination_schedule_id,
            $request->subject_id,
            $request->class_id,
            $request->section_id
        );
    
        return view('backend.school_admin.examination.results.ajax_student', compact('examStudent', 'examinationScheduleId', 'subjectId', 'subjectName'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function getExamAssignStudents($id, $classId, $sectionId)
    {
        $students = $this->formService->getExamAssignStudents($id, $classId, $sectionId);
        return response()->json($students);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function saveStudentsMarks(Request $request)
    {
        try {
            $exam_schedule_id = $request->input('exam_schedule_id');
            $subject_id = $request->input('subject_id');
            //store the records
            foreach ($request->student_id as $key => $studentId) {
                // Check if the checkbox is checked
                $attendanceValue = isset($request->attendance[$key]) ? $request->attendance[$key] : 1;
                $storeMarks = [
                    'exam_schedule_id' => $exam_schedule_id,
                    'subject_id' => $subject_id,
                    'student_session_id' => isset($request->student_session_id[$key]) ? $request->student_session_id[$key] : '',
                    'exam_student_id' => isset($request->exam_student_id[$key]) ? $request->exam_student_id[$key] : '',
                    'attendance' => $attendanceValue,
                    'participant_assessment' => isset($request->participant_assessment[$key]) ? $request->participant_assessment[$key] : 0,
                    'practical_assessment' => isset($request->practical_assessment[$key]) ? $request->practical_assessment[$key] : 0,
                    'theory_assessment' => isset($request->theory_assessment[$key]) ? $request->theory_assessment[$key] : 0,
                    'notes' => isset($request->notes[$key]) ? $request->notes[$key] : '',
                    'is_active' => 1
                ];
                // Update the record if it exists, otherwise create a new one
                ExamResult::updateOrCreate(
                    [
                        'exam_schedule_id' => $exam_schedule_id,
                        'student_session_id' => $request->student_session_id[$key]
                    ],
                    $storeMarks
                );
            }
            return back()->withToastSuccess('Marks successfully updated!!');
        } catch (\Exception $e) {
            return back()->withToastError('Error registering marks: ' . $e->getMessage());
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'class_id' => 'required',
            'section_id' => 'required',
            'subject_id' => 'required',
            'exam_schedule_id' => 'required',
            'exam_id' => 'required',
            'file' => 'required|file|mimes:csv,txt',
        ]);

        DB::beginTransaction();
        try {
            $import = new CombinedImport($request->all());
            Excel::import($import, $request->file('file'));

            DB::commit();
            return back()->withToastSuccess('Excel successfully uploaded');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withToastError($e->getMessage());
        }
    }

}
