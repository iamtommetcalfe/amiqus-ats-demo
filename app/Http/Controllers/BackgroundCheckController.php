<?php

namespace App\Http\Controllers;

use App\Models\BackgroundCheck;
use App\Models\Candidate;
use App\Models\RequestTemplate;
use App\Services\Interfaces\AmiqusOAuthServiceInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BackgroundCheckController extends Controller
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
     * Display a listing of background checks for a candidate.
     *
     * @param  string  $candidateId
     * @return \Illuminate\Http\Response
     */
    public function index(string $candidateId)
    {
        $candidate = Candidate::findOrFail($candidateId);

        $backgroundChecks = $candidate->backgroundChecks()
            ->with('requestTemplate')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($check) {
                return [
                    'id' => $check->id,
                    'amiqus_record_id' => $check->amiqus_record_id,
                    'status' => $check->status,
                    'template_name' => $check->requestTemplate->name,
                    'created_at' => $check->created_at,
                    'expires_at' => $check->expires_at,
                    'completed_at' => $check->completed_at,
                    'amiqus_record_url' => $check->getAmiqusRecordUrl(),
                ];
            });

        return response()->json([
            'background_checks' => $backgroundChecks,
        ]);
    }

    /**
     * Create a new background check for a candidate.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $candidateId
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, string $candidateId)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'template_id' => 'required|exists:request_templates,amiqus_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Find the candidate
        $candidate = Candidate::findOrFail($candidateId);

        // Check if the candidate is connected to Amiqus
        if (!$candidate->isConnectedToAmiqus()) {
            return response()->json([
                'success' => false,
                'message' => 'Candidate is not connected to an Amiqus client. Please create an Amiqus client first.'
            ], 400);
        }

        // Find the request template
        $template = RequestTemplate::where('amiqus_id', $request->template_id)->first();
        if (!$template) {
            return response()->json([
                'success' => false,
                'message' => 'Request template not found.'
            ], 404);
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
            // Prepare the payload for creating a record in Amiqus
            $payload = [
                'client' => $candidate->amiqus_client_id,
                'template' => $request->template_id,
            ];

            // Make request to Amiqus API to create a record
            $response = $this->httpClient->post(config('amiqus.auth_url') . '/api/v2/records', [
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

            // Create a new background check record
            $backgroundCheck = new BackgroundCheck([
                'candidate_id' => $candidate->id,
                'request_template_id' => $template->id,
                'amiqus_record_id' => $data['id'],
                'amiqus_client_id' => $candidate->amiqus_client_id,
                'status' => $data['status'] ?? 'pending',
                'perform_url' => $data['perform_url'] ?? null,
                'response_data' => $data,
                'expires_at' => isset($data['expired_at']) ? Carbon::parse($data['expired_at']) : null,
                'completed_at' => isset($data['completed_at']) ? Carbon::parse($data['completed_at']) : null,
            ]);

            $backgroundCheck->save();

            return response()->json([
                'success' => true,
                'message' => 'Background check created successfully.',
                'background_check' => [
                    'id' => $backgroundCheck->id,
                    'amiqus_record_id' => $backgroundCheck->amiqus_record_id,
                    'status' => $backgroundCheck->status,
                    'template_name' => $template->name,
                    'created_at' => $backgroundCheck->created_at,
                    'expires_at' => $backgroundCheck->expires_at,
                    'completed_at' => $backgroundCheck->completed_at,
                    'amiqus_record_url' => $backgroundCheck->getAmiqusRecordUrl(),
                ],
            ]);
        } catch (RequestException $e) {
            Log::error('Amiqus API record creation error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create record in Amiqus API.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
