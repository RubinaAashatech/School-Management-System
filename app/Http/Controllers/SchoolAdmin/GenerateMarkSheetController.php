<?php

namespace App\Http\Controllers\SchoolAdmin;

use Carbon\Carbon;
use App\Models\Classg;
use App\Models\School;
use App\Models\Section;
use App\Models\Student;

use App\Models\ExamResult;
use App\Models\MarksGrade;
use App\Models\Examination;
use App\Models\ExamSchedule;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\MarkSheetDesign;
use Yajra\Datatables\Datatables;
use App\Http\Services\PdfService;
use App\Http\Services\FormService;
use Spatie\LaravelPdf\Facades\Pdf;
use Spatie\Browsershot\Browsershot;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use App\Http\Services\ExamResultService;
use App\Http\Services\StudentUserService;
use function Spatie\LaravelPdf\Support\pdf;

class GenerateMarkSheetController extends Controller
{

    protected $pdfService;
    protected $formService;
    protected $studentUserService;
    protected $examResultService;

    public function __construct(FormService $formService, StudentUserService $studentUserService, ExamResultService $examResultService, PdfService $pdfService)
    {
        $this->pdfService = $pdfService;
        $this->formService = $formService;
        $this->studentUserService = $studentUserService;
        $this->examResultService = $examResultService;
    }

    public function index()
    {
        $page_title = 'Generate Marksheet';
        $schoolId = session('school_id');
        $classes = Classg::where('school_id', $schoolId)
            ->orderBy('created_at', 'desc')
            ->get();
        $marksheet_designs = MarkSheetDesign::all();
        $examination = Examination::all();
        return view('backend.school_admin.generate_mark_sheet.index', compact('page_title', 'classes', 'schoolId', 'marksheet_designs', 'examination'));
    }

    public function create()
    {
    }

    // RETRIVING SECTIONS OF THE RESPECTIVE CLASS
    public function getSections($classId)
    {
        $sections = Classg::find($classId)->sections()->pluck('sections.section_name', 'sections.id');
        return response()->json($sections);
    }

    public function getAllStudent(Request $request)
    {
        // dd($request->all());
        // dd("HELLO");
        $marksheetdesign_id = $request->input('marksheet_design_id');
        if ($request->has('class_id') && $request->has('section_id')) {
            $classId = $request->input('class_id');
            $sectionId = $request->input('section_id');
            $examination_id = $request->input('examination_id');
            $students = $this->studentUserService->getStudentsForDataTable($request->all())
                ->where('class_id', $classId)
                ->where('section_id', $sectionId);

            return Datatables::of($students)
                ->escapeColumns([])
                ->editColumn('f_name', function ($row) {
                    return $row->f_name;
                })
                ->editColumn('l_name', function ($row) {
                    return $row->l_name;
                })
                ->editColumn('roll_no', function ($row) {
                    return $row->roll_no;
                })
                ->editColumn('father_name', function ($row) {
                    return $row->father_name;
                })
                ->editColumn('mother_name', function ($row) {
                    return $row->mother_name;
                })
                ->editColumn('guardian_is', function ($row) {
                    return $row->guardian_is;
                })
                ->addColumn('created_at', function ($user) {
                    return $user->created_at->diffForHumans();
                })
                ->addColumn('status', function ($student) {
                    return $student->is_active == 1 ? '<span class="btn-sm btn-success">Active</span>' : '<span class="btn-sm btn-danger">Inactive</span>';
                })
                ->addColumn('actions', function ($student) use ($marksheetdesign_id, $examination_id) {
                    return view('backend.school_admin.generate_mark_sheet.partials.controller_action', ['student' => $student, 'marksheet_design_id' => $marksheetdesign_id, 'examination_id' => $examination_id])->render();
                })
                // ->addColumn('actions', function ($student) use ($marksheetdesign_id) {
                //     return view('backend.school_admin.generate_mark_sheet.partials.controller_action', ['student' => $student, 'marksheet_design_id' => $marksheetdesign_id])->render();
                // })

                ->make(true);

            return Datatables::of([])
                ->escapeColumns([])
                ->make(true);
        }
    }

    public function getAllMarksheets($examination_id)
    {
        $page_title = 'Print Marksheet';
        $schoolId = session('school_id');
        $classes = Classg::where('school_id', $schoolId)
            ->orderBy('created_at', 'desc')
            ->get();
        $marksheet_designs = MarkSheetDesign::all();
        $examination_id = Examination::findOrFail($examination_id);

        return view('backend.school_admin.examination.print_marksheet', compact('page_title', 'classes', 'marksheet_designs', 'examination_id'));
    }

    // public function generateExamResult($examinations)
    // {
    //     return $this->examResultService->getStudentResultsBySubject($examinations);
    // }

    // SHOW FUNCTION
    public function showMarkSheetDesign($student_id, $class_id, $section_id, $marksheetdesign_id, $examination_id)
    {
        $school = School::findOrFail(session('school_id'));

        $marksheet = MarkSheetDesign::findOrFail($marksheetdesign_id);
        // $exam_result = ExamResult::all();
        $examinations = Examination::find($examination_id);
        $markgrades = MarksGrade::all();

        $studentSessions = $this->generateExamResult($student_id, $examinations);
        // Prepare data for the view
        $data = [
            'marksheet' => $marksheet,
            'studentSessions' => $studentSessions,
            'examinations' => $examinations,
            'school' => $school,
            'markgrades' => $markgrades,
            'today' => Carbon::today(),
        ];

        if ($examinations->exam_type == "terminal") {
            return view('backend.school_admin.mark_sheet_design.marksheetdesignterminal', $data);
        } else {
            return view('backend.school_admin.mark_sheet_design.marksheetdesignfinal', $data);
        }

        // Return the view with the prepared data
    }

    public function generateExamResult($student_id, $examinations)
    {
        return $this->examResultService->getStudentResultsBySubject($examinations, $student_id);
    }
    public function generateBase64EncodedImage($filename)
    {
        $filePath = public_path($filename);
        if (file_exists($filePath)) {
            return base64_encode(file_get_contents($filePath));
        }
        return null;
    }
    public function downloadStudentMarkSheet($student_id, $class_id, $section_id, $marksheetdesign_id, $examination_id)
    {
        $school = School::findOrFail(session('school_id'));
        if ($school) {
            $school->logo = $this->generateBase64EncodedImage($school->logo);
        }
        $marksheet = MarkSheetDesign::findOrFail($marksheetdesign_id);
        // $exam_result = ExamResult::all();
        $examinations = Examination::find($examination_id);
        $markgrades = MarksGrade::all();

        $studentSessions = $this->generateExamResult($student_id, $examinations);
        // Prepare data for the view
        $data = [
            'marksheet' => $marksheet,
            'studentSessions' => $studentSessions,
            'examinations' => $examinations,
            'school' => $school,
            'markgrades' => $markgrades,
            'today' => Carbon::today(),
        ];
        if ($examinations->exam_type == "terminal") {
            // return Pdf::view('backend.school_admin.mark_sheet_design.downloadmarksheetterminal', $data)
            //     ->format('a4')
            //     ->name('invoice-2023-04-10.pdf')
            //     ->download();

            // $html = view('backend.school_admin.mark_sheet_design.downloadmarksheetterminal', $data)->render();
            // // dd($html);
            // $this->html($html)->setIncludePath(config('services.browsershort.include_path'))->download('mark_sheet' . $studentSessions->student_id . '.pdf');

            return $this->pdfService->loadView('backend.school_admin.mark_sheet_design.downloadmarksheetterminal', $data)->stream('mark_sheet' . $studentSessions->student_id . '.pdf');
        } else {
            return $this->pdfService->loadView('backend.school_admin.mark_sheet_design.downloadmarksheetfinal', $data)->stream('mark_sheet' . $studentSessions->student_id . '.pdf');
        }
    }
}