<?php

namespace Database\Seeders;

use App\Models\InterviewStage;
use Illuminate\Database\Seeder;

class InterviewStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stages = [
            [
                'name' => 'Applied',
                'description' => 'Candidate has applied for the position',
                'order' => 1,
                'is_default' => true,
                'color' => '#6366F1', // Indigo
            ],
            [
                'name' => 'Resume Screening',
                'description' => 'Resume is being reviewed',
                'order' => 2,
                'is_default' => false,
                'color' => '#8B5CF6', // Violet
            ],
            [
                'name' => 'Phone Screen',
                'description' => 'Initial phone interview',
                'order' => 3,
                'is_default' => false,
                'color' => '#EC4899', // Pink
            ],
            [
                'name' => 'Technical Interview',
                'description' => 'Technical skills assessment',
                'order' => 4,
                'is_default' => false,
                'color' => '#F59E0B', // Amber
            ],
            [
                'name' => 'HR Interview',
                'description' => 'Interview with HR team',
                'order' => 5,
                'is_default' => false,
                'color' => '#10B981', // Emerald
            ],
            [
                'name' => 'Final Interview',
                'description' => 'Final interview with hiring manager',
                'order' => 6,
                'is_default' => false,
                'color' => '#3B82F6', // Blue
            ],
            [
                'name' => 'Offer',
                'description' => 'Job offer extended',
                'order' => 7,
                'is_default' => false,
                'color' => '#14B8A6', // Teal
            ],
            [
                'name' => 'Hired',
                'description' => 'Candidate has accepted the offer',
                'order' => 8,
                'is_default' => false,
                'color' => '#22C55E', // Green
            ],
            [
                'name' => 'Rejected',
                'description' => 'Candidate was not selected',
                'order' => 9,
                'is_default' => false,
                'color' => '#EF4444', // Red
            ],
        ];

        foreach ($stages as $stage) {
            InterviewStage::create($stage);
        }
    }
}
