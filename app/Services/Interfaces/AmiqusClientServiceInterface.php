<?php

namespace App\Services\Interfaces;

use App\Models\Candidate;
use Illuminate\Http\Request;

interface AmiqusClientServiceInterface
{
    /**
     * Check if there's an active connection to Amiqus and get a valid access token.
     */
    public function getValidAccessToken(): array;

    /**
     * Create a client in Amiqus and link it to the candidate.
     */
    public function createClient(Candidate $candidate, Request $request): array;

    /**
     * Update a client in Amiqus.
     */
    public function updateClient(Candidate $candidate, Request $request): array;

    /**
     * Prepare the payload for Amiqus API requests.
     */
    public function prepareClientPayload(Candidate $candidate, Request $request): array;
}
