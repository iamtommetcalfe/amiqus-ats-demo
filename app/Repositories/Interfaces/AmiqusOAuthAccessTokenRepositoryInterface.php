<?php

namespace App\Repositories\Interfaces;

use App\Models\AmiqusOAuthAccessToken;
use App\Models\AmiqusOAuthClient;

interface AmiqusOAuthAccessTokenRepositoryInterface
{
    /**
     * Create a new OAuth access token.
     *
     * @param AmiqusOAuthClient $client
     * @param array $data
     * @return AmiqusOAuthAccessToken
     */
    public function create(AmiqusOAuthClient $client, array $data): AmiqusOAuthAccessToken;

    /**
     * Get the latest access token for a client.
     *
     * @param AmiqusOAuthClient $client
     * @return AmiqusOAuthAccessToken|null
     */
    public function getLatestForClient(AmiqusOAuthClient $client): ?AmiqusOAuthAccessToken;

    /**
     * Delete all access tokens for a client.
     *
     * @param AmiqusOAuthClient $client
     * @return bool
     */
    public function deleteAllForClient(AmiqusOAuthClient $client): bool;
}
