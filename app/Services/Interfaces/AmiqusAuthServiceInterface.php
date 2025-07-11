<?php

namespace App\Services\Interfaces;

use Illuminate\Http\Request;

interface AmiqusAuthServiceInterface
{
    /**
     * Generate the authorization URL.
     */
    public function getAuthorizationUrl(): string;

    /**
     * Handle the callback from the OAuth provider.
     */
    public function handleProviderCallback(Request $request): array;
}
