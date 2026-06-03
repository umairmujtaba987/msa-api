<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Collection;

final class SettingService
{
    /**
     * @return Collection<string, mixed>
     */
    public function allKeyed(): Collection
    {
        return Setting::query()->pluck('value', 'key');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return Collection<string, mixed>
     */
    public function upsertMany(array $data): Collection
    {
        foreach ($data as $key => $value) {
            Setting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return $this->allKeyed();
    }
}
