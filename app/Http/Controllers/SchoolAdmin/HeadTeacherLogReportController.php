<?php

namespace App\Http\Controllers\SchoolAdmin;

use App\Models\School;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\HeadTeacherLog;
use App\Models\StudentSession;
use App\Models\StaffAttendance;
use App\Models\StudentAttendance;
use App\Http\Controllers\Controller;

class HeadTeacherLogReportController extends Controller
{
    public function index(Request $request)
    {
        try {
            $page_title = "List Head Teacher Log Reports";

            return view('backend.school_admin.logs.head_teacher_log_reports.index', compact('page_title'));
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function getAttendanceReport(Request $request)
    {
        $date = $request->input('date');

        // Retrieve active student sessions for the given date
        $activeSessions = StudentSession::where('is_active', 1)
            ->get();

        // Extract user IDs of students from the active sessions
        $studentUserIds = $activeSessions->pluck('user_id')->toArray();

        // Fetch students associated with the active sessions
        $students = Student::whereIn('user_id', $studentUserIds)->get();


        // Fetch student attendance data for the specified date
        $studentAttendanceData = StudentAttendance::with('studentSession.student.user')
            ->where('date', $date)
            ->get();


        // Initialize variables to store counts of male and female students
        $presentMaleCount = 0;
        $presentFemaleCount = 0;
        $absentMaleCount = 0;
        $absentFemaleCount = 0;

        // Iterate through the attendance data to count male and female students
        foreach ($studentAttendanceData as $attendance) {

            $user = optional($attendance->studentSession)->user;

            // Check if the attendance type is 'Present'  id = 1 (Present)
            if ($attendance->attendance_type_id == 1) {


                // Increment the respective count based on the user's gender
                if ($user && $user->gender) {
                    if ($user->gender == 'Male') {
                        $presentMaleCount++;
                    } elseif ($user->gender == 'Female') {
                        $presentFemaleCount++;
                    }
                }
            } elseif ($attendance->attendance_type_id == 2) { // Absent
                if ($user->gender == 'Male') {
                    $absentMaleCount++;
                } elseif ($user->gender == 'Female') {
                    $absentFemaleCount++;
                }
            }
        }

        // Get the count of present staff
        $presentStaffCount = StaffAttendance::where('attendance_type_id', 1)->count();
        $absentStaffCount = StaffAttendance::where('attendance_type_id', 2)->count();


        // Fetch data from HeadTeacherLog model where logged_date matches the provided date
        $teacherLog = HeadTeacherLog::whereDate('logged_date', $date)->first();

        // Check if no data exists for the provided date
        if (!$teacherLog && $studentAttendanceData->isEmpty()) {
            return response()->json(['message' => 'No data found for this date']);
        }

        $majorIncident = $teacherLog->major_incidents;
        $majorWorkObservation = $teacherLog->major_work_observation;
        $assemblyManagement = $teacherLog->assembly_management;
        $miscellaneous = $teacherLog->miscellaneous;


        // Return data as JSON response
        return response()->json([
            'students' => $students,
            'presentMaleCount' => $presentMaleCount,
            'presentFemaleCount' => $presentFemaleCount,
            'absentMaleCount' => $absentMaleCount,
            'absentFemaleCount' => $absentFemaleCount,
            'presentStaffCount' => $presentStaffCount,
            'absentStaffCount' => $absentStaffCount,
            'majorIncident' => $majorIncident,
            'majorWorkObservation' => $majorWorkObservation,
            'assemblyManagement' => $assemblyManagement,
            'miscellaneous' => $miscellaneous
        ]);
    }

    public function getTotalStudents()
    {
        $totalStudents = Student::count();
        return response()->json(['totalStudents' => $totalStudents]);
    }
}
