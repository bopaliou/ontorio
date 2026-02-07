<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    /**
     * Succès (200 OK)
     */
    public static function success(mixed $data = null, string $message = 'Opération réussie', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Erreur (4xx ou 5xx)
     */
    public static function error(string $message = 'Une erreur est survenue', int $code = 400, mixed $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }

    /**
     * Ressources créées (201 Created)
     */
    public static function created(mixed $data = null, string $message = 'Ressource créée avec succès'): JsonResponse
    {
        return self::success($data, $message, 201);
    }

    /**
     * Non trouvé (404 Not Found)
     */
    public static function notFound(string $message = 'Ressource non trouvée'): JsonResponse
    {
        return self::error($message, 404);
    }

    /**
     * Conflit / Garde métier (409 Conflict)
     */
    public static function conflict(string $message): JsonResponse
    {
        return self::error($message, 409);
    }
}
