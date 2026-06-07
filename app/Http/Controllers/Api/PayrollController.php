<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\Staff;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->query('month', now()->format('F Y'));

        $payrolls = Payroll::with('staff')
            ->where('payroll_month', $month)
            ->latest()
            ->get();

        return response()->json([
            'data' => $payrolls->map(fn ($payroll) => $this->formatPayroll($payroll))
        ]);
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'payroll_month' => 'required|string|max:50',
            'working_days' => 'nullable|integer|min:1|max:31',
        ]);

        $month = $validated['payroll_month'];
        $workingDays = $validated['working_days'] ?? 26;

        $staffList = Staff::where('status', 'Active')->get();

        foreach ($staffList as $staff) {
            $presentDays = $workingDays;
            $absentDays = 0;

            $salary = $staff->salary_type === 'Monthly'
                ? (float) $staff->monthly_salary
                : (float) $staff->daily_rate;

            $grossSalary = $staff->salary_type === 'Monthly'
                ? $salary
                : $salary * $presentDays;

            $deductions = $staff->salary_type === 'Monthly'
                ? 0
                : $salary * $absentDays;

            $netSalary = $grossSalary - $deductions;

            Payroll::updateOrCreate(
                [
                    'staff_id' => $staff->id,
                    'payroll_month' => $month,
                ],
                [
                    'present_days' => $presentDays,
                    'absent_days' => $absentDays,
                    'gross_salary' => $grossSalary,
                    'deductions' => $deductions,
                    'net_salary' => $netSalary,
                    'status' => 'Pending',
                ]
            );
        }

        return response()->json([
            'message' => 'Payroll generated successfully.'
        ]);
    }

    public function markAsPaid(Payroll $payroll)
    {
        $payroll->update([
            'status' => 'Paid'
        ]);

        return response()->json([
            'message' => 'Payroll marked as paid.',
            'data' => $this->formatPayroll($payroll->load('staff'))
        ]);
    }

    private function formatPayroll(Payroll $payroll)
    {
        return [
            'id' => $payroll->id,
            'employeeId' => 'JNS-' . str_pad($payroll->staff->id, 3, '0', STR_PAD_LEFT),
            'staffId' => $payroll->staff_id,
            'name' => $payroll->staff->name,
            'position' => $payroll->staff->position,
            'salaryType' => $payroll->staff->salary_type,
            'salary' => $payroll->staff->salary_type === 'Monthly'
                ? (float) $payroll->staff->monthly_salary
                : (float) $payroll->staff->daily_rate,
            'presentDays' => $payroll->present_days,
            'absentDays' => $payroll->absent_days,
            'grossSalary' => (float) $payroll->gross_salary,
            'deductions' => (float) $payroll->deductions,
            'netSalary' => (float) $payroll->net_salary,
            'status' => $payroll->status,
        ];
    }
}