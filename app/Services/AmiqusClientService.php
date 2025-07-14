<?php

namespace App\Services;

use App\Models\Candidate;
use App\Services\Interfaces\AmiqusApiLogServiceInterface;
use App\Services\Interfaces\AmiqusClientServiceInterface;
use App\Services\Interfaces\AmiqusOAuthServiceInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class AmiqusClientService implements AmiqusClientServiceInterface
{
    /**
     * The Amiqus OAuth service instance.
     *
     * @var \App\Services\Interfaces\AmiqusOAuthServiceInterface
     */
    protected $amiqusOAuthService;

    /**
     * The HTTP client instance.
     *
     * @var \GuzzleHttp\Client
     */
    protected $httpClient;

    /**
     * The Amiqus API log service instance.
     *
     * @var \App\Services\Interfaces\AmiqusApiLogServiceInterface
     */
    protected $apiLogService;

    /**
     * Create a new service instance.
     *
     * @return void
     */
    public function __construct(
        AmiqusOAuthServiceInterface $amiqusOAuthService,
        Client $httpClient,
        AmiqusApiLogServiceInterface $apiLogService
    ) {
        $this->amiqusOAuthService = $amiqusOAuthService;
        $this->httpClient = $httpClient;
        $this->apiLogService = $apiLogService;
    }

    /**
     * Check if there's an active connection to Amiqus and get a valid access token.
     */
    public function getValidAccessToken(): array
    {
        // Check if there's an active connection
        $settings = $this->amiqusOAuthService->getSettings();

        if (! $settings['isConnected']) {
            return [
                'success' => false,
                'message' => 'No active connection to Amiqus. Please connect first.',
                'status_code' => 400,
            ];
        }

        $client = $settings['client'];
        $accessToken = $client->accessTokens()->whereDate('expires_at', '>', Carbon::now())->latest()->first();

        if (! $accessToken) {
            return [
                'success' => false,
                'message' => 'No valid access token found. Please reconnect to Amiqus.',
                'status_code' => 400,
            ];
        }

        return [
            'success' => true,
            'client' => $client,
            'access_token' => $accessToken,
        ];
    }

    /**
     * Create a client in Amiqus and link it to the candidate.
     */
    public function createClient(Candidate $candidate, Request $request): array
    {
        // Check if the candidate is already connected to Amiqus
        if ($candidate->isConnectedToAmiqus()) {
            return [
                'success' => false,
                'message' => 'Candidate is already connected to an Amiqus client.',
                'candidate' => $candidate,
                'amiqus_client_url' => $candidate->getAmiqusClientUrl(),
                'status_code' => 400,
            ];
        }

        // Get a valid access token
        $tokenResult = $this->getValidAccessToken();
        if (! $tokenResult['success']) {
            return $tokenResult;
        }

        $accessToken = $tokenResult['access_token'];

        try {
            // Prepare the payload
            $payload = $this->prepareClientPayload($candidate, $request);

            $url = config('amiqus.auth_url').config('amiqus.endpoints.clients');
            $method = 'POST';
            $headers = [
                'Authorization' => 'Bearer '.$accessToken->access_token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ];

            // Record the start time
            $startTime = microtime(true);

            // Make request to Amiqus API to create a client
            $response = $this->httpClient->post($url, [
                'headers' => $headers,
                'json' => $payload,
            ]);

            // Calculate the duration
            $duration = microtime(true) - $startTime;

            // Log the API request and response
            $this->apiLogService->log(
                $candidate,
                $method,
                $url,
                $headers,
                $payload,
                $response,
                $duration
            );

            $data = json_decode((string) $response->getBody(), true);

            if (! isset($data['id'])) {
                return [
                    'success' => false,
                    'message' => 'Invalid response from Amiqus API.',
                    'status_code' => 400,
                ];
            }

            // Update the candidate with the Amiqus client ID
            $candidate->amiqus_client_id = $data['id'];
            $candidate->save();

            return [
                'success' => true,
                'message' => 'Amiqus client created and linked to candidate successfully.',
                'candidate' => $candidate,
                'amiqus_client' => $data,
                'amiqus_client_url' => $candidate->getAmiqusClientUrl(),
                'status_code' => 200,
            ];
        } catch (RequestException $e) {
            Log::error('Amiqus API client creation error: '.$e->getMessage());

            // Log the API error
            $url = config('amiqus.auth_url').config('amiqus.endpoints.clients');
            $method = 'POST';
            $headers = [
                'Authorization' => 'Bearer '.$accessToken->access_token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ];

            $this->apiLogService->log(
                $candidate,
                $method,
                $url,
                $headers,
                $payload,
                null,
                0,
                $e->getMessage()
            );

            return [
                'success' => false,
                'message' => 'Failed to create client in Amiqus API.',
                'error' => $e->getMessage(),
                'status_code' => 500,
            ];
        }
    }

    /**
     * Update a client in Amiqus.
     */
    public function updateClient(Candidate $candidate, Request $request): array
    {
        // Check if the candidate is connected to Amiqus
        if (! $candidate->isConnectedToAmiqus()) {
            return [
                'success' => false,
                'message' => 'Candidate is not connected to an Amiqus client. Please create an Amiqus client first.',
                'status_code' => 400,
            ];
        }

        // Get a valid access token
        $tokenResult = $this->getValidAccessToken();
        if (! $tokenResult['success']) {
            return $tokenResult;
        }

        $accessToken = $tokenResult['access_token'];

        try {
            // Prepare the payload
            $payload = $this->prepareClientPayload($candidate, $request);

            $url = config('amiqus.auth_url').config('amiqus.endpoints.clients').'/'.$candidate->amiqus_client_id;
            $method = 'PATCH';
            $headers = [
                'Authorization' => 'Bearer '.$accessToken->access_token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ];

            // Record the start time
            $startTime = microtime(true);

            // Make request to Amiqus API to update a client
            $response = $this->httpClient->patch($url, [
                'headers' => $headers,
                'json' => $payload,
            ]);

            // Calculate the duration
            $duration = microtime(true) - $startTime;

            // Log the API request and response
            $this->apiLogService->log(
                $candidate,
                $method,
                $url,
                $headers,
                $payload,
                $response,
                $duration
            );

            $data = json_decode((string) $response->getBody(), true);

            if (! isset($data['id'])) {
                return [
                    'success' => false,
                    'message' => 'Invalid response from Amiqus API.',
                    'status_code' => 400,
                ];
            }

            return [
                'success' => true,
                'message' => 'Amiqus client updated successfully.',
                'candidate' => $candidate,
                'amiqus_client' => $data,
                'amiqus_client_url' => $candidate->getAmiqusClientUrl(),
                'status_code' => 200,
            ];
        } catch (RequestException $e) {
            Log::error('Amiqus API client update error: '.$e->getMessage());

            // Log the API error
            $url = config('amiqus.auth_url').config('amiqus.endpoints.clients').'/'.$candidate->amiqus_client_id;
            $method = 'PATCH';
            $headers = [
                'Authorization' => 'Bearer '.$accessToken->access_token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ];

            $this->apiLogService->log(
                $candidate,
                $method,
                $url,
                $headers,
                $payload,
                null,
                0,
                $e->getMessage()
            );

            return [
                'success' => false,
                'message' => 'Failed to update client in Amiqus API.',
                'error' => $e->getMessage(),
                'status_code' => 500,
            ];
        }
    }

    /**
     * Prepare the payload for Amiqus API requests.
     */
    public function prepareClientPayload(Candidate $candidate, Request $request): array
    {
        return [
            'name' => [
                'title' => $request->has('title') ? $request->input('title') : null,
                'first_name' => $candidate->first_name,
                'middle_name' => $request->has('middle_name') ? $request->input('middle_name') : null,
                'last_name' => $candidate->last_name,
            ],
            'email' => $candidate->email,
            'reference' => $request->has('reference') ? $request->input('reference') : 'amiqus-ats-demo-'.$candidate->id,
        ];
    }
}
