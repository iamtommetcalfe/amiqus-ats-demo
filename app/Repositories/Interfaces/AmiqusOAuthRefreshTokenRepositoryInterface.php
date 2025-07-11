<?php

namespace App\Repositories\Interfaces;

use App\Models\AmiqusOAuthAccessToken;
use App\Models\AmiqusOAuthRefreshToken;

interface AmiqusOAuthRefreshTokenRepositoryInterface
{
    /**
     * Create a new OAuth refresh token.
     */
    public function create(AmiqusOAuthAccessToken $accessToken, array $data): AmiqusOAuthRefreshToken;

    /**
     * Delete a refresh token.
     */
    public function delete(AmiqusOAuthRefreshToken $refreshToken): bool;

    /**
     * Delete all refresh tokens for an access token.
     */
    public function deleteForAccessToken(AmiqusOAuthAccessToken $accessToken): bool;
}
