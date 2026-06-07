<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'position' => 'sometimes|required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'salary_type' => 'sometimes|required|in:Monthly,Daily',
            'monthly_salary' => 'nullable|numeric|min:0',
            'daily_rate' => 'nullable|numeric|min:0',
            'status' => 'sometimes|required|in:Active,Inactive',
            'attendance' => 'nullable|in:Present,Absent,Not Timed In',
            'avatar' => 'nullable|image|mimes:jpeg,jpg,png|max:4096',
        ];
    }
}