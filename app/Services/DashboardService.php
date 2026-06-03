<?php

namespace App\Services;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

final class DashboardService
{
    /**
     * @param  array{court?: string, sport?: string, date_from?: string, date_to?: string}  $filters
     * @return array<string, mixed>
     */
    public function stats(array $filters): array
    {
        $today = Carbon::today()->toDateString();
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();

        $baseQuery = Booking::query();

        if (! empty($filters['court'])) {
            $baseQuery->where('court', $filters['court']);
        }
        if (! empty($filters['sport'])) {
            $baseQuery->where('sport', $filters['sport']);
        }
        if (! empty($filters['date_from']) && ! empty($filters['date_to'])) {
            $baseQuery->whereBetween('booking_date', [$filters['date_from'], $filters['date_to']]);
        }

        $bookingsToday = (clone $baseQuery)->where('booking_date', $today)->count();
        $bookingsMonth = (clone $baseQuery)->where('booking_date', '>=', $startOfMonth)->count();

        $incomeToday = (clone $baseQuery)->where('booking_date', $today)->where('status', 'Paid')->sum('price');
        $incomeMonth = (clone $baseQuery)->where('booking_date', '>=', $startOfMonth)->where('status', 'Paid')->sum('price');

        $cricketBookings = (clone $baseQuery)->where('sport', 'Cricket')->count();
        $footballBookings = (clone $baseQuery)->where('sport', 'Football')->count();

        $sevenDaysAgo = Carbon::now()->subDays(6)->toDateString();
        $trends = (clone $baseQuery)->select(DB::raw('DATE(booking_date) as date'), DB::raw('count(*) as total'))
            ->where('booking_date', '>=', $sevenDaysAgo)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $monthlyRevenue = (clone $baseQuery)
            ->select(
                DB::raw('FLOOR((DAY(booking_date) - 1) / 7) + 1 as week'),
                DB::raw("SUM(CASE WHEN status = 'Paid' THEN price ELSE 0 END) as total")
            )
            ->where('booking_date', '>=', $startOfMonth)
            ->groupBy('week')
            ->orderBy('week')
            ->get();

        return [
            'bookings' => [
                'today' => $bookingsToday,
                'monthly' => $bookingsMonth,
            ],
            'income' => [
                'today' => $incomeToday,
                'monthly' => $incomeMonth,
            ],
            'sports' => [
                'cricket' => $cricketBookings,
                'football' => $footballBookings,
            ],
            'trends' => $trends,
            'revenue_trends' => $monthlyRevenue,
        ];
    }
}
