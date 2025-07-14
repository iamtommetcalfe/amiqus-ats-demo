<?php

namespace App\Http\Controllers;

use App\Models\Candidate;

class AmiqusApiLogController extends Controller
{
    /**
     * Display a listing of API logs for a candidate.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(string $candidateId)
    {
        // Find the candidate
        $candidate = Candidate::findOrFail($candidateId);

        // Get the API logs for the candidate
        $apiLogs = $candidate->apiLogs()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'method' => $log->method,
                    'url' => $log->url,
                    'request_headers' => $log->request_headers,
                    'request_body' => $log->request_body,
                    'response_status' => $log->response_status,
                    'response_headers' => $log->response_headers,
                    'response_body' => $log->response_body,
                    'duration' => $log->duration,
                    'error' => $log->error,
                    'created_at' => $log->created_at,
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'API logs retrieved successfully',
            'data' => [
                'api_logs' => $apiLogs,
            ],
        ]);
    }
}
