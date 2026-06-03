<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

/**
 * Standard JSON envelope for API responses (new / migrated endpoints).
 * Legacy endpoints (e.g. login, Laravel paginator root) intentionally keep their shape.
 */
final class ApiResponse
{
    public static function success(
        mixed $data = null,
        ?string $message = null,
        int $status = Response::HTTP_OK,
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message ?? 'OK',
            'data' => $data,
            'errors' => null,
        ], $status);
    }

    public static function error(
        string $message,
        mixed $errors = null,
        int $status = Response::HTTP_BAD_REQUEST,
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => $errors,
        ], $status);
    }

    /**
     * Serialize a JsonResource without wrapping it in Laravel's outer "data" only root
     * when you need the envelope above.
     */
    public static function fromResource(JsonResource $resource, ?string $message = null, int $status = Response::HTTP_OK): JsonResponse
    {
        return self::success($resource->resolve(request()), $message, $status);
    }
}
