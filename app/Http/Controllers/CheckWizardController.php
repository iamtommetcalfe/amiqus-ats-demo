<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\RequestTemplate;
use App\Services\Interfaces\AmiqusClientServiceInterface;
use App\Services\Interfaces\AmiqusOAuthServiceInterface;
use App\Services\Interfaces\ApiResponseServiceInterface;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckWizardController extends Controller
{
    /**
     * The API response service instance.
     *
     * @var \App\Services\Interfaces\ApiResponseServiceInterface
     */
    protected $apiResponse;

    /**
     * The Amiqus OAuth service instance.
     *
     * @var \App\Services\Interfaces\AmiqusOAuthServiceInterface
     */
    protected $amiqusOAuthService;

    /**
     * The Amiqus client service instance.
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
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        ApiResponseServiceInterface $apiResponse,
        AmiqusOAuthServiceInterface $amiqusOAuthService,
        AmiqusClientServiceInterface $amiqusClientService,
        Client $httpClient
    ) {
        $this->apiResponse = $apiResponse;
        $this->amiqusOAuthService = $amiqusOAuthService;
        $this->amiqusClientService = $amiqusClientService;
        $this->httpClient = $httpClient;
    }

    /**
     * Get all candidates for the search dropdown.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCandidates(Request $request)
    {
        try {
            $query = $request->input('query', '');

            if (empty($query)) {
                return $this->apiResponse->send(
                    $this->apiResponse->success([
                        'candidates' => [],
                    ], 'Empty search query')
                );
            }

            // Search for candidates
            $candidates = Candidate::where('first_name', 'like', "%{$query}%")
                ->orWhere('last_name', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%")
                ->orWhere('current_position', 'like', "%{$query}%")
                ->orWhere('current_company', 'like', "%{$query}%")
                ->limit(10)
                ->get();

            return $this->apiResponse->send(
                $this->apiResponse->success([
                    'candidates' => $candidates,
                ], 'Candidates retrieved successfully')
            );
        } catch (\Exception $e) {
            Log::error('Error retrieving candidates: '.$e->getMessage());

            return $this->apiResponse->send(
                $this->apiResponse->serverError('An error occurred while retrieving candidates')
            );
        }
    }

    /**
     * Get all request templates.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTemplates()
    {
        try {
            $templates = RequestTemplate::orderBy('name')->get();

            return $this->apiResponse->send(
                $this->apiResponse->success([
                    'templates' => $templates,
                ], 'Templates retrieved successfully')
            );
        } catch (\Exception $e) {
            Log::error('Error retrieving templates: '.$e->getMessage());

            return $this->apiResponse->send(
                $this->apiResponse->serverError('An error occurred while retrieving templates')
            );
        }
    }

    /**
     * Process the wizard submission.
     *
     * @return \Illuminate\Http\Response
     */
    public function process(Request $request)
    {
        try {
            // Validate request
            $request->validate([
                'candidate_id' => 'required|exists:candidates,id',
                'template_id' => 'required|exists:request_templates,amiqus_id',
            ]);

            // Find the candidate
            $candidate = Candidate::findOrFail($request->candidate_id);

            // Check if the candidate is connected to Amiqus
            if (! $candidate->isConnectedToAmiqus()) {
                // Create Amiqus client for the candidate
                $clientResult = $this->amiqusClientService->createClient($candidate, $request);

                if (! $clientResult['success']) {
                    return $this->apiResponse->send(
                        $this->apiResponse->error(
                            $clientResult['message'],
                            $clientResult['error'] ?? null,
                            $clientResult['status_code'] ?? 400
                        )
                    );
                }
            }

            // Find the request template
            $template = RequestTemplate::where('amiqus_id', $request->template_id)->first();
            if (! $template) {
                return $this->apiResponse->send(
                    $this->apiResponse->error('Request template not found.', null, 404)
                );
            }

            // Check if there's an active connection
            $settings = $this->amiqusOAuthService->getSettings();

            if (! $settings['isConnected']) {
                return $this->apiResponse->send(
                    $this->apiResponse->error('No active connection to Amiqus. Please connect first.', null, 400)
                );
            }

            // Create the background check
            $backgroundCheckRequest = new Request([
                'template_id' => $request->template_id,
            ]);

            // Create a new instance of BackgroundCheckController
            $backgroundCheckController = app()->make(BackgroundCheckController::class);

            // Call the store method
            $response = $backgroundCheckController->store($backgroundCheckRequest, $request->candidate_id);

            // Get the response data
            $responseData = json_decode($response->getContent(), true);

            // Add the candidate ID to the response for redirection
            $responseData['candidate_id'] = $request->candidate_id;

            return response()->json($responseData, $response->getStatusCode());
        } catch (\Exception $e) {
            Log::error('Error processing check wizard: '.$e->getMessage());

            return $this->apiResponse->send(
                $this->apiResponse->serverError('An error occurred while processing the check wizard')
            );
        }
    }
}
