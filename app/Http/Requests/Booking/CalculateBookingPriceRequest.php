<?php

namespace App\Http\Requests\Booking;

use App\Enums\Court;
use App\Enums\Sport;
use Illuminate\Foundation\Http\FormRequest;

class CalculateBookingPriceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'court' => ['required', Court::validationRule()],
            'sport' => ['required', Sport::validationRule()],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
        ];
    }
}
