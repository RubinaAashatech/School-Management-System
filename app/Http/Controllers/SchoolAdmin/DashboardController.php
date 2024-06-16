<?php

namespace App\Http\Controllers\SchoolAdmin;

// use DB, Auth;
use App\Models\Unit;
use App\Models\User;
use App\Models\Stock;
use App\Models\School;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Staff;
use App\Models\StudentAttendance;
use App\Models\StaffAttendance;
use Illuminate\Support\Facades\DB;

use App\Models\StudentSession;
use App\Http\Controllers\Controller;
use App\Http\Services\SchoolService;
use App\Http\Services\DashboardService;



class DashboardController extends Controller
{
    protected $dashboardService;
    protected $schoolService;

    public function __construct(DashboardService $dashboardService, SchoolService $schoolService)
    {
        $this->dashboardService = $dashboardService;
        $this->schoolService = $schoolService;
    }

    public function index(Request $request)
    {
        // $page_title = Auth::user()->getRoleNames()[0] . ' ' . "Dashboard";
        // $school_students = $this->schoolService->getSchoolStudent();
        // $school_students_count = $this->schoolWiseCountOfStudent($school_students);
        
        // // School staffs
        // $school_staffs = $this->schoolService->getSchoolStaff(session('school_id'));
        // $school_staffs_count = $this->schoolWiseCountOfStaff($school_staffs);

        // // School Wise Student's Attendance
        // $school_wise_student_attendences = $this->schoolService->getSchoolWiseStudentAttendence(session('school_id'));
        
        // // School Wise Staff's Attendance
        // $school_wise_staffs_attendences = $this->schoolService->getSchoolWiseStaffAttendence(session('school_id'));
        // // Class Wise students
        // $class_wise_students = $this->dashboardService->getClassWiseStudents(session('school_id'));

        // return view('backend.school_admin.dashboard.dashboard', compact('page_title', 'school_students_count', 'school_staffs_count', 'school_wise_student_attendences', 'school_wise_staffs_attendences', 'class_wise_students'));


        $schoolId = $request->session()->get('school_id');
        $Student_id=$request->session()->get('student_session_id');

        $totalStudents = DB::table('students')->where('school_id', $schoolId)->count();

        $presentStudents = DB::table('student_attendances')
            ->where('student_session_id', $Student_id)
            ->where('attendance_type_id', 1)
            ->count();

        $absentStudents = DB::table('student_attendances')
            ->where('student_session_id', $Student_id)
            ->where('attendance_type_id', 2)
            ->count();

        $totalStaffs = DB::table('staffs')->where('school_id', $schoolId)->count();

        $presentStaffs = DB::table('staff_attendances')
            ->where('school_id', $schoolId)
            ->where('attendance_type_id', 1)
            ->count();

        $absentStaffs = DB::table('staff_attendances')
            ->where('school_id', $schoolId)
            ->where('attendance_type_id', 2)
            ->count();

        return view('backend.school_admin.dashboard.dashboard', [
            'presentStudents' => $presentStudents,
            'totalStudents' => $totalStudents,
            'absentStudents' => $absentStudents,
            'totalStaffs' => $totalStaffs,
            'presentStaffs' => $presentStaffs,
            'absentStaffs' => $absentStaffs,
        ]);
    }

    public function schoolWiseCountOfStudent($originalData)
    {
        // Initialize labels and data arrays
        $labels = [];
        $data = [];

        // Iterate over the original data array
        foreach ($originalData as $item) {
            $labels[] = $item['name'];
            $data[] = $item['total_student'];
        }

        // Construct the required data structure
        $finalData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'School wise Student Count',
                    'data' => $data,
                    'borderWidth' => 1
                ]
            ]
        ];
        return $finalData;
    }

    public function schoolWiseCountOfStaff($originalData)
    {
        // Initialize labels and data arrays
        $labels = [];
        $data = [];

        // Iterate over the original data array
        foreach ($originalData as $item) {
            $labels[] = $item['name'];
            $data[] = $item['total_staff'];
        }

        // Construct the required data structure
        $finalData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'School wise Staff Count',
                    'data' => $data,
                    'borderWidth' => 1
                ]
            ]
        ];
        return $finalData;
    }
}
