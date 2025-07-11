<?php

namespace App\Services;

use App\Repositories\Interfaces\AmiqusOAuthAccessTokenRepositoryInterface;
use App\Repositories\Interfaces\AmiqusOAuthClientRepositoryInterface;
use App\Services\Interfaces\AmiqusApiServiceInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class AmiqusApiService implements AmiqusApiServiceInterface
{
    /**
     * The HTTP client instance.
     *
     * @var \GuzzleHttp\Client
     */
    protected $httpClient;

    /**
     * The client repository instance.
     *
     * @var \App\Repositories\Interfaces\AmiqusOAuthClientRepositoryInterface
     */
    protected $clientRepository;

    /**
     * The access token repository instance.
     *
     * @var \App\Repositories\Interfaces\AmiqusOAuthAccessTokenRepositoryInterface
     */
    protected $accessTokenRepository;

    /**
     * Create a new service instance.
     *
     * @return void
     */
    public function __construct(
        Client $httpClient,
        AmiqusOAuthClientRepositoryInterface $clientRepository,
        AmiqusOAuthAccessTokenRepositoryInterface $accessTokenRepository
    ) {
        $this->httpClient = $httpClient;
        $this->clientRepository = $clientRepository;
        $this->accessTokenRepository = $accessTokenRepository;
    }

    /**
     * Test the connection to the API.
     */
    public function testConnection(): array
    {
        $client = $this->clientRepository->getActiveClient();

        if (! $client) {
            return [
                'success' => false,
                'message' => 'No active OAuth client found.',
            ];
        }

        $accessToken = $this->accessTokenRepository->getLatestForClient($client);

        if (! $accessToken) {
            return [
                'success' => false,
                'message' => 'No access token available. Please connect to Amiqus.',
            ];
        }

        try {
            $response = $this->httpClient->get(config('amiqus.auth_url').config('amiqus.endpoints.user_info'), [
                'headers' => [
                    'Authorization' => 'Bearer '.$accessToken->access_token,
                ],
            ]);

            $userData = json_decode((string) $response->getBody(), true);

            return [
                'success' => true,
                'message' => 'Connection test successful.',
                'data' => $userData,
            ];
        } catch (RequestException $e) {
            Log::error('Amiqus API test error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to connect to Amiqus API.',
                'error' => $e->getMessage(),
            ];
        }
    }
}
