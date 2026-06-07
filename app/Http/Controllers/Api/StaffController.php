<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStaffRequest;
use App\Http\Requests\UpdateStaffRequest;
use App\Models\Staff;
use Illuminate\Support\Facades\Storage;

class StaffController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => Staff::latest()->get()->map(function ($staff) {
                $staff->avatar = $staff->avatar
                    ? asset('storage/' . $staff->avatar)
                    : null;

                return $staff;
            })
        ]);
    }

    public function store(StoreStaffRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request
                ->file('avatar')
                ->store('staff', 'public');
        }

        $staff = Staff::create($data);

        $staff->avatar = $staff->avatar
            ? asset('storage/' . $staff->avatar)
            : null;

        return response()->json([
            'message' => 'Staff created successfully.',
            'data' => $staff
        ], 201);
    }

    public function show(Staff $staff)
    {
        $staff->avatar = $staff->avatar
            ? asset('storage/' . $staff->avatar)
            : null;

        return response()->json([
            'data' => $staff
        ]);
    }

    public function update(UpdateStaffRequest $request, Staff $staff)
    {
        $data = $request->validated();

        if ($request->hasFile('avatar')) {

            if ($staff->avatar) {
                Storage::disk('public')->delete($staff->avatar);
            }

            $data['avatar'] = $request
                ->file('avatar')
                ->store('staff', 'public');
        }

        $staff->update($data);

        $staff->avatar = $staff->avatar
            ? asset('storage/' . $staff->avatar)
            : null;

        return response()->json([
            'message' => 'Staff updated successfully.',
            'data' => $staff
        ]);
    }

    public function destroy(Staff $staff)
    {
        if ($staff->avatar) {
            Storage::disk('public')->delete($staff->avatar);
        }

        $staff->delete();

        return response()->json([
            'message' => 'Staff deleted successfully.'
        ]);
    }
}