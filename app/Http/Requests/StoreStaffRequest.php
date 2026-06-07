<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStaffRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'salary_type' => 'required|in:Monthly,Daily',
            'monthly_salary' => 'nullable|numeric|min:0',
            'daily_rate' => 'nullable|numeric|min:0',
            'status' => 'required|in:Active,Inactive',
            'attendance' => 'nullable|in:Present,Absent,Not Timed In',
            'avatar' => 'nullable|image|mimes:jpeg,jpg,png|max:4096',
        ];
    }
}
