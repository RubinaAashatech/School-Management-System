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
use App\Http\Controllers\Controller;

class HeadTeacherLogReportController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = Auth::user()->school_id;
        $date = $request->input('logged_date', today()->toDateString()); // Use today's date if none provided

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
            ->whereDate('created_at', $date) // Filter by selected date
            ->count();

        // Count the present girls for today
        $presentGirls = StudentAttendance::where('attendance_type_id', 1)
            ->whereHas('student.user', function ($query) use ($schoolId) {
                $query->where('school_id', $schoolId)->where('gender', 'female');
            })
            ->whereDate('created_at', $date) // Filter by selected date
            ->count();

        // Count the present boys for today
        $presentBoys = StudentAttendance::where('attendance_type_id', 1)
            ->whereHas('student.user', function ($query) use ($schoolId) {
                $query->where('school_id', $schoolId)->where('gender', 'male');
            })
            ->whereDate('created_at', $date) // Filter by selected date
            ->count();

        // Count the absent students for today
        $absentStudents = StudentAttendance::where('attendance_type_id', 2)
            ->whereHas('student', function ($query) use ($schoolId) {
                $query->where('school_id', $schoolId);
            })
            ->whereDate('created_at', $date) // Filter by selected date
            ->count();

        // Count the absent girls for today
        $absentGirls = StudentAttendance::where('attendance_type_id', 2)
            ->whereHas('student.user', function ($query) use ($schoolId) {
                $query->where('school_id', $schoolId)->where('gender', 'female');
            })
            ->whereDate('created_at', $date) // Filter by selected date
            ->count();

        // Count the absent boys for today
        $absentBoys = StudentAttendance::where('attendance_type_id', 2)
            ->whereHas('student.user', function ($query) use ($schoolId) {
                $query->where('school_id', $schoolId)->where('gender', 'male');
            })
            ->whereDate('created_at', $date) // Filter by selected date
            ->count();

        $totalStaffs = Staff::where('school_id', $schoolId)->count();

        $presentStaffs = StaffAttendance::where('attendance_type_id', 1)
            ->whereHas('staff', function ($query) use ($schoolId) {
                $query->where('school_id', $schoolId);
            })
            ->whereDate('created_at', $date) // Filter by selected date
            ->count();

        // Count the absent staff members for today
        $absentStaffs = StaffAttendance::where('attendance_type_id', 2)
            ->whereHas('staff', function ($query) use ($schoolId) {
                $query->where('school_id', $schoolId);
            })
            ->whereDate('created_at', $date) // Filter by selected date
            ->count();

            $teacherLogs = HeadTeacherLog::whereDate('logged_date', $date)
            ->pluck('major_incidents', 'major_work_observation', 'assembly_management', 'miscellaneous')
            ->first(); // Assuming you want only the first record matching the condition
        
        // Access values from the collection
        $majorIncident = $teacherLogs['major_incidents'] ?? '';
        $majorWorkObservation = $teacherLogs['major_work_observation'] ?? '';
        $assemblyManagement = $teacherLogs['assembly_management'] ?? '';
        $miscellaneous = $teacherLogs['miscellaneous'] ?? '';

        return view('backend.school_admin.logs.head_teacher_log_reports.index', compact(
            'totalStudents', 'presentStudents', 'absentStudents', 'presentStaffs', 'absentStaffs',
            'presentGirls', 'presentBoys', 'absentGirls', 'absentBoys', 'majorIncident', 'majorWorkObservation',
            'assemblyManagement', 'miscellaneous', 'date'
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
