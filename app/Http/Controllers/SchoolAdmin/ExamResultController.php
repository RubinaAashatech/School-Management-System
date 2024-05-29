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
        // $exam_result = ExamResult::find($id);

        // try {
        //     $updateNow = $exam_result->delete();
        //     return redirect()->back()->withToastSuccess('Exam Result has been Successfully Deleted!');
        // } catch (\Exception $e) {
        //     return back()->withToastError($e->getMessage());
        // }

        // return back()->withToastError('Something went wrong. Please try again');
    }

    public function getAllExamResult(Request $request)
    {
        $exam_result = $this->getForDataTable($request->all());

        return Datatables::of($exam_result)
            ->escapeColumns([])
            // ->addColumn('school_id', function ($subject) {
            //     return $subject->school_id;
            // })
            ->addColumn('attendance', function ($exam_result) {
                return $exam_result->attendance;
            })
            ->addColumn('rank', function ($exam_result) {
                return $exam_result->marks;
            })
            ->addColumn('notes', function ($exam_result) {
                return $exam_result->notes;
            })

            ->addColumn('created_at', function ($exam_result) {
                return $exam_result->created_at->diffForHumans();
            })
            ->addColumn('status', function ($exam_result) {
                return $exam_result->is_active == 1 ? '<span class="btn-sm btn-success">Active</span>' : '<span class="btn-sm btn-danger">Inactive</span>';
            })
            ->addColumn('actions', function ($exam_result) {
                return view('backend.school_admin.exam_result.partials.controller_action', ['exam_result' => $exam_result])->render();
            })

            ->make(true);
    }

    public function getForDataTable($request)
    {
        $dataTableQuery = ExamResult::where(function ($query) use ($request) {
            if (isset($request->id)) {
                $query->where('id', $request->id);
            }
        })
            ->get();

        return $dataTableQuery;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function assignStudents(string $id)
    {
        $examinations = Examination::find($id);
        $page_title = "Store Students Marks To " . $examinations->exam;
        $classes = Classg::where('school_id', session('school_id'))
            ->orderBy('created_at', 'desc')
            ->get();

        return view('backend.school_admin.examination.results.create', compact('page_title', 'classes', 'examinations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function getRoutineDetails(Request $request)
    {
        $sectionId = $request->input('sections');
        $classId = $request->input('class_id');
        $examinationId = $request->input('examination_id');
        if ($sectionId && $classId && $examinationId) {
            $examSchedule = ExamSchedule::where('class_id', $classId)->where('section_id', $sectionId)->where('examination_id', $examinationId)->get();
            if ($examSchedule->isNotEmpty()) {
                return view('backend.school_admin.examination.results.ajax_subject', compact('examSchedule'));
            } else {
                return response()->json(['message' => 'No exam routine or schedule has been set yet!!!'], 400);
            }
        } else {
            // Handle the case where one or more parameters are missing
            return response()->json(['message' => 'Missing parameters'], 400);
        }
    }
    /**
     * Show the form for creating a new resource.
     */
    public function getStudentsDetails(Request $request)
    {
        $sectionId = $request->input('section_id');
        $classId = $request->input('class_id');
        $subjectId = $request->input('subject_id');
        $examinationId = $request->input('examination_id');
        $examinationScheduleId = $request->input('examination_schedule_id');

        // dd("here");
        // echo "section id" . $sectionId;
        // echo "classId id" . $classId;
        // echo "subjectId id" . $subjectId;
        // echo "examinationId id" . $examinationId;

        // Fetch the subject name
        $subjectName = Subject::find($subjectId)->subject;
        $subjectNameBold = '<strong>' . $subjectName . '</strong>';
        //Display subject name
        // echo  $subjectName;
        if ($sectionId && $classId && $examinationId) {
            $examStudent = $this->formService->getExamAssignStudentDetails($examinationId, $examinationScheduleId, $subjectId, $classId, $sectionId);
            // dd($examStudent);
            // Check if any examStudents relationship is not empty
            if ($examStudent->isNotEmpty()) {
                // Iterate over each StudentSession instance
                foreach ($examStudent as $studentSession) {
                    // dd($studentSession);
                    if ($studentSession->examStudents->isNotEmpty()) {

                        return view('backend.school_admin.examination.results.ajax_student', compact('examStudent', 'examinationScheduleId', 'subjectId', 'subjectName'));
                    } else {

                        return response()->json(['message' => 'Students has not been assigned for particular Examination!!!'], 400);
                    }
                }
            } else {
                return response()->json(['message' => 'No students found with the given search parameters!!!'], 400);
            }
        } else {
            // Handle the case where one or more parameters are missing
            return response()->json(['message' => 'Missing parameters'], 400);
        }
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
        try {
            // $file = $request->file('file');
            // $mimeType = $file->getMimeType();
            // dd($mimeType);
            $validator = Validator::make($request->all(), [
                'class_id' => 'required',
                'section_id' => 'required',
                'subject_id' => 'required',
                'exam_schedule_id' => 'required',
                'exam_id' => 'required',
                'file' => 'required|file|mimes:csv,txt',
            ]);

            if ($validator->fails()) {
                return back()->withToastError($validator->messages()->all()[0])->withInput();
            }
            // Begin a database transaction
            DB::beginTransaction();
            $array1 = Excel::toCollection(new CombinedImport, $request->file('file'));
            // Access the outer collection
            foreach ($array1 as $outerCollection) {
                // Iterate through the inner collections
                foreach ($outerCollection as $row) {

                    $validator = Validator::make($row->toArray(), [
                        'user_id' => 'required',
                        'attendance' => 'required',
                        'participant_assessment' => 'required',
                        'practical_assessment' => 'required',
                        'theory_assessment' => 'required',
                        'notes' => 'required'
                    ]);

                    if ($validator->fails()) {
                        return back()->withToastError($validator->messages()->all()[0])->withInput();
                    }
                    // Validate user data
                    $student_session = StudentSession::where('user_id', $row['user_id'])->where('school_id', session('school_id'))->where('class_id', $request->input('class_id'))->where('section_id', $request->input('section_id'))->first();

                    if (!$student_session) {
                        return back()->withToastError("Student not found for user ID {$row['user_id']} in school ID " . session('school_id') . ", class ID {$request->input('class_id')}, and section ID {$request->input('section_id')}.")->withInput();
                    }
                    // Validate user assigned to exam exam_id
                    $exam_student = ExamStudent::where('examination_id', $request->input('exam_id'))->where('student_session_id', $student_session->id)->first();

                    if (!$exam_student) {
                        return back()->withToastError("Student with user ID {$row['user_id']} is not assigned to examination: {$request->input('exam_id')}.")->withInput();
                    }

                    $storeMarks = [
                        'exam_schedule_id' => $request->input('exam_schedule_id'),
                        'subject_id' => $request->input('subject_id'),
                        'student_session_id' => $student_session->id,
                        'attendance' => isset($row['attendance']) ? 1 : 0,
                        'marks' => isset($row['marks']) ? $row['marks'] : 0,
                        'notes' => isset($row['notes']) ? $row['notes'] : '',
                        'is_active' => 1
                    ];

                    // Update the record if it exists, otherwise create a new one
                    ExamResult::updateOrCreate(
                        [
                            'exam_schedule_id' => $request->input('exam_schedule_id'),
                            'student_session_id' => $student_session->id
                        ],
                        $storeMarks
                    );
                }
            }
            DB::commit();
            return back()->with('success', 'Mark of Student has been uploaded');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withToastError($e->getMessage());
        }
    }
}
