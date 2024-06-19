<?php

namespace App\Http\Controllers\SchoolAdmin;

 use DB, Auth;
use App\Models\Unit;
use App\Models\User;
use App\Models\Staff;
use App\Models\Stock;
use App\Models\School;
use App\Models\Product;

use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\StudentSession;
use App\Models\StaffAttendance;
use App\Models\StudentAttendance;
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
    $schoolId = Auth::user()->school_id;

    // Count the students in the same school
    $totalStudents = Student::where('school_id', $schoolId)->count();

    // Count the girls in the same school
    $totalGirls = Student::where('school_id', $schoolId)
        ->whereHas('user', function ($query) {
            $query->where('gender', 'female');
        })
        ->count();

    // Count the boys in the same school
    $totalBoys = Student::where('school_id', $schoolId)
        ->whereHas('user', function ($query) {
            $query->where('gender', 'male');
        })
        ->count();

    // Count the present students for today
    $presentStudents = StudentAttendance::where('attendance_type_id', 1)
        ->whereHas('student', function ($query) use ($schoolId) {
            $query->where('school_id', $schoolId);
        })
        ->whereDate('updated_at', today()) // Filter by today's date
        ->count();

    // Count the present girls for today
    $presentGirls = StudentAttendance::where('attendance_type_id', 1)
        ->whereHas('student.user', function ($query) use ($schoolId) {
            $query->where('school_id', $schoolId)->where('gender', 'female');
        })
        ->whereDate('created_at', today()) // Filter by today's date
        ->count();

    // Count the present boys for today
    $presentBoys = StudentAttendance::where('attendance_type_id', 1)
        ->whereHas('student.user', function ($query) use ($schoolId) {
            $query->where('school_id', $schoolId)->where('gender', 'male');
        })
        ->whereDate('created_at', today()) // Filter by today's date
        ->count();

    // Count the absent students for today
    $absentStudents = StudentAttendance::where('attendance_type_id', 2)
        ->whereHas('student', function ($query) use ($schoolId) {
            $query->where('school_id', $schoolId);
        })
        ->whereDate('updated_at', today()) // Filter by today's date
        ->count();

        //count the absent girls for today
        $absentGirls = StudentAttendance::where('attendance_type_id', 2)
        ->whereHas('student.user', function ($query) use ($schoolId) {
            $query->where('school_id', $schoolId)->where('gender', 'female');
        })
        ->whereDate('created_at', today()) // Filter by today's date
        ->count();

         //count the absent boys for today
         $absentBoys = StudentAttendance::where('attendance_type_id', 2)
         ->whereHas('student.user', function ($query) use ($schoolId) {
             $query->where('school_id', $schoolId)->where('gender', 'male');
         })
         ->whereDate('created_at', today()) // Filter by today's date
         ->count();

    $totalStaffs = Staff::where('school_id', $schoolId)->count();

    $presentStaffs = StaffAttendance::where('attendance_type_id', 1)
        ->whereHas('staff', function ($query) use ($schoolId) {
            $query->where('school_id', $schoolId);
        })
        ->whereDate('created_at', today()) // Filter by today's date
        ->count();

    // Count the absent staff members for today
    $absentStaffs = StaffAttendance::where('attendance_type_id', 2)
        ->whereHas('staff', function ($query) use ($schoolId) {
            $query->where('school_id', $schoolId);
        })
        ->whereDate('created_at', today()) // Filter by today's date
        ->count();

    $page_title = Auth::user()->getRoleNames()[0] . ' ' . "Dashboard";
    $school_students = $this->schoolService->getSchoolStudent();
    $school_students_count = $this->schoolWiseCountOfStudent($school_students);

    // // School staffs
    $school_staffs = $this->schoolService->getSchoolStaff(session('school_id'));
    $school_staffs_count = $this->schoolWiseCountOfStaff($school_staffs);

    // School Wise Student's Attendance
    $school_wise_student_attendences = $this->schoolService->getSchoolWiseStudentAttendence(session('school_id'));

    // School Wise Staff's Attendance
    $school_wise_staffs_attendences = $this->schoolService->getSchoolWiseStaffAttendence(session('school_id'));

    // Class Wise students
    $class_wise_students = $this->dashboardService->getClassWiseStudents(session('school_id'));

    return view('backend.school_admin.dashboard.dashboard', compact(
        'page_title', 'school_students_count', 'school_staffs_count', 'school_wise_student_attendences',
        'school_wise_staffs_attendences', 'class_wise_students', 'totalStudents', 'presentStudents',
        'absentStudents', 'totalStaffs', 'presentStaffs', 'absentStaffs', 'totalGirls', 'totalBoys',
        'presentGirls', 'presentBoys','absentGirls','absentBoys'
    ));
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
