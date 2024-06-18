<?php

namespace App\Http\Controllers\MunicipalityAdmin;

use Auth;
use Carbon\Carbon;
use App\Models\Staff;
use App\Models\School;
use App\Models\Student;
use App\Models\StaffAttendance;
use App\Models\StudentAttendance;
use App\Http\Controllers\Controller;
use App\Http\Services\SchoolService;
use App\Http\Services\DashboardService;
use App\Models\HeadTeacherLog;

class DashboardController extends Controller
{
    protected $dashboardService;
    protected $schoolService;

    public function __construct(DashboardService $dashboardService, SchoolService $schoolService)
    {
        $this->dashboardService = $dashboardService;
        $this->schoolService = $schoolService;
    }

    // public function index()
    // {
    //     $totalStudents = Student::count();
    //     $presentStudents = StudentAttendance::where('attendance_type_id', 1)
    //     ->whereDate('created_at', today()) // Filter by today's date
    //     ->count();

    //     $absentStudents = StudentAttendance::where('attendance_type_id', 2)
    //     ->whereDate('created_at', today()) // Filter by today's date
    //     ->count();

    //     $totalStaffs = Staff::count();
        
    //     $presentStaffs = StaffAttendance::where('attendance_type_id', 1)
    //     ->whereDate('created_at', today()) // Filter by today's date
    //     ->count();

    //     // Count the absent staff members for today
    //     $absentStaffs = StaffAttendance::where('attendance_type_id', 2)
    //         ->whereDate('created_at', today()) // Filter by today's date
    //     ->count();

    


    //     $page_title = Auth::user()->getRoleNames()[0] . ' ' . "Dashboard";
    //     $schools_wise_reports = $this->schoolService->schoolWiseReportDetails();
        
    //     $school_students = $this->schoolService->getSchoolStudent();
    //     $school_students_count = $this->schoolWiseCountOfStudent($school_students);
       

    //     $school_staffs = $this->schoolService->getSchoolStaff();
    //     $school_staffs_count = $this->schoolWiseCountOfStaff($school_staffs);
    

    //     $school_wise_student_attendences = $this->schoolService->getSchoolWiseStudentAttendence();
    //     $school_staffs_count = $this->schoolWiseCountOfStaff($school_staffs);



        
     
    //     return view('backend.municipality_admin.dashboard.dashboard', [
    //         'presentStudents' => $presentStudents,
    //         'totalStudents' => $totalStudents,
    //         'absentStudents' => $absentStudents,
    //         'totalStaffs' => $totalStaffs,
    //         'presentStaffs' => $presentStaffs,
    //         'absentStaffs' => $absentStaffs,
    //         'schools_wise_reports' =>$schools_wise_reports,
    //         'school_students' => $school_students,
    //         'school_staffs ' => $school_staffs,
    //         'school_staffs_count' => $school_staffs_count,
    //         'school_students_count' => $school_students_count,
    //         'school_wise_student_attendences' => $school_wise_student_attendences
    //     ]);
    // }



    public function index()
    {
        // General Counts
        $totalStudents = Student::count();
        $presentStudents = StudentAttendance::where('attendance_type_id', 1)
            ->whereDate('created_at', today()) // Filter by today's date
            ->count();
    
        $absentStudents = StudentAttendance::where('attendance_type_id', 2)
            ->whereDate('created_at', today()) // Filter by today's date
            ->count();
    
        $totalStaffs = Staff::count();
        $presentStaffs = StaffAttendance::where('attendance_type_id', 1)
            ->whereDate('created_at', today()) // Filter by today's date
            ->count();
    
        $absentStaffs = StaffAttendance::where('attendance_type_id', 2)
            ->whereDate('created_at', today()) // Filter by today's date
            ->count();
    
        $majorIncidentsCount = HeadTeacherLog::whereDate('created_at', today()) // Filter by today's date
            ->count();
    
        // Municipality specific data
        $municipalityId = Auth::user()->municipality_id;
        $today = Carbon::today();
        $schools = School::where('municipality_id', $municipalityId)->get();
        $schoolData = [];
    
        foreach ($schools as $school) {
            $schoolId = $school->id;
    
            // Count the total students in the school
            $totalStudentsInSchool = Student::where('school_id', $schoolId)->count();
    
            // Count the present students for today
            $presentStudentsInSchool = StudentAttendance::where('attendance_type_id', 1)
                ->whereHas('student', function($query) use ($schoolId) {
                    $query->where('school_id', $schoolId);
                })
                ->whereDate('created_at', $today) // Filter by today's date
                ->count();
    
            // Count the absent students for today
            $absentStudentsInSchool = StudentAttendance::where('attendance_type_id', 2)
                ->whereHas('student', function($query) use ($schoolId) {
                    $query->where('school_id', $schoolId);
                })
                ->whereDate('created_at', $today) // Filter by today's date
                ->count();
    
            // Count the total staff in the school
            $totalStaffsInSchool = Staff::where('school_id', $schoolId)->count();
    
            // Count the present staff members for today
            $presentStaffsInSchool = StaffAttendance::where('attendance_type_id', 1)
                ->whereHas('staff', function($query) use ($schoolId) {
                    $query->where('school_id', $schoolId);
                })
                ->whereDate('created_at', $today) // Filter by today's date
                ->count();
    
            // Count the absent staff members for today
            $absentStaffsInSchool = StaffAttendance::where('attendance_type_id', 2)
                ->whereHas('staff', function($query) use ($schoolId) {
                    $query->where('school_id', $schoolId);
                })
                ->whereDate('created_at', $today) // Filter by today's date
                ->count();
    
            // Add the data to the array
            $schoolData[] = [
                'school_id' => $school->id,
                'school_name' => $school->name,
                'total_students' => $totalStudentsInSchool,
                'present_students' => $presentStudentsInSchool,
                'absent_students' => $absentStudentsInSchool,
                'total_staffs' => $totalStaffsInSchool,
                'present_staffs' => $presentStaffsInSchool,
                'absent_staffs' => $absentStaffsInSchool,
            ];
        }

        $totalSchools = School::count();
    
        $page_title = Auth::user()->getRoleNames()[0] . ' ' . "Dashboard";
        
        return view('backend.municipality_admin.dashboard.dashboard', [
            'presentStudents' => $presentStudents,
            'totalStudents' => $totalStudents,
            'absentStudents' => $absentStudents,
            'totalStaffs' => $totalStaffs,
            'presentStaffs' => $presentStaffs,
            'absentStaffs' => $absentStaffs,
            'schoolData' => $schoolData,
            'major_incidents' => $majorIncidentsCount ,
            'totalSchools' => $totalSchools
        ]);
    }
    
    public function schoolWiseCountOfStudent($originalData)
    {
        $labels = [];
        $data = [];

        foreach ($originalData as $item) {
            $labels[] = $item['name'];
            $data[] = $item['total_student'];
            
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'School wise Student Count',
                    'data' => $data,
                    'borderWidth' => 1
                ]
            ]
        ];
    }

    public function schoolWiseCountOfStaff($originalData)
    {
        $labels = [];
        $data = [];

        foreach ($originalData as $item) {
            $labels[] = $item['name'];
            $data[] = $item['total_staffs'];
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'School wise Staff Count',
                    'data' => $data,
                    'borderWidth' => 1
                ]
            ]
        ];
    }
}
