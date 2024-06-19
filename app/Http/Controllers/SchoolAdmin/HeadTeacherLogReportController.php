<?php

namespace App\Http\Controllers\SchoolAdmin;

use DB, Auth;
use App\Models\School;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\HeadTeacherLog;
use App\Models\StudentSession;
use App\Models\StaffAttendance;
use App\Models\Staff;
use App\Models\StudentAttendance;
use App\Models\AttendanceType;

use App\Http\Controllers\Controller;

use Carbon\Carbon;

class HeadTeacherLogReportController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = Auth::user()->school_id;
        $date = $request->input('date', Carbon::today()->toDateString()); // Use today's date if not provided

        // Fetch data based on the provided date
        $presentStaffs = StaffAttendance::where('attendance_type_id', 1)
            ->whereHas('staff', function ($query) use ($schoolId) {
                $query->where('school_id', $schoolId);
            })
            ->whereDate('created_at', $date)
            ->count();

        $absentStaffs = StaffAttendance::where('attendance_type_id', 2)
            ->whereHas('staff', function ($query) use ($schoolId) {
                $query->where('school_id', $schoolId);
            })
            ->whereDate('created_at', $date)
            ->count();

        $teacherLog = HeadTeacherLog::whereDate('created_at', $date)
            ->select('major_incidents', 'major_work_observation', 'assembly_management', 'miscellaneous')
            ->first();

        $majorIncident = $teacherLog->major_incidents ?? '';
        $majorWorkObservation = $teacherLog->major_work_observation ?? '';
        $assemblyManagement = $teacherLog->assembly_management ?? '';
        $miscellaneous = $teacherLog->miscellaneous ?? '';

        $totalStudents = Student::where('school_id', $schoolId)->count();

        $presentStudents = StudentAttendance::where('attendance_type_id', 1)
            ->whereHas('student', function ($query) use ($schoolId) {
                $query->where('school_id', $schoolId);
            })
            ->whereDate('created_at', $date)
            ->count();

        $absentStudents = StudentAttendance::where('attendance_type_id', 2)
            ->whereHas('student', function ($query) use ($schoolId) {
                $query->where('school_id', $schoolId);
            })
            ->whereDate('created_at', $date)
            ->count();

        $totalGirls = Student::where('school_id', $schoolId)
            ->whereHas('user', function ($query) {
                $query->where('gender', 'female');
            })
            ->count();

        $totalBoys = Student::where('school_id', $schoolId)
            ->whereHas('user', function ($query) {
                $query->where('gender', 'male');
            })
            ->count();

        $presentGirls = StudentAttendance::where('attendance_type_id', 1)
            ->whereHas('student.user', function ($query) use ($schoolId) {
                $query->where('school_id', $schoolId)->where('gender', 'female');
            })
            ->whereDate('created_at', $date)
            ->count();

        $presentBoys = StudentAttendance::where('attendance_type_id', 1)
            ->whereHas('student.user', function ($query) use ($schoolId) {
                $query->where('school_id', $schoolId)->where('gender', 'male');
            })
            ->whereDate('created_at', $date)
            ->count();

        $absentGirls = StudentAttendance::where('attendance_type_id', 2)
            ->whereHas('student.user', function ($query) use ($schoolId) {
                $query->where('school_id', $schoolId)->where('gender', 'female');
            })
            ->whereDate('created_at', $date)
            ->count();

        $absentBoys = StudentAttendance::where('attendance_type_id', 2)
            ->whereHas('student.user', function ($query) use ($schoolId) {
                $query->where('school_id', $schoolId)->where('gender', 'male');
            })
            ->whereDate('created_at', $date)
            ->count();

        if ($request->ajax()) {
            return response()->json([
                'totalStudents' => $totalStudents,
                'presentStudents' => $presentStudents,
                'absentStudents' => $absentStudents,
                'totalGirls' => $totalGirls,
                'totalBoys' => $totalBoys,
                'presentGirls' => $presentGirls,
                'presentBoys' => $presentBoys,
                'absentGirls' => $absentGirls,
                'absentBoys' => $absentBoys,
                'presentStaffs' => $presentStaffs,
                'absentStaffs' => $absentStaffs,
                'majorIncident' => $majorIncident,
                'majorWorkObservation' => $majorWorkObservation,
                'assemblyManagement' => $assemblyManagement,
                'miscellaneous' => $miscellaneous,
            ]);
        }

        $page_title = Auth::user()->getRoleNames()[0] . ' ' . "Dashboard";

        return view('backend.school_admin.logs.head_teacher_log_reports.index', compact(
            'page_title', 'totalStudents', 'presentStudents', 'absentStudents',
            'totalGirls', 'totalBoys', 'presentGirls', 'presentBoys', 'absentGirls', 'absentBoys',
            'presentStaffs', 'absentStaffs', 'majorIncident', 'majorWorkObservation', 'assemblyManagement', 'miscellaneous'
        ));
    }



    // public function getAttendanceReport(Request $request)
    // {
    //     $date = $request->input('date');
    //     $schoolId = Auth::user()->school_id;

    //     // Retrieve active student sessions for the given date
    //     $activeSessions = StudentSession::where('is_active', 1)
    //         ->get();

    //     // Extract user IDs of students from the active sessions
    //     $studentUserIds = $activeSessions->pluck('user_id')->toArray();

    //     // Fetch students associated with the active sessions
    //     $students = Student::whereIn('user_id', $studentUserIds)->get();


    //     // Fetch student attendance data for the specified date
    //     $studentAttendanceData = StudentAttendance::with('studentSession.student.user')
    //         ->where('date', $date)
    //         ->get();


    //     // Initialize variables to store counts of male and female students
    //     $presentMaleCount = 0;
    //     $presentFemaleCount = 0;
    //     $absentMaleCount = 0;
    //     $absentFemaleCount = 0;

    //     // Iterate through the attendance data to count male and female students
    //     // foreach ($studentAttendanceData as $attendance) {

    //     //     $user = optional($attendance->studentSession)->user;

    //     //     // Check if the attendance type is 'Present'  id = 1 (Present)
    //     //     if ($attendance->attendance_type_id == 1) {


    //     //         // Increment the respective count based on the user's gender
    //     //         if ($user && $user->gender) {
    //     //             if ($user->gender == 'Male') {
    //     //                 $presentMaleCount++;
    //     //             } elseif ($user->gender == 'Female') {
    //     //                 $presentFemaleCount++;
    //     //             }
    //     //         }
    //     //     } elseif ($attendance->attendance_type_id == 2) { // Absent
    //     //         if ($user->gender == 'Male') {
    //     //             $absentMaleCount++;
    //     //         } elseif ($user->gender == 'Female') {
    //     //             $absentFemaleCount++;
    //     //         }
    //     //     }
    //     // }
    //     $presentGirls = StudentAttendance::where('attendance_type_id', 1)
    //     ->whereHas('student.user', function ($query) use ($schoolId) {
    //         $query->where('school_id', $schoolId)->where('gender', 'female');
    //     })
    //     ->whereDate('created_at', today()) // Filter by today's date
    //     ->count();

    //     $presentBoys = StudentAttendance::where('attendance_type_id', 1)
    //     ->whereHas('student.user', function ($query) use ($schoolId) {
    //         $query->where('school_id', $schoolId)->where('gender', 'male');
    //     })
    //     ->whereDate('created_at', today()) // Filter by today's date
    //     ->count();


    //     $totalStaffs = Staff::where('school_id', $schoolId)->count();
    //     // Get the count of present staff
    //     $presentStaffCount = StaffAttendance::where('attendance_type_id', 1)
    //     ->whereHas('staff', function ($query) use ($schoolId) {
    //         $query->where('school_id', $schoolId);
    //     })
    //     ->whereDate('created_at', today()) // Filter by today's date
    //     ->count();
    //     $absentStaffCount = StaffAttendance::where('attendance_type_id', 2)
    //     ->whereHas('staff', function ($query) use ($schoolId) {
    //         $query->where('school_id', $schoolId);
    //     })
    //     ->whereDate('created_at', today()) // Filter by today's date
    //     ->count();


    //     // Fetch data from HeadTeacherLog model where logged_date matches the provided date
    //     $teacherLog = HeadTeacherLog::whereDate('logged_date', $date)->first();

    //     // Check if no data exists for the provided date
    //     if (!$teacherLog && $studentAttendanceData->isEmpty()) {
    //         return response()->json(['message' => 'No data found for this date']);
    //     }

    //     $majorIncident = $teacherLog->major_incidents;
    //     $majorWorkObservation = $teacherLog->major_work_observation;
    //     $assemblyManagement = $teacherLog->assembly_management;
    //     $miscellaneous = $teacherLog->miscellaneous;


    //     // Return data as JSON response
    //     return response()->json([
    //         'students' => $students,
    //         'presentMaleCount' => $presentBoys,
    //         'presentFemaleCount' => $presentGirls,
    //         'absentMaleCount' => $absentMaleCount,
    //         'absentFemaleCount' => $absentFemaleCount,
    //         'presentStaffCount' => $presentStaffCount,
    //         'absentStaffCount' => $absentStaffCount,
    //         'majorIncident' => $majorIncident,
    //         'majorWorkObservation' => $majorWorkObservation,
    //         'assemblyManagement' => $assemblyManagement,
    //         'miscellaneous' => $miscellaneous
    //     ]);
    // }

    // public function getTotalStudents()
    // {
    //     $schoolId = Auth::user()->school_id;
    //     $totalStudents = Student::where('school_id', $schoolId)->count();
    //     return response()->json(['totalStudents' => $totalStudents]);
    // }
}
