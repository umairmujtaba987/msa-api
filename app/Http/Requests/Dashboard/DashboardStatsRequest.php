<?php

namespace App\Http\Requests\Dashboard;

use App\Enums\Court;
use App\Enums\Sport;
use Illuminate\Foundation\Http\FormRequest;

class DashboardStatsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'court' => ['sometimes', Court::validationRule()],
            'sport' => ['sometimes', Sport::validationRule()],
            'date_from' => ['sometimes', 'date'],
            'date_to' => ['sometimes', 'date', 'after_or_equal:date_from'],
        ];
    }
}
