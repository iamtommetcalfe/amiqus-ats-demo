<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CacheRememberTest extends TestCase
{
    /**
     * Test that Cache::remember() checks for existing cache first.
     *
     * @return void
     */
    public function test_cache_remember_checks_existing_cache_first()
    {
        // Clear the cache
        Cache::forget('test_key');

        // Counter to track how many times the callback is executed
        $callbackExecutionCount = 0;

        // First call - should execute the callback
        $result1 = Cache::remember('test_key', 600, function () use (&$callbackExecutionCount) {
            $callbackExecutionCount++;

            return 'test_value';
        });

        // Assert that the callback was executed once
        $this->assertEquals(1, $callbackExecutionCount);
        $this->assertEquals('test_value', $result1);

        // Second call - should use the cache and not execute the callback again
        $result2 = Cache::remember('test_key', 600, function () use (&$callbackExecutionCount) {
            $callbackExecutionCount++;

            return 'different_value'; // This should not be returned
        });

        // Assert that the callback was not executed again
        $this->assertEquals(1, $callbackExecutionCount);
        $this->assertEquals('test_value', $result2); // Should return the cached value, not 'different_value'

        // Clear the cache
        Cache::forget('test_key');

        // Third call - should execute the callback again because cache was cleared
        $result3 = Cache::remember('test_key', 600, function () use (&$callbackExecutionCount) {
            $callbackExecutionCount++;

            return 'new_value';
        });

        // Assert that the callback was executed again
        $this->assertEquals(2, $callbackExecutionCount);
        $this->assertEquals('new_value', $result3);
    }
}
