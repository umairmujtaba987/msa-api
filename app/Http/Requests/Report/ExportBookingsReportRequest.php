<?php

namespace App\Http\Requests\Report;

use App\Enums\BookingStatus;
use App\Enums\Court;
use App\Enums\Sport;
use Illuminate\Foundation\Http\FormRequest;

class ExportBookingsReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'court_id' => ['nullable', Court::validationRule()],
            'status' => ['nullable', BookingStatus::validationRule()],
            'sport' => ['nullable', Sport::validationRule()],
        ];
    }
}
