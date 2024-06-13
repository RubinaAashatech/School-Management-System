<?php

namespace App\Http\Controllers\MunicipalityAdmin;

use Auth;
use App\Http\Controllers\Controller;
use App\Http\Services\DashboardService;
use App\Http\Services\SchoolService;
use App\Models\StudentAttendance;
use App\Models\StaffAttendance;
use App\Models\Student;
use App\Models\Staff;

class DashboardController extends Controller
{
    protected $dashboardService;
    protected $schoolService;

    public function __construct(DashboardService $dashboardService, SchoolService $schoolService)
    {
        $this->dashboardService = $dashboardService;
        $this->schoolService = $schoolService;
    }

    public function index()
    {

        $totalStudents = Student::count();
        $presentStudents = StudentAttendance::where('attendance_type_id', 1)->count();
        $absentStudents = StudentAttendance ::where('attendance_type_id', 2)->count();
        $totalStaffs = Staff::count();
        $presentStaffs = StaffAttendance::where('attendance_type_id', 1)->count();
        $absentStaffs = StaffAttendance ::where('attendance_type_id', 2)->count();

        $page_title = Auth::user()->getRoleNames()[0] . ' ' . "Dashboard";
        $schools_wise_reports = $this->schoolService->schoolWiseReportDetails();
        //school students

        $school_students = $this->schoolService->getSchoolStudent();
        $school_students_count = $this->schoolWiseCountOfStudent($school_students);
        //school staffs

        $school_staffs = $this->schoolService->getSchoolStaff();
        $school_staffs_count = $this->schoolWiseCountOfStaff($school_staffs);
        //school staffs

        $school_wise_student_attendences = $this->schoolService->getSchoolWiseStudentAttendence();
        $school_staffs_count = $this->schoolWiseCountOfStaff($school_staffs);

        // dd($school_wise_student_attendences);


        
     
        return view('backend.municipality_admin.dashboard.dashboard', [
            'presentStudents' => $presentStudents,
            'totalStudents' => $totalStudents,
            'absentStudents' => $absentStudents,
            'totalStaffs' => $totalStaffs,
            'presentStaffs' => $presentStaffs,
            'absentStaffs' => $absentStaffs,
            'schools_wise_reports' =>$schools_wise_reports,
            'school_students' => $school_students,
            'school_staffs ' => $school_staffs,
            'school_staffs_count' => $school_staffs_count,
            'school_students_count' => $school_students_count,
            'school_wise_student_attendences' => $school_wise_student_attendences
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
