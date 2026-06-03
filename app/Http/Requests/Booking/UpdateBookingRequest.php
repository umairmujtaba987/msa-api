<?php

namespace App\Http\Requests\Booking;

use App\Enums\BookingStatus;
use App\Enums\Court;
use App\Services\BookingService;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $sportRule = app(BookingService::class)->allowedSportsRule();

        return [
            'customer_name' => ['sometimes', 'string', 'max:255'],
            'phone_number' => ['sometimes', 'string', 'max:20'],
            'court' => ['sometimes', Court::validationRule()],
            'sport' => ['sometimes', $sportRule],
            'booking_date' => ['sometimes', 'date'],
            'start_time' => ['sometimes', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'status' => ['sometimes', BookingStatus::validationRule()],
            'notes' => ['nullable', 'string'],
            'price' => ['required', 'numeric'],
        ];
    }
}
