<?php

namespace Database\Seeders;

use App\Models\Candidate;
use App\Models\Interview;
use App\Models\InterviewStage;
use App\Models\JobPosting;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class InterviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all job postings, candidates, and interview stages
        $jobPostings = JobPosting::all();
        $candidates = Candidate::all();
        $stages = InterviewStage::orderBy('order')->get();

        // Define some sample interviews
        $interviews = [
            // Senior Software Engineer position
            [
                'job_posting_id' => 1,
                'candidate_id' => 1, // John Doe
                'interview_stage_id' => 4, // Technical Interview
                'scheduled_at' => Carbon::now()->addDays(2),
                'status' => 'scheduled',
            ],
            [
                'job_posting_id' => 1,
                'candidate_id' => 2, // Jane Smith
                'interview_stage_id' => 5, // HR Interview
                'scheduled_at' => Carbon::now()->addDays(1),
                'status' => 'scheduled',
                'notes' => 'Candidate has excellent technical skills and passed the technical interview with flying colors.',
            ],
            [
                'job_posting_id' => 1,
                'candidate_id' => 8, // Emma Taylor
                'interview_stage_id' => 2, // Resume Screening
                'status' => 'pending',
            ],

            // Product Manager position
            [
                'job_posting_id' => 2,
                'candidate_id' => 3, // Michael Johnson
                'interview_stage_id' => 6, // Final Interview
                'scheduled_at' => Carbon::now()->addDays(3),
                'status' => 'scheduled',
                'notes' => 'Candidate has extensive product management experience and performed well in previous interviews.',
            ],
            [
                'job_posting_id' => 2,
                'candidate_id' => 9, // Robert Anderson
                'interview_stage_id' => 3, // Phone Screen
                'scheduled_at' => Carbon::now()->addDays(4),
                'status' => 'scheduled',
            ],

            // UX Designer position
            [
                'job_posting_id' => 3,
                'candidate_id' => 4, // Emily Williams
                'interview_stage_id' => 7, // Offer
                'status' => 'completed',
                'notes' => 'Offer extended: £65,000 per year with standard benefits package.',
            ],
            [
                'job_posting_id' => 3,
                'candidate_id' => 10, // Olivia Thomas
                'interview_stage_id' => 1, // Applied
                'status' => 'pending',
            ],

            // DevOps Engineer position
            [
                'job_posting_id' => 4,
                'candidate_id' => 5, // David Brown
                'interview_stage_id' => 4, // Technical Interview
                'scheduled_at' => Carbon::now()->addDays(5),
                'status' => 'scheduled',
            ],

            // Marketing Specialist position
            [
                'job_posting_id' => 5,
                'candidate_id' => 6, // Sarah Miller
                'interview_stage_id' => 3, // Phone Screen
                'scheduled_at' => Carbon::now()->addDays(2),
                'status' => 'scheduled',
            ],

            // Customer Support Representative position (closed)
            [
                'job_posting_id' => 6,
                'candidate_id' => 7, // James Wilson
                'interview_stage_id' => 8, // Hired
                'status' => 'completed',
                'notes' => 'Candidate accepted the offer and will start on ' . Carbon::now()->addDays(14)->format('Y-m-d'),
            ],
        ];

        // Create the interviews
        foreach ($interviews as $interview) {
            Interview::create($interview);
        }
    }
}
