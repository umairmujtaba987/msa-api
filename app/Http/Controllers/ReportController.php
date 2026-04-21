<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function exportBookings(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'court_id' => 'nullable|in:A,B',
            'status' => 'nullable|in:Pending,Confirmed,Paid,Cancelled',
            'sport' => 'nullable|in:Cricket,Football',
        ]);

        $query = Booking::query()->orderBy('booking_date')->orderBy('start_time');

        if (!empty($validated['start_date'])) {
            $query->whereDate('booking_date', '>=', $validated['start_date']);
        }
        if (!empty($validated['end_date'])) {
            $query->whereDate('booking_date', '<=', $validated['end_date']);
        }
        if (!empty($validated['court_id'])) {
            $query->where('court', $validated['court_id']);
        }
        if (!empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }
        if (!empty($validated['sport'])) {
            $query->where('sport', $validated['sport']);
        }

        $bookings = $query->get();
        $totalRevenue = $bookings->where('status', 'Paid')->sum('price');

        $pdf = Pdf::loadView('reports.bookings', [
            'bookings' => $bookings,
            'filters' => $validated,
            'generatedAt' => now(),
            'totalBookings' => $bookings->count(),
            'totalRevenue' => $totalRevenue,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('booking-report-' . now()->format('Ymd-His') . '.pdf');
    }
}
