<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Models\Booking;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final class BookingService
{
    public function __construct(
        private readonly BookingConfigService $bookingConfig,
        private readonly BookingPricingService $bookingPricing,
    ) {
    }

    public function paginateFiltered(array $filters, int $perPage): LengthAwarePaginator
    {
        $query = $this->filteredQuery($filters);

        return $query->orderBy('booking_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate($perPage);
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    public function create(array $validated): Booking
    {  
        return Booking::query()->create($validated);
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    public function update(Booking $booking, array $validated): Booking
    {
        $priceContext = array_merge($booking->toArray(), $validated); 

        $booking->update($validated);

        return $booking->fresh();
    }

    public function delete(Booking $booking): void
    {
        $booking->delete();
    }

    public function markPaid(Booking $booking): Booking
    {
        $booking->update(['status' => BookingStatus::Paid->value]);

        return $booking->fresh();
    }

    public function confirm(Booking $booking): Booking
    {
        $booking->update(['status' => BookingStatus::Confirmed->value]);

        return $booking->fresh();
    }

    public function cancel(Booking $booking): Booking
    {
        $booking->update(['status' => BookingStatus::Cancelled->value]);

        return $booking->fresh();
    }

    public function allowedSportsRule(): string
    {
        return 'in:' . implode(',', $this->bookingConfig->allowedSportKeys());
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    private function filteredQuery(array $filters): Builder
    {
        $query = Booking::query();

        if (! empty($filters['court'])) {
            $query->where('court', $filters['court']);
        }
        if (! empty($filters['sport'])) {
            $query->where('sport', $filters['sport']);
        }
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (! empty($filters['date_from'])) {
            $query->where('booking_date', '>=', $filters['date_from']);
        }
        if (! empty($filters['date_to'])) {
            $query->where('booking_date', '<=', $filters['date_to']);
        }
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('customer_name', 'like', '%' . $search . '%')
                    ->orWhere('phone_number', 'like', '%' . $search . '%');
            });
        }

        return $query;
    }
}
