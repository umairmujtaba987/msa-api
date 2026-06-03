<?php

namespace App\Http\Requests\Booking;

use App\Enums\BookingStatus;
use App\Enums\Court;
use App\Enums\Sport;
use Illuminate\Foundation\Http\FormRequest;

class IndexBookingRequest extends FormRequest
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
            'status' => ['sometimes', BookingStatus::validationRule()],
            'date_from' => ['sometimes', 'date'],
            'date_to' => ['sometimes', 'date', 'after_or_equal:date_from'],
            'search' => ['sometimes', 'string', 'max:255'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'page' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}
