<?php

namespace App\Services\Interfaces;

interface AmiqusApiServiceInterface
{
    /**
     * Test the connection to the API.
     */
    public function testConnection(): array;
}
