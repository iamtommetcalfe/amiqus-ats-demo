<?php

namespace App\Repositories;

use App\Models\AmiqusOAuthAccessToken;
use App\Models\AmiqusOAuthRefreshToken;
use App\Repositories\Interfaces\AmiqusOAuthRefreshTokenRepositoryInterface;

class AmiqusOAuthRefreshTokenRepository implements AmiqusOAuthRefreshTokenRepositoryInterface
{
    /**
     * Create a new OAuth refresh token.
     */
    public function create(AmiqusOAuthAccessToken $accessToken, array $data): AmiqusOAuthRefreshToken
    {
        return $accessToken->refreshToken()->create($data);
    }

    /**
     * Delete a refresh token.
     */
    public function delete(AmiqusOAuthRefreshToken $refreshToken): bool
    {
        return $refreshToken->delete();
    }

    /**
     * Delete all refresh tokens for an access token.
     */
    public function deleteForAccessToken(AmiqusOAuthAccessToken $accessToken): bool
    {
        if ($accessToken->refreshToken) {
            return $accessToken->refreshToken->delete();
        }

        return true;
    }
}
