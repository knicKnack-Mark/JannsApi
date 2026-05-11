<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use Carbon\Carbon;

class BookingController extends Controller
{
    // ✅ GET BOOKINGS
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 1000);
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

        // 💰 PAYMENT STATUS FILTER
        if ($status === 'paid') {

            $query->whereColumn(
                'paid',
                '>=',
                'amount'
            );

        } elseif ($status === 'unpaid') {

            $query->whereColumn(
                'paid',
                '<',
                'amount'
            );
        }

        // 📅 DATE FILTER
        if ($date) {

            $query->where(function ($q) use ($date) {

                $q->whereDate(
                        'start_datetime',
                        $date
                    )

                  ->orWhereDate(
                        'end_datetime',
                        $date
                    )

                  ->orWhere(function ($q2) use ($date) {

                      $q2->where(
                            'start_datetime',
                            '<=',
                            $date . ' 23:59:59'
                        )

                        ->where(
                            'end_datetime',
                            '>=',
                            $date . ' 00:00:00'
                        );
                  });
            });
        }

        return response()->json(
            $query->latest()->paginate($perPage)
        );
    }

    // ✅ STORE BOOKING
    public function store(Request $request)
    {
        $validated = $request->validate([

            'name' => 'required|string',

            'address' => 'required|string',

            'cabin' => 'required|string',

            'start_datetime' => 'required|date',

            'end_datetime' => 'required|date',

            'guests' => 'required|integer|min:1',

            // ✅ EXTRA PAX
            'max_pax' => 'required|integer|min:1',

            'extra_pax_rate' =>
                'nullable|numeric|min:0',

            'extra_pax_discount' =>
                'nullable|numeric|min:0',

            // ✅ BASE PRICE
            'base_amount' =>
                'required|numeric|min:0',

            'videoke' => 'required|boolean',

            'paid' => 'nullable|numeric|min:0',

            'status' => 'nullable|string',
        ]);

        $start = Carbon::parse(
            $validated['start_datetime']
        );

        $end = Carbon::parse(
            $validated['end_datetime']
        );

        // 🔥 HANDLE OVERNIGHT
        if ($end <= $start) {
            $end->addDay();
        }

        // 🚫 OVERLAP CHECK
        $conflict = Booking::where(
                'cabin',
                $validated['cabin']
            )

            // ✅ IGNORE CANCELLED BOOKINGS
            ->where('status', '!=', 'cancelled')

            ->where(function ($q) use ($start, $end) {

                $q->whereBetween(
                        'start_datetime',
                        [$start, $end]
                    )

                  ->orWhereBetween(
                        'end_datetime',
                        [$start, $end]
                    )

                  ->orWhere(function ($q2)
                        use ($start, $end) {

                      $q2->where(
                            'start_datetime',
                            '<=',
                            $start
                        )

                        ->where(
                            'end_datetime',
                            '>=',
                            $end
                        );
                  });
            })

            ->exists();

        if ($conflict) {

            return response()->json([
                'message' =>
                    'Booking conflict: this cabin is already reserved for that time.'
            ], 422);
        }

        // ✅ EXTRA PAX COMPUTATION
        $extraPax = max(
            0,
            $validated['guests']
            - $validated['max_pax']
        );

        $extraPaxRate =
            $validated['extra_pax_rate']
            ?? 100;

        $extraPaxDiscount =
            $validated['extra_pax_discount']
            ?? 0;

        $extraPaxSubtotal =
            $extraPax * $extraPaxRate;

        $extraPaxTotal = max(
            0,
            $extraPaxSubtotal
            - $extraPaxDiscount
        );

        // ✅ FINAL TOTAL
        $totalAmount =
            $validated['base_amount']
            + $extraPaxTotal;

        // ✅ PAYMENT STATUS
        $status = 'unpaid';

        if (
            ($validated['paid'] ?? 0)
            >= $totalAmount
        ) {

            $status = 'paid';

        } elseif (
            ($validated['paid'] ?? 0) > 0
        ) {

            $status = 'partial';
        }

        // ✅ REFERENCE NUMBER
        $referenceNo =
            'BK-'
            . now()->format('Ymd')
            . '-'
            . rand(1000, 9999);

        $booking = Booking::create([

            'name' =>
                $validated['name'],

            'address' =>
                $validated['address'],

            'cabin' =>
                $validated['cabin'],

            'start_datetime' =>
                $start,

            'end_datetime' =>
                $end,

            'guests' =>
                $validated['guests'],

            // ✅ EXTRA PAX
            'max_pax' =>
                $validated['max_pax'],

            'extra_pax' =>
                $extraPax,

            'extra_pax_rate' =>
                $extraPaxRate,

            'extra_pax_discount' =>
                $extraPaxDiscount,

            'extra_pax_total' =>
                $extraPaxTotal,

            // ✅ TOTAL
            'amount' =>
                $totalAmount,

            'videoke' =>
                $validated['videoke'],

            'paid' =>
                $validated['paid'] ?? 0,

            'status' =>
                $status,

            // ✅ REFERENCE
            'reference_no' =>
                $referenceNo,
        ]);

        return response()->json([
            'message' =>
                'Booking created successfully',

            'data' => $booking
        ], 201);
    }

    // ✅ UPDATE BOOKING
    public function update(
        Request $request,
        $id
    ) {

        $booking = Booking::findOrFail($id);

        $data = $request->all();

        // 🔥 HANDLE DATETIME
        if (
            isset($data['start_datetime']) &&
            isset($data['end_datetime'])
        ) {

            $start = Carbon::parse(
                $data['start_datetime']
            );

            $end = Carbon::parse(
                $data['end_datetime']
            );

            if ($end <= $start) {
                $end->addDay();
            }

            // 🚫 OVERLAP CHECK
            $conflict = Booking::where(
                    'cabin',
                    $booking->cabin
                )

                // ✅ IGNORE CURRENT BOOKING
                ->where('id', '!=', $id)

                // ✅ IGNORE CANCELLED BOOKINGS
                ->where('status', '!=', 'cancelled')

                ->where(function ($q)
                    use ($start, $end) {

                    $q->whereBetween(
                            'start_datetime',
                            [$start, $end]
                        )

                      ->orWhereBetween(
                            'end_datetime',
                            [$start, $end]
                        )

                      ->orWhere(function ($q2)
                            use ($start, $end) {

                          $q2->where(
                                'start_datetime',
                                '<=',
                                $start
                            )

                            ->where(
                                'end_datetime',
                                '>=',
                                $end
                            );
                      });
                })

                ->exists();

            if ($conflict) {

                return response()->json([
                    'message' =>
                        'Booking conflict detected'
                ], 422);
            }

            $data['start_datetime'] = $start;

            $data['end_datetime'] = $end;
        }

        // ✅ RECALCULATE EXTRA PAX
        if (
            isset($data['guests']) &&
            isset($data['max_pax'])
        ) {

            $extraPax = max(
                0,
                $data['guests']
                - $data['max_pax']
            );

            $extraPaxRate =
                $data['extra_pax_rate']
                ?? $booking->extra_pax_rate
                ?? 100;

            $extraPaxDiscount =
                $data['extra_pax_discount']
                ?? $booking->extra_pax_discount
                ?? 0;

            $extraPaxTotal = max(
                0,
                ($extraPax * $extraPaxRate)
                - $extraPaxDiscount
            );

            $baseAmount =
                $data['base_amount']
                ?? (
                    $booking->amount
                    - $booking->extra_pax_total
                );

            $data['extra_pax'] =
                $extraPax;

            $data['extra_pax_rate'] =
                $extraPaxRate;

            $data['extra_pax_discount'] =
                $extraPaxDiscount;

            $data['extra_pax_total'] =
                $extraPaxTotal;

            $data['amount'] =
                $baseAmount
                + $extraPaxTotal;
        }

        // ✅ AUTO STATUS
        $paid =
            $data['paid']
            ?? $booking->paid;

        $amount =
            $data['amount']
            ?? $booking->amount;

        if ($paid >= $amount) {

            $data['status'] = 'paid';

        } elseif ($paid > 0) {

            $data['status'] = 'partial';

        } else {

            $data['status'] = 'unpaid';
        }

        $booking->update($data);

        return response()->json([
            'message' =>
                'Booking updated',

            'data' => $booking
        ]);
    }

    // ✅ DELETE
    public function destroy($id)
    {
        Booking::findOrFail($id)->delete();

        return response()->json([
            'message' =>
                'Booking deleted'
        ]);
    }

    // ✅ ADD PAYMENT
    public function addPayment(
        Request $request,
        $id
    ) {

        $booking = Booking::findOrFail($id);

        $request->validate([
            'amount' =>
                'required|numeric|min:1'
        ]);

        $newPaid =
            $booking->paid
            + $request->amount;

        if (
            $newPaid > $booking->amount
        ) {

            return response()->json([
                'message' =>
                    'Payment exceeds remaining balance'
            ], 400);
        }

        $booking->paid = $newPaid;

        // ✅ AUTO STATUS
        if (
            $booking->paid
            >= $booking->amount
        ) {

            $booking->status = 'paid';

        } else {

            $booking->status = 'partial';
        }

        $booking->save();

        return response()->json([
            'message' =>
                'Payment added',

            'data' => $booking
        ]);
    }

    // ✅ CANCEL BOOKING
    public function cancel($id)
    {
        $booking = Booking::findOrFail($id);

        $booking->status = 'cancelled';

        $booking->save();

        return response()->json([
            'message' =>
                'Booking cancelled',

            'data' => $booking
        ]);
    }
}