<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use Carbon\Carbon;

class BookingController extends Controller
{
    // ✅ GET BOOKINGS (WITH OVERNIGHT SUPPORT)
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 5);
        $search = $request->get('search');
        $status = $request->get('status');
        $date = $request->get('date');

        $query = Booking::query();

        // 🔍 SEARCH
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('address', 'like', "%$search%")
                  ->orWhere('cabin', 'like', "%$search%");
            });
        }

        // 💰 STATUS FILTER
        if ($status === 'paid') {
            $query->whereColumn('paid', '>=', 'amount');
        } elseif ($status === 'unpaid') {
            $query->whereColumn('paid', '<', 'amount');
        }

        // 📅 DATE FILTER (OVERNIGHT SAFE)
        if ($date) {
            $query->where(function ($q) use ($date) {
                $q->whereDate('start_datetime', $date)
                  ->orWhereDate('end_datetime', $date)
                  ->orWhere(function ($q2) use ($date) {
                      $q2->where('start_datetime', '<=', $date . ' 23:59:59')
                         ->where('end_datetime', '>=', $date . ' 00:00:00');
                  });
            });
        }

        return response()->json(
            $query->latest()->paginate($perPage)
        );
    }

    // ✅ STORE BOOKING (🔥 WITH OVERLAP PREVENTION)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
            'cabin' => 'required|string',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date',
            'guests' => 'required|integer',
            'videoke' => 'required|boolean',
            'amount' => 'required|numeric',
            'paid' => 'nullable|numeric',
        ]);

        $start = Carbon::parse($validated['start_datetime']);
        $end = Carbon::parse($validated['end_datetime']);

        // 🔥 HANDLE OVERNIGHT
        if ($end <= $start) {
            $end->addDay();
        }

        // 🚫 OVERLAP CHECK
        $conflict = Booking::where('cabin', $validated['cabin'])
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_datetime', [$start, $end])
                  ->orWhereBetween('end_datetime', [$start, $end])
                  ->orWhere(function ($q2) use ($start, $end) {
                      $q2->where('start_datetime', '<=', $start)
                         ->where('end_datetime', '>=', $end);
                  });
            })
            ->exists();

        if ($conflict) {
            return response()->json([
                'message' => 'Booking conflict: this cabin is already reserved for that time.'
            ], 422);
        }

        $booking = Booking::create([
            ...$validated,
            'start_datetime' => $start,
            'end_datetime' => $end,
            'paid' => $validated['paid'] ?? 0
        ]);

        return response()->json([
            'message' => 'Booking created successfully',
            'data' => $booking
        ], 201);
    }

    // ✅ UPDATE BOOKING (🔥 WITH OVERLAP PREVENTION)
    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $data = $request->all();

        if (isset($data['start_datetime']) && isset($data['end_datetime'])) {
            $start = Carbon::parse($data['start_datetime']);
            $end = Carbon::parse($data['end_datetime']);

            if ($end <= $start) {
                $end->addDay();
            }

            // 🚫 OVERLAP CHECK (exclude self)
            $conflict = Booking::where('cabin', $booking->cabin)
                ->where('id', '!=', $id)
                ->where(function ($q) use ($start, $end) {
                    $q->whereBetween('start_datetime', [$start, $end])
                      ->orWhereBetween('end_datetime', [$start, $end])
                      ->orWhere(function ($q2) use ($start, $end) {
                          $q2->where('start_datetime', '<=', $start)
                             ->where('end_datetime', '>=', $end);
                      });
                })
                ->exists();

            if ($conflict) {
                return response()->json([
                    'message' => 'Booking conflict detected'
                ], 422);
            }

            $data['start_datetime'] = $start;
            $data['end_datetime'] = $end;
        }

        $booking->update($data);

        return response()->json([
            'message' => 'Booking updated',
            'data' => $booking
        ]);
    }

    // ✅ DELETE
    public function destroy($id)
    {
        Booking::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Booking deleted'
        ]);
    }

    // ✅ ADD PAYMENT
    public function addPayment(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->paid >= $booking->amount) {
            return response()->json([
                'message' => 'Booking is already fully paid'
            ], 400);
        }

        $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);

        $newPaid = $booking->paid + $request->amount;

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