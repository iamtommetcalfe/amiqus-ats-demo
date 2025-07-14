<?php

namespace App\Services\Interfaces;

use App\Models\AmiqusApiLog;
use GuzzleHttp\Psr7\Response;
use Illuminate\Database\Eloquent\Model;

interface AmiqusApiLogServiceInterface
{
    /**
     * Log an API request and response.
     *
     * @param  Model  $loggable  The model that triggered the API request
     * @param  string  $method  The HTTP method
     * @param  string  $url  The URL
     * @param  array  $requestHeaders  The request headers
     * @param  array|null  $requestBody  The request body
     * @param  Response|null  $response  The response
     * @param  float  $duration  The request duration in seconds
     * @param  string|null  $error  Any error that occurred
     */
    public function log(
        Model $loggable,
        string $method,
        string $url,
        array $requestHeaders,
        ?array $requestBody,
        ?Response $response,
        float $duration,
        ?string $error = null
    ): AmiqusApiLog;
}
