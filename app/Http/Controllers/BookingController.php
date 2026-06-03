<?php

namespace App\Http\Controllers;

use App\Http\Requests\Booking\CalculateBookingPriceRequest;
use App\Http\Requests\Booking\IndexBookingRequest;
use App\Http\Requests\Booking\StoreBookingRequest;
use App\Http\Requests\Booking\UpdateBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Services\BookingConfigService;
use App\Services\BookingPricingService;
use App\Services\BookingService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(
        private readonly BookingService $bookingService,
        private readonly BookingConfigService $bookingConfig,
        private readonly BookingPricingService $bookingPricing,
    ) {
    }

    public function config(): JsonResponse
    {
        return ApiResponse::success(
            $this->bookingConfig->buildConfig(),
            'Booking configuration loaded successfully.',
        );
    }

    public function calculatePrice(CalculateBookingPriceRequest $request): JsonResponse
    {
        $price = $this->bookingPricing->calculateForPayload($request->validated());

        return ApiResponse::success(
            ['price' => $price],
            'Price calculated successfully.',
        );
    }

    public function index(IndexBookingRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $perPage = (int) ($filters['per_page'] ?? 10);

        $paginator = $this->bookingService->paginateFiltered($filters, $perPage);

        $paginator->through(fn (Booking $booking) => (new BookingResource($booking))->resolve($request));

        return response()->json($paginator);
    }

    public function show(Request $request, Booking $booking): JsonResponse
    {
        return response()->json((new BookingResource($booking))->resolve($request));
    }

    public function store(StoreBookingRequest $request): JsonResponse
    {
        $booking = $this->bookingService->create($request->validated());
        return response()->json((new BookingResource($booking))->resolve($request), 201);
    }

    public function update(UpdateBookingRequest $request, Booking $booking): JsonResponse
    {
        $booking = $this->bookingService->update($booking, $request->validated());

        return response()->json((new BookingResource($booking))->resolve($request));
    }

    public function destroy(Booking $booking): JsonResponse
    {
        $this->bookingService->delete($booking);

        return response()->json(['message' => 'Booking deleted']);
    }

    public function markPaid(Booking $booking): JsonResponse
    {
        $booking = $this->bookingService->markPaid($booking);

        return response()->json((new BookingResource($booking))->resolve(request()));
    }

    public function confirm(Booking $booking): JsonResponse
    {
        $booking = $this->bookingService->confirm($booking);

        return response()->json((new BookingResource($booking))->resolve(request()));
    }

    public function cancel(Booking $booking): JsonResponse
    {
        $booking = $this->bookingService->cancel($booking);

        return response()->json((new BookingResource($booking))->resolve(request()));
    }
}
