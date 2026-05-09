<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Booking;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /* =========================
       READ (TODAY ATTENDANCE)
    ========================= */
    public function index()
    {
        $today = now()->toDateString();

        $data = Attendance::with('booking')
            ->whereHas('booking', function ($q) use ($today) {
                $q->whereDate('start_datetime', $today);
            })
            ->get()
            ->groupBy(fn($a) => $a->booking->cabin);

        return response()->json([
            'date' => $today,
            'data' => $data
        ]);
    }

    /* =========================
       CREATE (BULK ATTENDANCE 🔥)
    ========================= */
    public function store(Request $request)
    {
        $data = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'guests' => 'required|array',
            'guests.*' => 'required|string|max:255'
        ]);

        $booking = Booking::find($data['booking_id']);

        /* =========================
           FIXED DATE CHECK 🔥
        ========================= */
        $bookingDate = Carbon::parse($booking->start_datetime)
            ->timezone('Asia/Manila')
            ->toDateString();

        $today = now()->toDateString();

        if ($bookingDate !== $today) {
            return response()->json([
                'message' => 'Not today booking',
                'debug' => [
                    'booking_date' => $bookingDate,
                    'today' => $today
                ]
            ], 400);
        }

        /* =========================
           CREATE ATTENDANCE
        ========================= */
        $created = [];

        foreach ($data['guests'] as $name) {
            $created[] = Attendance::create([
                'booking_id' => $booking->id,
                'guest_name' => $name
            ]);
        }

        return response()->json([
            'message' => 'Attendance saved',
            'data' => $created
        ]);
    }

    /* =========================
       DELETE (REMOVE ONE PERSON)
    ========================= */
    public function destroy($id)
    {
        $attendance = Attendance::find($id);

        if (!$attendance) {
            return response()->json([
                'message' => 'Not found'
            ], 404);
        }

        $attendance->delete();

        return response()->json([
            'message' => 'Removed'
        ]);
    }
}