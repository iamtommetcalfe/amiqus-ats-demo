<?php

namespace App\Services;

use App\Repositories\Interfaces\AmiqusOAuthAccessTokenRepositoryInterface;
use App\Repositories\Interfaces\AmiqusOAuthClientRepositoryInterface;
use App\Repositories\Interfaces\AmiqusOAuthRefreshTokenRepositoryInterface;
use App\Services\Interfaces\AmiqusTokenServiceInterface;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class AmiqusTokenService implements AmiqusTokenServiceInterface
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
     * Refresh the access token.
     */
    public function refreshToken(): array
    {
        $client = $this->clientRepository->getActiveClient();

        if (! $client) {
            return [
                'success' => false,
                'message' => 'No active OAuth client found.',
            ];
        }

        $accessToken = $this->accessTokenRepository->getLatestForClient($client);

        if (! $accessToken || ! $accessToken->refreshToken) {
            return [
                'success' => false,
                'message' => 'No refresh token available. Please reconnect to Amiqus.',
            ];
        }

        try {
            $response = $this->httpClient->post(config('amiqus.auth_url').config('amiqus.oauth.token_endpoint'), [
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
                'message' => 'Access token refreshed successfully.',
            ];
        } catch (RequestException $e) {
            Log::error('Amiqus OAuth refresh error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to refresh access token from Amiqus.',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Disconnect from the OAuth provider.
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
}
