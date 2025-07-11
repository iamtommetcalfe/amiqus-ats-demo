<?php

namespace App\Repositories\Interfaces;

use App\Models\AmiqusOAuthAccessToken;
use App\Models\AmiqusOAuthClient;

interface AmiqusOAuthAccessTokenRepositoryInterface
{
    /**
     * Create a new OAuth access token.
     */
    public function create(AmiqusOAuthClient $client, array $data): AmiqusOAuthAccessToken;

    /**
     * Get the latest access token for a client.
     */
    public function getLatestForClient(AmiqusOAuthClient $client): ?AmiqusOAuthAccessToken;

    /**
     * Delete all access tokens for a client.
     */
    public function deleteAllForClient(AmiqusOAuthClient $client): bool;
}
