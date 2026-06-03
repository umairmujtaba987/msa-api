<?php

namespace App\Services;

use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Collection;

final class ReportService
{
    /**
     * @param  array{start_date?: string|null, end_date?: string|null, court_id?: string|null, status?: string|null, sport?: string|null}  $filters
     * @return Collection<int, Booking>
     */
    public function bookingsForExport(array $filters): Collection
    {
        return $this->filteredBookingsQuery($filters)->get();
    }

    /**
     * @param  array{start_date?: string|null, end_date?: string|null, court_id?: string|null, status?: string|null, sport?: string|null}  $filters
     */
    public function downloadBookingsPdf(array $filters): Response
    {
        $bookings = $this->bookingsForExport($filters);
        $totalRevenue = $bookings->where('status', 'Paid')->sum('price');

        $pdf = Pdf::loadView('reports.bookings', [
            'bookings' => $bookings,
            'filters' => $filters,
            'generatedAt' => now(),
            'totalBookings' => $bookings->count(),
            'totalRevenue' => $totalRevenue,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('booking-report-' . now()->format('Ymd-His') . '.pdf');
    }

    /**
     * @param  array{start_date?: string|null, end_date?: string|null, court_id?: string|null, status?: string|null, sport?: string|null}  $filters
     */
    private function filteredBookingsQuery(array $filters): Builder
    {
        $query = Booking::query()->orderBy('booking_date')->orderBy('start_time');

        if (! empty($filters['start_date'])) {
            $query->whereDate('booking_date', '>=', $filters['start_date']);
        }
        if (! empty($filters['end_date'])) {
            $query->whereDate('booking_date', '<=', $filters['end_date']);
        }
        if (! empty($filters['court_id'])) {
            $query->where('court', $filters['court_id']);
        }
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (! empty($filters['sport'])) {
            $query->where('sport', $filters['sport']);
        }

        return $query;
    }
}
