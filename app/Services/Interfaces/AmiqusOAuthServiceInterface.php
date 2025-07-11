<?php

namespace App\Services\Interfaces;

use App\Models\AmiqusOAuthClient;
use App\Models\AmiqusOAuthAccessToken;
use Illuminate\Http\Request;

interface AmiqusOAuthServiceInterface
{
    /**
     * Get the settings for Amiqus integration.
     *
     * @return array
     */
    public function getSettings(): array;

    /**
     * Store the client credentials.
     *
     * @param array $data
     * @return AmiqusOAuthClient
     */
    public function storeCredentials(array $data): AmiqusOAuthClient;

    /**
     * Delete the client credentials.
     *
     * @return bool
     */
    public function deleteCredentials(): bool;

    /**
     * Generate the authorization URL.
     *
     * @return string
     */
    public function getAuthorizationUrl(): string;

    /**
     * Handle the callback from the OAuth provider.
     *
     * @param Request $request
     * @return array
     */
    public function handleProviderCallback(Request $request): array;

    /**
     * Refresh the access token.
     *
     * @return array
     */
    public function refreshToken(): array;

    /**
     * Disconnect from the OAuth provider.
     *
     * @return bool
     */
    public function disconnect(): bool;

    /**
     * Test the connection to the API.
     *
     * @return array
     */
    public function testConnection(): array;
}
