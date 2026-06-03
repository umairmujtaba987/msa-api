<?php

namespace App\Services;

use App\Models\Setting;

/**
 * Builds booking UI config (courts, pricing) from persisted settings.
 */
final class BookingConfigService
{
    /**
     * @return array{courts: list<array<string, mixed>>, pricing: array<string, float>}
     */
    public function buildConfig(): array
    {
        $settings = Setting::query()->pluck('value', 'key')->toArray();

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

    /**
     * @return list<string> Sport keys that have a configured hourly rate.
     */
    public function allowedSportKeys(): array
    {
        return array_keys($this->buildConfig()['pricing']);
    }
}
