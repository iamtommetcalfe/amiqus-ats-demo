<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\JobPosting;
use App\Services\Interfaces\ApiResponseServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{
    /**
     * The API response service instance.
     *
     * @var \App\Services\Interfaces\ApiResponseServiceInterface
     */
    protected $apiResponse;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ApiResponseServiceInterface $apiResponse)
    {
        $this->apiResponse = $apiResponse;
    }

    /**
     * Search for candidates and job postings.
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        try {
            $query = $request->input('query');

            if (empty($query)) {
                return $this->apiResponse->send(
                    $this->apiResponse->success([
                        'candidates' => [],
                        'jobs' => [],
                    ], 'Empty search query')
                );
            }

            // Search for candidates
            $candidates = Candidate::where('first_name', 'like', "%{$query}%")
                ->orWhere('last_name', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")
                ->orWhere('current_position', 'like', "%{$query}%")
                ->orWhere('current_company', 'like', "%{$query}%")
                ->limit(5)
                ->get();

            // Search for job postings
            $jobs = JobPosting::where('title', 'like', "%{$query}%")
                ->orWhere('department', 'like', "%{$query}%")
                ->orWhere('location', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->limit(5)
                ->get();

            return $this->apiResponse->send(
                $this->apiResponse->success([
                    'candidates' => $candidates,
                    'jobs' => $jobs,
                ], 'Search results retrieved successfully')
            );
        } catch (\Exception $e) {
            Log::error('Error performing search: '.$e->getMessage());

            return $this->apiResponse->send(
                $this->apiResponse->serverError('An error occurred while performing the search')
            );
        }
    }
}
