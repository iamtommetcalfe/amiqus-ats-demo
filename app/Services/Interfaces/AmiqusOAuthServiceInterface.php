<?php

namespace App\Services\Interfaces;

use App\Models\AmiqusOAuthClient;
use Illuminate\Http\Request;

interface AmiqusOAuthServiceInterface
{
    /**
     * Get the settings for Amiqus integration.
     */
    public function getSettings(): array;

    /**
     * Store the client credentials.
     */
    public function storeCredentials(array $data): AmiqusOAuthClient;

    /**
     * Delete the client credentials.
     */
    public function deleteCredentials(): bool;

    /**
     * Generate the authorization URL.
     */
    public function getAuthorizationUrl(): string;

    /**
     * Handle the callback from the OAuth provider.
     */
    public function handleProviderCallback(Request $request): array;

    /**
     * Refresh the access token.
     */
    public function refreshToken(): array;

    /**
     * Disconnect from the OAuth provider.
     */
    public function disconnect(): bool;

    /**
     * Test the connection to the API.
     */
    public function testConnection(): array;
}
