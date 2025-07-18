<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Check if the test user already exists before creating it
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => 'password',
            ]
        );

        // Seed ATS data
        // CandidateSeeder must run first to create random candidates
        $this->call([
            CandidateSeeder::class,
            InterviewStageSeeder::class,
            JobPostingSeeder::class,
            InterviewSeeder::class,
        ]);
    }
}
