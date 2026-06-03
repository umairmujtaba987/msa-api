<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpsertSettingsRequest;
use App\Services\SettingService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class SettingController extends Controller
{
    public function __construct(
        private readonly SettingService $settingService,
    ) {
    }

    public function index(): JsonResponse
    {
        return ApiResponse::success(
            $this->settingService->allKeyed(),
            'Settings fetched successfully.',
        );
    }

    public function update(UpsertSettingsRequest $request): JsonResponse
    {
        $settings = $this->settingService->upsertMany($request->validated());

        return ApiResponse::success(
            $settings,
            'Settings updated successfully.',
        );
    }
}
