<?php

namespace App\Http\Controllers\SchoolAdmin;

use App\Models\Staff;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\StaffAttendance;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Services\StaffRoleDateService;
use App\Models\AttendanceType;
use Illuminate\Support\Facades\Validator;
use Anuzpandey\LaravelNepaliDate\LaravelNepaliDate;

class StaffAttendanceController extends Controller
{
    //
    protected $StaffRoleDateService;

    public function __construct(StaffRoleDateService $StaffRoleDateService)
    {
        $this->StaffRoleDateService = $StaffRoleDateService;
    }

    public function index()
    {
        $page_title = 'Staff Attendance';
        $schoolId = session('school_id');
        $attendance_types = AttendanceType::all();

        return view('backend.school_admin.staff_attendance.index', compact('page_title', 'attendance_types','schoolId'));
    }
    public function saveAttendance(Request $request)
    {
        try {
            $attendanceData = $request->input('attendance_data');
            $staticSchoolId = session('school_id');
    
            foreach ($attendanceData as $data) {
                $staffId = $data['staff_id'];
                $attendanceType = $data['attendance_type_id'];
                $date = $data['date'] ?? now()->format('Y-m-d');
                $remarks = $data['remarks'] ?? '';
    
                $staff = Staff::find($staffId);
                if ($staff) {
                    $userId = $staff->user_id;
                    $staffRole = $staff->role;
    
                    $existingAttendance = StaffAttendance::where('date', $date)
                        ->where('staff_id', $staffId)
                        ->first();
    
                    if ($existingAttendance) {
                        $existingAttendance->attendance_type_id = $attendanceType;
                        $existingAttendance->remarks = $remarks;
                        $existingAttendance->save();
                    } else {
                        $newAttendance = new StaffAttendance();
                        $newAttendance->attendance_type_id = $attendanceType;
                        $newAttendance->date = $date;
                        $newAttendance->remarks = $remarks;
                        $newAttendance->school_id = $staticSchoolId;
                        $newAttendance->staff_id = $staffId;
                        $newAttendance->role = $staffRole;
                        $newAttendance->save();
                    }
                } else {
                    return response()->json(['message' => 'No staff found for the given ID'], 404);
                }
            }
    
            return back()->withToastSuccess('Attendance saved successfully');
        } catch (\Exception $e) {
            return back()->withToastError('Error saving staff attendance: ' . $e->getMessage());
        }
    }
    
    public function store(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'attendance_type_id' => 'required|integer',
            'date' => 'required|string',
            'remarks' => 'nullable|string',
        ]);

        if ($validatedData->fails()) {
            return back()->withToastError($validatedData->messages()->all()[0])->withInput();
        }

        try {
            $staffAttendanceData = $request->all();
            $savedData = StaffAttendance::create($staffAttendanceData);
            return redirect()->back()->withToastSuccess('attendance Saved Successfully!');
        } catch (\Exception $e) {
            return back()->withToastError($e->getMessage());
        }
    }

    public function getStaffName(Request $request)
    {
        // dd($request);
        try {
            $role = $request->role;
            $date = $request->date;
            if (!isset($request->date)) {
                $date = LaravelNepaliDate::from(Carbon::now())->toNepaliDate();
            }

            // Fetch staff details from the service, filtering by role and date
            $staffDetails = $this->StaffRoleDateService->getStaffDateRoleForDataTable($request, $date);
            // dd($staffDetails);

            $responseArray = [];

            foreach ($staffDetails as $staff) {
                $attendanceTypes = AttendanceType::where('is_active', 1)->get();
                $attendance = '';
                if (isset($staff->staffs->staffAttendance)) {

                    $attendance = $staff->staffs->staffAttendance;
                    $staff['attendance_type_id'] = $staff->staffs->staffAttendance ? $staff->staffs->staffAttendance->attendance_type_id : '';

                    $staff['remarks'] = $staff->staffs->staffAttendance ? $staff->staffs->staffAttendance->remarks : '';
                    $staff['staff_id'] = $staff->staffs->id;
                }
                $responseArray[] = [
                    'staff_id' => $staff->staffs->id,
                    'staff' => $staff,
                    'staff_name' => $staff->f_name,
                    'role_id' => $staff->role_id,
                    'attendance_types' => $attendanceTypes->toArray(),
                    'staff_attendances' => $attendance

                ];
            }

            // dd($responseArray);

            return response()->json(['original' => $responseArray, 'date' => $date]);
        } catch (\Exception $e) {

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}