<?php

namespace App\Repositories\Interfaces;

use App\Models\AmiqusOAuthClient;
use Illuminate\Database\Eloquent\Collection;

interface AmiqusOAuthClientRepositoryInterface
{
    /**
     * Get the active OAuth client.
     *
     * @return AmiqusOAuthClient|null
     */
    public function getActiveClient(): ?AmiqusOAuthClient;

    /**
     * Create a new OAuth client.
     *
     * @param array $data
     * @return AmiqusOAuthClient
     */
    public function create(array $data): AmiqusOAuthClient;

    /**
     * Deactivate all OAuth clients.
     *
     * @return bool
     */
    public function deactivateAll(): bool;

    /**
     * Delete the active OAuth client.
     *
     * @return bool
     */
    public function deleteActive(): bool;
}
