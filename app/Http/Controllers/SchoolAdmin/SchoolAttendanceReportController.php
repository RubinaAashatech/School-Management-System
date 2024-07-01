<?php

namespace App\Http\Controllers\SchoolAdmin;

use Illuminate\Http\Request;
use App\Models\StudentAttendance;
use App\Models\Classg;
use App\Models\ClassSection;
use App\Models\StudentSession;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Anuzpandey\LaravelNepaliDate\LaravelNepaliDate;
use Yajra\DataTables\DataTables;
use DB, Auth;

class SchoolAttendanceReportController extends Controller
{
    public function index()
    {
        $schoolId = Auth::user()->school_id;
        $classes = Classg::where('school_id', $schoolId)->get();
        $sections = ClassSection::where('school_id', $schoolId)->get();
        return view('backend.school_admin.attendancereport.index', compact('classes', 'sections'));
    }

    public function report(Request $request)
    {
        $inputDate = $request->input('date', Carbon::today()->format('Y-m-d'));
        $schoolId = Auth::user()->school_id;
        $classes = Classg::where('school_id', $schoolId)->get();
        $sections = ClassSection::where('school_id', $schoolId)->get();
        return view('backend.school_admin.attendancereport.index', compact('classes', 'sections', 'inputDate'));
    }

    public function getSectionsByClass($classId)
    {
        $schoolId = Auth::user()->school_id;

        // Fetch sections based on the school ID and class ID
        $sections = ClassSection::where('school_id', $schoolId)
                                ->where('class_id', $classId)
                                ->pluck('name', 'id')
                                ->toArray();

        // Return JSON response
        return response()->json($sections);
    }
 


    public function getData(Request $request)
    {
        $query = StudentAttendance::with(['student.user', 'studentSession'])
            ->whereHas('studentSession', function ($q) {
                $q->where('school_id', Auth::user()->school_id); // Filter by logged-in school
            });

        if ($request->has('class')) {
            $query->whereHas('studentSession', function ($q) use ($request) {
                $q->where('class_id', $request->class);
            });
        }

        if ($request->has('section')) {
            $query->whereHas('studentSession', function ($q) use ($request) {
                $q->where('section_id', $request->section);
            });
        }

        if ($request->has('from_date') && $request->has('to_date')) {
            $fromDate = LaravelNepaliDate::from($request->from_date)->toEnglishDate();
            $toDate = LaravelNepaliDate::from($request->to_date)->toEnglishDate();
            $query->whereBetween('created_at', [$fromDate, $toDate]);
        }

        if ($request->has('student_name')) {
            $query->whereHas('student.user', function ($q) use ($request) {
                $q->where('f_name', 'like', '%' . $request->student_name . '%')
                  ->orWhere('l_name', 'like', '%' . $request->student_name . '%');
            });
        }

        if ($request->has('admission_no')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('admission_no', 'like', '%' . $request->admission_no . '%');
            });
        }

        return DataTables::of($query)
            ->addColumn('student_name', function ($attendance) {
                return $attendance->student->user->f_name . ' ' . $attendance->student->user->l_name;
            })
            ->addColumn('attendance_type', function ($attendance) {
                return $attendance->attendance_type_id == 1 ? 'Present' : 'Absent';
            })
            ->rawColumns(['student_name', 'attendance_type'])
            ->make(true);
    }
}
