<?php

namespace App\Services;

use App\Services\Interfaces\ApiResponseServiceInterface;

class ApiResponseService implements ApiResponseServiceInterface
{
    /**
     * Create a success response.
     *
     * @param  mixed  $data
     * @param  string  $message
     * @param  int  $statusCode
     * @return array
     */
    public function success($data = null, string $message = 'Operation successful', int $statusCode = 200): array
    {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'status_code' => $statusCode
        ];
    }

    /**
     * Create an error response.
     *
     * @param  string  $message
     * @param  mixed  $errors
     * @param  int  $statusCode
     * @return array
     */
    public function error(string $message = 'An error occurred', $errors = null, int $statusCode = 400): array
    {
        return [
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'status_code' => $statusCode
        ];
    }

    /**
     * Create a not found response.
     *
     * @param  string  $message
     * @param  int  $statusCode
     * @return array
     */
    public function notFound(string $message = 'Resource not found', int $statusCode = 404): array
    {
        return $this->error($message, null, $statusCode);
    }

    /**
     * Create an unauthorized response.
     *
     * @param  string  $message
     * @param  int  $statusCode
     * @return array
     */
    public function unauthorized(string $message = 'Unauthorized', int $statusCode = 401): array
    {
        return $this->error($message, null, $statusCode);
    }

    /**
     * Create a validation error response.
     *
     * @param  mixed  $errors
     * @param  string  $message
     * @param  int  $statusCode
     * @return array
     */
    public function validationError($errors, string $message = 'Validation failed', int $statusCode = 422): array
    {
        return $this->error($message, $errors, $statusCode);
    }

    /**
     * Create a server error response.
     *
     * @param  string  $message
     * @param  mixed  $errors
     * @param  int  $statusCode
     * @return array
     */
    public function serverError(string $message = 'Server error', $errors = null, int $statusCode = 500): array
    {
        return $this->error($message, $errors, $statusCode);
    }

    /**
     * Send the response as JSON.
     *
     * @param  array  $response
     * @return \Illuminate\Http\JsonResponse
     */
    public function send(array $response)
    {
        $statusCode = $response['status_code'] ?? 200;

        // Remove status_code from the response
        if (isset($response['status_code'])) {
            unset($response['status_code']);
        }

        return response()->json($response, $statusCode);
    }
}
