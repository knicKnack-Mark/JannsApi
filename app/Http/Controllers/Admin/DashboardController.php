namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        $month = $request->month ?? now()->format('Y-m');

        // Get all bookings for that month
        $bookings = Booking::where('date', 'like', "$month%")
            ->get(['date', 'shift']);

        // Group by date
        $grouped = [];

        foreach ($bookings as $b) {
            $grouped[$b->date][] = $b->shift;
        }

        $bookedDays = 0;
        $partialDays = 0;

        foreach ($grouped as $date => $shifts) {
            if (in_array('AM', $shifts) && in_array('PM', $shifts)) {
                $bookedDays++;
            } else {
                $partialDays++;
            }
        }

        // Total days in month
        $carbon = Carbon::createFromFormat('Y-m', $month);
        $totalDays = $carbon->daysInMonth;

        $availableDays = $totalDays - $bookedDays - $partialDays;

        return response()->json([
            'booked_days' => $bookedDays,
            'partial_days' => $partialDays,
            'available_days' => $availableDays,
            'total_days' => $totalDays,
        ]);
    }
}