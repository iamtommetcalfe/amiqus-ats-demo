<?php

namespace App\Services;

use App\Models\AmiqusApiLog;
use App\Services\Interfaces\AmiqusApiLogServiceInterface;
use GuzzleHttp\Psr7\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class AmiqusApiLogService implements AmiqusApiLogServiceInterface
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
    ): AmiqusApiLog {
        try {
            // Filter out sensitive information from headers
            $filteredRequestHeaders = $this->filterSensitiveHeaders($requestHeaders);
            $filteredResponseHeaders = $response ? $this->filterSensitiveHeaders($response->getHeaders()) : null;

            // Get response body
            $responseBody = null;
            $responseStatus = null;
            if ($response) {
                $responseStatus = $response->getStatusCode();
                $responseBody = (string) $response->getBody();

                // Try to parse JSON response
                if ($responseBody) {
                    try {
                        $responseBody = json_decode($responseBody, true);
                    } catch (\Exception $e) {
                        // If it's not valid JSON, keep it as a string
                    }
                }
            }

            // Create the log entry
            return AmiqusApiLog::create([
                'loggable_type' => get_class($loggable),
                'loggable_id' => $loggable->id,
                'method' => $method,
                'url' => $url,
                'request_headers' => $filteredRequestHeaders,
                'request_body' => $requestBody,
                'response_status' => $responseStatus,
                'response_headers' => $filteredResponseHeaders,
                'response_body' => $responseBody,
                'duration' => $duration,
                'error' => $error,
            ]);
        } catch (\Exception $e) {
            // Log the error but don't fail the request
            Log::error('Failed to log API request/response: '.$e->getMessage());

            return new AmiqusApiLog;
        }
    }

    /**
     * Filter out sensitive information from headers.
     */
    private function filterSensitiveHeaders(array $headers): array
    {
        $sensitiveHeaders = [
            'authorization',
            'cookie',
            'set-cookie',
        ];

        $filteredHeaders = [];
        foreach ($headers as $name => $value) {
            $lowerName = strtolower($name);
            if (in_array($lowerName, $sensitiveHeaders)) {
                // Mask the value
                $filteredHeaders[$name] = ['[REDACTED]'];
            } else {
                $filteredHeaders[$name] = $value;
            }
        }

        return $filteredHeaders;
    }
}
