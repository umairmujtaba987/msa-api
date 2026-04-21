<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpsertSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
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
}
