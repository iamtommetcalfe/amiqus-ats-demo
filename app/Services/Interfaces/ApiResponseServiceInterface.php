<?php

namespace App\Services\Interfaces;

interface ApiResponseServiceInterface
{
    /**
     * Create a success response.
     *
     * @param  mixed  $data
     */
    public function success($data = null, string $message = 'Operation successful', int $statusCode = 200): array;

    /**
     * Create an error response.
     *
     * @param  mixed  $errors
     */
    public function error(string $message = 'An error occurred', $errors = null, int $statusCode = 400): array;

    /**
     * Create a not found response.
     */
    public function notFound(string $message = 'Resource not found', int $statusCode = 404): array;

    /**
     * Create an unauthorized response.
     */
    public function unauthorized(string $message = 'Unauthorized', int $statusCode = 401): array;

    /**
     * Create a validation error response.
     *
     * @param  mixed  $errors
     */
    public function validationError($errors, string $message = 'Validation failed', int $statusCode = 422): array;

    /**
     * Create a server error response.
     *
     * @param  mixed  $errors
     */
    public function serverError(string $message = 'Server error', $errors = null, int $statusCode = 500): array;

    /**
     * Send the response as JSON.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function send(array $response);
}
