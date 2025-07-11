<?php

namespace App\Repositories;

use App\Models\AmiqusOAuthAccessToken;
use App\Models\AmiqusOAuthClient;
use App\Repositories\Interfaces\AmiqusOAuthAccessTokenRepositoryInterface;

class AmiqusOAuthAccessTokenRepository implements AmiqusOAuthAccessTokenRepositoryInterface
{
    /**
     * Create a new OAuth access token.
     */
    public function create(AmiqusOAuthClient $client, array $data): AmiqusOAuthAccessToken
    {
        return $client->accessTokens()->create($data);
    }

    /**
     * Get the latest access token for a client.
     */
    public function getLatestForClient(AmiqusOAuthClient $client): ?AmiqusOAuthAccessToken
    {
        return $client->accessTokens()->latest()->first();
    }

    /**
     * Delete all access tokens for a client.
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
