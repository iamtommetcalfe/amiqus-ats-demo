<?php

namespace App\Http\Controllers;

use App\Models\AmiqusOAuthClient;
use App\Models\AmiqusOAuthAccessToken;
use App\Models\AmiqusOAuthRefreshToken;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AmiqusOAuthController extends Controller
{
    /**
     * The Amiqus API base URL.
     *
     * @var string
     */
    protected $apiBaseUrl;

    /**
     * The Amiqus OAuth authorization URL.
     *
     * @var string
     */
    protected $authorizationUrl;

    /**
     * The Amiqus OAuth token URL.
     *
     * @var string
     */
    protected $tokenUrl;

    /**
     * The HTTP client instance.
     *
     * @var \GuzzleHttp\Client
     */
    protected $httpClient;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->httpClient = new Client();
        $this->apiBaseUrl = env('AMIQUS_API_URL', 'https://api.amiqus.co');
        $this->authorizationUrl = env('AMIQUS_AUTH_URL', 'https://id.amiqus.co') . '/oauth/authorize';
        $this->tokenUrl = env('AMIQUS_AUTH_URL', 'https://id.amiqus.co') . '/oauth/token';
    }

    /**
     * Show the settings page for Amiqus integration.
     *
     * @return \Illuminate\Http\Response
     */
    public function settings()
    {
        $client = AmiqusOAuthClient::where('is_active', true)->first();

        return response()->json([
            'client' => $client,
            'isConnected' => $client && $client->accessTokens()->whereDate('expires_at', '>', Carbon::now())->exists(),
        ]);
    }

    /**
     * Store the client credentials.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeCredentials(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'client_id' => 'required|string|max:255',
            'client_secret' => 'required|string|max:255',
            'redirect_uri' => 'required|url|max:255',
            'scope' => 'nullable|string|max:255',
        ]);

        // Deactivate all existing clients
        AmiqusOAuthClient::where('is_active', true)->update(['is_active' => false]);

        // Create a new client
        $client = AmiqusOAuthClient::create([
            'name' => $request->name,
            'client_id' => $request->client_id,
            'client_secret' => $request->client_secret,
            'redirect_uri' => $request->redirect_uri,
            'scope' => $request->scope,
            'is_active' => true,
        ]);

        return response()->json([
            'message' => 'Client credentials saved successfully.',
            'client' => $client
        ], 200);
    }

    /**
     * Redirect the user to the Amiqus authorization page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider()
    {
        $client = AmiqusOAuthClient::where('is_active', true)->firstOrFail();

        $state = Str::random(40);
        session(['amiqus_oauth_state' => $state]);

        $query = http_build_query([
            'client_id' => $client->client_id,
            'redirect_uri' => $client->redirect_uri,
            'response_type' => 'code',
            'scope' => $client->scope,
            'state' => $state,
        ]);

        return redirect($this->authorizationUrl . '?' . $query);
    }

    /**
     * Handle the callback from Amiqus.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback(Request $request)
    {
        if ($request->has('error')) {
            return redirect()->route('amiqus.settings')
                ->with('error', 'Error from Amiqus: ' . $request->error_description);
        }

        if ($request->state !== session('amiqus_oauth_state')) {
            return redirect()->route('amiqus.settings')
                ->with('error', 'Invalid state parameter. The request may have been tampered with.');
        }

        $client = AmiqusOAuthClient::where('is_active', true)->firstOrFail();

        try {
            $response = $this->httpClient->post($this->tokenUrl, [
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
            $accessToken = $client->accessTokens()->create([
                'access_token' => $tokenData['access_token'],
                'token_type' => $tokenData['token_type'],
                'expires_in' => $tokenData['expires_in'],
                'expires_at' => Carbon::now()->addSeconds($tokenData['expires_in']),
            ]);

            // Store the refresh token if provided
            if (isset($tokenData['refresh_token'])) {
                $accessToken->refreshToken()->create([
                    'refresh_token' => $tokenData['refresh_token'],
                    'expires_in' => isset($tokenData['refresh_expires_in']) ? $tokenData['refresh_expires_in'] : 0,
                    'expires_at' => isset($tokenData['refresh_expires_in'])
                        ? Carbon::now()->addSeconds($tokenData['refresh_expires_in'])
                        : Carbon::now()->addYears(10), // Set a far future date for tokens without expiry
                ]);
            }

            return redirect()->route('amiqus.settings')
                ->with('success', 'Successfully connected to Amiqus.');
        } catch (RequestException $e) {
            Log::error('Amiqus OAuth error: ' . $e->getMessage());

            return redirect()->route('amiqus.settings')
                ->with('error', 'Failed to obtain access token from Amiqus.');
        }
    }

    /**
     * Refresh the access token.
     *
     * @return \Illuminate\Http\Response
     */
    public function refreshToken()
    {
        $client = AmiqusOAuthClient::where('is_active', true)->firstOrFail();
        $accessToken = $client->accessTokens()->latest()->first();

        if (!$accessToken || !$accessToken->refreshToken) {
            return response()->json([
                'message' => 'No refresh token available. Please reconnect to Amiqus.',
                'success' => false
            ], 400);
        }

        try {
            $response = $this->httpClient->post($this->tokenUrl, [
                'form_params' => [
                    'grant_type' => 'refresh_token',
                    'client_id' => $client->client_id,
                    'client_secret' => $client->client_secret,
                    'refresh_token' => $accessToken->refreshToken->refresh_token,
                ],
            ]);

            $tokenData = json_decode((string) $response->getBody(), true);

            // Store the new access token
            $newAccessToken = $client->accessTokens()->create([
                'access_token' => $tokenData['access_token'],
                'token_type' => $tokenData['token_type'],
                'expires_in' => $tokenData['expires_in'],
                'expires_at' => Carbon::now()->addSeconds($tokenData['expires_in']),
            ]);

            // Store the new refresh token if provided
            if (isset($tokenData['refresh_token'])) {
                $newAccessToken->refreshToken()->create([
                    'refresh_token' => $tokenData['refresh_token'],
                    'expires_in' => isset($tokenData['refresh_expires_in']) ? $tokenData['refresh_expires_in'] : 0,
                    'expires_at' => isset($tokenData['refresh_expires_in'])
                        ? Carbon::now()->addSeconds($tokenData['refresh_expires_in'])
                        : Carbon::now()->addYears(10), // Set a far future date for tokens without expiry
                ]);
            }

            return response()->json([
                'message' => 'Access token refreshed successfully.',
                'success' => true
            ], 200);
        } catch (RequestException $e) {
            Log::error('Amiqus OAuth refresh error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to refresh access token from Amiqus.',
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Disconnect from Amiqus.
     *
     * @return \Illuminate\Http\Response
     */
    public function disconnect()
    {
        $client = AmiqusOAuthClient::where('is_active', true)->first();

        if ($client) {
            // Delete all access tokens and refresh tokens
            foreach ($client->accessTokens as $accessToken) {
                $accessToken->refreshToken()->delete();
                $accessToken->delete();
            }
        }

        return response()->json([
            'message' => 'Disconnected from Amiqus successfully.',
            'success' => true
        ], 200);
    }

    /**
     * Delete the client credentials.
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteCredentials()
    {
        $client = AmiqusOAuthClient::where('is_active', true)->first();

        if ($client) {
            // Delete all access tokens and refresh tokens
            foreach ($client->accessTokens as $accessToken) {
                $accessToken->refreshToken()->delete();
                $accessToken->delete();
            }

            // Delete the client
            $client->delete();

            return response()->json(['message' => 'Integration deleted successfully'], 200);
        }

        return response()->json(['message' => 'No active integration found'], 404);
    }

    /**
     * Test the connection to Amiqus API.
     *
     * @return \Illuminate\Http\Response
     */
    public function testConnection()
    {
        $client = AmiqusOAuthClient::where('is_active', true)->firstOrFail();
        $accessToken = $client->accessTokens()->latest()->first();

        if (!$accessToken) {
            return response()->json([
                'message' => 'No access token available. Please connect to Amiqus.',
                'success' => false
            ], 400);
        }

        try {
            $response = $this->httpClient->get('https://id.amiqus.co/api/v2/me', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken->access_token,
                ],
            ]);

            $userData = json_decode((string) $response->getBody(), true);

            return response()->json([
                'message' => 'Connection test successful.',
                'success' => true,
                'data' => $userData
            ], 200);
        } catch (RequestException $e) {
            Log::error('Amiqus API test error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Failed to connect to Amiqus API.',
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
