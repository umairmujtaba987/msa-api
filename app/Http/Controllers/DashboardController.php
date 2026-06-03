<?php

namespace App\Http\Controllers;

use App\Http\Requests\Dashboard\DashboardStatsRequest;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService,
    ) {
    }

    public function getStats(DashboardStatsRequest $request): JsonResponse
    {
        $payload = $this->dashboardService->stats($request->validated());

        return response()->json($payload);
    }
}
