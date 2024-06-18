<?php

namespace App\Http\Controllers\MunicipalityAdmin;

use App\Http\Controllers\Controller;

use App\Models\School;
use App\Models\StudentAttendance;
use App\Models\StaffAttendance;
use Carbon\Carbon;

use Illuminate\Http\Request;

class AttendenceReportController extends Controller
{
    
    public function index()
    {
        return view('backend.municipality_admin.attendencereport.index');
    }

    public function report(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString()); // Default to today's date if not provided
        
        $studentAttendances = StudentAttendance::whereDate('created_at', $date)
            ->get();

        return view('backend.municipality_admin.attendencereport.index', compact('studentAttendances', 'date'));
    }
}
