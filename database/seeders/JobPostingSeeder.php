<?php

namespace Database\Seeders;

use App\Models\JobPosting;
use Carbon\Carbon;

class JobPostingSeeder extends BaseSeeder
{
    /**
     * The model class to seed.
     *
     * @var string
     */
    protected $model = JobPosting::class;

    /**
     * Get the data to seed.
     *
     * @return array
     */
    protected function getData(): array
    {
        return [
            [
                'title' => 'Senior Software Engineer',
                'description' => 'We are looking for a Senior Software Engineer to join our team. You will be responsible for developing and maintaining our core products.',
                'location' => 'Edinburgh, UK',
                'department' => 'Engineering',
                'employment_type' => 'Full-time',
                'salary_min' => 70000,
                'salary_max' => 90000,
                'status' => 'open',
                'posted_at' => Carbon::now()->subDays(30),
                'closes_at' => Carbon::now()->addDays(30),
            ],
            [
                'title' => 'Product Manager',
                'description' => 'We are seeking a Product Manager to help us define and execute our product roadmap. You will work closely with engineering, design, and business teams.',
                'location' => 'Glasgow, UK',
                'department' => 'Product',
                'employment_type' => 'Full-time',
                'salary_min' => 60000,
                'salary_max' => 80000,
                'status' => 'open',
                'posted_at' => Carbon::now()->subDays(15),
                'closes_at' => Carbon::now()->addDays(45),
            ],
            [
                'title' => 'UX Designer',
                'description' => 'We are looking for a UX Designer to create amazing user experiences. You will work on designing and prototyping new features.',
                'location' => 'Remote',
                'department' => 'Design',
                'employment_type' => 'Full-time',
                'salary_min' => 50000,
                'salary_max' => 70000,
                'status' => 'open',
                'posted_at' => Carbon::now()->subDays(10),
                'closes_at' => Carbon::now()->addDays(50),
            ],
            [
                'title' => 'DevOps Engineer',
                'description' => 'We are seeking a DevOps Engineer to help us build and maintain our infrastructure. You will be responsible for deployment, scaling, and security.',
                'location' => 'Edinburgh, UK',
                'department' => 'Engineering',
                'employment_type' => 'Full-time',
                'salary_min' => 65000,
                'salary_max' => 85000,
                'status' => 'open',
                'posted_at' => Carbon::now()->subDays(5),
                'closes_at' => Carbon::now()->addDays(55),
            ],
            [
                'title' => 'Marketing Specialist',
                'description' => 'We are looking for a Marketing Specialist to help us grow our brand. You will be responsible for creating and executing marketing campaigns.',
                'location' => 'London, UK',
                'department' => 'Marketing',
                'employment_type' => 'Full-time',
                'salary_min' => 45000,
                'salary_max' => 60000,
                'status' => 'open',
                'posted_at' => Carbon::now()->subDays(2),
                'closes_at' => Carbon::now()->addDays(58),
            ],
            [
                'title' => 'Customer Support Representative',
                'description' => 'We are seeking a Customer Support Representative to help our customers succeed. You will be the first point of contact for customer inquiries.',
                'location' => 'Glasgow, UK',
                'department' => 'Customer Support',
                'employment_type' => 'Full-time',
                'salary_min' => 30000,
                'salary_max' => 40000,
                'status' => 'closed',
                'posted_at' => Carbon::now()->subDays(60),
                'closes_at' => Carbon::now()->subDays(10),
            ],
        ];
    }
}
