<?php

namespace App\Services;

use Carbon\Carbon;

final class BookingPricingService
{
    public function __construct(
        private readonly BookingConfigService $bookingConfig,
    ) {
    }

    /**
     * @param  array{court: string, sport: string, start_time: string, end_time?: string|null}  $payload
     */
    public function calculateForPayload(array $payload): float
    {
        $config = $this->bookingConfig->buildConfig();
        $hourlyRate = (float) ($config['pricing'][$payload['sport']] ?? 0);

        $start = Carbon::createFromFormat('H:i', substr($payload['start_time'], 0, 5));
        $endTimeRaw = ! empty($payload['end_time']) ? substr($payload['end_time'], 0, 5) : $start->copy()->addHour()->format('H:i');
        $end = Carbon::createFromFormat('H:i', $endTimeRaw);

        if ($end->lessThanOrEqualTo($start)) {
            return $hourlyRate;
        }

        $minutes = $start->diffInMinutes($end);
        $hours = max($minutes / 60, 1);

        return round($hourlyRate * $hours, 2);
    }
}
