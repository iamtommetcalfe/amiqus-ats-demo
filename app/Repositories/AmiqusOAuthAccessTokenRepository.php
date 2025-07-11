<?php

namespace App\Repositories;

use App\Models\AmiqusOAuthAccessToken;
use App\Models\AmiqusOAuthClient;
use App\Repositories\Interfaces\AmiqusOAuthAccessTokenRepositoryInterface;

class AmiqusOAuthAccessTokenRepository implements AmiqusOAuthAccessTokenRepositoryInterface
{
    /**
     * Create a new OAuth access token.
     *
     * @param AmiqusOAuthClient $client
     * @param array $data
     * @return AmiqusOAuthAccessToken
     */
    public function create(AmiqusOAuthClient $client, array $data): AmiqusOAuthAccessToken
    {
        return $client->accessTokens()->create($data);
    }

    /**
     * Get the latest access token for a client.
     *
     * @param AmiqusOAuthClient $client
     * @return AmiqusOAuthAccessToken|null
     */
    public function getLatestForClient(AmiqusOAuthClient $client): ?AmiqusOAuthAccessToken
    {
        return $client->accessTokens()->latest()->first();
    }

    /**
     * Delete all access tokens for a client.
     *
     * @param AmiqusOAuthClient $client
     * @return bool
     */
    public function deleteAllForClient(AmiqusOAuthClient $client): bool
    {
        foreach ($client->accessTokens as $accessToken) {
            if ($accessToken->refreshToken) {
                $accessToken->refreshToken->delete();
            }
            $accessToken->delete();
        }

        return true;
    }
}
