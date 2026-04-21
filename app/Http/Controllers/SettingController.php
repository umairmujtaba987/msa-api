<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Http\Requests\UpsertSettingsRequest;

class SettingController extends Controller
{
    // GET /api/settings
    public function index()
    {
        $settings = Setting::pluck('value', 'key');
        return response()->json([
            'success' => true,
            'data' => $settings,
        ]);
    }

    // PUT /api/settings
    public function update(UpsertSettingsRequest $request)
    {
        $data = $request->validated();

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully',
            'data' => Setting::pluck('value', 'key'),
        ]);
    }
}
