<?php

namespace Database\Seeders;

use App\Models\Candidate;
use Illuminate\Database\Seeder;

class CandidateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $candidates = [
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@example.com',
                'phone' => '+44 7700 900123',
                'current_company' => 'ABC Tech',
                'current_position' => 'Software Engineer',
                'source' => 'LinkedIn',
            ],
            [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane.smith@example.com',
                'phone' => '+44 7700 900124',
                'current_company' => 'XYZ Solutions',
                'current_position' => 'Senior Developer',
                'source' => 'Indeed',
            ],
            [
                'first_name' => 'Michael',
                'last_name' => 'Johnson',
                'email' => 'michael.johnson@example.com',
                'phone' => '+44 7700 900125',
                'current_company' => 'Tech Innovators',
                'current_position' => 'Product Manager',
                'source' => 'Referral',
            ],
            [
                'first_name' => 'Emily',
                'last_name' => 'Williams',
                'email' => 'emily.williams@example.com',
                'phone' => '+44 7700 900126',
                'current_company' => 'Design Masters',
                'current_position' => 'UX Designer',
                'source' => 'Company Website',
            ],
            [
                'first_name' => 'David',
                'last_name' => 'Brown',
                'email' => 'david.brown@example.com',
                'phone' => '+44 7700 900127',
                'current_company' => 'Cloud Systems',
                'current_position' => 'DevOps Engineer',
                'source' => 'LinkedIn',
            ],
            [
                'first_name' => 'Sarah',
                'last_name' => 'Miller',
                'email' => 'sarah.miller@example.com',
                'phone' => '+44 7700 900128',
                'current_company' => 'Marketing Pro',
                'current_position' => 'Marketing Manager',
                'source' => 'Indeed',
            ],
            [
                'first_name' => 'James',
                'last_name' => 'Wilson',
                'email' => 'james.wilson@example.com',
                'phone' => '+44 7700 900129',
                'current_company' => 'Support Solutions',
                'current_position' => 'Customer Support Lead',
                'source' => 'Referral',
            ],
            [
                'first_name' => 'Emma',
                'last_name' => 'Taylor',
                'email' => 'emma.taylor@example.com',
                'phone' => '+44 7700 900130',
                'current_company' => 'Data Insights',
                'current_position' => 'Data Scientist',
                'source' => 'LinkedIn',
            ],
            [
                'first_name' => 'Robert',
                'last_name' => 'Anderson',
                'email' => 'robert.anderson@example.com',
                'phone' => '+44 7700 900131',
                'current_company' => 'Security First',
                'current_position' => 'Security Engineer',
                'source' => 'Company Website',
            ],
            [
                'first_name' => 'Olivia',
                'last_name' => 'Thomas',
                'email' => 'olivia.thomas@example.com',
                'phone' => '+44 7700 900132',
                'current_company' => 'Frontend Experts',
                'current_position' => 'Frontend Developer',
                'source' => 'Indeed',
            ],
        ];

        foreach ($candidates as $candidate) {
            Candidate::create($candidate);
        }
    }
}
