<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Schedule;

class ScheduleController extends Controller
{
    /**
     * Display all schedules
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => Schedule::orderBy('id')->get()
        ]);
    }

    /**
     * Create schedule
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i',
            'type'       => 'required|in:day,night,whole_day',
        ]);

        $schedule = Schedule::create([
            'name'       => $request->name,
            'slug'       => Str::slug($request->name),
            'start_time' => $request->start_time,
            'end_time'   => $request->end_time,
            'type'       => $request->type,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Schedule created successfully.',
            'data'    => $schedule
        ], 201);
    }

    /**
     * Update schedule
     */
    public function update(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);

        $request->validate([
            'name'       => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i',
            'type'       => 'required|in:day,night,whole_day',
        ]);

        $schedule->update([
            'name'       => $request->name,
            'slug'       => Str::slug($request->name),
            'start_time' => $request->start_time,
            'end_time'   => $request->end_time,
            'type'       => $request->type,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Schedule updated successfully.',
            'data'    => $schedule
        ]);
    }

    /**
     * Delete schedule
     */
    public function destroy($id)
    {
        $schedule = Schedule::findOrFail($id);

        $schedule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Schedule deleted successfully.'
        ]);
    }
}