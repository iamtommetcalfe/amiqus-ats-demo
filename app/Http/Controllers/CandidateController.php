<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\InterviewStage;
use App\Services\Interfaces\AmiqusOAuthServiceInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
     * The HTTP client instance.
     *
     * @var \GuzzleHttp\Client
     */
    protected $httpClient;

    /**
     * Create a new controller instance.
     *
     * @param \App\Services\Interfaces\AmiqusOAuthServiceInterface $amiqusOAuthService
     * @param \GuzzleHttp\Client $httpClient
     * @return void
     */
    public function __construct(AmiqusOAuthServiceInterface $amiqusOAuthService, Client $httpClient)
    {
        $this->amiqusOAuthService = $amiqusOAuthService;
        $this->httpClient = $httpClient;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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

        return response()->json([
            'candidate' => $candidate,
            'applications' => $applications,
            'amiqus' => [
                'is_connected' => $candidate->isConnectedToAmiqus(),
                'client_url' => $amiqusClientUrl,
            ],
        ]);
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
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function createAmiqusClient(Request $request, string $id)
    {
        // Find the candidate
        $candidate = Candidate::findOrFail($id);

        // Check if the candidate is already connected to Amiqus
        if ($candidate->isConnectedToAmiqus()) {
            return response()->json([
                'success' => false,
                'message' => 'Candidate is already connected to an Amiqus client.',
                'candidate' => $candidate,
                'amiqus_client_url' => $candidate->getAmiqusClientUrl(),
            ], 400);
        }

        // Check if there's an active connection
        $settings = $this->amiqusOAuthService->getSettings();

        if (!$settings['isConnected']) {
            return response()->json([
                'success' => false,
                'message' => 'No active connection to Amiqus. Please connect first.'
            ], 400);
        }

        $client = $settings['client'];
        $accessToken = $client->accessTokens()->whereDate('expires_at', '>', Carbon::now())->latest()->first();

        if (!$accessToken) {
            return response()->json([
                'success' => false,
                'message' => 'No valid access token found. Please reconnect to Amiqus.'
            ], 400);
        }

        try {
            // Prepare the payload for creating a client in Amiqus
            $payload = [
                'name' => [
                    'title' => $request->has('title') ? $request->input('title') : null,
                    'first_name' => $candidate->first_name,
                    'middle_name' => $request->has('middle_name') ? $request->input('middle_name') : null,
                    'last_name' => $candidate->last_name,
                ],
                'email' => $candidate->email,
                'reference' => $request->has('reference') ? $request->input('reference') : 'amiqus-ats-demo-' . $candidate->id,
            ];

            // Make request to Amiqus API to create a client
            $response = $this->httpClient->post(config('amiqus.auth_url') . config('amiqus.endpoints.clients'), [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken->access_token,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
            ]);

            $data = json_decode((string) $response->getBody(), true);

            if (!isset($data['id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid response from Amiqus API.'
                ], 400);
            }

            // Update the candidate with the Amiqus client ID
            $candidate->amiqus_client_id = $data['id'];
            $candidate->save();

            return response()->json([
                'success' => true,
                'message' => 'Amiqus client created and linked to candidate successfully.',
                'candidate' => $candidate,
                'amiqus_client' => $data,
                'amiqus_client_url' => $candidate->getAmiqusClientUrl(),
            ]);
        } catch (RequestException $e) {
            Log::error('Amiqus API client creation error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create client in Amiqus API.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a client in Amiqus.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function updateAmiqusClient(Request $request, string $id)
    {
        // Find the candidate
        $candidate = Candidate::findOrFail($id);

        // Check if the candidate is connected to Amiqus
        if (!$candidate->isConnectedToAmiqus()) {
            return response()->json([
                'success' => false,
                'message' => 'Candidate is not connected to an Amiqus client. Please create an Amiqus client first.',
            ], 400);
        }

        // Check if there's an active connection
        $settings = $this->amiqusOAuthService->getSettings();

        if (!$settings['isConnected']) {
            return response()->json([
                'success' => false,
                'message' => 'No active connection to Amiqus. Please connect first.'
            ], 400);
        }

        $client = $settings['client'];
        $accessToken = $client->accessTokens()->whereDate('expires_at', '>', Carbon::now())->latest()->first();

        if (!$accessToken) {
            return response()->json([
                'success' => false,
                'message' => 'No valid access token found. Please reconnect to Amiqus.'
            ], 400);
        }

        try {
            // Prepare the payload for updating a client in Amiqus
            $payload = [
                'name' => [
                    'title' => $request->has('title') ? $request->input('title') : null,
                    'first_name' => $candidate->first_name,
                    'middle_name' => $request->has('middle_name') ? $request->input('middle_name') : null,
                    'last_name' => $candidate->last_name,
                ],
                'email' => $candidate->email,
                'reference' => $request->has('reference') ? $request->input('reference') : 'amiqus-ats-demo-' . $candidate->id,
            ];

            // Make request to Amiqus API to update a client
            $response = $this->httpClient->patch(config('amiqus.auth_url') . config('amiqus.endpoints.clients') . '/' . $candidate->amiqus_client_id, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken->access_token,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
            ]);

            $data = json_decode((string) $response->getBody(), true);

            if (!isset($data['id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid response from Amiqus API.'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Amiqus client updated successfully.',
                'candidate' => $candidate,
                'amiqus_client' => $data,
                'amiqus_client_url' => $candidate->getAmiqusClientUrl(),
            ]);
        } catch (RequestException $e) {
            Log::error('Amiqus API client update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update client in Amiqus API.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
