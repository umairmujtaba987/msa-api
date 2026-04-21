<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    private function getSettingsMap(): array
    {
        return Setting::pluck('value', 'key')->toArray();
    }

    private function buildBookingConfig(): array
    {
        $settings = $this->getSettingsMap();

        $allSports = ['Cricket', 'Football'];
        $courts = [
            [
                'id' => 'A',
                'label' => 'Court A',
                'is_active' => (bool) ($settings['court_a_status'] ?? true),
                'configured_sport' => $settings['court_a_sport'] ?? 'Cricket',
            ],
            [
                'id' => 'B',
                'label' => 'Court B',
                'is_active' => (bool) ($settings['court_b_status'] ?? true),
                'configured_sport' => $settings['court_b_sport'] ?? 'Football',
            ],
        ];

        $activeCourts = collect($courts)
            ->filter(fn ($court) => $court['is_active'])
            ->map(function ($court) use ($allSports) {
                $allowedSports = $court['configured_sport'] === 'Multi'
                    ? $allSports
                    : [$court['configured_sport']];

                return [
                    ...$court,
                    'allowed_sports' => $allowedSports,
                    'default_sport' => $allowedSports[0] ?? null,
                ];
            })
            ->values()
            ->all();

        return [
            'courts' => $activeCourts,
            'pricing' => [
                'Cricket' => (float) ($settings['cricket_price'] ?? 0),
                'Football' => (float) ($settings['football_price'] ?? 0),
            ],
        ];
    }

    private function calculatePriceForPayload(array $payload): float
    {
        $config = $this->buildBookingConfig();
        $hourlyRate = (float) ($config['pricing'][$payload['sport']] ?? 0);

        $start = Carbon::createFromFormat('H:i', substr($payload['start_time'], 0, 5));
        $endTimeRaw = !empty($payload['end_time']) ? substr($payload['end_time'], 0, 5) : $start->copy()->addHour()->format('H:i');
        $end = Carbon::createFromFormat('H:i', $endTimeRaw);

        if ($end->lessThanOrEqualTo($start)) {
            return $hourlyRate;
        }

        $minutes = $start->diffInMinutes($end);
        $hours = max($minutes / 60, 1);

        return round($hourlyRate * $hours, 2);
    }

    public function config()
    {
        return response()->json([
            'success' => true,
            'data' => $this->buildBookingConfig(),
        ]);
    }

    public function calculatePrice(Request $request)
    {
        $validated = $request->validate([
            'court' => 'required|in:A,B',
            'sport' => 'required|in:Cricket,Football',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
        ]);

        $price = $this->calculatePriceForPayload($validated);

        return response()->json([
            'success' => true,
            'data' => [
                'price' => $price,
            ],
        ]);
    }

    // GET /api/bookings
    public function index(Request $request)
    {
        $query = Booking::query()->orderBy('booking_date', 'desc')->orderBy('start_time', 'desc');

        if ($request->has('court')) {
            $query->where('court', $request->court);
        }
        if ($request->has('sport')) {
            $query->where('sport', $request->sport);
        }
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('date_from')) {
            $query->where('booking_date', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->where('booking_date', '<=', $request->date_to);
        }
        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('customer_name', 'like', '%' . $request->search . '%')
                  ->orWhere('phone_number', 'like', '%' . $request->search . '%');
            });
        }

        return $query->paginate($request->per_page ?? 10);
    }

    // POST /api/bookings
    public function store(Request $request)
    {
        $allowedSports = array_keys($this->buildBookingConfig()['pricing']);
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'court' => 'required|in:A,B',
            'sport' => 'required|in:' . implode(',', $allowedSports),
            'booking_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'status' => 'in:Pending,Confirmed,Paid,Cancelled',
            'notes' => 'nullable|string'
        ]);

        $validated['price'] = $this->calculatePriceForPayload($validated);
        $booking = Booking::create($validated);
        return response()->json($booking, 201);
    }

    // PUT /api/bookings/{id}
    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $allowedSports = array_keys($this->buildBookingConfig()['pricing']);
        $validated = $request->validate([
            'customer_name' => 'string|max:255',
            'phone_number' => 'string|max:20',
            'court' => 'in:A,B',
            'sport' => 'in:' . implode(',', $allowedSports),
            'booking_date' => 'date',
            'start_time' => 'date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'status' => 'in:Pending,Confirmed,Paid,Cancelled',
            'notes' => 'nullable|string'
        ]);

        $priceContext = array_merge($booking->toArray(), $validated);
        if (!empty($priceContext['start_time']) && !empty($priceContext['sport'])) {
            $validated['price'] = $this->calculatePriceForPayload($priceContext);
        }

        $booking->update($validated);
        return response()->json($booking);
    }

    // DELETE /api/bookings/{id}
    public function destroy($id)
    {
        Booking::findOrFail($id)->delete();
        return response()->json(['message' => 'Booking deleted']);
    }

    // Helper: Mark Paid
    public function markPaid($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => 'Paid']);
        return response()->json($booking);
    }

    // Helper: Confirm
    public function confirm($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => 'Confirmed']);
        return response()->json($booking);
    }

    // Helper: Cancel
    public function cancel($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => 'Cancelled']);
        return response()->json($booking);
    }
}
