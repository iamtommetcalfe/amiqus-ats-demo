<?php

namespace Database\Seeders;

use App\Models\Candidate;
use Faker\Factory as Faker;

class CandidateSeeder extends BaseSeeder
{
    /**
     * The model class to seed.
     *
     * @var string
     */
    protected $model = Candidate::class;

    /**
     * Get the data to seed.
     *
     * @return array
     */
    protected function getData(): array
    {
        $faker = Faker::create();
        $candidates = [];
        $sources = ['LinkedIn', 'Indeed', 'Referral', 'Company Website', 'Job Fair', 'University', 'Recruitment Agency'];

        // Generate 150 random candidates
        for ($i = 0; $i < 150; $i++) {
            $firstName = $faker->firstName;
            $lastName = $faker->lastName;

            $candidates[] = [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->phoneNumber,
                'current_company' => $faker->company,
                'current_position' => $faker->jobTitle,
                'source' => $faker->randomElement($sources),
            ];
        }

        return $candidates;
    }
}
