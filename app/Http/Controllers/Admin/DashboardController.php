<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\StaffAttendance;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function stats()
    {
        $today = Carbon::today()->toDateString();

        $totalStaff = Staff::count();

        $presentToday = StaffAttendance::whereDate('attendance_date', $today)
            ->where('status', 'Present')
            ->count();

        $absentToday = StaffAttendance::whereDate('attendance_date', $today)
            ->where('status', 'Absent')
            ->count();

        $attendanceRate = $totalStaff > 0
            ? round(($presentToday / $totalStaff) * 100)
            : 0;

        return response()->json([
            'analytics' => [
                [
                    'title' => 'Total Staff',
                    'value' => $totalStaff,
                    'icon' => 'solar:users-group-rounded-bold-duotone',
                    'color' => 'green',
                    'description' => 'Registered employees',
                ],
                [
                    'title' => 'Present Today',
                    'value' => $presentToday,
                    'icon' => 'solar:user-check-bold-duotone',
                    'color' => 'blue',
                    'description' => $attendanceRate . '% attendance',
                ],
                [
                    'title' => 'Absent Today',
                    'value' => $absentToday,
                    'icon' => 'solar:user-cross-bold-duotone',
                    'color' => 'red',
                    'description' => 'Needs monitoring',
                ],
                [
                    'title' => 'Salary Expenses',
                    'value' => '₱0',
                    'icon' => 'solar:wallet-money-bold-duotone',
                    'color' => 'orange',
                    'description' => 'Monthly payroll',
                ],
            ],

            'attendanceChart' => [
                'weekly' => [
                    'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    'present' => [0, 0, 0, 0, 0, 0, 0],
                    'absent' => [0, 0, 0, 0, 0, 0, 0],
                ],

                'monthly' => [
                    'labels' => [
                        'Jan',
                        'Feb',
                        'Mar',
                        'Apr',
                        'May',
                        'Jun',
                        'Jul',
                        'Aug',
                        'Sep',
                        'Oct',
                        'Nov',
                        'Dec',
                    ],
                    'present' => array_fill(0, 12, 0),
                    'absent' => array_fill(0, 12, 0),
                ],

                'yearly' => [
                    'labels' => [
                        '2025',
                        '2026',
                        '2027',
                        '2028',
                        '2029',
                        '2030',
                    ],
                    'present' => array_fill(0, 6, 0),
                    'absent' => array_fill(0, 6, 0),
                ],
            ],

            'quickStats' => [
                [
                    'title' => 'Payroll Processed',
                    'value' => '₱0',
                    'icon' => 'solar:dollar-bold-duotone',
                ],
                [
                    'title' => 'Overtime Hours',
                    'value' => '0 hrs',
                    'icon' => 'solar:clock-circle-bold-duotone',
                ],
                [
                    'title' => 'On Leave',
                    'value' => '0 Employees',
                    'icon' => 'solar:calendar-bold-duotone',
                ],
                [
                    'title' => 'Maintenance Tasks',
                    'value' => '0 Pending',
                    'icon' => 'solar:settings-bold-duotone',
                ],
            ],

            'activities' => [],

            'notifications' => [],

            'departments' => [],
        ]);
    }
}