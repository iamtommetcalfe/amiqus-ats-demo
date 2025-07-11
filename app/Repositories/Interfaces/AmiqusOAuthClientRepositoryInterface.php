<?php

namespace App\Repositories\Interfaces;

use App\Models\AmiqusOAuthClient;

interface AmiqusOAuthClientRepositoryInterface
{
    /**
     * Get the active OAuth client.
     */
    public function getActiveClient(): ?AmiqusOAuthClient;

    /**
     * Create a new OAuth client.
     */
    public function create(array $data): AmiqusOAuthClient;

    /**
     * Deactivate all OAuth clients.
     */
    public function deactivateAll(): bool;

    /**
     * Delete the active OAuth client.
     */
    public function deleteActive(): bool;
}
