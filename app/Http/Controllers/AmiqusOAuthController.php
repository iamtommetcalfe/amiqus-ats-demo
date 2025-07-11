<?php

namespace App\Http\Controllers;

use App\Http\Requests\AmiqusOAuth\StoreCredentialsRequest;
use App\Services\Interfaces\AmiqusOAuthServiceInterface;
use Illuminate\Http\Request;

class AmiqusOAuthController extends Controller
{
    /**
     * The Amiqus OAuth service instance.
     *
     * @var \App\Services\Interfaces\AmiqusOAuthServiceInterface
     */
    protected $amiqusOAuthService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(AmiqusOAuthServiceInterface $amiqusOAuthService)
    {
        $this->amiqusOAuthService = $amiqusOAuthService;
    }

    /**
     * Show the settings page for Amiqus integration.
     *
     * @return \Illuminate\Http\Response
     */
    public function settings()
    {
        $settings = $this->amiqusOAuthService->getSettings();

        return response()->json($settings);
    }

    /**
     * Store the client credentials.
     *
     * @return \Illuminate\Http\Response
     */
    public function storeCredentials(StoreCredentialsRequest $request)
    {
        $client = $this->amiqusOAuthService->storeCredentials($request->validated());

        return response()->json([
            'message' => 'Client credentials saved successfully.',
            'client' => $client,
        ], 200);
    }

    /**
     * Redirect the user to the Amiqus authorization page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider()
    {
        try {
            $authorizationUrl = $this->amiqusOAuthService->getAuthorizationUrl();

            return redirect($authorizationUrl);
        } catch (\Exception $e) {
            return redirect()->route('amiqus.settings')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Handle the callback from Amiqus.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback(Request $request)
    {
        $result = $this->amiqusOAuthService->handleProviderCallback($request);

        if ($result['success']) {
            return redirect()->route('amiqus.settings')
                ->with('success', $result['message']);
        } else {
            return redirect()->route('amiqus.settings')
                ->with('error', $result['message']);
        }
    }

    /**
     * Refresh the access token.
     *
     * @return \Illuminate\Http\Response
     */
    public function refreshToken()
    {
        $result = $this->amiqusOAuthService->refreshToken();

        if ($result['success']) {
            return response()->json([
                'message' => $result['message'],
                'success' => true,
            ], 200);
        } else {
            return response()->json([
                'message' => $result['message'],
                'success' => false,
                'error' => $result['error'] ?? null,
            ], 400);
        }
    }

    /**
     * Disconnect from Amiqus.
     *
     * @return \Illuminate\Http\Response
     */
    public function disconnect()
    {
        $success = $this->amiqusOAuthService->disconnect();

        return response()->json([
            'message' => 'Disconnected from Amiqus successfully.',
            'success' => $success,
        ], $success ? 200 : 400);
    }

    /**
     * Delete the client credentials.
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteCredentials()
    {
        $success = $this->amiqusOAuthService->deleteCredentials();

        if ($success) {
            return response()->json(['message' => 'Integration deleted successfully'], 200);
        }

        return response()->json(['message' => 'No active integration found'], 404);
    }

    /**
     * Test the connection to Amiqus API.
     *
     * @return \Illuminate\Http\Response
     */
    public function testConnection()
    {
        $result = $this->amiqusOAuthService->testConnection();

        return response()->json($result, $result['success'] ? 200 : 400);
    }
}
