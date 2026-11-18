<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class BaseApiController extends Controller
{
    /**
     * Return standardized success JSON response
     */
    public function sendResponse(mixed $data = null, ?string $message = null, int $code = 200, array $meta = []): JsonResponse
    {
        $response = [
            'success' => true,
        ];

        if ($message !== null) {
            $response['message'] = $message;
        }

        if ($data !== null) {
            $response['data'] = $data;
        }

        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $code);
    }

    /**
     * Return standardized error JSON response
     */
    public function sendError(string $message, mixed $errors = [], int $code = 400): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Return standardized paginated JSON response
     */
    public function sendPaginated(mixed $paginator, ?string $message = null, int $code = 200, array $extra = []): JsonResponse
    {
        $underlying = $paginator instanceof \Illuminate\Http\Resources\Json\ResourceCollection 
            ? $paginator->resource 
            : $paginator;

        $paginationMeta = [
            'current_page' => $underlying->currentPage(),
            'last_page' => $underlying->lastPage(),
            'per_page' => $underlying->perPage(),
            'total' => $underlying->total(),
            'from' => $underlying->firstItem(),
            'to' => $underlying->lastItem(),
        ];

        if (!empty($extra)) {
            $paginationMeta = array_merge($paginationMeta, $extra);
        }

        $items = $paginator instanceof \Illuminate\Http\Resources\Json\ResourceCollection
            ? $paginator->resolve()
            : $underlying->items();

        return $this->sendResponse(
            data: $items,
            message: $message,
            code: $code,
            meta: $paginationMeta
        );
    }
}
