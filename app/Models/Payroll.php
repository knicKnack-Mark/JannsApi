<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $fillable = [
        'staff_id',
        'payroll_month',
        'present_days',
        'absent_days',
        'gross_salary',
        'deductions',
        'net_salary',
        'status',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}