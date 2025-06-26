<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;

class ApiResponseFormatter
{
    public function success(mixed $data = null, int $statusCode = 200): JsonResponse
    {
        return new JsonResponse([
            'success' => true,
            'data' => $data,
            'errors' => null,
        ], $statusCode);
    }

    public function error(array|string $errors, int $statusCode = 400): JsonResponse
    {
        return new JsonResponse([
            'success' => false,
            'data' => null,
            'errors' => is_array($errors) ? $errors : [$errors],
        ], $statusCode);
    }
}
