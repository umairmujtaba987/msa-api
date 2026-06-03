<?php

namespace App\Http\Requests\Booking;

use App\Enums\BookingStatus;
use App\Enums\Court;
use App\Services\BookingService;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $sportRule = app(BookingService::class)->allowedSportsRule();

        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:20'],
            'court' => ['required', Court::validationRule()],
            'sport' => ['required', $sportRule],
            'booking_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'status' => ['nullable', BookingStatus::validationRule()],
            'notes' => ['nullable', 'string'],
            'price' => ['required', 'numeric'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'Customer name is required.',
            'phone_number.required' => 'Phone number is required.',
            'court.required' => 'Select a court.',
            'sport.required' => 'Select a sport.',
            'booking_date.required' => 'Booking date is required.',
            'start_time.required' => 'Start time is required.',
        ];
    }
}
