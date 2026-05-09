<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;

class TodayController extends Controller
{
    /* =========================
       READ (TODAY ATTENDANCE)
    ========================= */
    public function index()
    {
        $today = now()->toDateString();

        $bookings = Booking::whereDate('start_datetime', $today)
            ->where('checked_in', true) // 🔥 ONLY ATTENDED
            ->get();

        $grouped = $bookings->groupBy('cabin');

        return response()->json([
            'date' => $today,
            'data' => $grouped
        ]);
    }

    /* =========================
       CHECK-IN (ATTENDANCE)
    ========================= */
    public function checkIn(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|exists:bookings,id'
        ]);

        $booking = Booking::find($data['id']);

        // 🔥 ensure today booking only
        if ($booking->start_datetime->toDateString() !== now()->toDateString()) {
            return response()->json([
                'message' => 'Not a today booking'
            ], 400);
        }

        $booking->update([
            'checked_in' => true,
            'checked_in_at' => now()
        ]);

        return response()->json([
            'message' => 'Guest checked in',
            'data' => $booking
        ]);
    }

    /* =========================
       BULK CHECK-IN (GROUP ARRIVAL)
    ========================= */
    public function bulkCheckin(Request $request)
    {
        $data = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:bookings,id'
        ]);

        $updated = [];

        foreach ($data['ids'] as $id) {

            $booking = Booking::find($id);

            if (!$booking) continue;

            if ($booking->start_datetime->toDateString() !== now()->toDateString()) {
                continue;
            }

            $booking->update([
                'checked_in' => true,
                'checked_in_at' => now()
            ]);

            $updated[] = $booking;
        }

        return response()->json([
            'message' => 'Guests checked in',
            'data' => $updated
        ]);
    }

    /* =========================
       UPDATE (OPTIONAL)
    ========================= */
    public function update(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|exists:bookings,id',
            'paid' => 'nullable|numeric',
            'status' => 'nullable|string',
        ]);

        $booking = Booking::find($data['id']);

        $booking->update($request->only(['paid', 'status']));

        return response()->json([
            'message' => 'Updated successfully',
            'data' => $booking
        ]);
    }

    /* =========================
       DELETE (UN-CHECK-IN)
    ========================= */
    public function destroy($id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json(['message' => 'Not found'], 404);
        }

        // 🔥 instead of delete → uncheck attendance
        $booking->update([
            'checked_in' => false,
            'checked_in_at' => null
        ]);

        return response()->json([
            'message' => 'Attendance removed'
        ]);
    }
}