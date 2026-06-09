<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::first();

        if (!$settings) {
            $settings = Setting::create($this->defaultSettings());
        }

        return response()->json([
            'message' => 'Settings fetched successfully.',
            'settings' => $settings,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'profile' => 'required|array',
            'system' => 'required|array',
            'attendance' => 'required|array',
            'payroll' => 'required|array',
            'departments' => 'required|array',
            'positions' => 'required|array',

            'profile.name' => 'nullable|string|max:255',
            'profile.username' => 'nullable|string|max:255',
            'profile.email' => 'nullable|email|max:255',
            'profile.contact' => 'nullable|string|max:50',
            'profile.password' => 'nullable|string',
            'profile.confirmPassword' => 'nullable|string',

            'system.name' => 'nullable|string|max:255',
            'system.type' => 'nullable|string|max:255',
            'system.currency' => 'nullable|string|max:20',
            'system.timezone' => 'nullable|string|max:100',
            'system.description' => 'nullable|string',

            'attendance.workStart' => 'nullable|string',
            'attendance.workEnd' => 'nullable|string',
            'attendance.gracePeriod' => 'nullable|numeric',
            'attendance.defaultStatus' => 'nullable|string',
            'attendance.workingDays' => 'nullable|array',

            'payroll.cycle' => 'nullable|string',
            'payroll.salaryType' => 'nullable|string',
            'payroll.overtimeRate' => 'nullable|numeric',
            'payroll.holidayRate' => 'nullable|numeric',
            'payroll.sssDeduction' => 'nullable|numeric',
            'payroll.philHealthDeduction' => 'nullable|numeric',
            'payroll.pagIbigDeduction' => 'nullable|numeric',
            'payroll.autoPaid' => 'nullable|boolean',
        ]);

        $settings = Setting::first();

        if (!$settings) {
            $settings = Setting::create($this->defaultSettings());
        }

        $settings->update($validated);

        return response()->json([
            'message' => 'Settings saved successfully.',
            'settings' => $settings,
        ]);
    }

    private function defaultSettings(): array
    {
        return [
            'profile' => [
                'name' => 'Administrator',
                'username' => 'admin',
                'email' => 'admin@jannsresort.com',
                'contact' => '09123456789',
                'password' => '',
                'confirmPassword' => '',
            ],

            'system' => [
                'name' => 'JANNS SPRING RESORT',
                'type' => 'Staff Management System',
                'currency' => 'PHP',
                'timezone' => 'Asia/Manila',
                'description' => 'A staff management system for attendance, payroll, and staff expenses.',
            ],

            'attendance' => [
                'workStart' => '08:00',
                'workEnd' => '17:00',
                'gracePeriod' => 15,
                'defaultStatus' => 'Present',
                'workingDays' => [
                    'Monday',
                    'Tuesday',
                    'Wednesday',
                    'Thursday',
                    'Friday',
                    'Saturday',
                ],
            ],

            'payroll' => [
                'cycle' => 'Monthly',
                'salaryType' => 'Monthly',
                'overtimeRate' => 100,
                'holidayRate' => 2,
                'sssDeduction' => 0,
                'philHealthDeduction' => 0,
                'pagIbigDeduction' => 0,
                'autoPaid' => false,
            ],

            'departments' => [
                'Front Desk',
                'Housekeeping',
                'Kitchen',
                'Maintenance',
                'Admin',
            ],

            'positions' => [
                'Manager',
                'Receptionist',
                'Cleaner',
                'Cook',
                'Staff',
            ],
        ];
    }
}