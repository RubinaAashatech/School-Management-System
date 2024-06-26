<?php
namespace App\Http\Controllers\MunicipalityAdmin;
use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\StudentAttendance;
use App\Models\StaffAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Anuzpandey\LaravelNepaliDate\LaravelNepaliDate;

class AttendenceReportController extends Controller
{
    public function index()
    {
        return view('backend.municipality_admin.attendencereport.index');
    }
    public function report(Request $request)
    {
        $inputDate = $request->input('date', Carbon::today()->format('Y-m-d')); // Default to today's date if not provided
        $date = LaravelNepaliDate::from($inputDate)->toEnglishDate();
        $studentAttendances = StudentAttendance::whereDate('created_at', $date)
            ->get();
        return view('backend.municipality_admin.attendencereport.index', compact('studentAttendances', 'date'));
    }
}