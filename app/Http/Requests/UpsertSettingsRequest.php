<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpsertSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'court_a_sport' => 'required|string|in:Cricket,Football,Multi',
            'court_b_sport' => 'required|string|in:Cricket,Football,Multi',
            'court_a_status' => 'required|boolean',
            'court_b_status' => 'required|boolean',
            'cricket_price' => 'required|numeric|min:0',
            'football_price' => 'required|numeric|min:0',
            'arena_name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'court_a_sport.required' => 'Court A sport is required.',
            'court_b_sport.required' => 'Court B sport is required.',
            'cricket_price.required' => 'Cricket hourly price is required.',
            'football_price.required' => 'Football hourly price is required.',
            'arena_name.required' => 'Arena name is required.',
            'contact_email.email' => 'Enter a valid contact email.',
        ];
    }
}
