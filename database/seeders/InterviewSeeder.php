<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Interview;
use App\Models\InterviewStage;
use App\Models\JobPosting;
use Carbon\Carbon;

class InterviewSeeder extends BaseSeeder
{
    /**
     * The model class to seed.
     *
     * @var string
     */
    protected $model = Interview::class;

    /**
     * Cache for job postings, interview stages, and all candidates.
     *
     * @var array
     */
    protected $cache = [
        'jobPostings' => [],
        'stages' => [],
        'allCandidates' => [],
    ];

    /**
     * Hook that runs before seeding.
     */
    protected function beforeSeeding(): void
    {
        // Cache job postings by title for easier lookup
        JobPosting::all()->each(function ($jobPosting) {
            $this->cache['jobPostings'][$jobPosting->title] = $jobPosting->id;
        });

        // Get all candidates from the database
        $this->cache['allCandidates'] = Candidate::all()->pluck('id')->toArray();

        // Cache interview stages by name for easier lookup
        InterviewStage::orderBy('order')->get()->each(function ($stage) {
            $this->cache['stages'][$stage->name] = $stage->id;
        });
    }

    /**
     * Get the data to seed.
     *
     * @return array
     */
    protected function getData(): array
    {
        $data = [];
        $candidateCount = count($this->cache['allCandidates']);

        if ($candidateCount === 0) {
            return $data; // No candidates available
        }

        // Get all job posting IDs
        $jobPostingIds = array_values($this->cache['jobPostings']);
        $jobPostingCount = count($jobPostingIds);

        // Get all interview stage IDs
        $stageIds = array_values($this->cache['stages']);
        $stageCount = count($stageIds);

        // Possible statuses for interviews
        $statuses = ['pending', 'scheduled', 'completed'];

        // Create an interview record for each candidate
        foreach ($this->cache['allCandidates'] as $candidateId) {
            // Assign a random job posting
            $randomJobPostingId = $jobPostingIds[rand(0, $jobPostingCount - 1)];

            // Assign a random interview stage
            $randomStageId = $stageIds[rand(0, $stageCount - 1)];

            // Assign a random status
            $randomStatus = $statuses[rand(0, count($statuses) - 1)];

            // Create the interview record
            $interviewData = [
                'job_posting_id' => $randomJobPostingId,
                'candidate_id' => $candidateId,
                'interview_stage_id' => $randomStageId,
                'status' => $randomStatus,
            ];

            // Add scheduled_at date if status is scheduled
            if ($randomStatus === 'scheduled') {
                $interviewData['scheduled_at'] = Carbon::now()->addDays(rand(1, 30));
            }

            // Add notes for some interviews (about 20%)
            if (rand(1, 5) === 1) {
                $interviewData['notes'] = 'Notes for candidate #' . $candidateId;
            }

            $data[] = $interviewData;
        }

        return $data;
    }

    /**
     * Get the job posting ID by title.
     *
     * @param string $title
     * @return int
     */
    protected function getJobPostingId(string $title): int
    {
        return $this->cache['jobPostings'][$title] ?? 0;
    }


    /**
     * Get the interview stage ID by name.
     *
     * @param string $name
     * @return int
     */
    protected function getStageId(string $name): int
    {
        return $this->cache['stages'][$name] ?? 0;
    }
}
