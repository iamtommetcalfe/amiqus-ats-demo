<?php

namespace App\Repositories\Interfaces;

use App\Models\AmiqusOAuthAccessToken;
use App\Models\AmiqusOAuthRefreshToken;

interface AmiqusOAuthRefreshTokenRepositoryInterface
{
    /**
     * Create a new OAuth refresh token.
     *
     * @param AmiqusOAuthAccessToken $accessToken
     * @param array $data
     * @return AmiqusOAuthRefreshToken
     */
    public function create(AmiqusOAuthAccessToken $accessToken, array $data): AmiqusOAuthRefreshToken;

    /**
     * Delete a refresh token.
     *
     * @param AmiqusOAuthRefreshToken $refreshToken
     * @return bool
     */
    public function delete(AmiqusOAuthRefreshToken $refreshToken): bool;

    /**
     * Delete all refresh tokens for an access token.
     *
     * @param AmiqusOAuthAccessToken $accessToken
     * @return bool
     */
    public function deleteForAccessToken(AmiqusOAuthAccessToken $accessToken): bool;
}
