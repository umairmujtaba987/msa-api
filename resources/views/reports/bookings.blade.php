<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #1f2937;
        }
        .header {
            margin-bottom: 14px;
        }
        .title {
            font-size: 22px;
            font-weight: bold;
            margin: 0 0 8px 0;
        }
        .meta, .filters {
            margin: 0;
            line-height: 1.5;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 8px;
            text-align: left;
        }
        th {
            background: #f3f4f6;
            font-weight: bold;
        }
        .status-paid { color: #065f46; }
        .status-cancelled { color: #991b1b; }
        .footer {
            margin-top: 16px;
            border-top: 1px solid #d1d5db;
            padding-top: 8px;
            display: flex;
            justify-content: space-between;
        }
        .empty {
            margin-top: 16px;
            padding: 12px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    <div class="header">
        <p class="title">Booking Report</p>
        <p class="meta">Generated: {{ $generatedAt->format('Y-m-d H:i') }}</p>
        <p class="filters">
            Filters:
            Date {{ $filters['start_date'] ?? 'Any' }} to {{ $filters['end_date'] ?? 'Any' }},
            Court {{ $filters['court_id'] ?? 'Any' }},
            Status {{ $filters['status'] ?? 'Any' }},
            Sport {{ $filters['sport'] ?? 'Any' }}
        </p>
    </div>

    @if($bookings->isEmpty())
        <div class="empty">No bookings found for selected filters.</div>
    @else
        <table>
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>User Name</th>
                    <th>Court</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Price</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bookings as $booking)
                    <tr>
                        <td>#{{ $booking->id }}</td>
                        <td>{{ $booking->customer_name }}</td>
                        <td>Court {{ $booking->court }}</td>
                        <td>{{ $booking->booking_date }}</td>
                        <td>{{ $booking->start_time }}{{ $booking->end_time ? ' - ' . $booking->end_time : '' }}</td>
                        <td>PKR {{ number_format((float) $booking->price, 2) }}</td>
                        <td class="{{ $booking->status === 'Paid' ? 'status-paid' : ($booking->status === 'Cancelled' ? 'status-cancelled' : '') }}">
                            {{ $booking->status }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        <span>Total bookings: {{ $totalBookings }}</span>
        <span>Total revenue (Paid): PKR {{ number_format((float) $totalRevenue, 2) }}</span>
    </div>
</body>
</html>
