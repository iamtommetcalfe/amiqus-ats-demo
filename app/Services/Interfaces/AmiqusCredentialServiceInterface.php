<?php

namespace App\Services\Interfaces;

use App\Models\AmiqusOAuthClient;

interface AmiqusCredentialServiceInterface
{
    /**
     * Get the settings for Amiqus integration.
     */
    public function getSettings(): array;

    /**
     * Store the client credentials.
     */
    public function storeCredentials(array $data): AmiqusOAuthClient;

    /**
     * Delete the client credentials.
     */
    public function deleteCredentials(): bool;
}
