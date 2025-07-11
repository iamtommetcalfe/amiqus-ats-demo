<?php

namespace App\Services;

use App\Repositories\Interfaces\AmiqusOAuthAccessTokenRepositoryInterface;
use App\Repositories\Interfaces\AmiqusOAuthClientRepositoryInterface;
use App\Repositories\Interfaces\AmiqusOAuthRefreshTokenRepositoryInterface;
use App\Services\Interfaces\AmiqusAuthServiceInterface;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AmiqusAuthService implements AmiqusAuthServiceInterface
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
     * Generate the authorization URL.
     */
    public function getAuthorizationUrl(): string
    {
        $client = $this->clientRepository->getActiveClient();

        if (! $client) {
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

        return config('amiqus.auth_url').config('amiqus.oauth.authorize_endpoint').'?'.$query;
    }

    /**
     * Handle the callback from the OAuth provider.
     */
    public function handleProviderCallback(Request $request): array
    {
        if ($request->has('error')) {
            return [
                'success' => false,
                'message' => 'Error from Amiqus: '.$request->error_description,
            ];
        }

        if ($request->state !== session('amiqus_oauth_state')) {
            return [
                'success' => false,
                'message' => 'Invalid state parameter. The request may have been tampered with.',
            ];
        }

        $client = $this->clientRepository->getActiveClient();

        if (! $client) {
            return [
                'success' => false,
                'message' => 'No active OAuth client found.',
            ];
        }

        try {
            $response = $this->httpClient->post(config('amiqus.auth_url').config('amiqus.oauth.token_endpoint'), [
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
                'message' => 'Successfully connected to Amiqus.',
            ];
        } catch (RequestException $e) {
            Log::error('Amiqus OAuth error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to obtain access token from Amiqus.',
            ];
        }
    }
}
