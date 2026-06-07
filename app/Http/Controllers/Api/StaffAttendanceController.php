<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\StaffAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StaffAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->query('date', now()->toDateString());

        $staffList = Staff::latest()->get();

        $attendance = $staffList->map(function ($staff) use ($date) {
            $record = StaffAttendance::firstOrCreate(
                [
                    'staff_id' => $staff->id,
                    'attendance_date' => $date,
                ],
                [
                    'status' => 'Not Timed In',
                ]
            );

            return $this->formatAttendance($record->load('staff'));
        });

        return response()->json([
            'data' => $attendance,
        ]);
    }

    public function timeIn(Request $request, StaffAttendance $attendance)
    {
        $attendance->update([
            'time_in' => now()->format('H:i:s'),
            'status' => 'Present',
        ]);

        $attendance->staff->update([
            'attendance' => 'Present',
        ]);

        return response()->json([
            'message' => 'Time in recorded successfully.',
            'data' => $this->formatAttendance($attendance->load('staff')),
        ]);
    }

    public function timeOut(Request $request, StaffAttendance $attendance)
    {
        if (!$attendance->time_in) {
            return response()->json([
                'message' => 'Staff has not timed in yet.',
            ], 422);
        }

        $timeIn = Carbon::parse($attendance->attendance_date . ' ' . $attendance->time_in);
        $timeOut = now();

        $renderedHours = round($timeIn->diffInMinutes($timeOut) / 60, 2);

        $attendance->update([
            'time_out' => $timeOut->format('H:i:s'),
            'rendered_hours' => $renderedHours,
            'status' => 'Present',
        ]);

        return response()->json([
            'message' => 'Time out recorded successfully.',
            'data' => $this->formatAttendance($attendance->load('staff')),
        ]);
    }

    public function markAbsent(StaffAttendance $attendance)
    {
        $attendance->update([
            'time_in' => null,
            'time_out' => null,
            'rendered_hours' => 0,
            'status' => 'Absent',
        ]);

        $attendance->staff->update([
            'attendance' => 'Absent',
        ]);

        return response()->json([
            'message' => 'Staff marked as absent.',
            'data' => $this->formatAttendance($attendance->load('staff')),
        ]);
    }

    public function updateRemarks(Request $request, StaffAttendance $attendance)
    {
        $validated = $request->validate([
            'remarks' => 'nullable|string|max:1000',
        ]);

        $attendance->update($validated);

        return response()->json([
            'message' => 'Remarks updated successfully.',
            'data' => $this->formatAttendance($attendance->load('staff')),
        ]);
    }

    private function formatAttendance(StaffAttendance $attendance)
    {
        $staff = $attendance->staff;

        return [
            'id' => $attendance->id,
            'staffId' => $attendance->staff_id,
            'employeeId' => 'JNS-' . str_pad($attendance->staff_id, 3, '0', STR_PAD_LEFT),
            'name' => $staff?->name,
            'position' => $staff?->position,
            'department' => $staff?->department ?? 'Operations',

            'image' => $staff?->avatar
                ? asset('storage/' . $staff->avatar)
                : null,

            'date' => $attendance->attendance_date,
            'timeIn' => $attendance->time_in,
            'timeOut' => $attendance->time_out,
            'renderedHours' => (float) $attendance->rendered_hours,
            'status' => $attendance->status,
            'remarks' => $attendance->remarks,
        ];
    }
}