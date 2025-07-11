<?php

namespace App\Repositories;

use App\Models\AmiqusOAuthClient;
use App\Repositories\Interfaces\AmiqusOAuthClientRepositoryInterface;

class AmiqusOAuthClientRepository implements AmiqusOAuthClientRepositoryInterface
{
    /**
     * Get the active OAuth client.
     */
    public function getActiveClient(): ?AmiqusOAuthClient
    {
        return AmiqusOAuthClient::where('is_active', true)->first();
    }

    /**
     * Create a new OAuth client.
     */
    public function create(array $data): AmiqusOAuthClient
    {
        return AmiqusOAuthClient::create($data);
    }

    /**
     * Deactivate all OAuth clients.
     */
    public function deactivateAll(): bool
    {
        return AmiqusOAuthClient::where('is_active', true)->update(['is_active' => false]) > 0;
    }

    /**
     * Delete the active OAuth client.
     */
    public function deleteActive(): bool
    {
        $client = $this->getActiveClient();

        if ($client) {
            return $client->delete();
        }

        return false;
    }
}
