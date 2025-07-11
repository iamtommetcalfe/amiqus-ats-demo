<?php

namespace App\Services\Interfaces;

interface ApiResponseServiceInterface
{
    /**
     * Create a success response.
     *
     * @param  mixed  $data
     * @param  string  $message
     * @param  int  $statusCode
     * @return array
     */
    public function success($data = null, string $message = 'Operation successful', int $statusCode = 200): array;

    /**
     * Create an error response.
     *
     * @param  string  $message
     * @param  mixed  $errors
     * @param  int  $statusCode
     * @return array
     */
    public function error(string $message = 'An error occurred', $errors = null, int $statusCode = 400): array;

    /**
     * Create a not found response.
     *
     * @param  string  $message
     * @param  int  $statusCode
     * @return array
     */
    public function notFound(string $message = 'Resource not found', int $statusCode = 404): array;

    /**
     * Create an unauthorized response.
     *
     * @param  string  $message
     * @param  int  $statusCode
     * @return array
     */
    public function unauthorized(string $message = 'Unauthorized', int $statusCode = 401): array;

    /**
     * Create a validation error response.
     *
     * @param  mixed  $errors
     * @param  string  $message
     * @param  int  $statusCode
     * @return array
     */
    public function validationError($errors, string $message = 'Validation failed', int $statusCode = 422): array;

    /**
     * Create a server error response.
     *
     * @param  string  $message
     * @param  mixed  $errors
     * @param  int  $statusCode
     * @return array
     */
    public function serverError(string $message = 'Server error', $errors = null, int $statusCode = 500): array;

    /**
     * Send the response as JSON.
     *
     * @param  array  $response
     * @return \Illuminate\Http\JsonResponse
     */
    public function send(array $response);
}
