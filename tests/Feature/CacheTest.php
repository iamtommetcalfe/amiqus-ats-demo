<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CacheTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Run migrations and seed the database
        Artisan::call('migrate');
        Artisan::call('db:seed');
    }

    /**
     * Test that the job postings index endpoint is cached.
     *
     * @return void
     */
    public function test_job_postings_index_is_cached()
    {
        // Clear the cache
        Cache::forget('job_postings.index');

        // Make a request to the endpoint
        $response = $this->get('/api/ats/jobs');

        // Assert that the response is successful
        $response->assertStatus(200);

        // Assert that the cache has been created
        $this->assertTrue(Cache::has('job_postings.index'));

        // Make another request to the endpoint
        $response2 = $this->get('/api/ats/jobs');

        // Assert that the response is successful
        $response2->assertStatus(200);

        // Assert that the responses are identical
        $this->assertEquals($response->getContent(), $response2->getContent());
    }

    /**
     * Test that the job posting show endpoint is cached.
     *
     * @return void
     */
    public function test_job_posting_show_is_cached()
    {
        // Get the first job posting ID
        $response = $this->get('/api/ats/jobs');
        $jobPostings = json_decode($response->getContent(), true);
        $jobPostingId = $jobPostings[0]['id'];

        // Clear the cache
        Cache::forget("job_postings.show.{$jobPostingId}");

        // Make a request to the endpoint
        $response = $this->get("/api/ats/jobs/{$jobPostingId}");

        // Assert that the response is successful
        $response->assertStatus(200);

        // Assert that the cache has been created
        $this->assertTrue(Cache::has("job_postings.show.{$jobPostingId}"));

        // Make another request to the endpoint
        $response2 = $this->get("/api/ats/jobs/{$jobPostingId}");

        // Assert that the response is successful
        $response2->assertStatus(200);

        // Assert that the responses are identical
        $this->assertEquals($response->getContent(), $response2->getContent());
    }

    /**
     * Test that the candidates index endpoint is cached.
     *
     * @return void
     */
    public function test_candidates_index_is_cached()
    {
        // Clear the cache
        Cache::forget('candidates.index');

        // Make a request to the endpoint
        $response = $this->get('/api/ats/candidates');

        // Assert that the response is successful
        $response->assertStatus(200);

        // Assert that the cache has been created
        $this->assertTrue(Cache::has('candidates.index'));

        // Make another request to the endpoint
        $response2 = $this->get('/api/ats/candidates');

        // Assert that the response is successful
        $response2->assertStatus(200);

        // Assert that the responses are identical
        $this->assertEquals($response->getContent(), $response2->getContent());
    }

    /**
     * Test that the background checks all endpoint is cached.
     *
     * @return void
     */
    public function test_background_checks_all_is_cached()
    {
        // Clear the cache
        Cache::forget('background_checks.all');

        // Make a request to the endpoint
        $response = $this->get('/api/ats/background-checks');

        // Assert that the response is successful
        $response->assertStatus(200);

        // Assert that the cache has been created
        $this->assertTrue(Cache::has('background_checks.all'));

        // Make another request to the endpoint
        $response2 = $this->get('/api/ats/background-checks');

        // Assert that the response is successful
        $response2->assertStatus(200);

        // Assert that the responses are identical
        $this->assertEquals($response->getContent(), $response2->getContent());
    }
}
