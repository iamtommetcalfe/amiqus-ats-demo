<?php

namespace App\Services;

use App\Models\AmiqusOAuthClient;
use App\Models\AmiqusOAuthAccessToken;
use App\Repositories\Interfaces\AmiqusOAuthClientRepositoryInterface;
use App\Repositories\Interfaces\AmiqusOAuthAccessTokenRepositoryInterface;
use App\Repositories\Interfaces\AmiqusOAuthRefreshTokenRepositoryInterface;
use App\Services\Interfaces\AmiqusOAuthServiceInterface;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AmiqusOAuthService implements AmiqusOAuthServiceInterface
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
     * The refresh token repository instance.
     *
     * @var \App\Repositories\Interfaces\AmiqusOAuthRefreshTokenRepositoryInterface
     */
    protected $refreshTokenRepository;

    /**
     * Create a new service instance.
     *
     * @param \GuzzleHttp\Client $httpClient
     * @param \App\Repositories\Interfaces\AmiqusOAuthClientRepositoryInterface $clientRepository
     * @param \App\Repositories\Interfaces\AmiqusOAuthAccessTokenRepositoryInterface $accessTokenRepository
     * @param \App\Repositories\Interfaces\AmiqusOAuthRefreshTokenRepositoryInterface $refreshTokenRepository
     * @return void
     */
    public function __construct(
        Client $httpClient,
        AmiqusOAuthClientRepositoryInterface $clientRepository,
        AmiqusOAuthAccessTokenRepositoryInterface $accessTokenRepository,
        AmiqusOAuthRefreshTokenRepositoryInterface $refreshTokenRepository
    ) {
        $this->httpClient = $httpClient;
        $this->clientRepository = $clientRepository;
        $this->accessTokenRepository = $accessTokenRepository;
        $this->refreshTokenRepository = $refreshTokenRepository;
    }

    /**
     * Get the settings for Amiqus integration.
     *
     * @return array
     */
    public function getSettings(): array
    {
        $client = $this->clientRepository->getActiveClient();

        return [
            'client' => $client,
            'isConnected' => $client && $client->accessTokens()->whereDate('expires_at', '>', Carbon::now())->exists(),
        ];
    }

    /**
     * Store the client credentials.
     *
     * @param array $data
     * @return AmiqusOAuthClient
     */
    public function storeCredentials(array $data): AmiqusOAuthClient
    {
        // Deactivate all existing clients
        $this->clientRepository->deactivateAll();

        // Create a new client
        $data['is_active'] = true;
        return $this->clientRepository->create($data);
    }

    /**
     * Delete the client credentials.
     *
     * @return bool
     */
    public function deleteCredentials(): bool
    {
        $client = $this->clientRepository->getActiveClient();

        if ($client) {
            // Delete all access tokens and refresh tokens
            $this->accessTokenRepository->deleteAllForClient($client);

            // Delete the client
            return $this->clientRepository->deleteActive();
        }

        return false;
    }

    /**
     * Generate the authorization URL.
     *
     * @return string
     */
    public function getAuthorizationUrl(): string
    {
        $client = $this->clientRepository->getActiveClient();

        if (!$client) {
            throw new \Exception('No active OAuth client found.');
        }

        $state = Str::random(40);
        session(['amiqus_oauth_state' => $state]);

        $query = http_build_query([
            'client_id' => $client->client_id,
            'redirect_uri' => $client->redirect_uri,
            'response_type' => 'code',
            'scope' => $client->scope,
            'state' => $state,
        ]);

        return config('amiqus.auth_url') . config('amiqus.oauth.authorize_endpoint') . '?' . $query;
    }

    /**
     * Handle the callback from the OAuth provider.
     *
     * @param Request $request
     * @return array
     */
    public function handleProviderCallback(Request $request): array
    {
        if ($request->has('error')) {
            return [
                'success' => false,
                'message' => 'Error from Amiqus: ' . $request->error_description
            ];
        }

        if ($request->state !== session('amiqus_oauth_state')) {
            return [
                'success' => false,
                'message' => 'Invalid state parameter. The request may have been tampered with.'
            ];
        }

        $client = $this->clientRepository->getActiveClient();

        if (!$client) {
            return [
                'success' => false,
                'message' => 'No active OAuth client found.'
            ];
        }

        try {
            $response = $this->httpClient->post(config('amiqus.auth_url') . config('amiqus.oauth.token_endpoint'), [
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'client_id' => $client->client_id,
                    'client_secret' => $client->client_secret,
                    'redirect_uri' => $client->redirect_uri,
                    'code' => $request->code,
                ],
            ]);

            $tokenData = json_decode((string) $response->getBody(), true);

            // Store the access token
            $accessToken = $this->accessTokenRepository->create($client, [
                'access_token' => $tokenData['access_token'],
                'token_type' => $tokenData['token_type'],
                'expires_in' => $tokenData['expires_in'],
                'expires_at' => Carbon::now()->addSeconds($tokenData['expires_in']),
            ]);

            // Store the refresh token if provided
            if (isset($tokenData['refresh_token'])) {
                $this->refreshTokenRepository->create($accessToken, [
                    'refresh_token' => $tokenData['refresh_token'],
                    'expires_in' => isset($tokenData['refresh_expires_in']) ? $tokenData['refresh_expires_in'] : 0,
                    'expires_at' => isset($tokenData['refresh_expires_in'])
                        ? Carbon::now()->addSeconds($tokenData['refresh_expires_in'])
                        : Carbon::now()->addYears(10), // Set a far future date for tokens without expiry
                ]);
            }

            return [
                'success' => true,
                'message' => 'Successfully connected to Amiqus.'
            ];
        } catch (RequestException $e) {
            Log::error('Amiqus OAuth error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to obtain access token from Amiqus.'
            ];
        }
    }

    /**
     * Refresh the access token.
     *
     * @return array
     */
    public function refreshToken(): array
    {
        $client = $this->clientRepository->getActiveClient();

        if (!$client) {
            return [
                'success' => false,
                'message' => 'No active OAuth client found.'
            ];
        }

        $accessToken = $this->accessTokenRepository->getLatestForClient($client);

        if (!$accessToken || !$accessToken->refreshToken) {
            return [
                'success' => false,
                'message' => 'No refresh token available. Please reconnect to Amiqus.'
            ];
        }

        try {
            $response = $this->httpClient->post(config('amiqus.auth_url') . config('amiqus.oauth.token_endpoint'), [
                'form_params' => [
                    'grant_type' => 'refresh_token',
                    'client_id' => $client->client_id,
                    'client_secret' => $client->client_secret,
                    'refresh_token' => $accessToken->refreshToken->refresh_token,
                ],
            ]);

            $tokenData = json_decode((string) $response->getBody(), true);

            // Store the new access token
            $newAccessToken = $this->accessTokenRepository->create($client, [
                'access_token' => $tokenData['access_token'],
                'token_type' => $tokenData['token_type'],
                'expires_in' => $tokenData['expires_in'],
                'expires_at' => Carbon::now()->addSeconds($tokenData['expires_in']),
            ]);

            // Store the new refresh token if provided
            if (isset($tokenData['refresh_token'])) {
                $this->refreshTokenRepository->create($newAccessToken, [
                    'refresh_token' => $tokenData['refresh_token'],
                    'expires_in' => isset($tokenData['refresh_expires_in']) ? $tokenData['refresh_expires_in'] : 0,
                    'expires_at' => isset($tokenData['refresh_expires_in'])
                        ? Carbon::now()->addSeconds($tokenData['refresh_expires_in'])
                        : Carbon::now()->addYears(10), // Set a far future date for tokens without expiry
                ]);
            }

            return [
                'success' => true,
                'message' => 'Access token refreshed successfully.'
            ];
        } catch (RequestException $e) {
            Log::error('Amiqus OAuth refresh error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to refresh access token from Amiqus.',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Disconnect from the OAuth provider.
     *
     * @return bool
     */
    public function disconnect(): bool
    {
        $client = $this->clientRepository->getActiveClient();

        if ($client) {
            // Delete all access tokens and refresh tokens
            $this->accessTokenRepository->deleteAllForClient($client);
            return true;
        }

        return false;
    }

    /**
     * Test the connection to the API.
     *
     * @return array
     */
    public function testConnection(): array
    {
        $client = $this->clientRepository->getActiveClient();

        if (!$client) {
            return [
                'success' => false,
                'message' => 'No active OAuth client found.'
            ];
        }

        $accessToken = $this->accessTokenRepository->getLatestForClient($client);

        if (!$accessToken) {
            return [
                'success' => false,
                'message' => 'No access token available. Please connect to Amiqus.'
            ];
        }

        try {
            $response = $this->httpClient->get(config('amiqus.auth_url') . config('amiqus.endpoints.user_info'), [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken->access_token,
                ],
            ]);

            $userData = json_decode((string) $response->getBody(), true);

            return [
                'success' => true,
                'message' => 'Connection test successful.',
                'data' => $userData
            ];
        } catch (RequestException $e) {
            Log::error('Amiqus API test error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to connect to Amiqus API.',
                'error' => $e->getMessage()
            ];
        }
    }
}
