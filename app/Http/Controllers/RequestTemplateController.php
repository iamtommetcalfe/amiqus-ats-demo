<?php

namespace App\Http\Controllers;

use App\Models\RequestTemplate;
use App\Services\Interfaces\AmiqusOAuthServiceInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class RequestTemplateController extends Controller
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
     * @return void
     */
    public function __construct(AmiqusOAuthServiceInterface $amiqusOAuthService, Client $httpClient)
    {
        $this->amiqusOAuthService = $amiqusOAuthService;
        $this->httpClient = $httpClient;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $templates = RequestTemplate::orderBy('name')->get();

        return response()->json([
            'templates' => $templates,
        ]);
    }

    /**
     * Import templates from Amiqus API.
     *
     * @return \Illuminate\Http\Response
     */
    public function import()
    {
        // Check if there's an active connection
        $settings = $this->amiqusOAuthService->getSettings();

        if (! $settings['isConnected']) {
            return response()->json([
                'success' => false,
                'message' => 'No active connection to Amiqus. Please connect first.',
            ], 400);
        }

        $client = $settings['client'];
        $accessToken = $client->accessTokens()->whereDate('expires_at', '>', Carbon::now())->latest()->first();

        if (! $accessToken) {
            return response()->json([
                'success' => false,
                'message' => 'No valid access token found. Please reconnect to Amiqus.',
            ], 400);
        }

        try {
            // Make request to Amiqus API to get templates
            $response = $this->httpClient->get(config('amiqus.auth_url').config('amiqus.endpoints.templates'), [
                'headers' => [
                    'Authorization' => 'Bearer '.$accessToken->access_token,
                    'Accept' => 'application/json',
                ],
            ]);

            $data = json_decode((string) $response->getBody(), true);

            if (! isset($data['data']) || ! is_array($data['data'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid response from Amiqus API.',
                ], 400);
            }

            $imported = 0;
            $updated = 0;

            // Process templates
            foreach ($data['data'] as $templateData) {
                $template = RequestTemplate::updateOrCreate(
                    ['amiqus_id' => $templateData['id']],
                    [
                        'name' => $templateData['name'],
                        'description' => $templateData['description'] ?? null,
                        'presets' => $templateData['presets'] ?? null,
                        'is_enabled' => $templateData['is_enabled'] ?? true,
                        'amiqus_created_at' => isset($templateData['created_at']) ? Carbon::parse($templateData['created_at']) : null,
                        'amiqus_updated_at' => isset($templateData['updated_at']) ? Carbon::parse($templateData['updated_at']) : null,
                    ]
                );

                if ($template->wasRecentlyCreated) {
                    $imported++;
                } else {
                    $updated++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Templates imported successfully. Imported: $imported, Updated: $updated",
                'imported' => $imported,
                'updated' => $updated,
                'total' => count($data['data']),
            ]);
        } catch (RequestException $e) {
            Log::error('Amiqus API import error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to import templates from Amiqus API.',
                'error' => $e->getMessage(),
            ], 500);
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
        //
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
}
