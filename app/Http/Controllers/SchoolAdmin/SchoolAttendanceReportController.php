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
use Carbon\CarbonPeriod;
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

        // Generate date range
        $period = CarbonPeriod::create($fromDate, $toDate);
        $dates = [];
        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d');
        }

        return view('backend.school_admin.attendancereport.index', compact('classes', 'dates'));
    }

    public function getData(Request $request)
    {
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $classId = $request->input('class_id');
        $studentName = $request->input('student_name');

        $period = CarbonPeriod::create($fromDate, $toDate);
        $dates = [];
        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d');
        }

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

        $data = [];
        foreach ($query as $attendance) {
            foreach ($dates as $date) {
                if (Carbon::parse($attendance->created_at)->format('Y-m-d') == $date) {
                    $data[] = [
                        'date' => $date,
                        'student_name' => $attendance->student->user->f_name . ' ' . $attendance->student->user->l_name,
                        'attendance_type' => $attendance->attendance_type,
                    ];
                }
            }
        }

        return DataTables::of($data)->make(true);
    }
     // RETRIVING SECTIONS OF THE RESPECTIVE CLASS
    //  public function getSections($classId)
    //  {
    //      $sections = Classg::find($classId)->sections()->pluck('sections.section_name', 'sections.id');
    //      return response()->json($sections);
    //  }
}
