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
use Illuminate\Support\Facades\Log;

class StaffAttendanceController extends Controller
{
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

        return view('backend.school_admin.staff_attendance.index', compact('page_title', 'attendance_types', 'schoolId'));
    }

    public function saveAttendance(Request $request)
    {
        try {
            $attendanceData = $request->input('attendance_data');
            $schoolId = session('school_id');

            if (empty($attendanceData) || !is_array($attendanceData)) {
                Log::error('Invalid attendance data provided.');
                return back()->withToastError('Invalid attendance data.');
            }

            foreach ($attendanceData as $data) {
                $staffId = $data['staff_id'] ?? null;
                $attendanceType = $data['attendance_type_id'] ?? null;
                $date = $data['date'] ?? now()->format('Y-m-d');
                $remarks = $data['remarks'] ?? '';

                if (!$staffId || !$attendanceType) {
                    Log::error('Missing required fields: Staff ID or Attendance Type.');
                    return back()->withToastError('Staff ID and Attendance Type are required.');
                }

                $staff = Staff::where('school_id', $schoolId)->find($staffId);

                if ($staff) {
                    Log::info('Processing attendance for staff ID: ' . $staffId);

                    $existingAttendance = StaffAttendance::where('date', $date)
                        ->where('staff_id', $staffId)
                        ->first();

                    if ($existingAttendance) {
                        Log::info('Updating existing attendance record for staff ID: ' . $staffId);
                        $existingAttendance->attendance_type_id = $attendanceType;
                        $existingAttendance->remarks = $remarks;
                        $existingAttendance->save();
                    } else {
                        Log::info('Creating new attendance record for staff ID: ' . $staffId);
                        $newAttendance = new StaffAttendance();
                        $newAttendance->attendance_type_id = $attendanceType;
                        $newAttendance->date = $date;
                        $newAttendance->remarks = $remarks;
                        $newAttendance->school_id = $schoolId;
                        $newAttendance->staff_id = $staffId;
                        $newAttendance->role = $staff->role; // Assuming $staff->role exists
                        $newAttendance->save();
                    }
                } else {
                    Log::warning('No staff found for the given ID: ' . $staffId . ' in school ID: ' . $schoolId);
                    return response()->json(['message' => 'No staff found for the given ID'], 404);
                }
            }

            return back()->withToastSuccess('Attendance saved successfully');
        } catch (\Exception $e) {
            Log::error('Error saving staff attendance: ' . $e->getMessage(), ['stack' => $e->getTraceAsString()]);
            return back()->withToastError('Error saving staff attendance. Please try again later.');
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
            return redirect()->back()->withToastSuccess('Attendance saved successfully!');
        } catch (\Exception $e) {
            Log::error('Error saving attendance: ' . $e->getMessage(), ['stack' => $e->getTraceAsString()]);
            return back()->withToastError($e->getMessage());
        }
    }

    public function getStaffName(Request $request)
    {
        try {
            $role = $request->role;
            $date = $request->date;
            $schoolId = session('school_id'); // Fetch school ID from session

            if (!isset($date)) {
                $date = LaravelNepaliDate::from(Carbon::now())->toNepaliDate();
            }

            // Fetch staff details from the service, filtering by role, date, and school ID
            $staffDetails = $this->StaffRoleDateService->getStaffDateRoleForDataTable($request);

            $responseArray = [];

            foreach ($staffDetails as $staff) {
                $attendanceTypes = AttendanceType::where('is_active', 1)->get();
                $attendance = '';

                if (isset($staff->staffAttendance)) {
                    $attendance = $staff->staffAttendance;
                    $staff['attendance_type_id'] = $staff->staffAttendance ? $staff->staffAttendance->attendance_type_id : '';
                    $staff['remarks'] = $staff->staffAttendance ? $staff->staffAttendance->remarks : '';
                    $staff['staff_id'] = $staff->id;
                }

                // Check if role filtering is enabled and match the requested role
            if ($role && $staff->role_id != $role) {
                continue; // Skip this staff member if roles don't match
            }

                $responseArray[] = [
                    'staff_id' => $staff->id,
                    'staff' => $staff,
                    'staff_name' => $staff->f_name,
                    'role_id' => $staff->role_id,
                    'attendance_types' => $attendanceTypes->toArray(),
                    'staff_attendances' => $attendance
                ];
            }

            return response()->json(['original' => $responseArray, 'date' => $date]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function markHolidayRange(Request $request)
    {
        try {
            $schoolId = session('school_id');
            $startDate = Carbon::parse($request->input('start_date'));
            $endDate = Carbon::parse($request->input('end_date'));
            $reason = $request->input('reason');
    
            DB::beginTransaction();
    
            // Iterate through each date in the range
            for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
                // Update existing attendance records to holiday for all staff in the school
                $updatedCount = StaffAttendance::where('school_id', $schoolId)
                    ->where('date', $date->toDateString())
                    ->update(['attendance_type_id' => 4, 'remarks' => $reason]);
    
                // Insert new attendance records for staff without existing records for the date
                $staffMembers = Staff::where('school_id', $schoolId)->get();
    
                foreach ($staffMembers as $staff) {
                    // Check if attendance record already exists for the staff and date
                    $existingAttendance = StaffAttendance::where('staff_id', $staff->id)
                        ->where('date', $date->toDateString())
                        ->first();
    
                    if (!$existingAttendance) {
                        // Create a new attendance record
                        $newAttendance = new StaffAttendance();
                        $newAttendance->staff_id = $staff->id;
                        $newAttendance->school_id = $schoolId;
                        $newAttendance->date = $date->toDateString();
                        $newAttendance->attendance_type_id = 4; // Set to 4 for holiday
                        $newAttendance->remarks = $reason; // Optional: Add remarks if needed
                        $newAttendance->role = $staff->role;
    
                        $newAttendance->save();
                    }
                }
            }
    
            DB::commit();
    
            return response()->json(['success' => true, 'message' => "Staff attendances updated/inserted successfully for holiday range."]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update/insert staff attendances for holiday range: ' . $e->getMessage(), ['stack' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Failed to update/insert staff attendances for holiday range: ' . $e->getMessage()]);
        }
    }
}
