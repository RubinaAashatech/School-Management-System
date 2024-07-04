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
        return view('backend.school_admin.attendancereport.index', compact('classes'));
    }

    public function report(Request $request)
    {
        $inputFromDate = $request->input('from_date', Carbon::today()->format('Y-m-d'));
        $inputToDate = $request->input('to_date', Carbon::today()->format('Y-m-d'));
        $fromDate = LaravelNepaliDate::from($inputFromDate)->toEnglishDate();
        $toDate = LaravelNepaliDate::from($inputToDate)->toEnglishDate();
        $schoolId = Auth::user()->school_id;
        $classes = Classg::where('school_id', $schoolId)->get();
        $studentAttendances = StudentAttendance::whereDate('created_at', [$fromDate, $toDate])->get();
        return view('backend.school_admin.attendancereport.index', compact('classes', 'fromDate', 'toDate', 'studentAttendances'));
    }
    public function getData(Request $request)
{
    $fromDate = $request->input('from_date');
    $toDate = $request->input('to_date');
    $classId = $request->input('class_id');
    $studentName = $request->input('student_name');

    $query = StudentAttendance::with(['student.user', 'studentSession'])
        ->whereBetween('created_at', [$fromDate, $toDate])
        ->when($classId, function ($query, $classId) {
            return $query->whereHas('studentSession', function ($q) use ($classId) {
                $q->where('class_id', $classId);
            });
        })
        ->when($studentName, function ($query, $studentName) {
            return $query->whereHas('student.user', function ($q) use ($studentName) {
                $q->where('f_name', 'like', '%'.$studentName.'%')
                  ->orWhere('l_name', 'like', '%'.$studentName.'%');
            });
        })
        ->get();

    return DataTables::of($query)
        ->addColumn('date', function($row) {
            return date('Y-m-d', strtotime($row->created_at));
        })
        ->addColumn('student_name', function($row) {
            return $row->student->user->f_name . ' ' . $row->student->user->l_name;
        })
        ->addColumn('attendance_type', function($row) {
            return $row->attendance_type;
        })
        ->make(true);
}
     // RETRIVING SECTIONS OF THE RESPECTIVE CLASS
    //  public function getSections($classId)
    //  {
    //      $sections = Classg::find($classId)->sections()->pluck('sections.section_name', 'sections.id');
    //      return response()->json($sections);
    //  }
}
