<?php

namespace App\Services\Interfaces;

interface AmiqusTokenServiceInterface
{
    /**
     * Refresh the access token.
     */
    public function refreshToken(): array;

    /**
     * Disconnect from the OAuth provider.
     */
    public function disconnect(): bool;
}
