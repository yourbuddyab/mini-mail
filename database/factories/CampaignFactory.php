<?php

namespace Database\Factories;

use App\Models\Campaign;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CampaignFactory extends Factory
{
    /**
     * The name of the corresponding model.
     *
     * @var string
     */
    protected $model = Campaign::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->sentence,
            'user_id' => \App\Models\User::factory(), // Assuming you have a User model
            'csv_file' => $this->faker->filePath(), // Assuming a CSV file path is needed
            'scheduled_at' => now()->addDays(1),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
