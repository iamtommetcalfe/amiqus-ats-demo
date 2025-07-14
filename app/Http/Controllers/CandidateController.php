<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Services\Interfaces\AmiqusClientServiceInterface;
use App\Services\Interfaces\AmiqusOAuthServiceInterface;
use App\Services\Interfaces\ApiResponseServiceInterface;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CandidateController extends Controller
{
    /**
     * The Amiqus OAuth service instance.
     *
     * @var \App\Services\Interfaces\AmiqusOAuthServiceInterface
     */
    protected $amiqusOAuthService;

    /**
     * The Amiqus Client service instance.
     *
     * @var \App\Services\Interfaces\AmiqusClientServiceInterface
     */
    protected $amiqusClientService;

    /**
     * The HTTP client instance.
     *
     * @var \GuzzleHttp\Client
     */
    protected $httpClient;

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
    public function __construct(
        AmiqusOAuthServiceInterface $amiqusOAuthService,
        AmiqusClientServiceInterface $amiqusClientService,
        ApiResponseServiceInterface $apiResponse,
        Client $httpClient
    ) {
        $this->amiqusOAuthService = $amiqusOAuthService;
        $this->amiqusClientService = $amiqusClientService;
        $this->apiResponse = $apiResponse;
        $this->httpClient = $httpClient;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Cache the response for 10 minutes
            return Cache::remember('candidates.index', 600, function () {
                // Get all candidates sorted by newest first
                $candidates = Candidate::orderBy('created_at', 'desc')->get();

                return $this->apiResponse->send(
                    $this->apiResponse->success($candidates, 'Candidates retrieved successfully')
                );
            });
        } catch (\Exception $e) {
            Log::error('Error retrieving candidates: '.$e->getMessage());

            return $this->apiResponse->send(
                $this->apiResponse->serverError('An error occurred while retrieving candidates')
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            // Cache the response for 10 minutes
            return Cache::remember("candidates.show.{$id}", 600, function () use ($id) {
                // Get the candidate with their job applications
                $candidate = Candidate::with(['interviews.interviewStage', 'jobPostings'])
                    ->findOrFail($id);

                // Group applications by job posting
                $applications = [];
                foreach ($candidate->interviews as $interview) {
                    $jobPosting = $interview->jobPosting;
                    $applications[] = [
                        'job_posting' => $jobPosting,
                        'interview_stage' => $interview->interviewStage,
                        'status' => $interview->status,
                        'scheduled_at' => $interview->scheduled_at,
                        'notes' => $interview->notes,
                        'feedback' => $interview->feedback,
                    ];
                }

                // Add Amiqus client information if the candidate is connected
                $amiqusClientUrl = null;
                if ($candidate->isConnectedToAmiqus()) {
                    $amiqusClientUrl = $candidate->getAmiqusClientUrl();
                }

                $data = [
                    'candidate' => $candidate,
                    'applications' => $applications,
                    'amiqus' => [
                        'is_connected' => $candidate->isConnectedToAmiqus(),
                        'client_url' => $amiqusClientUrl,
                    ],
                ];

                return $this->apiResponse->send(
                    $this->apiResponse->success($data, 'Candidate details retrieved successfully')
                );
            });
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->apiResponse->send(
                $this->apiResponse->notFound('Candidate not found')
            );
        } catch (\Exception $e) {
            Log::error('Error retrieving candidate: '.$e->getMessage());

            return $this->apiResponse->send(
                $this->apiResponse->serverError('An error occurred while retrieving the candidate')
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Create a client in Amiqus and link it to the candidate.
     *
     * @return \Illuminate\Http\Response
     */
    public function createAmiqusClient(Request $request, string $id)
    {
        try {
            // Find the candidate
            $candidate = Candidate::findOrFail($id);

            // Use the service to create the client
            $result = $this->amiqusClientService->createClient($candidate, $request);

            // Check if the operation was successful
            if ($result['success']) {
                return $this->apiResponse->send(
                    $this->apiResponse->success(
                        collect($result)->except(['success', 'message', 'status_code'])->toArray(),
                        $result['message'],
                        $result['status_code'] ?? 200
                    )
                );
            } else {
                return $this->apiResponse->send(
                    $this->apiResponse->error(
                        $result['message'],
                        $result['error'] ?? null,
                        $result['status_code'] ?? 400
                    )
                );
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->apiResponse->send(
                $this->apiResponse->notFound('Candidate not found')
            );
        } catch (\Exception $e) {
            Log::error('Error creating Amiqus client: '.$e->getMessage());

            return $this->apiResponse->send(
                $this->apiResponse->serverError('An error occurred while creating the Amiqus client')
            );
        }
    }

    /**
     * Update a client in Amiqus.
     *
     * @return \Illuminate\Http\Response
     */
    public function updateAmiqusClient(Request $request, string $id)
    {
        try {
            // Find the candidate
            $candidate = Candidate::findOrFail($id);

            // Use the service to update the client
            $result = $this->amiqusClientService->updateClient($candidate, $request);

            // Check if the operation was successful
            if ($result['success']) {
                return $this->apiResponse->send(
                    $this->apiResponse->success(
                        collect($result)->except(['success', 'message', 'status_code'])->toArray(),
                        $result['message'],
                        $result['status_code'] ?? 200
                    )
                );
            } else {
                return $this->apiResponse->send(
                    $this->apiResponse->error(
                        $result['message'],
                        $result['error'] ?? null,
                        $result['status_code'] ?? 400
                    )
                );
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->apiResponse->send(
                $this->apiResponse->notFound('Candidate not found')
            );
        } catch (\Exception $e) {
            Log::error('Error updating Amiqus client: '.$e->getMessage());

            return $this->apiResponse->send(
                $this->apiResponse->serverError('An error occurred while updating the Amiqus client')
            );
        }
    }
}
