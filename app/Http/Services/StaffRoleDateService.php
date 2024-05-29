<?php

namespace App\Http\Services;

use Log;
use Carbon\Carbon;
use App\Models\User;

class StaffRoleDateService
{
    public function getStaffDateRoleForDataTable($request, $date)
    {

        if ($request->role) {
            $request->role = [$request->role];
        } else {
            $request->role = ['6', '7', '8', '9', '10'];
        }
        return User::where('school_id', session('school_id'))
            ->with([
                'roles',
                'staffs',
                'staffs.staffAttendance' => function ($attendanceQuery) use ($date) {
                    if ($date) {
                        $attendanceQuery->where('date', $date);
                    }
                }
            ])
            ->when($request->role, function ($query) use ($request) {
                // dd($request);
                $query->whereIn('role_id', $request->role);
            })
            ->get();
    }

    // public function getStaffDateRoleForDataTable($request)
    // {
    //     $staffData = User::with(['staffs', 'roles'])
    //         ->where('user_type_id', 6)
    //         ->when($request->role, function ($query) use ($request) {
    //             $query->where('role_id', $request->role);
    //         })
    //         ->when($request->date, function ($query) use ($request) {
    //             $query->whereHas('staffs', function ($subQuery) use ($request) {
    //                 $subQuery->whereHas('staffAttendance', function ($subQuery) use ($request) {
    //                     $subQuery->whereDate('date', $request->date);
    //                 });
    //             });
    //         })
    //         ->get();

    //     // Dump and die
    //     // dd($staffData);

    //     return $staffData;
    // }
}