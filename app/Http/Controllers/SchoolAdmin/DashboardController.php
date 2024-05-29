<?php

namespace App\Http\Controllers\SchoolAdmin;

use DB, Auth;
use App\Models\Unit;
use App\Models\User;
use App\Models\Stock;
use App\Models\School;
use App\Models\Product;
use Illuminate\Http\Request;

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

    public function index()
    {
        $page_title = Auth::user()->getRoleNames()[0] . ' ' . "Dashboard";
        $school_students = $this->schoolService->getSchoolStudent();
        $school_students_count = $this->schoolWiseCountOfStudent($school_students);
        //school staffs
        $school_staffs = $this->schoolService->getSchoolStaff(session('school_id'));
        $school_staffs_count = $this->schoolWiseCountOfStaff($school_staffs);

        //School Wise Student's Attendence
        $school_wise_student_attendences = $this->schoolService->getSchoolWiseStudentAttendence(session('school_id'));
        //School Wise Staff's Attendence
        $school_wise_staffs_attendences = $this->schoolService->getSchoolWiseStaffAttendence(session('school_id'));
        //Class Wise students
        $class_wise_students = $this->dashboardService->getClassWiseStudents(session('school_id'));
        // dd($school_wise_staffs_attendences);
        return view('backend.school_admin.dashboard.dashboard', compact('page_title', 'school_students_count', 'school_staffs_count', 'school_wise_student_attendences', 'school_wise_staffs_attendences', 'class_wise_students'));
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


}