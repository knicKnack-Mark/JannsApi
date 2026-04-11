<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;


class BookingController extends Controller
{
    // GET all bookings
    public function index()
    {
        return response()->json([
            'data' => Booking::latest()->get()
        ]);
    }

    // STORE booking
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
            'cabin' => 'required|string',
            'date' => 'required|date',
            'time' => 'required|string',
            'guests' => 'required|integer',
            'videoke' => 'required|boolean',
            'amount' => 'required|numeric',
            'paid' => 'nullable|numeric',
        ]);

        $booking = Booking::create([
            ...$validated,
            'paid' => $validated['paid'] ?? 0
        ]);

        return response()->json([
            'message' => 'Booking created successfully',
            'data' => $booking
        ], 201);
    }

    // UPDATE booking
    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $booking->update($request->all());

        return response()->json([
            'message' => 'Booking updated',
            'data' => $booking
        ]);
    }

    // DELETE booking
    public function destroy($id)
    {
        Booking::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Booking deleted'
        ]);
    }

    // ADD PAYMENT
   public function addPayment(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        // ❌ BLOCK if already fully paid
        if ($booking->paid >= $booking->amount) {
            return response()->json([
                'message' => 'Booking is already fully paid'
            ], 400);
        }

        $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);

        $newPaid = $booking->paid + $request->amount;

        // ❌ BLOCK if overpayment attempt
        if ($newPaid > $booking->amount) {
            return response()->json([
                'message' => 'Payment exceeds remaining balance'
            ], 400);
        }

        $booking->paid = $newPaid;
        $booking->save();

        return response()->json([
            'message' => 'Payment added',
            'data' => $booking
        ]);
    }


}