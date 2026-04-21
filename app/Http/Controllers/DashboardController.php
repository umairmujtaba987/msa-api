<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getStats(Request $request)
    {
        $today = Carbon::today()->toDateString();
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
        
        $baseQuery = Booking::query();

        // Apply Global Dashboard Filters
        if ($request->has('court')) {
            $baseQuery->where('court', $request->court);
        }
        if ($request->has('sport')) {
            $baseQuery->where('sport', $request->sport);
        }
        if ($request->has('date_from') && $request->has('date_to')) {
            $baseQuery->whereBetween('booking_date', [$request->date_from, $request->date_to]);
        }
        
        // Total Bookings
        $bookingsToday = (clone $baseQuery)->where('booking_date', $today)->count();
        $bookingsMonth = (clone $baseQuery)->where('booking_date', '>=', $startOfMonth)->count();

        // Total Income (Paid out)
        $incomeToday = (clone $baseQuery)->where('booking_date', $today)->where('status', 'Paid')->sum('price');
        $incomeMonth = (clone $baseQuery)->where('booking_date', '>=', $startOfMonth)->where('status', 'Paid')->sum('price');

        // Sport Stats
        $cricketBookings = (clone $baseQuery)->where('sport', 'Cricket')->count();
        $footballBookings = (clone $baseQuery)->where('sport', 'Football')->count();

        // Daily Trends (last 7 days grouped)
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

        return response()->json([
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
                'football' => $footballBookings
            ],
            'trends' => $trends,
            'revenue_trends' => $monthlyRevenue,
        ]);
    }
}
