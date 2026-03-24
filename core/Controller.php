<?php

namespace Core;

use Throwable;

class Controller
{
    /**
     * Send a JSON response and stop execution.
     */
    protected function jsonResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Read request data from JSON or form-urlencoded bodies.
     */
    protected function getRequestData(): array
    {
        $raw = file_get_contents('php://input');

        if ($raw !== false && trim($raw) !== '') {
            $decoded = json_decode($raw, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        return $_POST;
    }

    protected function successResponse(string $message, array $data = [], int $statusCode = 200): void
    {
        $this->jsonResponse([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    protected function sanitizeString(string $value): string
    {
        return trim(strip_tags($value));
    }

    protected function validationError(array $errors): void
    {
        $this->jsonResponse([
            'success' => false,
            'message' => 'Validation failed.',
            'errors' => $errors,
        ], 422);
    }

    protected function notFound(string $message = 'Resource not found.'): void
    {
        $this->jsonResponse([
            'success' => false,
            'message' => $message,
        ], 404);
    }

    protected function serverError(string $message, ?Throwable $exception = null): void
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (env('APP_DEBUG', 'false') === 'true' && $exception !== null) {
            $response['error'] = $exception->getMessage();
        }

        $this->jsonResponse($response, 500);
    }

    protected function isValidDate(string $date): bool
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return false;
        }

        [$year, $month, $day] = array_map('intval', explode('-', $date));

        return checkdate($month, $day, $year);
    }

    protected function isValidTime(string $time): bool
    {
        return (bool) preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $time);
    }

    protected function isValidPhone(string $phone): bool
    {
        return (bool) preg_match('/^[0-9+\-\s()]{7,20}$/', $phone);
    }
}