<?php

namespace App\Http\Controllers;

use App\Models\Interview;
use App\Models\InterviewStage;
use App\Models\JobPosting;
use Illuminate\Http\Request;

class JobPostingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all open job postings with applicant counts, ordered by most recent
        $jobPostings = JobPosting::open()
            ->withCount('interviews as applicants_count')
            ->orderBy('posted_at', 'desc')
            ->get();

        return response()->json($jobPostings);
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
        // Get the job posting with its applicants grouped by interview stage
        $jobPosting = JobPosting::with(['interviews.candidate', 'interviews.interviewStage'])
            ->findOrFail($id);

        // Group applicants by interview stage
        $stages = InterviewStage::orderBy('order')->get();
        $applicantsByStage = [];

        foreach ($stages as $stage) {
            $applicantsByStage[$stage->id] = [
                'stage' => $stage,
                'applicants' => $jobPosting->interviews()->where('interview_stage_id', $stage->id)
                    ->with('candidate')
                    ->get()
                    ->map(function ($interview) {
                        return [
                            'id' => $interview->id,
                            'candidate' => $interview->candidate,
                            'scheduled_at' => $interview->scheduled_at,
                            'status' => $interview->status,
                            'notes' => $interview->notes,
                        ];
                    }),
            ];
        }

        return response()->json([
            'job_posting' => $jobPosting,
            'applicants_by_stage' => $applicantsByStage,
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
}
