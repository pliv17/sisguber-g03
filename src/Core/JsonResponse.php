<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Respuestas JSON homogéneas para la API.
 */
final class JsonResponse
{
    /**
     * @param array<string, mixed> $data
     */
    public static function send(int $status, array $payload): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        exit;
    }

    /**
     * Listado paginado homogéneo.
     *
     * @param array<int, array<string, mixed>> $rows
     * @param array<string, int|string>       $meta
     */
    public static function list(array $rows, array $meta, string $message = 'OK'): never
    {
        self::send(200, [
            'ok'      => true,
            'message' => $message,
            'data'    => $rows,
            'meta'    => $meta,
        ]);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function ok(array $data = [], string $message = 'OK'): never
    {
        self::send(200, ['ok' => true, 'message' => $message, 'data' => $data]);
    }

    /**
     * @param array<string, mixed> $row
     */
    public static function item(array $row, string $message = 'OK'): never
    {
        self::send(200, ['ok' => true, 'message' => $message, 'data' => $row]);
    }

    public static function created(array $row, string $message = 'Created'): never
    {
        self::send(201, ['ok' => true, 'message' => $message, 'data' => $row]);
    }

    public static function noContent(): never
    {
        http_response_code(204);
        exit;
    }

    /**
     * @param array<string, list<string>>|array<string, string> $errors
     */
    public static function validationError(array $errors, string $message = 'Validation failed'): never
    {
        self::send(422, ['ok' => false, 'message' => $message, 'errors' => $errors]);
    }

    public static function error(int $status, string $message, ?string $code = null): never
    {
        $body = ['ok' => false, 'message' => $message];
        if ($code !== null) {
            $body['code'] = $code;
        }
        self::send($status, $body);
    }

    public static function unauthorized(string $message = 'Unauthorized'): never
    {
        self::error(401, $message, 'UNAUTHORIZED');
    }

    public static function forbidden(string $message = 'Forbidden'): never
    {
        self::error(403, $message, 'FORBIDDEN');
    }

    public static function notFound(string $message = 'Not found'): never
    {
        self::error(404, $message, 'NOT_FOUND');
    }

    public static function conflict(string $message = 'Conflict'): never
    {
        self::error(409, $message, 'CONFLICT');
    }

    public static function badRequest(string $message = 'Bad request'): never
    {
        self::error(400, $message, 'BAD_REQUEST');
    }
}
