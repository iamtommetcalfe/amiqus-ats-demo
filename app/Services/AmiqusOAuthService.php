<?php

namespace App\Services;

use App\Models\AmiqusOAuthClient;
use App\Services\Interfaces\AmiqusApiServiceInterface;
use App\Services\Interfaces\AmiqusAuthServiceInterface;
use App\Services\Interfaces\AmiqusCredentialServiceInterface;
use App\Services\Interfaces\AmiqusOAuthServiceInterface;
use App\Services\Interfaces\AmiqusTokenServiceInterface;
use Illuminate\Http\Request;

class AmiqusOAuthService implements AmiqusOAuthServiceInterface
{
    /**
     * The credential service instance.
     *
     * @var \App\Services\Interfaces\AmiqusCredentialServiceInterface
     */
    protected $credentialService;

    /**
     * The auth service instance.
     *
     * @var \App\Services\Interfaces\AmiqusAuthServiceInterface
     */
    protected $authService;

    /**
     * The token service instance.
     *
     * @var \App\Services\Interfaces\AmiqusTokenServiceInterface
     */
    protected $tokenService;

    /**
     * The API service instance.
     *
     * @var \App\Services\Interfaces\AmiqusApiServiceInterface
     */
    protected $apiService;

    /**
     * Create a new service instance.
     *
     * @return void
     */
    public function __construct(
        AmiqusCredentialServiceInterface $credentialService,
        AmiqusAuthServiceInterface $authService,
        AmiqusTokenServiceInterface $tokenService,
        AmiqusApiServiceInterface $apiService
    ) {
        $this->credentialService = $credentialService;
        $this->authService = $authService;
        $this->tokenService = $tokenService;
        $this->apiService = $apiService;
    }

    /**
     * Get the settings for Amiqus integration.
     */
    public function getSettings(): array
    {
        return $this->credentialService->getSettings();
    }

    /**
     * Store the client credentials.
     */
    public function storeCredentials(array $data): AmiqusOAuthClient
    {
        return $this->credentialService->storeCredentials($data);
    }

    /**
     * Delete the client credentials.
     */
    public function deleteCredentials(): bool
    {
        return $this->credentialService->deleteCredentials();
    }

    /**
     * Generate the authorization URL.
     */
    public function getAuthorizationUrl(): string
    {
        return $this->authService->getAuthorizationUrl();
    }

    /**
     * Handle the callback from the OAuth provider.
     */
    public function handleProviderCallback(Request $request): array
    {
        return $this->authService->handleProviderCallback($request);
    }

    /**
     * Refresh the access token.
     */
    public function refreshToken(): array
    {
        return $this->tokenService->refreshToken();
    }

    /**
     * Disconnect from the OAuth provider.
     */
    public function disconnect(): bool
    {
        return $this->tokenService->disconnect();
    }

    /**
     * Test the connection to the API.
     */
    public function testConnection(): array
    {
        return $this->apiService->testConnection();
    }
}
