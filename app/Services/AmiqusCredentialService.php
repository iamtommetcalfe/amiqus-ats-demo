<?php

namespace App\Services;

use App\Models\AmiqusOAuthClient;
use App\Repositories\Interfaces\AmiqusOAuthAccessTokenRepositoryInterface;
use App\Repositories\Interfaces\AmiqusOAuthClientRepositoryInterface;
use App\Services\Interfaces\AmiqusCredentialServiceInterface;
use Carbon\Carbon;

class AmiqusCredentialService implements AmiqusCredentialServiceInterface
{
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
        AmiqusOAuthClientRepositoryInterface $clientRepository,
        AmiqusOAuthAccessTokenRepositoryInterface $accessTokenRepository
    ) {
        $this->clientRepository = $clientRepository;
        $this->accessTokenRepository = $accessTokenRepository;
    }

    /**
     * Get the settings for Amiqus integration.
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
}
