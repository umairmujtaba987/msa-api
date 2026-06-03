<?php

namespace App\Http\Controllers;

use App\Http\Requests\Report\ExportBookingsReportRequest;
use App\Services\ReportService;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    public function __construct(
        private readonly ReportService $reportService,
    ) {
    }

    public function exportBookings(ExportBookingsReportRequest $request): Response
    {
        return $this->reportService->downloadBookingsPdf($request->validated());
    }
}
